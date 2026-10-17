<?php
/**
 * Presentation metadata for the Therapy Types accordion.
 * Keyed by URL slug (deep-link anchor). The copy itself stays in the
 * `therapies` table — this file only holds side-panel extras and icons.
 *
 * TODO: Patricia to confirm wording (AHPRA advertising: avoid claims of benefit).
 */

return [
    'ifs' => [
        'match'      => 'Internal Family Systems',
        'label'      => 'IFS',
        'helps'      => ['Inner conflict', 'Protective parts', 'Self-understanding'],
        'session'    => 'We notice the parts of you that show up and get to know them with curiosity, at your pace.',
        'works_with' => ['brainspotting', 'schema'],
        // Nested circles
        'icon'       => '<svg class="t-ico" viewBox="0 0 28 28" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" aria-hidden="true"><circle cx="14" cy="14" r="10.5"/><circle cx="14" cy="14" r="6.5"/><circle cx="14" cy="14" r="2.5"/></svg>',
    ],
    'brainspotting' => [
        'match'      => 'Brainspotting',
        'label'      => 'Brainspotting',
        'helps'      => ['Body-held memories', 'Eye positions', 'Felt sense'],
        'session'    => 'We find the eye position connected to what we are working on and stay with it, gently.',
        'works_with' => ['ifs', 'hrv'],
        // Eye / focus point
        'icon'       => '<svg class="t-ico" viewBox="0 0 28 28" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" aria-hidden="true"><path d="M2.5 14C6 8.8 9.9 6.5 14 6.5S22 8.8 25.5 14C22 19.2 18.1 21.5 14 21.5S6 19.2 2.5 14Z"/><circle cx="14" cy="14" r="3.5"/><circle cx="14" cy="14" r=".75" fill="currentColor" stroke="none"/></svg>',
    ],
    'schema' => [
        'match'      => 'Schema Therapy',
        'label'      => 'Schema Therapy',
        'helps'      => ['Early patterns', 'Repeating themes', 'Feeling stuck'],
        'session'    => 'We map the patterns that formed early in life and notice where they appear today.',
        'works_with' => ['ifs', 'personal-growth'],
        // Connected nodes
        'icon'       => '<svg class="t-ico" viewBox="0 0 28 28" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" aria-hidden="true"><circle cx="6.5" cy="7.5" r="2.5"/><circle cx="21.5" cy="9.5" r="2.5"/><circle cx="14" cy="21" r="2.5"/><path d="M8.9 8.1l10.2 1.1M7.5 9.9 13 18.8M20.4 11.7 15.2 19"/></svg>',
    ],
    'hrv' => [
        'match'      => 'HRV Biofeedback',
        'label'      => 'HRV Biofeedback',
        'helps'      => ['Heart rhythms', 'Breath pacing', 'Settling the body'],
        'session'    => 'A sensor shows your heart rhythm on screen while we practise slow, paced breathing together.',
        'works_with' => ['irest', 'brainspotting'],
        // Heart-rhythm wave (pathLength=1 so the draw animation is length-independent)
        'icon'       => '<svg class="t-ico t-ico-hrv" viewBox="0 0 28 28" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1.5 14h5l2.5-6 4 12 3-9 2 3h8.5" pathLength="1"/></svg>',
    ],
    'irest' => [
        'match'      => 'iRest',
        'label'      => 'iRest®',
        'helps'      => ['Deep rest', 'Guided practice', 'Nervous system'],
        'session'    => 'You rest comfortably while being guided through a structured deep-relaxation practice.',
        'works_with' => ['hrv', 'personal-growth'],
        // Crescent moon
        'icon'       => '<svg class="t-ico t-ico-irest" viewBox="0 0 28 28" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 3.5A11.5 11.5 0 1 0 24.5 18.5 9 9 0 0 1 18 3.5Z"/></svg>',
    ],
    'expressive-arts' => [
        'match'      => 'Expressive Arts',
        'label'      => 'Expressive Arts',
        'helps'      => ['Beyond words', 'Drawing', 'Bilateral music'],
        'session'    => 'We might draw — free-flowing or structured — sometimes paired with bilateral music.',
        'works_with' => ['ifs', 'irest'],
        // Brush stroke
        'icon'       => '<svg class="t-ico" viewBox="0 0 28 28" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12.5 16.5 24 4.5"/><path d="M4.5 23.5c.5-4 3.5-6 6-5s2.5 5.5-1.5 6.5c-1.8.45-3.5.1-4.5-1.5Z"/></svg>',
    ],
    'personal-growth' => [
        'match'      => 'Personal Growth',
        'label'      => 'Personal Growth',
        'helps'      => ['Enneagram', 'Meditation', 'Mindfulness'],
        'session'    => 'We explore where you are now and what you would like to understand more deeply.',
        'works_with' => ['schema', 'irest'],
        // Sprout
        'icon'       => '<svg class="t-ico" viewBox="0 0 28 28" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 25.5v-9.5"/><path d="M14 16c0-4.5-3.2-7.5-7.5-7.5 0 4.5 3.2 7.5 7.5 7.5Z"/><path d="M14 16c0-4.5 3.2-7.5 7.5-7.5 0 4.5-3.2 7.5-7.5 7.5Z"/></svg>',
    ],
];
