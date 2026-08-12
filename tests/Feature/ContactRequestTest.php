<?php

namespace Tests\Feature;

use App\Mail\ContactRequestMailable;
use App\Models\ContactLead;
use App\Models\ContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'contact.sheet.enabled' => false,
            'contact.recipient' =>
                'contact.smartschoolacademy@gmail.com',
            'contact.default_country_code' => '212',
        ]);

        Mail::fake();
    }

    public function test_it_creates_a_contact_and_request_history()
    {
        $response = $this->post(
            route('contact.store'),
            [
                'first_name' => 'Mohamed',
                'last_name' => 'Alaoui',
                'email' => 'mohamed@example.com',
                'phone' => '06 12 34 56 78',
                'country' => 'Maroc',
                'reason' =>
                    'Je souhaite connaître les tarifs.',
                'marketing_consent' => '1',
                'website' => '',
            ]
        );

        $response->assertRedirect(
            route('home')
            . '#prise-de-contact'
        );

        $this->assertDatabaseCount(
            'contact_leads',
            1
        );

        $this->assertDatabaseCount(
            'contact_requests',
            1
        );

        $lead = ContactLead::first();

        $this->assertSame(
            1,
            $lead->submissions_count
        );

        $this->assertTrue(
            $lead->marketing_consent
        );

        Mail::assertSent(
            ContactRequestMailable::class
        );
    }

    public function test_duplicate_contact_increments_counter_instead_of_creating_new_lead()
    {
        $this->post(
            route('contact.store'),
            [
                'first_name' => 'Mohamed',
                'last_name' => 'Alaoui',
                'email' => 'Mohamed@Example.com',
                'phone' => '06 12 34 56 78',
                'country' => 'Maroc',
                'reason' => 'Première demande',
                'website' => '',
            ]
        );

        $this->post(
            route('contact.store'),
            [
                'first_name' => 'Mohamed',
                'last_name' => 'Alaoui',
                'email' => 'mohamed@example.com',
                'phone' => '+212 6 12 34 56 78',
                'country' => 'Maroc',
                'reason' => 'Deuxième demande',
                'website' => '',
            ]
        );

        $this->assertDatabaseCount(
            'contact_leads',
            1
        );

        $this->assertDatabaseCount(
            'contact_requests',
            2
        );

        $lead = ContactLead::first();

        $this->assertSame(
            2,
            $lead->submissions_count
        );

        $this->assertSame(
            'Deuxième demande',
            $lead->latest_reason
        );
    }

    public function test_same_phone_also_counts_as_a_duplicate()
    {
        $this->post(
            route('contact.store'),
            [
                'first_name' => 'Sara',
                'last_name' => 'Benali',
                'email' => 'sara1@example.com',
                'phone' => '0611223344',
                'country' => 'Maroc',
                'reason' => 'Information',
                'website' => '',
            ]
        );

        $this->post(
            route('contact.store'),
            [
                'first_name' => 'Sara',
                'last_name' => 'Benali',
                'email' => 'sara2@example.com',
                'phone' => '+212611223344',
                'country' => 'Maroc',
                'reason' => 'Horaires',
                'website' => '',
            ]
        );

        $this->assertDatabaseCount(
            'contact_leads',
            1
        );

        $this->assertDatabaseCount(
            'contact_requests',
            2
        );

        $this->assertSame(
            2,
            ContactLead::first()
                ->submissions_count
        );
    }

    public function test_required_fields_are_validated()
    {
        $response = $this->post(
            route('contact.store'),
            [
                'website' => '',
            ]
        );

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'phone',
            'country',
            'reason',
        ]);
    }
}
