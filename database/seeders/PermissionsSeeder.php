<?php
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
 
class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
 
        // ─── Définir toutes les permissions ───────────────────────
        $permissions = [
            // Dashboard
            'view_dashboard_full',      // KPIs, revenus, stats complètes
            'view_dashboard_today',     // Arrivées/départs du jour seulement
 
            // Réservations
            'view_reservations',
            'confirm_reservation',
            'checkin_reservation',
            'checkout_reservation',
            'cancel_reservation',
            'manage_reservations',      // Modifier, supprimer
 
            // Chambres
            'view_rooms',
            'create_room',
            'edit_room',
            'delete_room',
            'change_room_status',
 
            // Clients / Utilisateurs
            'view_clients',
            'manage_clients',           // Désactiver, changer rôle
            'create_user',              // Admin seulement
            'delete_user',              // Admin seulement
 
            // Rapports
            'view_reports',
 
            // Paramètres
            'manage_settings',          // Admin seulement
        ];
 
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }
 
        // ─── Admin — tout ─────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->syncPermissions($permissions);
 
        // ─── Manager ──────────────────────────────────────────────
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);
        $manager->syncPermissions([
            'view_dashboard_full',
            'view_dashboard_today',
            'view_reservations',
            'confirm_reservation',
            'checkin_reservation',
            'checkout_reservation',
            'cancel_reservation',
            'manage_reservations',
            'view_rooms',
            'create_room',
            'edit_room',
            'change_room_status',
            // pas delete_room
            'view_clients',
            // pas manage_clients, create_user, delete_user
            'view_reports',
        ]);
 
        // ─── Réceptionniste ───────────────────────────────────────
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'sanctum']);
        $receptionist->syncPermissions([
            'view_dashboard_today',
            'view_reservations',
            'confirm_reservation',
            'checkin_reservation',
            'checkout_reservation',
            'view_rooms',
            'change_room_status',
            // pas create_room, edit_room, delete_room
            // pas view_clients, manage_clients
            // pas view_reports
        ]);
 
        // ─── Client — aucune permission admin ─────────────────────
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'sanctum']);
        $client->syncPermissions([]);
 
        $this->command->info('✅ Permissions créées et assignées !');
    }
}