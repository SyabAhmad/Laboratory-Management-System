<?php

namespace App\Models\Tests;

/**
 * Urinalysis Test Model
 * Handles logic, validation, and formatting for Urinalysis test data.
 */
class Urinal
{
    public static function analytes()
    {
        return [
            'Color' => ['units' => '', 'ref_range' => 'Yellow'],
            'Appearance' => ['units' => '', 'ref_range' => 'Clear'],
            'pH' => ['units' => '', 'ref_range' => '4.6-8.0'],
            'Protein' => ['units' => 'mg/dL', 'ref_range' => 'Negative'],
            'Glucose' => ['units' => 'mg/dL', 'ref_range' => 'Negative'],
            'Ketones' => ['units' => 'mg/dL', 'ref_range' => 'Negative'],
            'Blood' => ['units' => '', 'ref_range' => 'Negative'],
            'Leukocytes' => ['units' => '', 'ref_range' => 'Negative'],
            'Nitrite' => ['units' => '', 'ref_range' => 'Negative'],
        ];
    }

    public static function validate($data)
    {
        $errors = [];
        $analytes = $data['analytes'] ?? [];
        $defs = self::analytes();
        foreach ($defs as $name => $meta) {
            $found = false;
            foreach ($analytes as $a) {
                if (($a['name'] ?? null) === $name) {
                    $found = true;
                    if (!isset($a['value']) || $a['value'] === '') {
                        $errors[] = "$name value missing";
                    }
                }
            }
            if (!$found) {
                $errors[] = "$name missing";
            }
        }
        return [count($errors) === 0, $errors];
    }

    public static function formatForDisplay($data)
    {
        $rows = [];
        $defs = self::analytes();
        $analytes = $data['analytes'] ?? [];
        foreach ($defs as $name => $meta) {
            $row = [
                'name' => $name,
                'value' => null,
                'units' => $meta['units'],
                'ref_range' => $meta['ref_range'],
                'flags' => null,
            ];
            foreach ($analytes as $a) {
                if (($a['name'] ?? null) === $name) {
                    $row['value'] = $a['value'] ?? null;
                    $row['flags'] = $a['flags'] ?? null;
                }
            }
            $rows[] = $row;
        }
        return $rows;
    }
}
