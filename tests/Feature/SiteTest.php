<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_available_in_all_languages(): void
    {
        $this->seed();

        foreach (['ar', 'en', 'zh'] as $locale) {
            $this->get('/'.$locale)->assertOk();
        }
    }

    public function test_property_page_has_unique_seo_and_faq_schema(): void
    {
        $this->seed();

        $this->get('/ar/warehouses/t-51')
            ->assertOk()
            ->assertSee('مستودعات للإيجار في الرياض')
            ->assertSee('FAQPage');
    }

    public function test_inquiry_can_be_saved(): void
    {
        $this->post('/ar/inquiries', ['name' => 'عميل تجريبي', 'phone' => '0500000000'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('inquiries', ['phone' => '0500000000']);
    }
}
