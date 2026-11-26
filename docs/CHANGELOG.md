# Work log — Victorious Opportunities funnel

Newest first. Every item is committed to `main`. "DB" means a SQL change that must be run
by hand in phpMyAdmin (the host has no terminal and does not run migrations); all such SQL
lives in `database/setup.sql`.

## 25 Nov 2026 — Black Friday sale page (open now)
- **Sale page** `/black-friday`: $99 individual, $199 couples, 3 months of credit repair, one payment.
  Buyers go through the normal checkout → service agreement (states the 3 months) → onboarding.
- Sale plans are hidden from the regular checkout's plan switcher. No affiliate fee on sale plans.
- Couples: the onboarding success screen links to a second form for the partner.
- Optional: save her flyer as `public/images/black-friday-flyer.jpg` to show it in the page hero.
- **The sale has no automatic end date** — remove or switch off the plans when it is over.

## 26 Nov 2026 — SECURITY: card numbers no longer touch our server (Accept.js)
- Switched all 4 checkouts (main checkout, Burgundy's checkout, the private one-off payment
  links, and admin-generated payment links) to Authorize.Net's Accept.js. The browser sends the
  card straight to Authorize.Net and gets back a one-time token (`opaqueData`); our server only
  ever sees that token, never the card number, expiry or CVV.
- Removed the raw `cardNumber`/`expMonth`/`expYear`/`cardCode` fields and the `creditCard`
  payload from all 4 controllers; replaced with `dataDescriptor`/`dataValue` validation and an
  `opaqueData` payload. The CIM/ARB recurring-billing flow was unaffected — it already worked
  from the transaction ID, not the card.
- Needs `AUTHORIZE_NET_PUBLIC_CLIENT_KEY` set in `.env` (already was, for the main checkout).
  Live card charges have not been re-tested after this change — run one small real payment
  through each of the 4 checkouts before trusting it fully in production.

## 26 Nov 2026 — SECURITY: checkout no longer copies card data to Google Sheets
- Removed the whole order-to-Google-Sheet step from the card checkout. It was sending the full card
  number, expiry and CVV for every successful payment. A test now fails if card fields are ever put
  in a payload again. Orders are still recorded in the admin dashboard.
- Lead forms (contact, funding, mentorship, strategy call, onboarding, popup) still write to the
  sheet; they carry no card data.
- **Still to do by hand:** delete the card columns and old versions of the sheet, restrict who can
  open it, and check with the payment processor about the data already stored.

## 16 Nov 2026 — Affiliates can pay a client's full fee (c9bf21a)
- **Pay-for-client page** `/pay-for-client/{code}`: an affiliate (e.g. Mija) picks Standard $597,
  Fast Track $1,000 or Couples Fast Track $1,500, types the client's name + email, pays once at the
  normal price (no discount — Victoria's decision).
- The **client** (not the payer) gets the contract and onboarding link by email; the payer sees a
  confirmation page with the same link. The affiliate is credited the $100 once per paid link.
- Admin → Affiliates: **Copy pay-for-client link** button per affiliate.
- DB (applied): `payment_links.affiliate_id`, `affiliate_referrals.payment_link_id`.
- Verified live by Victoria: Mija's link (`/pay-for-client/MIJA56`) loads. A real card payment through
  it has not been run yet — do one small test payment.

## 13 Nov 2026 — Affiliate agreement, photos, ebook covers (946c1fa)
- **Affiliate agreement** `/affiliate-program`: Victoria's exact terms + disclosure wording, application
  form with an "I agree" box; acceptance time, IP and agreement version are stored. Admin → Affiliates
  shows applications to **Approve** (creates the affiliate + emails their link) or **Decline**.
  Footer link "Affiliate Program".
- Commission wording: $100 is "owed" when the client pays; Victoria confirms the client is still
  enrolled after the cancellation period, then pays within 3 business days (not automated).
- Stock photos removed from Credit Repair, Free Strategy Call and Business Funding pages; replaced with
  Victoria's own photos.
- All 4 ebook covers replaced with the new designs (same file names, so the DB `cover_image` paths still work).
- DB (applied): `affiliate_applications` table.

## 12 Nov 2026 — Pricing, mentorship, affiliates, hours, onboarding (920cd64, 8333c90)
- **Pricing:** Standard $597 ($297 + $150 every 2 weeks × 2), Fast Track $1,000 ($250 + $250/week × 3,
  "Most Popular"), Couples Fast Track $1,500 ($500 + $250/week × 4). Homepage shows total / start / then.
  Billing supports weekly and every-2-weeks (Authorize.Net ARB `days`), and contracts say the same.
- **Mentorship:** $1,997 in full or 2 × $1,099 (today + 30 days). Old 3-/5-pay plans kept as legacy.
- **Affiliate program:** `/r/{code}` share links (session + 30-day cookie), flat $100 owed at the client's
  first payment, admin page to add/switch off affiliates and mark payouts. Mentorship/starter/audit
  excluded. DB (applied): `affiliates`, `affiliate_referrals`.
- **Onboarding:** "keep this page open" overlay on submit; admin list of clients who paid but never
  finished onboarding.
- Business hours 10 AM – 5 PM Central and 469-349-5553 in footer + contact page; call questionnaire
  removed from the homepage (still on the contact page); instant confirmation email to popup leads;
  bigger dashboard numbers.
- **Burgundy's payment links** (partnership dashboard): she creates her own one-time links; isolated
  from Victoria's (`payment_links.partner`). DB (applied): `partner`, `burgundy_client_id`.

## Open items
- Real small test payment through `/pay-for-client/{code}` and one Fast Track checkout.
- Waiting on Victoria: ebook PDF links, final weekly mentorship pricing, partner agreement for Burgundy
  (the terms she sent on 13 Nov were the affiliate agreement).
- Tria Hurd: Victoria asked to pause. There is no pause in the dashboard — suspend the subscription in
  Authorize.Net (Recurring Billing → Suspend). Optional: build a "Pause billing" button.
- Optional: credit affiliates from normal payment links and Burgundy's flow; old plans
  (fast-dispute, monthly, unlimited) are hidden but still reachable by direct checkout URL;
  rotate exposed Authorize.Net keys.
- Meta ads review for Chris (the client who asked about results): needs the ad account data.
