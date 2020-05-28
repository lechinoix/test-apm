<?php

declare(strict_types=1);

namespace App\Tests;

trait FixtureAwareCaseTrait
{
    protected static function loadFixtures(string $fixtureFileName): void
    {
        $container = static::$kernel->getContainer();
        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');
        if (method_exists($loader, 'load')) {
            $loader->load(['tests/fixtures/'.$fixtureFileName]);
        }
    }
}
