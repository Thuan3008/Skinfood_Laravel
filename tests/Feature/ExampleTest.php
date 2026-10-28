<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_page_returns_successfully(): void
    {
        $this->get('/')->assertOk()->assertSee('THẾ GIỚI SKINFOOD');
    }

    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/health')->assertOk()->assertJson(['status' => 'ok']);
    }
}
