<?php

namespace App\Models\Tests;

/**
 * Factory to resolve test model class by test name.
 */
class TestModelFactory
{
    /**
     * Get the test model class for a given test name.
     * @param string $testName
     * @return string|null Fully qualified class name or null if not found
     */
    public static function getModelClass($testName)
    {
        $map = [
            'CBC' => CBC::class,
            'Urinal' => Urinal::class,
            // Add more mappings as you add more test models
        ];
        return $map[$testName] ?? null;
    }
}
