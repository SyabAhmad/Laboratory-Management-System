<?php

return [
    'X-Ray' => [
        'fields' => [
            [
                'name' => 'examination_area',
                'label' => 'Examination Area',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'findings',
                'label' => 'Findings',
                'type' => 'textarea',
                'required' => true,
            ],
            [
                'name' => 'impression',
                'label' => 'Impression',
                'type' => 'textarea',
                'required' => true,
            ],
            [
                'name' => 'radiologist_notes',
                'label' => 'Radiologist Notes',
                'type' => 'textarea',
                'required' => false,
            ],
        ],
    ],
    'plplp' => [
        'fields' => [
            [
                'name' => 'hemoglobin',
                'label' => 'Hemoglobin (g/dL)',
                'type' => 'number',
                'required' => true,
                'step' => '0.1',
            ],
            [
                'name' => 'wbc_count',
                'label' => 'WBC Count (cells/µL)',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'platelet_count',
                'label' => 'Platelet Count (cells/µL)',
                'type' => 'number',
                'required' => true,
            ],
            [
                'name' => 'remarks',
                'label' => 'Remarks',
                'type' => 'textarea',
                'required' => false,
            ],
        ],
    ],
];