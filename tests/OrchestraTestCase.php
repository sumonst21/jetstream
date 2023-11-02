<?php

namespace Laravel\Jetstream\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Fortify\FortifyServiceProvider;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\JetstreamServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

abstract class OrchestraTestCase extends TestCase
{
    use LazilyRefreshDatabase, WithWorkbench;

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function defineHasOrganizationEnvironment($app)
    {
        $features = $app->config->get('jetstream.features', []);

        $features[] = Features::organizations(['invitations' => true]);

        $app->config->set('jetstream.features', $features);
    }
}
