<?php

use App\Http\Controllers\AcceptJsPaymentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EbooksController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\AuthorizeNetWebhookController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EbookCheckoutController;
use App\Http\Controllers\FundingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

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

// Post-payment onboarding form — submits new clients to Credit Repair Cloud
Route::get('/onboarding',  [OnboardingController::class, 'show'])->name('onboarding.show');
Route::post('/onboarding', [OnboardingController::class, 'submit'])->name('onboarding.submit');

// Contact form + Calendly booking page
Route::get('/contact',  [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Funding qualification application (embedded on the DIY funding service page)
Route::post('/funding-application', [FundingController::class, 'submit'])->name('funding.submit');

// Lead-capture popup (homepage)
Route::post('/lead', [LeadController::class, 'submit'])->name('lead.submit');

// ============ SECURE CHECKOUT (Authorize.Net Accept.js) ============
Route::get('/checkout/{plan?}', [AcceptJsPaymentController::class, 'showCheckout'])
    ->where('plan', 'audit|monthly|onetime|couple|vip')
    ->name('checkout.show');
Route::post('/checkout/process', [AcceptJsPaymentController::class, 'processPayment'])
    ->name('checkout.process');

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

    Route::middleware('admin')->group(function () {
        Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/search',    [DashboardController::class, 'search'])->name('search');

        Route::get('/funding',   [DashboardController::class, 'funding'])->name('funding');
        Route::get('/funding/{funding}', [DashboardController::class, 'fundingShow'])->name('funding.show');
        Route::patch('/funding/{funding}/status', [DashboardController::class, 'fundingStatus'])->name('funding.status');

        Route::get('/mentorship', [DashboardController::class, 'mentorship'])->name('mentorship');
        Route::get('/mentorship/{mentorship}', [DashboardController::class, 'mentorshipShow'])->name('mentorship.show');
        Route::patch('/mentorship/{mentorship}/status', [DashboardController::class, 'mentorshipStatus'])->name('mentorship.status');

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
