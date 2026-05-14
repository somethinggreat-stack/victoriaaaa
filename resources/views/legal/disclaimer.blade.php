@extends('layouts.app')

@section('title', 'Disclaimer | Victorious Opportunities')
@section('description', 'Important disclaimers about the educational, financial, and credit-repair information and services provided by Victorious Opportunities.')
@section('bodyClass', 'page-legal')

@section('content')
<style>
  .legal-hero {
    padding: 110px 0 40px;
    background: linear-gradient(180deg, var(--bg) 0%, var(--bg-2) 100%);
    border-bottom: 1px solid var(--line);
  }
  .legal-hero .container { max-width: 880px; }
  .legal-hero h1 {
    font-family: 'Instrument Serif', serif;
    font-size: clamp(40px, 6vw, 64px);
    line-height: 1.05;
    letter-spacing: -0.02em;
    color: var(--ink);
    margin: 18px 0 10px;
  }
  .legal-hero .updated {
    display: inline-block;
    font-size: 13px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--pink);
    font-weight: 600;
  }
  .legal-hero p.lede {
    font-size: 17px;
    color: var(--ink-2);
    max-width: 720px;
    margin-top: 8px;
  }

  .legal-body { padding: 64px 0 96px; background: var(--bg); }
  .legal-body .container { max-width: 820px; }
  .legal-body h2 {
    font-family: 'Instrument Serif', serif;
    font-size: 30px;
    margin-top: 48px;
    margin-bottom: 14px;
    color: var(--ink);
    letter-spacing: -0.01em;
  }
  .legal-body h2:first-of-type { margin-top: 0; }
  .legal-body h3 {
    font-size: 17px;
    font-weight: 700;
    margin-top: 26px;
    margin-bottom: 8px;
    color: var(--ink);
  }
  .legal-body p,
  .legal-body li {
    color: var(--ink-2);
    font-size: 15.5px;
    line-height: 1.75;
  }
  .legal-body p { margin-bottom: 14px; }
  .legal-body ul, .legal-body ol { padding-left: 22px; margin-bottom: 18px; }
  .legal-body li { margin-bottom: 6px; }
  .legal-body a { color: var(--pink); text-decoration: underline; }
  .legal-body a:hover { color: var(--ink); }
  .legal-body strong { color: var(--ink); font-weight: 700; }

  .legal-callout {
    margin: 22px 0;
    padding: 22px 26px;
    background: var(--pink-tint);
    border-left: 4px solid var(--pink);
    border-radius: 10px;
  }
  .legal-callout p { margin-bottom: 4px; }

  .legal-contact-card {
    margin-top: 40px;
    padding: 26px 30px;
    background: var(--pink-tint);
    border-left: 4px solid var(--pink);
    border-radius: 10px;
  }
  .legal-contact-card p { margin-bottom: 4px; }
</style>

<section class="legal-hero">
  <div class="container">
    <span class="updated">Last updated: May 14, 2026</span>
    <h1>Disclaimer</h1>
    <p class="lede">Please read this Disclaimer carefully before using <strong>victorialovecredit.com</strong> or any service or product offered by Victorious Opportunities. By using the site, you accept everything below.</p>
  </div>
</section>

<section class="legal-body">
  <div class="container">

    <h2>General educational content</h2>
    <p>All content on this website — including articles, videos, eBooks, scripts, frameworks, lender lists, and any other materials — is for <strong>educational and informational purposes only</strong>. Nothing on this site constitutes legal, financial, tax, accounting, investment, real-estate, or other professional advice. Always consult a qualified, licensed professional before making decisions about your specific situation.</p>

    <h2>Not a law firm. Not an attorney.</h2>
    <p>Victorious Opportunities is <strong>not a law firm</strong>. We do not provide legal advice, do not represent you in any legal matter, and using our services does not create an attorney-client relationship. If you need legal advice about your credit, finances, or any other matter, please consult a licensed attorney in your jurisdiction.</p>

    <h2>Not a financial advisor, broker, or lender</h2>
    <p>We are <strong>not a financial advisor, registered investment advisor, mortgage broker, real-estate brokerage, lender, or insurance agent</strong> (unless explicitly stated on the page describing a specific service). Information about credit, funding, real estate, and business is generalized and may not apply to your unique situation. Before applying for any credit or financial product, review the terms with the actual lender or provider.</p>

    <h2>Credit-repair results</h2>
    <div class="legal-callout">
      <p><strong>We do not guarantee that any specific item will be removed from your credit report, that your credit score will increase by any specific number of points, or that any dispute we file will be successful.</strong></p>
    </div>
    <p>Credit-repair outcomes depend on the accuracy of the items on your report, the responsiveness of the credit bureaus and furnishers, applicable federal and state law, and many factors outside our control. Some clients see large score increases; others see smaller changes; some see no change. Your experience may differ from anything shown in testimonials, screenshots, or examples on this site.</p>

    <h2>Funding-related content</h2>
    <p>Information about business funding, credit cards, business loans, and lender approval is educational. We do <strong>not</strong> originate loans, do <strong>not</strong> issue cards, do <strong>not</strong> guarantee approval, and do <strong>not</strong> guarantee any specific funding amount. Approval, terms, and limits are decided entirely by the lender or card issuer based on their own underwriting. Past results from other clients do not predict your results.</p>

    <h2>Testimonials &amp; results</h2>
    <p>Any testimonials, case studies, success stories, screenshots, or income examples shown on the site represent the experience of <strong>individual clients only</strong>. They are not a guarantee of future results and are not typical of every client. Your results will depend on your effort, your starting point, your financial situation, market conditions, lender decisions, bureau responses, and many other variables. We do not offer get-rich-quick promises.</p>

    <h2>No affiliation with credit bureaus or government agencies</h2>
    <p>Victorious Opportunities is <strong>not affiliated with</strong> Equifax, Experian, TransUnion, the Federal Trade Commission, the Consumer Financial Protection Bureau, any credit-card issuer, any bank, the Internal Revenue Service, or any other government agency. We are a private company providing educational and consulting services.</p>

    <h2>Real-estate content</h2>
    <p>Any information about buying a home, qualifying for a mortgage, real-estate licensing, or related topics is educational. Real-estate transactions involve significant financial and legal risk. Always work with a licensed real-estate agent, mortgage lender, title company, and attorney as appropriate for your jurisdiction.</p>

    <h2>External links</h2>
    <p>The site may contain links to third-party websites or services. These links are provided for convenience and do not imply endorsement. We are not responsible for the content, accuracy, privacy practices, or terms of any third-party site.</p>

    <h2>Affiliate &amp; referral disclosure</h2>
    <p>Some links on this site may be referral or affiliate links, meaning we may receive a small commission if you sign up for or purchase a third-party product through them, at no additional cost to you. We only recommend products and services we believe are useful. The commission does not influence our editorial content.</p>

    <h2>Errors &amp; omissions</h2>
    <p>While we work to keep all information on this site accurate and up to date, laws, lender criteria, bureau practices, and credit-scoring models change frequently. We make no representation that the content is current, complete, or error-free. Always verify critical information with a qualified professional before acting on it.</p>

    <h2>“At your own risk”</h2>
    <p>You are solely responsible for any decisions you make based on the content of this site or the services we provide. Use the information, frameworks, and recommendations at your own risk. We expressly disclaim any liability for actions you take — or fail to take — in reliance on anything you read here.</p>

    <h2>Limitation of liability</h2>
    <p>To the fullest extent permitted by law, Victorious Opportunities, its owners, employees, contractors, and affiliates are not liable for any direct, indirect, incidental, special, consequential, or exemplary damages arising out of your use of the site or services. For more, see our <a href="{{ route('legal.terms-of-service') }}">Terms of Service</a>.</p>

    <h2>Changes to this Disclaimer</h2>
    <p>We may update this Disclaimer from time to time. The "Last updated" date at the top of this page reflects the most recent revision. Continued use of the site after a change means you accept the updated Disclaimer.</p>

    <h2>Contact</h2>
    <div class="legal-contact-card">
      <p><strong>Victorious Opportunities</strong></p>
      <p>Email: <a href="mailto:info@victoriousopportunities.com">info@victoriousopportunities.com</a></p>
      <p>Website: <a href="https://victorialovecredit.com">victorialovecredit.com</a></p>
      <p>Or use our <a href="{{ route('contact.show') }}">contact form</a>.</p>
    </div>

  </div>
</section>
@endsection
