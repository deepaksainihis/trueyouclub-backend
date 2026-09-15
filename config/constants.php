<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Request Type List
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the request type list 
    |
    */ 
    'app_name' => env('APP_NAME'),
    'front_end_url' => env('FRONTEND_URL'),
    'app_mode' => env('APP_MODE','staging'),
    'default' => [
        'logo' => 'default/logo.svg',
        'favicon' => 'default/favicon.svg',
        'admin_favicon' => 'default/favicon.svg',
        'short_logo' => 'default/favicon.svg',
        'admin_logo' => 'default/favicon.svg',
        'transparent_logo' => 'assets/logo/logo-transparent.png',
        'profile_image' => 'default/default-user.svg',
        'email_logo' => 'default/elogo.png',
        'no_image' => 'default/no-image.jpg',
    ],

    'role'=>[
        'super_admin' => 1,
        'user' => 2, 
        'volunteer' => 3 ,
    ],

    'profile_image_size' =>'2048', // 1024 = 1 MB

    'owner_email' => env('OWNER_MAIL'),
    'twilio_country_code'=>env('TWILIO_COUNTRY_CODE'),
    'buyer_application_free_price_id'=>env('BUYER_APPLICATION_FEE_PRICE_ID'),
    'buyer_profile_update_price_id'=>env('BUYER_PROFILE_UPDATE_PRICE_ID'),

    // 'owner_email' => 'amitpandey.his@gmail.com',
    // 'owner_email' => 'rohithelpfullinsight@gmail.com',
    
    'date_format' => 'm/d/Y',
    'date_month_year' => 'd-m-Y',
    'datetime_format' => 'm/d/Y h:i:s A',
    'full_datetime_format' => 'd F Y h:i A',
    'full_date_format'=>'d F Y',
    'full_time_format'=>'h:i A',
    'search_full_date_format' => '%d %M %Y',
    'search_full_time_format' => '%h:%i %p',
    'search_full_datetime_format' => '%d %M %Y %h:%i %p',
    'search_datetime_format' => '%m/%d/%Y %H:%i',
    'search_date_format' => '%m/%d/%Y',
    'set_timezone' => 'Asia/kolkata', // set timezone
    
    'logo_min_width' => '250', // logo min width
    'logo_min_height' => '150', // logo min height
   
    'img_max_size' => '1024', // In KB
    'video_max_size' => '102400', // 102400 KB => 100MB

    'token_expired_time'=>60,

    'datatable_entries' => [10, 25, 50, 100],

    'number_of_rows' => [
        10 => '10',
        25 => '25',
        50 => '50',
        75 => '75',
        100 => '100',
    ],


    'default_currency' => env('DEFAULT_CURRENCY','usd'),
    'default_country' => 233,

    'webinar_notification_message' => 'A new webinar has been added. Visit the webinar page and join.',
    'seminar_notification_message' => 'A new seminar has been added. Visit the seminar page to book your seat.',
    'user_register_notification_message' => 'Welcome to the '.env('APP_NAME').'! We are excited to have you on board.',
    'seminar_booked_notification_message' => 'Thank you for your purchase! Your seminar ticket is confirmed. Get ready for an insightful experience!',

    'user_star_no_with_task_count' => [
        '1_star'    => 63,
        '2_star'    => 126,
        '3_star'    => 189,
        '4_star'    => 252,
        '5_star'    => 315,
    ],

    'ratings'=>[
        1,2,3,4,5
    ],
   
    'event_invite_status' => [
        'pending'   => 0,
        'accepted'  => 1,
        'declined'  => 2,
    ],

    'event_request_status'=>[
        0=>'pending',
        1=>'accepted',
        2=>'declined',
    ],

    'education_video_type'=>[
        'video_link'=> 'Video Link',
        'upload_video'=> 'Upload Video',        
    ],

    'event_confirmation_days' => 1,

    'embed_video_link' => 'https://www.youtube.com/embed/',
];