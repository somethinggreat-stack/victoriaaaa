<?php

namespace App\Services;

use Illuminate\Support\Carbon;

/**
 * Builds the service agreement text for a sale.
 *
 * Every figure is passed in from the transaction that actually happened — the
 * amount the card was charged and the recurring amount written onto the
 * subscription — never read back from a plan catalogue. A couple previously
 * refunded because the contract showed a different price from the one they
 * paid, and that can only happen when the document is generated from a
 * template rather than from the sale.
 *
 * This is a services agreement. It deliberately contains no credit-reporting
 * rights disclosure and no statutory cancellation notice.
 */
class ServiceAgreement
{
    public const TERMS_VERSION = 'services-v1';

    public const COMPANY = 'Victoria Love Credit';

    /**
     * @param array{
     *   client_name: string,
     *   plan_label: string,
     *   charged_today: float,
     *   recurring_amount?: float|null,
     *   recurring_count?: int|null,
     *   service_description?: string|null,
     *   signed_on?: \DateTimeInterface|null
     * } $sale
     */
    public static function build(array $sale): string
    {
        $name    = trim($sale['client_name'] ?? '') ?: '__________';
        $label   = trim($sale['plan_label'] ?? '') ?: 'Credit Restoration Program';
        $today   = (float) ($sale['charged_today'] ?? 0);
        $monthly = isset($sale['recurring_amount']) ? (float) $sale['recurring_amount'] : null;
        $count   = $sale['recurring_count'] ?? null;
        // 'week' or 'month'. Getting this wrong misstates the whole commitment.
        $every   = ($sale['recurring_interval'] ?? 'month') === 'week' ? 'week' : 'month';
        $plural  = $every . 's';
        $desc    = trim((string) ($sale['service_description'] ?? '')) ?: null;

        $date = ($sale['signed_on'] ?? null)
            ? Carbon::instance(Carbon::parse($sale['signed_on']))->format('F j, Y')
            : now()->format('F j, Y');

        $money = fn (float $v) => '$' . number_format($v, 2);

        // ── The money, stated before anything else ───────────────────────────
        $lines = [
            'SERVICE AGREEMENT',
            self::COMPANY,
            "Date: {$date}",
            '',
            '--------------------------------------------------',
            'WHAT YOU ARE PAYING',
            '--------------------------------------------------',
            "Service:        {$label}",
            'Charged today:  ' . $money($today),
        ];

        if ($monthly !== null && $monthly > 0) {
            $lines[] = $count
                ? 'Then:           ' . $money($monthly) . " per {$every} for {$count} " . ($count === 1 ? $every : $plural)
                : 'Then:           ' . $money($monthly) . " per {$every} until you cancel";
            $lines[] = $count
                ? 'Total:          ' . $money($today + ($monthly * $count))
                : 'Minimum term:   None — cancel any time';
        } else {
            $lines[] = 'Then:           Nothing further. This is a one-time payment.';
        }

        $lines[] = '--------------------------------------------------';
        $lines[] = '';
        $lines[] = "This Agreement is between " . self::COMPANY . " (\"Company\") and {$name} (\"Client\").";
        $lines[] = '';

        // ── 1. Services ──────────────────────────────────────────────────────
        $lines[] = '1. SERVICES.';
        $lines[] = $desc
            ? "   {$desc}"
            : '   Company will review Client\'s credit reports, identify items Client believes to be'
              . ' inaccurate, incomplete or unverifiable, prepare and submit disputes on Client\'s'
              . ' behalf, and provide ongoing credit guidance for as long as this Agreement is active.';
        $lines[] = '';

        // ── 2. Fees ──────────────────────────────────────────────────────────
        $lines[] = '2. FEES.';
        $lines[] = "   Client has been charged {$money($today)} today, {$date}.";

        if ($monthly !== null && $monthly > 0) {
            $starts = $every === 'week' ? 'one week' : '30 days';
            $lines[] = $count
                ? "   Client will then be charged {$money($monthly)} per {$every} for {$count} "
                  . ($count === 1 ? $every : $plural) . ", beginning approximately {$starts} from today,"
                  . ' for a total of ' . $money($today + ($monthly * $count)) . '.'
                : "   Client will then be charged {$money($monthly)} per {$every}, beginning approximately"
                  . " {$starts} from today, and continuing each {$every} until Client cancels.";
            $lines[] = '   These are the only amounts Company will charge. Any change to the fee above';
            $lines[] = '   requires Client\'s agreement in advance.';
        } else {
            $lines[] = '   This is a one-time fee. There are no recurring charges under this Agreement.';
        }
        $lines[] = '';

        // ── 3. Authorization ─────────────────────────────────────────────────
        $lines[] = '3. AUTHORIZATION TO CHARGE.';
        $lines[] = '   Client authorizes Company to charge the payment method provided at checkout for';
        $lines[] = '   the amounts set out in Section 2, on their due dates, processed securely through';
        $lines[] = '   Authorize.Net. This authorization continues until the Agreement ends.';
        $lines[] = '';

        // ── 4. Term and cancellation ─────────────────────────────────────────
        $lines[] = '4. TERM AND CANCELLATION.';
        $lines[] = '   There is no minimum term and no cancellation fee. Client may cancel at any time,';
        $lines[] = '   for any reason, by written notice to Company.';

        if ($monthly !== null && $monthly > 0) {
            $lines[] = "   Cancellation takes effect at the end of the current billing {$every}. No further";
            $lines[] = '   charges are made once Company has received notice.';
        }
        $lines[] = '';

        // ── 5. Refunds ───────────────────────────────────────────────────────
        $lines[] = '5. REFUNDS.';
        $lines[] = '   Fees already charged for periods in which Company has provided services are not';
        $lines[] = '   refundable. Cancelling stops future charges; it does not reverse past ones.';
        $lines[] = '';

        // ── 6. No guarantee ──────────────────────────────────────────────────
        $lines[] = '6. NO GUARANTEE.';
        $lines[] = '   Company does not guarantee any particular credit score increase, the removal of';
        $lines[] = '   any specific item, approval for any loan or credit product, or any particular';
        $lines[] = '   outcome or timeframe. Results depend on the information reported and on the';
        $lines[] = '   responses of the credit bureaus and furnishers.';
        $lines[] = '';

        // ── 7. Client responsibilities ───────────────────────────────────────
        $lines[] = '7. CLIENT RESPONSIBILITIES.';
        $lines[] = '   Client agrees to provide accurate and complete information, to forward any';
        $lines[] = '   correspondence received from the credit bureaus promptly, and to keep the payment';
        $lines[] = '   method on file current.';
        $lines[] = '';

        // ── 8. Entire agreement ──────────────────────────────────────────────
        $lines[] = '8. ENTIRE AGREEMENT.';
        $lines[] = '   This document is the entire agreement between the parties and replaces any prior';
        $lines[] = '   discussion or quote. The amounts in Section 2 are the amounts that apply.';
        $lines[] = '';

        // ── 9. Signature ─────────────────────────────────────────────────────
        $lines[] = '9. ELECTRONIC SIGNATURE.';
        $lines[] = '   By typing my full legal name and drawing my signature below, I acknowledge that I';
        $lines[] = '   have read, understand, and agree to be bound by this Agreement, and that my';
        $lines[] = '   electronic signature is the legal equivalent of my handwritten signature.';
        $lines[] = '';
        $lines[] = "Signed by: {$name}";
        $lines[] = "Date:      {$date}";

        return implode("\n", $lines);
    }
}
