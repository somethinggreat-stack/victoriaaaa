<?php

namespace Tests\Feature;

use App\Models\OnboardingSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The paid-clients list has to make a failed Apex handoff obvious. A client who
 * paid and onboarded but never reached Apex has nobody working their file, and
 * previously that was only visible by opening each record one at a time.
 */
class OnboardingApexColumnTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name'     => 'Victoria',
            'email'    => 'admin@example.com',
            'password' => 'secret-password',
        ]);
    }

    private function submission(string $name, ?string $apexStatus): OnboardingSubmission
    {
        return OnboardingSubmission::create([
            'firstname'  => $name,
            'lastname'   => 'Tester',
            'email'      => strtolower($name) . '@example.com',
            'phone'      => '4690000000',
            'city'       => 'Dallas',
            'state'      => 'TX',
            'zip'        => '75208',
            'ssn'        => '351909701',
            'birth_date' => '1995-03-06',
            'crc_status' => $apexStatus,
        ]);
    }

    public function test_the_list_shows_apex_state_per_client(): void
    {
        $this->submission('Lanique', 'failed');
        $this->submission('Chanel', 'sent');
        $this->submission('Jamari', 'pending');

        $page = $this->actingAs($this->admin())->get(route('admin.onboarding'));

        $page->assertOk();
        $page->assertSee('badge failed', false);
        $page->assertSee('badge sent', false);
        $page->assertSee('badge pending', false);
    }

    public function test_it_warns_how_many_never_reached_apex(): void
    {
        $this->submission('Lanique', 'failed');
        $this->submission('Chanel', 'failed');
        $this->submission('Jamari', 'sent');

        $this->actingAs($this->admin())
            ->get(route('admin.onboarding'))
            ->assertSee('2 clients never reached Apex.');
    }

    public function test_the_warning_is_hidden_when_nothing_failed(): void
    {
        $this->submission('Chanel', 'sent');

        $this->actingAs($this->admin())
            ->get(route('admin.onboarding'))
            ->assertDontSee('never reached Apex');
    }

    public function test_filtering_to_failed_shows_only_those(): void
    {
        $this->submission('Lanique', 'failed');
        $this->submission('Chanel', 'sent');

        $page = $this->actingAs($this->admin())
            ->get(route('admin.onboarding', ['apex' => 'failed']));

        $page->assertSee('Lanique');
        $page->assertDontSee('Chanel');
    }

    public function test_a_submission_with_no_apex_state_set_counts_as_pending(): void
    {
        // crc_status is NOT NULL with a 'pending' default, so an unset value
        // lands there rather than as NULL.
        OnboardingSubmission::create([
            'firstname' => 'Older', 'lastname' => 'Tester',
            'email' => 'older@example.com', 'phone' => '4690000000',
            'city' => 'Dallas', 'state' => 'TX', 'zip' => '75208',
            'ssn' => '351909701', 'birth_date' => '1995-03-06',
        ]);
        $this->submission('Chanel', 'sent');

        $page = $this->actingAs($this->admin())
            ->get(route('admin.onboarding', ['apex' => 'pending']));

        $page->assertSee('Older');
        $page->assertDontSee('Chanel');
    }

    public function test_the_apex_filter_survives_paging_and_combines_with_search(): void
    {
        $this->submission('Lanique', 'failed');
        $this->submission('Chanel', 'failed');

        $page = $this->actingAs($this->admin())
            ->get(route('admin.onboarding', ['apex' => 'failed', 'q' => 'Lanique']));

        $page->assertSee('Lanique');
        $page->assertDontSee('Chanel');
    }
}
