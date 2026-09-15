<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $all_permissions = Permission::all();

        $superAdminPermissions = $all_permissions->filter(function ($permission) {
            return substr($permission->title, 0, 11) != 'permission_' && substr($permission->title, 0, 5) != 'role_'  && substr($permission->title, 0, 11) != 'page_create' && substr($permission->title, 0, 11) != 'page_delete';
        });



        Role::findOrFail(1)->permissions()->sync($superAdminPermissions->pluck('id'));

        // $userPermissions = $all_permissions->filter(function ($permission) {
        //     return substr($permission->title, 0, 11) != 'permission_' && substr($permission->title, 0, 5) != 'role_'  && substr($permission->title, 0, 8) != 'setting_' && substr($permission->title, 0, 12) != 'transaction_' && substr($permission->title, 0, 5) != 'user_'  && substr($permission->title, 0, 8) != 'webinar_'  && substr($permission->title, 0, 5) != 'blog_'  && substr($permission->title, 0, 5) != 'news_'  && substr($permission->title, 0, 6) != 'quote_' && substr($permission->title, 0, 8) != 'seminar_' && substr($permission->title, 0, 7) != 'health_' && substr($permission->title, 0, 8) != 'contact_' && substr($permission->title, 0, 5) != 'page_';
        // });

        // Role::findOrFail(2)->permissions()->sync($userPermissions);
    }
}
