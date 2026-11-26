<?php

namespace Tests\Feature;

use App\Http\Controllers\AcceptJsPaymentController;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Card details go to Authorize.Net and nowhere else. The checkout used to copy
 * the full number, expiry and CVV into a Google Sheet; this stops it returning.
 */
class CardDataNeverLeavesTest extends TestCase
{
    public function test_the_checkout_sends_nothing_to_google_sheets(): void
    {
        $this->assertFalse(method_exists(AcceptJsPaymentController::class, 'saveToGoogleSheet'));
    }

    public function test_no_code_builds_a_payload_with_card_fields(): void
    {
        foreach (File::allFiles(app_path()) as $file) {
            $this->assertDoesNotMatchRegularExpression(
                '/[\'"](card_number|card_cvv|card_exp|card_code)[\'"]\s*=>/',
                $file->getContents(),
                $file->getRelativePathname() . ' puts card data in an array',
            );
        }
    }
}
