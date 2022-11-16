<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        // Если в тестах будет реальный запрос http, то выпадет exception
        Http::preventStrayRequests();
    }
}
