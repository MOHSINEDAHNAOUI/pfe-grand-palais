<?php
// database/seeders/RolesPermissionsSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_reservations', 'manage_reservations',
            'view_rooms', 'manage_rooms',
            'view_users', 'manage_users',
            'view_reports', 'manage_promotions',
            'checkin_checkout', 'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'admin' => $permissions,
            'manager' => ['view_reservations', 'manage_reservations', 'view_rooms', 'view_reports', 'checkin_checkout', 'manage_promotions'],
            'receptionist' => ['view_reservations', 'checkin_checkout', 'view_rooms'],
            'client' => [],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}