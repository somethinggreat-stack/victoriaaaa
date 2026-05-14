@extends('layouts.app')

@section('title', 'Terms of Service | Victorious Opportunities')
@section('description', 'The terms and conditions that govern your use of victorialovecredit.com and the services provided by Victorious Opportunities.')
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

  .legal-toc {
    background: var(--bg-3);
    border: 1px solid var(--line);
    border-radius: 14px;
    padding: 22px 26px;
    margin-bottom: 36px;
  }
  .legal-toc h4 {
    font-size: 12px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 12px;
    font-weight: 700;
  }
  .legal-toc ol { padding-left: 22px; margin: 0; }
  .legal-toc li { margin-bottom: 4px; font-size: 14.5px; }
  .legal-toc a { color: var(--ink); text-decoration: none; }
  .legal-toc a:hover { color: var(--pink); }

  .legal-callout {
    margin: 22px 0;
    padding: 22px 26px;
    background: var(--pink-tint);
    border-left: 4px solid var(--pink);
    border-radius: 10px;
  }
  .legal-callout p { margin-bottom: 4px; }
  .legal-callout strong { color: var(--ink); }

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
    <h1>Terms of Service</h1>
    <p class="lede">These Terms of Service (“Terms”) form a legally binding agreement between you and Victorious Opportunities. By using <strong>victorialovecredit.com</strong> or any of our services, you agree to these Terms.</p>
  </div>
</section>

<section class="legal-body">
  <div class="container">

    <div class="legal-toc">
      <h4>On this page</h4>
      <ol>
        <li><a href="#acceptance">Acceptance of these Terms</a></li>
        <li><a href="#services">Description of services</a></li>
        <li><a href="#eligibility">Eligibility</a></li>
        <li><a href="#account">Your account &amp; information</a></li>
        <li><a href="#fees">Fees, billing &amp; refunds</a></li>
        <li><a href="#croa">Credit Repair Organizations Act notice</a></li>
        <li><a href="#no-guarantee">No guarantee of results</a></li>
        <li><a href="#use">Acceptable use</a></li>
        <li><a href="#ip">Intellectual property</a></li>
        <li><a href="#termination">Termination</a></li>
        <li><a href="#disclaimers">Disclaimers</a></li>
        <li><a href="#liability">Limitation of liability</a></li>
        <li><a href="#indemnification">Indemnification</a></li>
        <li><a href="#disputes">Disputes &amp; governing law</a></li>
        <li><a href="#changes">Changes to these Terms</a></li>
        <li><a href="#contact">Contact us</a></li>
      </ol>
    </div>

    <h2 id="acceptance">1. Acceptance of these Terms</h2>
    <p>By accessing or using our website, signing up for any service, or purchasing any product, you agree to be bound by these Terms and our <a href="{{ route('legal.privacy-policy') }}">Privacy Policy</a>. If you do not agree, do not use the site or our services.</p>

    <h2 id="services">2. Description of services</h2>
    <p>Victorious Opportunities provides educational and consulting services related to personal credit and business funding, including:</p>
    <ul>
      <li><strong>Credit-repair services</strong> — analyzing your credit report, identifying inaccurate or unverifiable items, and filing disputes with the three major credit bureaus.</li>
      <li><strong>Credit consultations</strong> — one-on-one calls reviewing your credit profile and providing a written action plan.</li>
      <li><strong>DIY business funding guidance</strong> — frameworks, lender lists, and instructions to help you pursue business funding on your own.</li>
      <li><strong>1:1 mentorship</strong> — personalized coaching for clients accepted into the mentorship program.</li>
      <li><strong>Digital products (eBooks)</strong> — educational PDFs delivered electronically after purchase.</li>
    </ul>
    <p>We are not a law firm, a financial advisor, a tax advisor, or a lender. See our <a href="{{ route('legal.disclaimer') }}">Disclaimer</a>.</p>

    <h2 id="eligibility">3. Eligibility</h2>
    <p>You must be <strong>at least 18 years old</strong> and a legal resident of the United States to use our services. By submitting any form on our site, you represent that:</p>
    <ul>
      <li>You are at least 18.</li>
      <li>The information you provide is true, accurate, and complete.</li>
      <li>You have the legal authority to enter into this agreement.</li>
    </ul>

    <h2 id="account">4. Your account &amp; information</h2>
    <p>You are responsible for keeping the information you provide accurate and current. You agree not to impersonate another person or submit false information. We may suspend or terminate your access if we discover material inaccuracies, suspected fraud, or violations of these Terms.</p>

    <h2 id="fees">5. Fees, billing &amp; refunds</h2>

    <h3>Fees</h3>
    <p>Fees for each service or product are displayed on the relevant checkout page before you complete your purchase. All amounts are in U.S. Dollars.</p>

    <h3>One-time purchases</h3>
    <p>One-time-only services and eBooks are charged at checkout in a single payment.</p>

    <h3>Recurring monthly plans</h3>
    <p>If you select a recurring monthly plan:</p>
    <ul>
      <li>An <strong>initial payment</strong> is charged today.</li>
      <li>The <strong>recurring monthly charge</strong> begins thirty (30) days after your initial purchase and continues each month thereafter until you cancel.</li>
      <li>You may cancel after the 90-day program completes by emailing <a href="mailto:info@victoriousopportunities.com">info@victoriousopportunities.com</a>.</li>
      <li>By providing your card and checking the agreement box at checkout, you authorize Victorious Opportunities to charge your card on a recurring basis until you cancel.</li>
    </ul>

    <h3>Refunds</h3>
    <p>Because credit-repair work begins immediately after you enroll and our services are tied to time-bound regulatory processes, <strong>all sales are final and non-refundable</strong> once work has begun, except as expressly required by law. Digital products (eBooks) are non-refundable once the download link has been delivered. If you believe you were charged in error, contact us within seven (7) days of the charge and we will investigate in good faith.</p>

    <h3>Failed payments</h3>
    <p>If a recurring payment fails, your subscription enters a 7-day grace period during which we may retry the charge. If payment cannot be collected, your subscription will be paused or terminated.</p>

    <h2 id="croa">6. Credit Repair Organizations Act notice</h2>
    <div class="legal-callout">
      <p><strong>Consumer Credit File Rights Under State and Federal Law.</strong></p>
      <p>You have the right to dispute inaccurate information in your credit report by contacting the credit bureau directly. You also have the right to obtain a copy of your credit report from the bureau, and your file disclosure is free if you request it within 60 days of being denied credit. <strong>You have a right to cancel your contract with any credit-repair organization for any reason within three (3) business days from the date you signed it.</strong> Credit-repair companies are not allowed to charge you until the services have been performed, make untrue or misleading statements about your credit, or perform any services until they have your written agreement and have given you the federal Consumer Credit File Rights notice.</p>
      <p>You have a right to sue a credit-repair organization that violates the Credit Repair Organizations Act (CROA). The Federal Trade Commission and the Consumer Financial Protection Bureau enforce CROA.</p>
    </div>
    <p>If you wish to cancel your credit-repair contract within the three-business-day cancellation period, send written notice to <a href="mailto:info@victoriousopportunities.com">info@victoriousopportunities.com</a>.</p>

    <h2 id="no-guarantee">7. No guarantee of results</h2>
    <p>While we work diligently on your behalf, <strong>we do not guarantee any specific outcome</strong>. We cannot promise that any item on your credit report will be removed, that your credit score will increase by any specific amount, that you will be approved for any specific funding amount, or that you will close on any specific timeline. Results depend on your individual credit profile, financial situation, and other factors outside our control. See our <a href="{{ route('legal.disclaimer') }}">Disclaimer</a> for more.</p>

    <h2 id="use">8. Acceptable use</h2>
    <p>You agree not to:</p>
    <ul>
      <li>Use the site or services for any unlawful, fraudulent, or deceptive purpose.</li>
      <li>Submit information that is not yours or that you do not have permission to submit.</li>
      <li>Attempt to access non-public areas, probe vulnerabilities, scrape data, or circumvent security.</li>
      <li>Resell, sublicense, or commercially exploit any content or service without our written permission.</li>
      <li>Share account credentials with anyone else.</li>
    </ul>

    <h2 id="ip">9. Intellectual property</h2>
    <p>All content on the site — text, design, graphics, logos, images, eBooks, scripts, code, videos, audio, and downloadable resources — is owned by Victorious Opportunities or licensed to us, and is protected by U.S. and international copyright, trademark, and other laws.</p>
    <p>When you purchase an eBook or course, you receive a <strong>limited, personal, non-transferable, non-exclusive license</strong> to use it for your own personal purposes. You may not redistribute, resell, publish, or modify any of our materials without our prior written consent.</p>

    <h2 id="termination">10. Termination</h2>
    <p>We may suspend or terminate your access to the site or any service at any time, with or without cause, including for violations of these Terms, suspected fraud, or failure to pay. Upon termination, your right to use the service ends, but provisions that by their nature should survive — including intellectual property, disclaimers, indemnification, limitation of liability, and dispute resolution — will continue.</p>

    <h2 id="disclaimers">11. Disclaimers</h2>
    <p>The site and all services are provided <strong>“as is” and “as available,”</strong> without warranties of any kind, express or implied. To the fullest extent permitted by law, we disclaim all warranties, including merchantability, fitness for a particular purpose, non-infringement, and warranties arising from course of dealing. We do not warrant that the site will be uninterrupted, secure, or error-free.</p>

    <h2 id="liability">12. Limitation of liability</h2>
    <p>To the fullest extent permitted by law, Victorious Opportunities, its owners, employees, contractors, and affiliates will not be liable for any indirect, incidental, special, consequential, exemplary, or punitive damages arising out of or related to your use of the site or services, even if we have been advised of the possibility of such damages. Our total aggregate liability for any claim arising out of these Terms will not exceed the greater of (a) the amount you paid us in the twelve (12) months preceding the event giving rise to the claim, or (b) one hundred U.S. dollars (US $100).</p>

    <h2 id="indemnification">13. Indemnification</h2>
    <p>You agree to defend, indemnify, and hold harmless Victorious Opportunities and its owners, employees, contractors, and affiliates from any claim, loss, or liability — including reasonable attorneys' fees — arising out of (a) your use of the site or services, (b) information you submitted that turns out to be false or that you did not have the right to submit, (c) your violation of these Terms, or (d) your violation of any law or third-party right.</p>

    <h2 id="disputes">14. Disputes &amp; governing law</h2>
    <p>These Terms are governed by the laws of the State of Texas, without regard to its conflict-of-laws rules. Any dispute that cannot be resolved informally will be brought in the state or federal courts located in <strong>Harris County, Texas</strong>, and you consent to the personal jurisdiction of those courts. Nothing in this section prevents either party from seeking injunctive relief in any court of competent jurisdiction.</p>

    <h2 id="changes">15. Changes to these Terms</h2>
    <p>We may update these Terms from time to time. When we do, we'll update the "Last updated" date at the top of this page. Material changes will be communicated to active customers by email. Your continued use of the site after the changes take effect means you accept the updated Terms.</p>

    <h2 id="contact">16. Contact us</h2>
    <div class="legal-contact-card">
      <p><strong>Victorious Opportunities</strong></p>
      <p>Email: <a href="mailto:info@victoriousopportunities.com">info@victoriousopportunities.com</a></p>
      <p>Website: <a href="https://victorialovecredit.com">victorialovecredit.com</a></p>
      <p>Or use our <a href="{{ route('contact.show') }}">contact form</a>.</p>
    </div>

  </div>
</section>
@endsection
