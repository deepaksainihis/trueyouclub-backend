<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $updateDate = $createDate = date('Y-m-d H:i:s');
        $permissions = [
            [
                'title'      => 'permission_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'permission_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'permission_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'permission_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'permission_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'role_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'role_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'role_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'role_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'role_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'user_management_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'user_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'user_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'user_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'user_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'user_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'setting_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'transaction_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],


            [
                'title'      => 'webinar_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'webinar_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'webinar_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'webinar_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'webinar_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'blog_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'blog_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'blog_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'blog_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'blog_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'news_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'news_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'news_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'news_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'news_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'quote_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'quote_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'quote_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'quote_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'quote_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],


            [
                'title'      => 'seminar_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'seminar_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'seminar_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'seminar_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'seminar_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'health_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'health_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'health_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'health_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'health_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'contact_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'contact_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            // [
            //     'title'      => 'contact_edit',
            //     'created_at' => $createDate,
            //     'updated_at' => $updateDate,
            // ],
            [
                'title'      => 'contact_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'contact_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'page_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'page_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'page_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'page_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'page_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'volunteer_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'volunteer_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'volunteer_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'volunteer_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'volunteer_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'heroes_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'heroes_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'heroes_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'heroes_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'heroes_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'category_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'category_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'category_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'category_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'category_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'education_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'education_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'education_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'education_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'education_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'event_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'event_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'event_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'event_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'event_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'event_request_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'event_invite_volunteer_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],


            [
                'title'      => 'mis_report_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],

            [
                'title'      => 'location_access',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'location_create',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'location_edit',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'location_show',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'title'      => 'location_delete',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
        ];

        Permission::insert($permissions);
    }
}
