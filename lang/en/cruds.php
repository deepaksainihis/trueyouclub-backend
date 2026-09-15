<?php

return [
    'userManagement' => [
        'title'          => 'User Management',
        'title_singular' => 'User Management',
    ],
    'setting' => [
        'title'          => 'Settings',
        'title_singular' => 'Setting',
    ],
    'user'           => [
        'title'          => 'Users',
        'title_singular' => 'User',
        'fields'         => [
            'id'                       => 'ID',
            'first_name'               => 'First Name',
            'last_name'                => 'Last Name',
            'name'                     => 'Name',
            'full_name'                => 'Full name',
            'email'                    => 'Email',
            'phone'                    => 'Phone Number',
            'profile_image'            => 'Profile Image',
            'status'                   => 'Status',
            'password'                 => 'Password',
            'confirm_password'         => 'Password Confirm',
            'role'                     => 'User Level',
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',
            'rating'                   => 'Rating',
            'want_a_break'             => ' Want A Break ?',
        ],
    ],
    'volunteer'   => [
        'title'          => 'Volunteers',
        'title_singular' => 'Volunteer',
        'list'           => 'Volunteer List',
        'fields'         => [
            'id'                       => 'ID',
            'first_name'               => 'First Name',
            'last_name'                => 'Last Name',
            'name'                     => 'Name',
            'full_name'                => 'Full name',
            'email'                    => 'Email',
            'phone'                    => 'Phone Number',
            'profile_image'            => 'Profile Image',
            'status'                   => 'Status',
            'password'                 => 'Password',
            'confirm_password'         => 'Password Confirm',
            'location_id'              => 'Location',
            'role'                     => 'User Level',
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',
            'rating'                   => 'Rating',

        ],
    ],

    'heroe'   => [
        'title'          => 'Our Heroes',
        'title_singular' => 'Heroe',
        'list'           => 'Our Heroes',
        'fields'         => [
            'id'                       => 'ID',            
            'name'                     => 'Name',            
            'description'              => 'Description',            
            'status'                   => 'Status',       
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',
        ],
    ],

    'category'   => [
        'title'          => 'Category Management',
        'title_singular' => 'Category',
        'list'           => 'Category List',
        'fields'         => [
            'id'                       => 'ID',            
            'name'                     => 'Name',            
            'status'                   => 'Status',                   
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',
        ],
    ],

    'education'   => [
        'title'          => 'Education',
        'title_singular' => 'Education',
        'list'           => 'Education List',
        'menu_list'           => 'List',
        'fields'         => [
            'id'                       => 'ID',            
            'title'                    => 'Title',            
            'description'               => 'Description',            
            'video_link'               => 'Video Link',            
            'upload_video'             => 'Upload Video',          
            'category'                 => 'Category Id',            
            'status'                   => 'Status',       
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',
            'select_category'          => 'Select Category',
            'select_video_type'        => 'Select Video type',
            'view_video'               => 'View Video',
        ],
    ],

    'event'   => [
        'title'          => 'Events',
        'title_singular' => 'Event',
        'list'           => 'Event List',
        'event_attendance'      => 'Event Attendance',
        'fields'         => [
            'id'                       => 'ID',            
            'title'                    => 'Title',            
            'description'              => 'Description',                              
            'status'                   => 'Status',       
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',            
            'select_event'             => 'Select Event',  
            'event_date'               => 'Event Date',
            'start_time'               => 'Start Time',
            'end_time'                 => 'End Time',
            'end_date'                 => 'End Date',
            'start_date_time'          => 'Start Datetime',
            'end_date_time'            => 'End Datetime',          
            'required_volunteer'        => 'Number of volunteer',
            'required_volunteers'       => 'Required Volunteers',          
        ],
    ],


    'permission'     => [
        'title'          => 'Permissions',
        'title_singular' => 'Permission',
        'fields'         => [
            'id'                => 'ID',
            'title'             => 'Title',
            'created_at'        => 'Created at',
            'updated_at'        => 'Updated at',
            'deleted_at'        => 'Deleted at',
        ],
    ],


    'dashboard'  => [],

    'transaction' => [
        'title'                 => 'Transactions',
        'title_singular'        => 'Transaction',
        'fields' => [
            'user'   => 'User',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'payment_type'   => 'Payment Type',
            'payment_method' => 'Payment Method',
            'status'         => 'Status',
        ],
    ],

    'webinar' => [
        'title'                 => 'Webinars',
        'title_singular'        => 'Webinar',
        'fields' => [
            'title'   => 'Title',
            'start_date'    => 'Webinar Date',
            'start_time'    => 'Start Time',
            'end_date'      => 'End Date',
            'end_time'      => 'End Time',
            'meeting_link' => 'Meeting Link',
            'start_date_time'   => 'Start Datetime',
            'end_date_time'     => 'End Datetime',
            'join_meeting'      => 'Join The Webinar',
        ],
    ],

    'blog' => [
        'title'                 => 'Blogs',
        'title_singular'        => 'Blog',
        'fields' => [
            'title'   => 'Title',
            'content' => 'Content',
            'publish_date' => 'Publish Date'
        ],
    ],

    'news' => [
        'title'                 => 'News',
        'title_singular'        => 'News',
        'fields' => [
            'title'   => 'Title',
            'content' => 'Content',
            'publish_date' => 'Publish Date'
        ]
    ],

    'quote' => [
        'title'                 => 'Quotes',
        'title_singular'        => 'Quote',
        'fields' => [
            'message'   => 'Quote Message',

        ],
    ],

    'seminar' => [
        'title'                 => 'Seminars',
        'title_singular'        => 'Seminar',
        'fields' => [
            'title'         => 'Title',
            'total_ticket'  => 'Total Number of Tickets',
            'ticket_price'  => 'Ticket Price',
            'start_date'    => 'Seminar Date',
            'start_time'    => 'Start Time',
            'end_date'      => 'End Date',
            'end_time'      => 'End Time',
            'venue'         => 'Venue',
            'start_date_time'   => 'Start Datetime',
            'end_date_time'     => 'End Datetime',

        ],
    ],

    'health' => [
        'title'                 => 'Health',
        'title_singular'        => 'Health',
        'fields' => [
            'title'   => 'Title',
            'content' => 'Content',
            'publish_date' => 'Publish Date'
        ]
    ],

    'contacts' => [
        'title'                 => 'Contact Form',
        'title_singular'        => 'Contact',
        'fields' => [
            'full_name'   => 'Full Name',
            'first_name'   => 'First Name',
            'last_name' => 'Last Name',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'message' => 'Message'
        ]
    ],

    'pages' => [
        'title'      => 'Pages',
        'title_singular' => 'Page',
        'fields' => [
            'page_name'   => 'Page Name',
            'page_name'   => 'Page Name',
            'title'   => 'Title',
            'subtitle'   => 'Sub Title',
            'description'   => 'Description',
            'button' => 'Button Title',
            'link' => 'Link',
        ]
    ],

    'sections' => [
        'title'          => 'Sections',
        'title_singular' => 'Section',
        'fields' => [
            'title'          => 'Title',
            'content_text'   => 'Content Text',
            'button'         => 'Button Title',
            'link'           => 'Link',
        ]
    ],

    'booking' => [
        'title'                 => 'Bookings',
        'title_singular'        => 'Booking',
        'fields' => [
            'seminar_name'    => 'Seminar Name',
            'user_name'       => 'Name',
            'user_email'      => 'Email',
            'booking_number'  => 'Booking Number',
            'bookings'  => 'B',
        ],
    ],

    'mis_reports' => [
        'title'=>'MIS Reports',
        'title_singular'=>'MIS Report',
        'fields'=>[
            'date' => 'Quote Date',
            'user_name' => 'User Name',
            'quote' => 'Quote',
            'total_skipped' => 'Total Skipped',
            'total_completed' => 'Total Completed',
            'total_leave' => 'Total Leave',
            'total_user' => 'Total User',
            'status' => 'Status',
        ]
    ],

    'location'   => [
        'title'          => 'Locations',
        'title_singular' => 'Location',
        'list'           => 'Location List',
        'fields'         => [
            'id'                       => 'ID',
            'name'               => 'Name',
            'created_at'               => 'Created',
            'updated_at'               => 'Updated',
            'deleted_at'               => 'Deleted',
            'rating'                   => 'Rating',

        ],
    ],
];
