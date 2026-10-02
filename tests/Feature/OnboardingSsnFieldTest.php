<?php

namespace Tests\Feature;

use Tests\TestCase;

class OnboardingSsnFieldTest extends TestCase
{
    /**
     * The SSN box masks what it shows. It once carried the form value too, so
     * every keystroke rebuilt the number from a row of bullets, kept only the
     * character that had just arrived, and no client could enter more than one
     * digit. The digits now travel in a hidden field of their own.
     */
    public function test_the_ssn_the_form_submits_is_not_the_masked_box(): void
    {
        $html = $this->get('/onboarding')->assertOk()->getContent();

        // between() runs to the LAST delimiter in the page, so take the first.
        $visible = \Illuminate\Support\Str::before(\Illuminate\Support\Str::after($html, '<input type="text" id="ob-ssn"'), '>');
        $hidden  = \Illuminate\Support\Str::before(\Illuminate\Support\Str::after($html, '<input type="hidden" name="ssn" id="ob-ssn-raw"'), '>');

        // The box people see must not be what gets posted.
        $this->assertStringNotContainsString('name="ssn"', $visible);
        $this->assertNotEmpty($hidden);
    }

    public function test_the_server_takes_nine_plain_digits(): void
    {
        // What the hidden field sends. Bullets would strip to nothing and fail
        // 'required', which is exactly how this showed up — a form that could
        // not be submitted at all.
        $res = $this->post('/onboarding', ['ssn' => '123456789']);

        $res->assertSessionHasErrors();
        $this->assertArrayNotHasKey('ssn', $res->getSession()->get('errors')->getMessages());
    }
}
