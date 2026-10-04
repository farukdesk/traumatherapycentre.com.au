<?php
/**
 * Content table definitions for the generic admin CRUD (manage.php).
 */

function admin_tables(): array
{
    return [
        'therapies' => [
            'label'  => 'Therapy Types',
            'fields' => [
                'title'    => ['label' => 'Title', 'type' => 'text', 'required' => true],
                'subtitle' => ['label' => 'Subtitle', 'type' => 'text'],
                'body'     => ['label' => 'Body (blank line = new paragraph, "- " = list item)', 'type' => 'textarea', 'required' => true],
            ],
            'list' => ['title', 'subtitle'],
        ],
        'gold_standards' => [
            'label'  => 'Gold Standards',
            'fields' => [
                'num_label' => ['label' => 'Number label (e.g. 01)', 'type' => 'text', 'required' => true],
                'title'     => ['label' => 'Short title', 'type' => 'text', 'required' => true],
                'full_name' => ['label' => 'Full name', 'type' => 'text', 'required' => true],
                'body'      => ['label' => 'Body (blank line = new paragraph)', 'type' => 'textarea', 'required' => true],
            ],
            'list' => ['num_label', 'title'],
        ],
        'fee_steps' => [
            'label'  => 'Fee / Appointment Cards',
            'fields' => [
                'title'         => ['label' => 'Title', 'type' => 'text', 'required' => true],
                'items'         => ['label' => 'List items (one per line)', 'type' => 'textarea', 'required' => true],
                'flag'          => ['label' => 'Warning flag (optional)', 'type' => 'text'],
                'show_timeline' => ['label' => 'Show reminder timeline', 'type' => 'checkbox'],
            ],
            'list' => ['title'],
        ],
        'cancellation_tiers' => [
            'label'  => 'Cancellation Tiers',
            'fields' => [
                'notice_label' => ['label' => 'Notice given', 'type' => 'text', 'required' => true],
                'fee_label'    => ['label' => 'Fee', 'type' => 'text', 'required' => true],
                'fee_note'     => ['label' => 'Fee note', 'type' => 'text'],
            ],
            'list' => ['notice_label', 'fee_label'],
        ],
        'qualifications' => [
            'label'  => 'Qualifications',
            'fields' => [
                'category' => ['label' => 'Category', 'type' => 'select', 'options' => ['registrations' => 'Registrations', 'training' => 'Training & memberships'], 'required' => true],
                'item'     => ['label' => 'Item', 'type' => 'text', 'required' => true],
            ],
            'list' => ['category', 'item'],
        ],
        'availability' => [
            'label'  => 'Availability',
            'fields' => [
                'day_label'   => ['label' => 'Day', 'type' => 'text', 'required' => true],
                'hours_label' => ['label' => 'Hours', 'type' => 'text', 'required' => true],
            ],
            'list' => ['day_label', 'hours_label'],
        ],
        'marquee_items' => [
            'label'  => 'Marquee Items',
            'fields' => [
                'label' => ['label' => 'Label', 'type' => 'text', 'required' => true],
            ],
            'list' => ['label'],
        ],
        'hero_stats' => [
            'label'  => 'Hero Stats',
            'fields' => [
                'stat_value' => ['label' => 'Value (e.g. 10)', 'type' => 'text', 'required' => true],
                'stat_label' => ['label' => 'Label', 'type' => 'text', 'required' => true],
            ],
            'list' => ['stat_value', 'stat_label'],
        ],
    ];
}
