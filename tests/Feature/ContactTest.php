<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_contact_page_shows_current_boutique_address(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('720 Market St', false)
            ->assertSee('San Francisco, CA 94102', false);
    }
}
