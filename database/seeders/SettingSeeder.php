<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [

                'key'    => 'site_logo',
                'value'  => null,
                'type'   => 'image',
                'display_name'  => 'Logo',
                'group'  => 'site',
                'details' => '170 × 60',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [

                'key'    => 'favicon',
                'value'  => null,
                'type'   => 'image',
                'display_name'  => 'Favicon',
                'group'  => 'site',
                'details' => '32 × 32',
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [

                'key'    => 'short_logo',
                'value'  => null,
                'type'   => 'image',
                'display_name'  => 'Short Logo',
                'group'  => 'site',
                'details' => '40 × 60',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [

                'key'    => 'footer_logo',
                'value'  => null,
                'type'   => 'image',
                'display_name'  => 'Footer Logo',
                'group'  => 'site',
                'details' => '232 × 54',
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
          
        
            [

                'key'    => 'company_email',
                'value'  => 'rohithelpfullinsight@gmail.com',
                'type'   => 'text',
                'display_name'  => 'Company Email',
                'group'  => 'site',
                'details' => null,
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

           
            [

                'key'    => 'support_email',
                'value'  => 'Info@trueyouclub.com',
                'type'   => 'text',
                'display_name'  => 'Email',
                'group'  => 'support',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [

                'key'    => 'support_phone',
                'value'  => '1234567890',
                'type'   => 'text',
                'display_name'  => 'Phone Number',
                'group'  => 'support',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [

                'key'    => 'support_location',
                'value'  => '132, My Street,Kingston, New York 12401 United States',
                'type'   => 'text',
                'display_name'  => 'Location',
                'group'  => 'support',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],


            [

                'key'    => 'welcome_mail_content',
                'value'  =>  null,
                'type'   => 'text_area',
                'display_name'  => 'Welcome Mail Content',
                'group'  => 'mail',
                'details' => '[NAME], [EMAIL], [SUPPORT_EMAIL], [SUPPORT_PHONE], [APP_NAME]',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [

                'key'    => 'reset_password_mail_content',
                'value'  =>  null,
                'type'   => 'text_area',
                'display_name'  => 'Reset Password Mail Content',
                'group'  => 'mail',
                'details' => '[NAME], [RESET_PASSWORD_BUTTON]',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [

                'key'    => 'contact_us_mail_content',
                'value'  =>  null,
                'type'   => 'text_area',
                'display_name'  => 'Contact Us Mail Content',
                'group'  => 'mail',
                'details' => '[APP_NAME], [MESSAGE], [NAME], [EMAIL]',
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'booked_seminar_mail_content',
                'value'  =>  null,
                'type'   => 'text_area',
                'display_name'  => 'Booked Seminar Mail Content',
                'group'  => 'mail',
                'details' => '[NAME], [BOOKING_NUMBER], [SEMINAR_TITLE], [SEMINAR_DATE], [SEMINAR_START_TIME], [SEMINAR_END_TIME], [SEMINAR_VENUE], [SUPPORT_EMAIL], [SUPPORT_PHONE], [APP_NAME]',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'passcode_mail_content',
                'value'  =>  null,
                'type'   => 'text_area',
                'display_name'  => 'Golden Gateway Code Mail Content',
                'group'  => 'mail',
                'details' => '[NAME], [PASSCODE], [SITE_URL], [SUPPORT_EMAIL], [SUPPORT_PHONE], [APP_NAME]',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
         
           
            [
                'key'    => 'stripe_secret_key',
                'value'  => null,
                'type'   => 'text',
                'display_name'  => 'Stripe Secret Key',
                'group'  => 'payment',
                'details' => '',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'stripe_publishable_key',
                'value'  => null,
                'type'   => 'text',
                'display_name'  => 'Stripe Publishable Key',
                'group'  => 'payment',
                'details' => '',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'stripe_webhook_secret_key',
                'value'  => null,
                'type'   => 'text',
                'display_name'  => 'Stripe Webhook Secret Key',
                'group'  => 'payment',
                'details' => '',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'stripe_status',
                'value'  => 'inactive',
                'type'   => 'toggle',
                'display_name'  => 'Stripe Status',
                'group'  => 'payment',
                'details' => 'active, inactive',
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'youtube',
                'value'  => '',
                'type'   => 'text',
                'display_name'  => 'YouTube',
                'group'  => 'social media',
                'details' => null,
                'status' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'instagram',
                'value'  => '',
                'type'   => 'text',
                'display_name'  => 'Instagram',
                'group'  => 'social media',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'linkedin',
                'value'  => '',
                'type'   => 'text',
                'display_name'  => 'LinkedIn',
                'group'  => 'social media',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'facebook',
                'value'  => '',
                'type'   => 'text',
                'display_name'  => 'Facebook',
                'group'  => 'social media',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

            [
                'key'    => 'max_skip_day',
                'value'  => '',
                'type'   => 'number',
                'display_name'  => 'Maximum Skip Day',
                'group'  => 'quote',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],

        ];

        Setting::insert($settings);
    }
}
