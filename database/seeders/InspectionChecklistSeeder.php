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
                    'title' => null,
                    'description' => 'Front Photo',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Back Photo',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 2,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Passenger Side',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 3,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Driver Side',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 4,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Front Right Tire',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 5,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Front Left Tire',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 6,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Rear Right Tire',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 7,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Rear Left Tire',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 8,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Add more photos',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 9,
                    'is_required' => false,
                    'min' => 0,
                    'max' => 3,
                    'created_at' => now(),
                ],
            ],
            'MOT Screenshots' => [
                [
                    'title' => null,
                    'description' => 'Upload upto 4 MOT Screenshots',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'min' => 1,
                    'max' => 4,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'I have visually verified previous MOT faults',
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
                    'title' => null,
                    'description' => 'Upload a dipstick photo',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Coolant Level Photo' => [
                [
                    'title' => null,
                    'description' => 'Upload a coolant level photo',
                    'item_type' => ChecklistItem::ITEM_TYPE_IMAGE,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                ],
            ],
            'Leakage Inspection' => [
                [
                    'title' => null,
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_SELECT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                    'options' => [
                        'I have not found any leakage',
                        'Leakage is present',
                    ]
                ],
            ],
            'Smell Check' => [
                [
                    'title' => null,
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_SELECT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                    'options' => [
                        'I cannot smell anything near the engine bay',
                        'There is a smell near the engine bay',
                    ]
                ],
            ],
            'Noise Check' => [
                [
                    'title' => null,
                    'description' => 'Mention the details on "Mechanical Faults" page.',
                    'item_type' => ChecklistItem::ITEM_TYPE_SELECT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                    'options' => [
                        'There is abnormal noise coming from the engine bay',
                        'No abnormal noise from the engine bay',
                    ]
                ],
            ],
            'Pre Test Drive Diagnostic Scan' => [
                [
                    'title' => null,
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
                    'title' => '1. Mechanical & Driving Performance',
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 1,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'Engine starts smoothly - no hesitation, rattles or warning lights',
                        'Idle is steady - no misfires or fluctuating revs',
                        'No excessive exhaust smoke - black, white, or blue',
                        'No dashboard warning lights',
                        'Gear changes are smooth - automatic or manual',
                        'Clutch engagement is smooth - no slipping or judder',
                        'No abnormal engine noises - tapping, knocking, whining',
                    ]
                ],
                [
                    'title' => '2. Steering & Suspension',
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 2,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'Steering wheel is straight when driving straight',
                        'No shaking or vibration through the wheel at idle or while driving',
                        'Steering is responsive with no excessive play',
                        'No knocking, creaking, or squeaking from suspension over bumps or uneven roads',
                        'Car tracks straight - no pulling to either side when braking or driving',
                    ]
                ],
                [
                    'title' => '3. Brakes',
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 3,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'Visual check of brake discs and pads - no heavy scoring or wear',
                        'Brakes feel strong and progressive',
                        'No grinding, squealing, or knocking sounds while braking',
                        'Handbrake holds the car securely on a slope',
                    ]
                ],
                [
                    'title' => '4. Handling & Ride Quality',
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 4,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'No excessive body roll on corners',
                        'No unusual noises from under the car while turning or accelerating',
                        'Suspension absorbs bumps well',
                    ]
                ],
                [
                    'title' => '5. Interior Check During Drive',
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 5,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'All dashboard instruments work - speedometer, rev counter, fuel, temperature',
                        'Heating, air conditioning, and fans function properly',
                        'No warning messages during the drive',
                        'Check for any abnormal smells - burning oil, coolant, or fuel',
                        'Windows are all working fine',
                    ]
                ],
                [
                    'title' => '6. Transmission & Drivetrain',
                    'description' => null,
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTISELECT,
                    'display_order' => 6,
                    'is_required' => false,
                    'created_at' => now(),
                    'options' => [
                        'No clunks or delay when engaging drive (automatic)',
                        'No crunching between gears (manual)',
                        'No whining or humming from gearbox or differential',
                    ]
                ],
            ],
            'Post Test Drive Diagnostic Scan' => [
                [
                    'title' => null,
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
                    'title' => null,
                    'description' => null,
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
                    'title' => null,
                    'description' => 'Is bodywork damage present?',
                    'item_type' => ChecklistItem::ITEM_TYPE_SELECT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                    'options' => [
                        'Yes',
                        'No',
                    ]
                ],
                [
                    'title' => null,
                    'description' => 'Upload upto 4 photos',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 2,
                    'min' => 0,
                    'max' => 4,
                    'is_required' => false,
                    'created_at' => now(),
                ],
            ],
            'Mechanical Faults' => [
                [
                    'title' => null,
                    'description' => 'Is mechanical fault present?',
                    'item_type' => ChecklistItem::ITEM_TYPE_SELECT,
                    'display_order' => 1,
                    'is_required' => true,
                    'created_at' => now(),
                    'options' => [
                        'Yes',
                        'No',
                    ]
                ],
                [
                    'title' => null,
                    'description' => 'Explain the fault in details',
                    'item_type' => ChecklistItem::ITEM_TYPE_TEXT,
                    'display_order' => 2,
                    'is_required' => false,
                    'created_at' => now(),
                ],
                [
                    'title' => null,
                    'description' => 'Upload upto 4 photos',
                    'item_type' => ChecklistItem::ITEM_TYPE_MULTI_IMAGE,
                    'display_order' => 3,
                    'min' => 0,
                    'max' => 4,
                    'is_required' => false,
                    'created_at' => now(),
                ],
            ],
            'Agreed Price' => [
                [
                    'title' => null,
                    'description' => 'Enter the agreed price here',
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
                'description' => null,
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
