<?php

namespace App\Models\Tests;

/**
 * CBC Test Model
 * Handles logic, validation, and formatting for CBC test data.
 */
class CBC
{
    /**
     * CBC analyte definitions (for validation, display, etc.)
     */
    public static function analytes()
    {
        return [
            'White Blood Cells' => ['units' => '10^9/L', 'ref_range' => '4.0-10.0'],
            'Red Blood Cells'   => ['units' => '10^12/L', 'ref_range' => '4.5-6.0'],
            'Hemoglobin'        => ['units' => 'g/dL',    'ref_range' => '13.0-17.0'],
            'Hematocrit'        => ['units' => '%',       'ref_range' => '40-50'],
            'MCV'               => ['units' => 'fL',      'ref_range' => '80-100'],
            'MCH'               => ['units' => 'pg',      'ref_range' => '27-33'],
            'MCHC'              => ['units' => 'g/dL',    'ref_range' => '32-36'],
            'Platelets'         => ['units' => '10^9/L',  'ref_range' => '150-400'],
        ];
    }

    /**
     * Validate CBC data structure (array of analytes)
     * @param array $data
     * @return array [is_valid, errors[]]
     */
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

    /**
     * Format CBC data for display (returns array of rows)
     * @param array $data
     * @return array
     */
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
