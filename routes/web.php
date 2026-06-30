<?php

use App\Http\Controllers\AcceptJsPaymentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EbooksController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\AuthorizeNetWebhookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomCheckoutController;
use App\Http\Controllers\EbookCheckoutController;
use App\Http\Controllers\FundingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PaymentAgreementController;
use App\Http\Controllers\ReviewerPreviewController;
use App\Http\Controllers\StrategyCallController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

// TEMPORARY one-time lead → GoHighLevel backfill. Token protected. Remove after use.
// Sends the `count` popup leads that come AFTER the lead named `after`, in the
// same newest-first order as the admin "Popup Submissions" list.
Route::get('/__lc_ghl_lead_backfill', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('k') === 'bf_ghl_7kQ2Lm', 404);
    $after = trim((string) $request->query('after', ''));
    $count = max(0, min(100, (int) $request->query('count', 20)));   // count=0 => no send, just report
    $ghlUrl = 'https://services.leadconnectorhq.com/hooks/rUFLKDzTiRHBm6G7eKbH/webhook-trigger/f3551d85-eebf-4072-abed-4232736efad1';

    $all = \App\Models\Lead::latest()->get();   // newest-first, same as admin
    $start = 0;
    if ($after !== '') {
        $idx = $all->search(fn ($l) => stripos((string) $l->name, $after) !== false);
        if ($idx === false) {
            return response()->json(['error' => "lead named '{$after}' not found"], 404);
        }
        $start = $idx + 1;
    }
    $batch = $all->slice($start, $count)->values();

    $sent = 0; $failed = 0; $log = [];
    foreach ($batch as $l) {
        $payload = [
            'type'         => 'lead',
            'submitted_at' => (string) $l->created_at,
            'name'         => $l->name ?? '',
            'email'        => $l->email,
            'phone'        => $l->phone ?? '',
            'score'        => $l->score ?? '',
            'issue'        => $l->issue ?? '',
            'goal'         => $l->goal ?? '',
            'source'       => $l->source ?? 'popup',
            'ip_address'   => $l->ip ?? '',
        ];
        $ch = curl_init($ghlUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code >= 200 && $code < 400) { $sent++; } else { $failed++; }
        $log[] = ['name' => $l->name, 'email' => $l->email, 'http' => $code];
        usleep(200000); // 0.2s between sends
    }
    return response()->json([
        'anchor' => $after, 'start_index' => $start, 'requested' => $count,
        'total_leads' => $all->count(),
        'remaining_after_batch' => max(0, $all->count() - ($start + $batch->count())),
        'sent' => $sent, 'failed' => $failed, 'leads' => $log,
    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// TEMPORARY: export all status=new popup leads as JSON (no GHL send). With ?mark=1
// it also flips exactly those leads to status=contacted. Token protected. Remove after use.
Route::get('/__lc_export_new_leads', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('k') === 'bf_ghl_7kQ2Lm', 404);
    $mark = $request->query('mark') === '1';

    $leads = \App\Models\Lead::where('status', 'new')->latest()->get();
    $data = $leads->map(fn ($l) => [
        'id'         => $l->id,
        'created_at' => (string) $l->created_at,
        'name'       => $l->name,
        'email'      => $l->email,
        'phone'      => $l->phone,
        'score'      => $l->score,
        'issue'      => $l->issue,
        'goal'       => $l->goal,
        'source'     => $l->source,
        'status'     => $l->status,
    ])->values();

    $marked = 0;
    if ($mark) {
        $marked = \App\Models\Lead::whereIn('id', $leads->pluck('id'))->update(['status' => 'contacted']);
    }

    return response()->json([
        'count'             => $data->count(),
        'marked_contacted'  => $marked,
        'leads'             => $data,
    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// TEMPORARY: find a person by name across every form table. Token protected. Remove after use.
Route::get('/__lc_find_person', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('k') === 'bf_ghl_7kQ2Lm', 404);
    $q = trim((string) $request->query('q', ''));
    if ($q === '') { return response()->json(['error' => 'pass ?q=name'], 400); }

    $results = [];
    $scan = function ($model, $type, array $cols) use ($q, &$results) {
        $rows = $model::query()->where(function ($w) use ($cols, $q) {
            foreach ($cols as $c) { $w->orWhere($c, 'like', "%{$q}%"); }
        })->get();
        foreach ($rows as $r) {
            $name = trim(implode(' ', array_filter(array_map(fn ($c) => $r->$c ?? '', $cols))));
            $results[] = [
                'table'      => $type,
                'id'         => $r->id,
                'name'       => $name,
                'email'      => $r->email ?? null,
                'phone'      => $r->phone ?? null,
                'status'     => $r->status ?? null,
                'created_at' => (string) ($r->created_at ?? ''),
            ];
        }
    };

    $scan(\App\Models\Lead::class, 'lead (popup)', ['name']);
    $scan(\App\Models\Contact::class, 'contact', ['name']);
    $scan(\App\Models\FundingApplication::class, 'funding', ['first_name', 'last_name']);
    $scan(\App\Models\MentorshipLead::class, 'mentorship', ['first_name', 'last_name']);
    $scan(\App\Models\StrategyCallRequest::class, 'strategy_call', ['name']);
    $scan(\App\Models\OnboardingSubmission::class, 'onboarding', ['firstname', 'lastname']);
    $scan(\App\Models\Subscription::class, 'subscription', ['first_name', 'last_name']);
    $scan(\App\Models\EbookOrder::class, 'ebook_order', ['first_name', 'last_name']);

    return response()->json(['query' => $q, 'match_count' => count($results), 'matches' => $results],
        200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// TEMPORARY: per-table counts (total + still status=new). Token protected. Remove after use.
Route::get('/__lc_counts', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('k') === 'bf_ghl_7kQ2Lm', 404);
    $tables = [
        'lead (popup)'  => \App\Models\Lead::class,
        'contact'       => \App\Models\Contact::class,
        'funding'       => \App\Models\FundingApplication::class,
        'mentorship'    => \App\Models\MentorshipLead::class,
        'strategy_call' => \App\Models\StrategyCallRequest::class,
        'onboarding'    => \App\Models\OnboardingSubmission::class,
        'subscription'  => \App\Models\Subscription::class,
        'ebook_order'   => \App\Models\EbookOrder::class,
    ];
    $perTable = []; $totalAll = 0; $totalNew = 0;
    foreach ($tables as $name => $model) {
        $all = $model::count();
        $new = null;
        try { $new = (clone $model::query())->where('status', 'new')->count(); } catch (\Throwable $e) {}
        $perTable[$name] = ['total' => $all, 'new' => $new];
        $totalAll += $all;
        if (is_int($new)) { $totalNew += $new; }
    }
    return response()->json([
        'per_table' => $perTable, 'total_all' => $totalAll, 'total_new' => $totalNew,
    ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// Standalone read-only reviewer preview (Authorize.Net underwriting, etc.).
// Self-contained: no DB, no Auth, no shared layout — credentials are checked
// against .env (REVIEWER_EMAIL / REVIEWER_PASSWORD). CSRF is disabled below.
Route::match(['get', 'post'], '/reviewer-access', [ReviewerPreviewController::class, 'show'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('reviewer.access');

Route::prefix('services')->name('services.')->group(function () {
    Route::view('/credit-repair',              'services.credit-repair')->name('credit-repair');
    Route::view('/diy-business-funding',       'services.diy-business-funding')->name('diy-business-funding');
    Route::view('/credit-consultations',       'services.credit-consultations')->name('credit-consultations');
});

// Legal pages — required for production. Linked from the sitewide footer and
// from the agreement checkboxes on every checkout / onboarding form.
Route::prefix('legal')->name('legal.')->group(function () {
    Route::view('/privacy-policy',    'legal.privacy-policy')->name('privacy-policy');
    Route::view('/terms-of-service',  'legal.terms-of-service')->name('terms-of-service');
    Route::view('/disclaimer',        'legal.disclaimer')->name('disclaimer');
});

// Mentorship — dedicated landing page for the 1:1 program
Route::view('/mentorship', 'mentorship')->name('mentorship');
Route::post('/mentorship-application', [MentorshipController::class, 'submit'])->name('mentorship.submit');

// Mentorship payment-plan contract — signed before checkout (instalment plans only)
Route::get('/mentorship-agreement/{plan}', [PaymentAgreementController::class, 'show'])
    ->where('plan', 'mentorship-3pay|mentorship-5pay')
    ->name('mentorship-agreement.show');
Route::post('/mentorship-agreement', [PaymentAgreementController::class, 'sign'])
    ->name('mentorship-agreement.sign');

// Post-payment onboarding form — submits new clients to Credit Repair Cloud
Route::get('/onboarding',  [OnboardingController::class, 'show'])->name('onboarding.show');
Route::post('/onboarding', [OnboardingController::class, 'submit'])->name('onboarding.submit');

// Contact form + Calendly booking page
Route::get('/contact',  [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Strategy-call qualification gate (every "Free Strategy Call" CTA flows through this)
Route::get('/strategy-call',         [StrategyCallController::class, 'show'])->name('strategy-call.show');
Route::post('/strategy-call',        [StrategyCallController::class, 'submit'])->name('strategy-call.submit');
Route::get('/strategy-call/booked',  [StrategyCallController::class, 'booked'])->name('strategy-call.booked');

// Funding qualification application (embedded on the DIY funding service page)
Route::post('/funding-application', [FundingController::class, 'submit'])->name('funding.submit');

// Lead-capture popup (homepage)
Route::post('/lead', [LeadController::class, 'submit'])->name('lead.submit');

// ============ SECURE CHECKOUT (Authorize.Net Accept.js) ============
Route::get('/checkout/{plan?}', [AcceptJsPaymentController::class, 'showCheckout'])
    ->where('plan', 'starter|audit|monthly|onetime|couple|vip|mentorship-3pay|mentorship-5pay|mentorship-full')
    ->name('checkout.show');
Route::post('/checkout/process', [AcceptJsPaymentController::class, 'processPayment'])
    ->name('checkout.process');

// ============ TEMPORARY one-off private payment links (NOT linked anywhere) ============
// Reached only by their obscure token URL; remove these once the clients have paid.
Route::get('/secure-pay/{token}', [CustomCheckoutController::class, 'show'])
    ->where('token', 'vlc-7k3p9q2x|vlc-4m8t6w1z')
    ->name('custom-pay.show');
Route::post('/secure-pay/process', [CustomCheckoutController::class, 'process'])
    ->name('custom-pay.process');

// ============ EBOOK CHECKOUT (Authorize.Net Accept.js) ============
Route::get('/ebooks/{slug}/checkout',  [EbookCheckoutController::class, 'show'])->name('ebooks.checkout');
Route::post('/ebooks/checkout/process', [EbookCheckoutController::class, 'processPayment'])->name('ebooks.checkout.process');
Route::get('/ebooks/thank-you/{order}', [EbookCheckoutController::class, 'thanks'])->name('ebooks.thanks');

// Authorize.Net webhook receiver (CSRF-exempt — see bootstrap/app.php)
Route::post('/authorize-net/webhook', [AuthorizeNetWebhookController::class, 'handle'])
    ->name('authorize-net.webhook');

// ============ ADMIN DASHBOARD (/victoria-admin) ============
Route::prefix('victoria-admin')->name('admin.')->group(function () {
    Route::get('/login',  [AuthController::class, 'show'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('admin')->name('logout');

    Route::middleware(['admin', 'reviewer'])->group(function () {
        Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/all-leads', [DashboardController::class, 'allLeads'])->name('all-leads');
        Route::get('/search',    [DashboardController::class, 'search'])->name('search');

        Route::get('/funding',   [DashboardController::class, 'funding'])->name('funding');
        Route::get('/funding/{funding}', [DashboardController::class, 'fundingShow'])->name('funding.show');
        Route::patch('/funding/{funding}/status', [DashboardController::class, 'fundingStatus'])->name('funding.status');

        Route::get('/mentorship', [DashboardController::class, 'mentorship'])->name('mentorship');
        Route::get('/mentorship/{mentorship}', [DashboardController::class, 'mentorshipShow'])->name('mentorship.show');
        Route::patch('/mentorship/{mentorship}/status', [DashboardController::class, 'mentorshipStatus'])->name('mentorship.status');

        Route::get('/strategy-calls', [DashboardController::class, 'strategyCalls'])->name('strategy-calls');
        Route::get('/strategy-calls/{strategy}', [DashboardController::class, 'strategyCallShow'])->name('strategy-calls.show');
        Route::patch('/strategy-calls/{strategy}/status', [DashboardController::class, 'strategyCallStatus'])->name('strategy-calls.status');

        Route::get('/leads',     [DashboardController::class, 'leads'])->name('leads');
        Route::get('/leads/{lead}', [DashboardController::class, 'leadShow'])->name('leads.show');
        Route::patch('/leads/{lead}/status', [DashboardController::class, 'leadStatus'])->name('leads.status');

        Route::get('/contacts',  [DashboardController::class, 'contacts'])->name('contacts');
        Route::get('/contacts/{contact}', [DashboardController::class, 'contactShow'])->name('contacts.show');
        Route::patch('/contacts/{contact}/status', [DashboardController::class, 'contactStatus'])->name('contacts.status');

        Route::get('/onboarding', [DashboardController::class, 'onboarding'])->name('onboarding');
        Route::get('/onboarding/{onboarding}', [DashboardController::class, 'onboardingShow'])->name('onboarding.show');
        Route::patch('/onboarding/{onboarding}/status', [DashboardController::class, 'onboardingStatus'])->name('onboarding.status');

        // ─── Payments / subscriptions / webhooks ───
        Route::get('/subscriptions',                [PaymentsController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/subscriptions/{subscription}', [PaymentsController::class, 'subscriptionShow'])->name('subscriptions.show');
        Route::patch('/subscriptions/{subscription}/status', [PaymentsController::class, 'subscriptionStatus'])->name('subscriptions.status');

        // Paid mentorship clients — everyone who bought any 1:1 mentorship plan
        Route::get('/mentorship-clients', [PaymentsController::class, 'mentorshipClients'])->name('mentorship-clients');

        Route::get('/payments', [PaymentsController::class, 'payments'])->name('payments');

        Route::get('/webhooks',           [PaymentsController::class, 'webhooks'])->name('webhooks');
        Route::get('/webhooks/{webhook}', [PaymentsController::class, 'webhookShow'])->name('webhooks.show');

        // ─── eBooks: catalog + orders ───
        Route::get('/ebooks',              [EbooksController::class, 'index'])->name('ebooks');
        Route::get('/ebooks/{ebook}/edit', [EbooksController::class, 'edit'])->name('ebooks.edit');
        Route::patch('/ebooks/{ebook}',    [EbooksController::class, 'update'])->name('ebooks.update');

        Route::get('/ebook-orders',           [EbooksController::class, 'orders'])->name('ebook-orders');
        Route::get('/ebook-orders/{order}',   [EbooksController::class, 'orderShow'])->name('ebook-orders.show');
    });
});
