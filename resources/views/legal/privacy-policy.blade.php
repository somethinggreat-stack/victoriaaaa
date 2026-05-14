@extends('layouts.app')

@section('title', 'Privacy Policy | Victorious Opportunities')
@section('description', 'How Victorious Opportunities collects, uses, stores, and protects your personal information. Read our complete privacy practices.')
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
  .legal-body ul { padding-left: 22px; margin-bottom: 18px; }
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
    <h1>Privacy Policy</h1>
    <p class="lede">This Privacy Policy explains how Victorious Opportunities (“we,” “us,” “our”) collects, uses, shares, and protects information when you visit <strong>victorialovecredit.com</strong> or use any of our services.</p>
  </div>
</section>

<section class="legal-body">
  <div class="container">

    <div class="legal-toc">
      <h4>On this page</h4>
      <ol>
        <li><a href="#info-we-collect">Information we collect</a></li>
        <li><a href="#how-we-use">How we use your information</a></li>
        <li><a href="#sharing">When we share your information</a></li>
        <li><a href="#security">How we protect your information</a></li>
        <li><a href="#cookies">Cookies &amp; tracking</a></li>
        <li><a href="#your-rights">Your rights &amp; choices</a></li>
        <li><a href="#retention">Data retention</a></li>
        <li><a href="#children">Children's privacy</a></li>
        <li><a href="#changes">Changes to this policy</a></li>
        <li><a href="#contact">Contact us</a></li>
      </ol>
    </div>

    <h2 id="info-we-collect">1. Information we collect</h2>
    <p>We collect information that you give us directly and information collected automatically when you use our site.</p>

    <h3>Information you provide</h3>
    <ul>
      <li><strong>Contact details:</strong> first name, last name, email address, phone number, mailing address.</li>
      <li><strong>Identity information for credit-repair clients:</strong> date of birth and Social Security Number, used solely to verify your identity with the credit bureaus and to file disputes on your behalf.</li>
      <li><strong>Financial &amp; credit-profile information:</strong> approximate FICO score range, credit-card limits, monthly income range, business situation, and other qualifying answers you submit through our applications.</li>
      <li><strong>Payment information:</strong> when you buy a service or eBook, your card details are entered directly into a secure form provided by our payment processor, <strong>Authorize.Net</strong>, and tokenized in your browser. <strong>We do not see, store, or transmit your full card number.</strong></li>
      <li><strong>Communications:</strong> any message you send through our contact form, mentorship application, funding application, or by email.</li>
    </ul>

    <h3>Information collected automatically</h3>
    <ul>
      <li>IP address, browser type, device and operating-system information.</li>
      <li>Pages you visit, links you click, time spent on each page, referring URL.</li>
      <li>Cookies and similar technologies (see Section 5).</li>
    </ul>

    <h2 id="how-we-use">2. How we use your information</h2>
    <p>We use the information we collect to:</p>
    <ul>
      <li>Deliver the services and products you sign up for (credit repair, consultations, mentorship, funding guidance, eBooks).</li>
      <li>Verify your identity with the three major credit bureaus and submit disputes on your behalf, when you enroll in credit-repair services.</li>
      <li>Process payments and manage recurring subscriptions through Authorize.Net.</li>
      <li>Send transactional emails (receipts, onboarding instructions, account updates).</li>
      <li>Respond to your questions and provide customer support.</li>
      <li>Send marketing emails about new services or content, <strong>only if you have opted in</strong>. You can unsubscribe at any time.</li>
      <li>Operate, secure, and improve our website (analytics, fraud prevention, debugging).</li>
      <li>Comply with our legal obligations.</li>
    </ul>

    <h2 id="sharing">3. When we share your information</h2>
    <p>We do not sell your personal information. We share it only with the following categories of recipients, and only as needed to deliver the service you signed up for:</p>
    <ul>
      <li><strong>Credit Repair Cloud</strong> — our credit-repair workflow platform. If you enroll as a credit-repair client, we send your contact, identity, and credit-profile information to Credit Repair Cloud so we can manage your case file and file disputes on your behalf.</li>
      <li><strong>Authorize.Net</strong> — our PCI-DSS Level 1 payment processor. Your card data goes directly to Authorize.Net; we only receive a tokenized reference, the last four digits, and a transaction ID.</li>
      <li><strong>Meta Platforms (Facebook / Instagram)</strong> — we use hashed conversion data via the Meta Conversions API to measure the effectiveness of our advertising. Hashing happens server-side before transmission.</li>
      <li><strong>Service providers</strong> — hosting, email delivery, and analytics vendors that process data on our behalf under strict confidentiality.</li>
      <li><strong>Legal &amp; safety</strong> — if required by law, court order, or to investigate fraud or protect the rights and safety of users, employees, or others.</li>
      <li><strong>Business transfers</strong> — in the event of a merger, acquisition, or sale of assets, your information may transfer to the new owner under the same protections described in this policy.</li>
    </ul>

    <h2 id="security">4. How we protect your information</h2>
    <p>We take reasonable administrative, technical, and physical safeguards to protect your information:</p>
    <ul>
      <li>All traffic to our website is encrypted with TLS (HTTPS).</li>
      <li>Social Security Numbers are encrypted at rest using strong symmetric encryption; only the last four digits are stored in a searchable form.</li>
      <li>Card data is never stored on our servers — it is tokenized by Authorize.Net before it ever reaches us.</li>
      <li>Access to customer data is limited to authorized personnel who need it to perform their work.</li>
      <li>Admin accounts are protected by rate-limited authentication and session encryption.</li>
    </ul>
    <p>No method of internet transmission or electronic storage is 100% secure. While we work hard to protect your information, we cannot guarantee absolute security.</p>

    <h2 id="cookies">5. Cookies &amp; tracking</h2>
    <p>We use cookies and similar technologies to keep you signed in, remember your form progress, measure site performance, and (with your consent where required) measure the performance of our advertising. You can disable cookies in your browser settings, but some parts of the site may not function as intended without them.</p>

    <h2 id="your-rights">6. Your rights &amp; choices</h2>
    <p>Depending on where you live, you may have the right to:</p>
    <ul>
      <li>Request a copy of the personal information we hold about you.</li>
      <li>Request that we correct inaccurate information.</li>
      <li>Request deletion of your personal information (subject to legal retention obligations).</li>
      <li>Opt out of marketing emails by clicking the unsubscribe link at the bottom of every marketing message.</li>
      <li>Opt out of the sale or sharing of your personal information — <strong>we do not sell personal information.</strong></li>
    </ul>
    <p>To exercise any of these rights, contact us using the details in Section 10. We will respond within the timeframe required by applicable law (typically 30–45 days).</p>

    <p><strong>California residents</strong> have additional rights under the CCPA / CPRA, including the right to know what categories of information we collect and the right not to be discriminated against for exercising your rights. <strong>EU/UK residents</strong> have additional rights under the GDPR / UK GDPR including the right to data portability and the right to lodge a complaint with a supervisory authority.</p>

    <h2 id="retention">7. Data retention</h2>
    <p>We retain personal information for as long as needed to deliver our services and comply with our legal, tax, and accounting obligations. Credit-repair client records are typically retained for at least <strong>five years</strong> after your last interaction with us, as required by federal record-keeping laws applicable to credit repair organizations. After that period, records are deleted or securely anonymized.</p>

    <h2 id="children">8. Children's privacy</h2>
    <p>Our services are not directed to children under 18. We do not knowingly collect personal information from anyone under 18. If you believe a child has provided us information, contact us and we will delete it.</p>

    <h2 id="changes">9. Changes to this policy</h2>
    <p>We may update this Privacy Policy from time to time. When we do, we'll update the "Last updated" date at the top of this page. Material changes will be communicated to active customers by email. Continued use of the site after a change means you accept the updated policy.</p>

    <h2 id="contact">10. Contact us</h2>
    <div class="legal-contact-card">
      <p><strong>Victorious Opportunities</strong></p>
      <p>Email: <a href="mailto:info@victoriousopportunities.com">info@victoriousopportunities.com</a></p>
      <p>Website: <a href="https://victorialovecredit.com">victorialovecredit.com</a></p>
      <p>Or use our <a href="{{ route('contact.show') }}">contact form</a>.</p>
    </div>

  </div>
</section>
@endsection
