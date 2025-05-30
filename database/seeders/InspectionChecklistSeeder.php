<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use App\Models\InspectionType;
use Illuminate\Database\Seeder;
use App\Models\InspectionChecklist;

class InspectionChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $inspectionType = InspectionType::first();

        $checklists = [
            'Pictures of Vehicle' => [
                [
                    'title' => 'Front Photo',
                    'description' => 'Front photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Back Photo',
                    'description' => 'Back photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 2,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Passenger Side',
                    'description' => 'Passenger side photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 3,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Driver Side',
                    'description' => 'Driver side photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 4,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Front Right Tire',
                    'description' => 'Front right tire photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 5,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Front Left Tire',
                    'description' => 'Front left tire photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 6,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Rear Right Tire',
                    'description' => 'Rear right tire photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 7,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Rear Left Tire',
                    'description' => 'Rear left tire photo of the vehicle',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 8,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Add more photos',
                    'description' => 'Add up to 3 more photos',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 9,
                    'is_required' => false,
                    'max' => 3,
                    'created_at' => now(),
                ],
            ],
            'MOT Screenshots' => [
                [
                    'title' => 'Upload MOT Screenshots',
                    'description' => 'The MOT screenshots',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'I have visually verified previous MOT faults',
                    'description' => 'Checkbox for MOT faults verification',
                    'item_type' => ChecklistItem::ITEM_TYPE_BOOLEAN,
                    'display_order' => 2,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Walk Around Video' => [
                [
                    'title' => 'Upload a video walk-around (with bonnet open)',
                    'description' => 'Upload or record a video',
                    'item_type' => ChecklistItem::ITEM_TYPE_VIDEO,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Dip Stick Photo' => [
                [
                    'title' => 'Upload a dipstick photo',
                    'description' => 'Upload a dipstick photo',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Coolant Level Photo' => [
                [
                    'title' => 'Upload a coolant level photo',
                    'description' => 'Upload a coolant level photo',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Leakage Inspection' => [
                [
                    'title' => 'Leakage Inspection',
                    'description' => 'Select the options where leakage is detected',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 1,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'Place 1 leakage area in vehicle',
                        'Place 2 leakage area in vehicle',
                        'Place 3 leakage area in vehicle',
                        'Place 4 leakage area in vehicle',
                        'Place 5 leakage area in vehicle',
                        'Place 6 leakage area in vehicle',
                        'Place 7 leakage area in vehicle',
                        'Place 8 leakage area in vehicle',
                    ]
                ],
            ],
            'Smell Check' => [
                [
                    'title' => 'Smell Check',
                    'description' => 'Select the options for smell check',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 1,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'Place 1 smell check',
                        'Place 2 smell check',
                        'Place 3 smell check',
                        'Place 4 smell check',
                        'Place 5 smell check',
                        'Place 6 smell check',
                        'Place 7 smell check',
                        'Place 8 smell check',
                    ]
                ],
            ],
            'Noise Check' => [
                [
                    'title' => 'Noise Check',
                    'description' => 'Select the options for noise check',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 1,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'Place 1 noise check',
                        'Place 2 noise check',
                        'Place 3 noise check',
                        'Place 4 noise check',
                        'Place 5 noise check',
                        'Place 6 noise check',
                        'Place 7 noise check',
                        'Place 8 noise check',
                    ]
                ],
            ],
            'Pre-Test Diagnostic Scan' => [
                [
                    'title' => 'Photos',
                    'description' => 'Upload upto 4 scans',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 1,
                    'min' => 1,
                    'max' => 4,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Test Drive Checklist' => [
                [
                    'title' => 'Test Drive',
                    'description' => 'Select the options related to test-drive',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                    'options' => [
                        'Gear shifts are smooth',
                        'No misfires',
                        'Proper boost',
                        'Option 4',
                        'Option 5',
                        'Option 6',
                        'Option 7',
                        'Option 8',
                    ]
                ],
            ],
            'Post-Test Diagnostic Scan' => [
                [
                    'title' => 'Photos',
                    'description' => 'Upload upto 4 scans',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 1,
                    'min' => 1,
                    'max' => 4,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Features Checklist' => [
                [
                    'title' => 'Features',
                    'description' => 'Select the options related to features',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                    'options' => [
                        'Windows',
                        'Pan roof',
                        'Heaters',
                        'Reverse camera',
                        'Parking sensors',
                        'Touch screen',
                        'Central locking',
                        'Boot & underfloor',
                        'Spare tyre',
                    ]
                ],
            ],
            'Bodywork Damage Photos (only if present)' => [
                [
                    'title' => 'Photos',
                    'description' => 'Upload upto 4 photos',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 1,
                    'min' => 0,
                    'max' => 4,
                    'is_required' => false,
                    'created_at' => now(),
                ],
            ],
            'Mechanical Faults' => [
                [
                    'title' => 'Mechanical Faults',
                    'description' => 'Explain the fault in details',
                    'item_type' => ChecklistItem::ITEM_TYPE_TEXT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => 'Photos',
                    'description' => 'Upload upto 4 photos',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 2,
                    'min' => 0,
                    'max' => 4,
                    'is_required' => false,
                    'created_at' => now(),
                ],
            ],
            'Agreed Price' => [
                [
                    'title' => 'Agreed Price',
                    'description' => 'Enter the price here',
                    'item_type' => ChecklistItem::ITEM_TYPE_NUMBER,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
        ];

        $checklistCounter = 0;

        foreach ($checklists as $title => $items) {
            $checklist = InspectionChecklist::create([
                'inspection_type_id' => $inspectionType->id,
                'uuid' => getUuid(),
                'title' => $title,
                'description' => fake()->sentence(),
                'display_order' => ++$checklistCounter,
                'is_required' => true,
                'created_at' => now(),
            ]);

            foreach ($items as $item) {
                $checklistItem = $checklist->checklistItems()->create(
                    array_diff_key($item, ['options' => ''])
                );

                if (isset($item['options'])) {
                    foreach ($item['options'] as $key => $option) {
                        $item['options'][$key] = [
                            'uuid' => getUuid(),
                            'label' => $option,
                            'created_at' => now(),
                        ];
                    }

                    $checklistItem->itemOptions()->createMany($item['options']);
                }
            }
        }
    }
}
