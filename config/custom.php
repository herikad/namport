<?php

// Custom Config
// -------------------------------------------------------------------------------------
//! IMPORTANT: Make sure you clear the browser local storage In order to see the config changes in the template.
//! To clear local storage: (https://www.leadshook.com/help/how-to-clear-local-storage-in-google-chrome-browser/).

return [
    'default_client_id'          => 1,
    'default_currency'          => '$',
    'default_records_limit'     => 20,
    'default_country_shortcode' => 'US',
    'default_country_code'      => '1',
    'default_timezone'          => 'UTC',
    'permission_denied' => 'Can’t perform this action.',
    'custom'                    => [
        'myLayout'            => 'vertical',      // Options[String]: vertical(default), horizontal  HORIZONTAL LAYOUT WOULD NOT WORK WITH SEMI DARK THEME
        'myTheme'             => 'theme-default', // Options[String]: theme-default(default), theme-bordered, theme-semi-dark  SEMI-DARK THEME WOULD NOT WORK WITH HORIZONTAL LAYOUT
        'myStyle'             => 'light',         // Options[String]: light(default), dark
        'myRTLSupport'        => true,            // options[Boolean]: true(default), false // To provide RTLSupport or not
        'myRTLMode'           => false,           // options[Boolean]: false(default), true // To set layout to RTL layout  (myRTLSupport must be true for rtl mode)
        'hasCustomizer'       => false,           // options[Boolean]: true(default), false // Display customizer or not THIS WILL REMOVE INCLUDED JS FILE. SO LOCAL STORAGE WON'T WORK
        'displayCustomizer'   => true,            // options[Boolean]: true(default), false // Display customizer UI or not, THIS WON'T REMOVE INCLUDED JS FILE. SO LOCAL STORAGE WILL WORK
        'menuFixed'           => true,            // options[Boolean]: true(default), false // Layout(menu) Fixed
        'menuCollapsed'       => false,           // options[Boolean]: false(default), true // Show menu collapsed, Only for vertical Layout
        'navbarFixed'         => false,           // options[Boolean]: false(default), true // Navbar Fixed
        'footerFixed'         => false,           // options[Boolean]: false(default), true // Footer Fixed
        'showDropdownOnHover' => true,            // true, false (for horizontal layout only)
        'customizerControls'  => [
            'rtl',
            'style',
            'layoutType',
            'showDropdownOnHover',
            'layoutNavbarFixed',
            'layoutFooterFixed',
            'themes',
        ], // To show/hide customizer options
    ],
    'mail_send_types'           => [
        'WELCOME_MAIL'             => 'WELCOME_MAIL',
        'RESET_PASSWORD'           => 'RESET_PASSWORD',
        'RESEND_VERIFICATION_MAIL' => 'RESEND_VERIFICATION_MAIL',
        'WELCOME_CLIENT_CONTACT' => 'WELCOME_CLIENT_CONTACT',
    ],
    'image_base_url'            => config('filesystems.default') == 's3' ? '' : '',
    'term_category'             => [
        'user_type'   => 'user_type',
        'gender_type' => 'gender_type',
        'age_group'   => 'age_group',
        'currency'    => 'currency',
    ],

    'association_type_term'     => [
        'admin'  => 'admin',
        'our_team' => 'our_team', // employee
        'client_contact' => 'client_contact',
        'mia_agent' => 'mia_agent',
    ],
    'status_term'               => [
        'active' => 'Active',
    ],
    'notification_types'        => [
        'club_owner_signup' => 'club_owner_signup',
    ],
    'crm_slug'                  => [
        'about_us'             => 'about-us',
        'privacy_policy'       => 'privacy-policy',
        'terms_and_conditions' => 'terms-and-conditions',
        'delete_account'       => 'delete-account',
    ],
    'user_type_term'            => [
        'super_admin' => 'super_admin',
        'client_contact'      => 'client_contact',
    ],
    'default_role'              => [
        'super_admin' => 'Super Admin',
    ],
    'currency_sign'             => [
        'usd' => '$',
    ],
    'profile_status_term' => [
        'not_started'   => 'Not Started',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
    ],
    'billing_term' => [
        'fixed_cost'   => 'Fixed Cost',
        'hourly' => 'Hourly',
    ],

    'send_notificaiton_term' => [
        'immediate'     => 'Send notification instantly after action (e.g. task assigned).',
        '1_day_before'  => 'Send notification 1 day before the due date.',
        '2_days_before' => 'Send notification 2 days before the due date.',
        'on_due'        => 'Send notification on the due date.',
        'daily'         => 'Send daily notification summaries.',
        'weekly'        => 'Send weekly notification summaries.',
        'manual'        => 'Only send notification when triggered manually.',
        'none'          => 'Do not send any notifications.',
    ],
    'designation_term' => [
        'project_manager'   => 'Project Manager',
        'business_analyst' => 'Business Analyst',
        'solution_architect' => 'Solution Architect',
        'qa_engineer' => 'QA Engineer',
        'devops_engineer' => 'DevOps Engineer',
        'technical_support' => 'Technical Support',
    ],
];
