<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\FundingApplication;
use App\Models\Lead;
use App\Models\MentorshipLead;
use App\Models\OnboardingSubmission;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $payments = [
            'gross_lifetime'     => (float) Payment::where('status', 'captured')
                                        ->whereIn('type', ['initial', 'recurring'])
                                        ->sum('amount'),
            'refunded_lifetime'  => (float) Payment::whereIn('type', ['refund', 'void'])->sum('amount'),
            'gross_today'        => (float) Payment::whereDate('charged_at', $today)
                                        ->where('status', 'captured')
                                        ->sum('amount'),
            'gross_mtd'          => (float) Payment::whereMonth('charged_at', now()->month)
                                        ->whereYear('charged_at', now()->year)
                                        ->where('status', 'captured')
                                        ->sum('amount'),
            'subs_total'         => Subscription::count(),
            'subs_active'        => Subscription::where('status', 'active')->count(),
            'subs_past_due'      => Subscription::where('status', 'past_due')->count(),
            'mrr'                => (float) Subscription::where('status', 'active')
                                        ->whereNotNull('recurring_amount')
                                        ->sum('recurring_amount'),
            'webhooks_total'     => WebhookEvent::count(),
            'webhooks_today'     => WebhookEvent::whereDate('received_at', $today)->count(),
            'webhooks_invalid'   => WebhookEvent::where('signature_valid', false)->count(),
        ];

        return view('admin.dashboard', [
            'counts' => [
                'onboarding'        => OnboardingSubmission::count(),
                'onboarding_today'  => OnboardingSubmission::whereDate('created_at', $today)->count(),
                'new_onboarding'    => OnboardingSubmission::where('status', 'new')->count(),
                'funding'           => FundingApplication::count(),
                'funding_today'     => FundingApplication::whereDate('created_at', $today)->count(),
                'new_funding'       => FundingApplication::where('status', 'new')->count(),
                'mentorship'        => MentorshipLead::count(),
                'mentorship_today'  => MentorshipLead::whereDate('created_at', $today)->count(),
                'new_mentorship'    => MentorshipLead::where('status', 'new')->count(),
                'contacts'          => Contact::count(),
                'contacts_today'    => Contact::whereDate('created_at', $today)->count(),
                'new_contacts'      => Contact::where('status', 'new')->count(),
                'leads'             => Lead::count(),
                'leads_today'       => Lead::whereDate('created_at', $today)->count(),
                'new_leads'         => Lead::where('status', 'new')->count(),
            ],
            'payments'         => $payments,
            'recentOnboarding' => OnboardingSubmission::latest()->limit(5)->get(),
            'recentFunding'    => FundingApplication::latest()->limit(5)->get(),
            'recentMentorship' => MentorshipLead::latest()->limit(5)->get(),
            'recentContacts'   => Contact::latest()->limit(5)->get(),
            'recentLeads'      => Lead::latest()->limit(5)->get(),
            'recentSubscriptions' => Subscription::latest()->limit(5)->get(),
            'recentPayments'      => Payment::with('subscription')->latest('charged_at')->limit(5)->get(),
            'recentWebhooks'      => WebhookEvent::latest('received_at')->limit(5)->get(),
        ]);
    }

    public function leads(Request $request)
    {
        $q = Lead::query();
        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('email', 'like', "%{$search}%")
                  ->orWhere('name',  'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        return view('admin.leads', [
            'leads' => $q->latest()->paginate(25)->withQueryString(),
        ]);
    }

    public function contacts(Request $request)
    {
        $q = Contact::query();
        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('email',   'like', "%{$search}%")
                  ->orWhere('name',  'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('message','like',"%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        return view('admin.contacts', [
            'contacts' => $q->latest()->paginate(25)->withQueryString(),
        ]);
    }

    public function contactShow(Contact $contact)
    {
        return view('admin.contact-show', compact('contact'));
    }

    public function onboarding(Request $request)
    {
        $q = OnboardingSubmission::query();
        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('email',     'like', "%{$search}%")
                  ->orWhere('firstname','like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('phone',    'like', "%{$search}%")
                  ->orWhere('ssn_last4','like', "%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        return view('admin.onboarding', [
            'rows' => $q->latest()->paginate(25)->withQueryString(),
        ]);
    }

    public function onboardingShow(OnboardingSubmission $onboarding)
    {
        return view('admin.onboarding-show', compact('onboarding'));
    }

    public function leadStatus(Lead $lead, Request $request)
    {
        $request->validate(['status' => 'required|in:new,contacted,converted,archived']);
        $lead->update(['status' => $request->status]);
        return back()->with('success', 'Lead status updated.');
    }

    public function contactStatus(Contact $contact, Request $request)
    {
        $request->validate(['status' => 'required|in:new,replied,archived']);
        $contact->update(['status' => $request->status]);
        return back()->with('success', 'Contact status updated.');
    }

    public function onboardingStatus(OnboardingSubmission $onboarding, Request $request)
    {
        $request->validate(['status' => 'required|in:new,in_progress,active,archived']);
        $onboarding->update(['status' => $request->status]);
        return back()->with('success', 'Onboarding status updated.');
    }

    public function funding(Request $request)
    {
        $q = FundingApplication::query();
        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('email',      'like', "%{$search}%")
                  ->orWhere('first_name','like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone',     'like', "%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        return view('admin.funding', [
            'rows' => $q->latest()->paginate(25)->withQueryString(),
        ]);
    }

    public function fundingShow(FundingApplication $funding)
    {
        return view('admin.funding-show', compact('funding'));
    }

    public function fundingStatus(FundingApplication $funding, Request $request)
    {
        $request->validate(['status' => 'required|in:new,contacted,qualified,funded,archived']);
        $funding->update(['status' => $request->status]);
        return back()->with('success', 'Funding status updated.');
    }

    public function leadShow(Lead $lead)
    {
        return view('admin.lead-show', compact('lead'));
    }

    public function mentorship(Request $request)
    {
        $q = MentorshipLead::query();
        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('first_name','like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email',    'like', "%{$search}%")
                  ->orWhere('phone',    'like', "%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }
        return view('admin.mentorship', [
            'rows' => $q->latest()->paginate(25)->withQueryString(),
        ]);
    }

    public function mentorshipShow(MentorshipLead $mentorship)
    {
        return view('admin.mentorship-show', compact('mentorship'));
    }

    public function mentorshipStatus(MentorshipLead $mentorship, Request $request)
    {
        $request->validate(['status' => 'required|in:new,contacted,qualified,enrolled,archived']);
        $mentorship->update(['status' => $request->status]);
        return back()->with('success', 'Mentorship status updated.');
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (strlen($q) < 2) {
            return view('admin.search', ['q' => $q, 'results' => null]);
        }

        $pattern = "%{$q}%";

        $paidClients = OnboardingSubmission::where(function ($w) use ($pattern) {
                $w->where('firstname', 'like', $pattern)
                  ->orWhere('lastname',  'like', $pattern)
                  ->orWhere('middlename','like', $pattern)
                  ->orWhere('email',     'like', $pattern)
                  ->orWhere('phone',     'like', $pattern)
                  ->orWhere('city',      'like', $pattern)
                  ->orWhere('state',     'like', $pattern)
                  ->orWhere('zip',       'like', $pattern)
                  ->orWhere('ssn_last4', 'like', $pattern);
            })->latest()->limit(25)->get();

        $fundingLeads = FundingApplication::where(function ($w) use ($pattern) {
                $w->where('first_name','like', $pattern)
                  ->orWhere('last_name', 'like', $pattern)
                  ->orWhere('email',    'like', $pattern)
                  ->orWhere('phone',    'like', $pattern)
                  ->orWhere('amount',   'like', $pattern)
                  ->orWhere('fico',     'like', $pattern)
                  ->orWhere('income',   'like', $pattern)
                  ->orWhere('situation','like', $pattern);
            })->latest()->limit(25)->get();

        $mentorshipLeads = MentorshipLead::where(function ($w) use ($pattern) {
                $w->where('first_name','like', $pattern)
                  ->orWhere('last_name', 'like', $pattern)
                  ->orWhere('email',     'like', $pattern)
                  ->orWhere('phone',     'like', $pattern)
                  ->orWhere('situation', 'like', $pattern)
                  ->orWhere('timeline',  'like', $pattern);
            })->latest()->limit(25)->get();

        $contacts = Contact::where(function ($w) use ($pattern) {
                $w->where('name',    'like', $pattern)
                  ->orWhere('email',   'like', $pattern)
                  ->orWhere('phone',   'like', $pattern)
                  ->orWhere('topic',   'like', $pattern)
                  ->orWhere('message', 'like', $pattern)
                  ->orWhere('source',  'like', $pattern);
            })->latest()->limit(25)->get();

        $leads = Lead::where(function ($w) use ($pattern) {
                $w->where('name',  'like', $pattern)
                  ->orWhere('email', 'like', $pattern)
                  ->orWhere('phone', 'like', $pattern)
                  ->orWhere('score', 'like', $pattern)
                  ->orWhere('issue', 'like', $pattern)
                  ->orWhere('goal',  'like', $pattern);
            })->latest()->limit(25)->get();

        $subscriptions = Subscription::where(function ($w) use ($pattern) {
                $w->where('first_name',           'like', $pattern)
                  ->orWhere('last_name',          'like', $pattern)
                  ->orWhere('email',              'like', $pattern)
                  ->orWhere('phone',              'like', $pattern)
                  ->orWhere('invoice_number',     'like', $pattern)
                  ->orWhere('transaction_id',     'like', $pattern)
                  ->orWhere('arb_subscription_id','like', $pattern)
                  ->orWhere('plan_label',         'like', $pattern);
            })->latest()->limit(25)->get();

        return view('admin.search', [
            'q' => $q,
            'results' => [
                'subscriptions'    => $subscriptions,
                'paid_clients'     => $paidClients,
                'funding_leads'    => $fundingLeads,
                'mentorship_leads' => $mentorshipLeads,
                'contacts'         => $contacts,
                'leads'            => $leads,
            ],
            'total' => $subscriptions->count() + $paidClients->count() + $fundingLeads->count() + $mentorshipLeads->count() + $contacts->count() + $leads->count(),
        ]);
    }
}
