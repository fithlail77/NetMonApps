<?php

namespace Database\Seeders;

use App\Models\DeviceType;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage_devices',
            'view_devices',
            'view_devices_status',
            'manage_alerts',
            'view_alerts',
            'manage_topology',
            'view_topology',
            'view_metrics',
            'view_reports',
            'manage_users',
            'manage_settings',
            'full_access',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo($permissions);

        $networkEngineer = Role::create(['name' => 'network_engineer', 'guard_name' => 'web']);
        $networkEngineer->givePermissionTo([
            'manage_devices',
            'view_devices',
            'manage_alerts',
            'view_alerts',
            'manage_topology',
            'view_topology',
            'view_metrics',
        ]);

        $itManager = Role::create(['name' => 'it_manager', 'guard_name' => 'web']);
        $itManager->givePermissionTo([
            'view_devices',
            'view_alerts',
            'view_topology',
            'view_metrics',
            'view_reports',
        ]);

        $helpdesk = Role::create(['name' => 'helpdesk', 'guard_name' => 'web']);
        $helpdesk->givePermissionTo([
            'view_devices_status',
            'view_alerts',
        ]);

        // Create default admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@netmon.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ])->assignRole('admin');

        // Create sample users
        User::create([
            'name' => 'Network Engineer',
            'email' => 'engineer@netmon.local',
            'password' => bcrypt('password'),
            'role' => 'network_engineer',
            'is_active' => true,
        ])->assignRole('network_engineer');

        User::create([
            'name' => 'IT Manager',
            'email' => 'manager@netmon.local',
            'password' => bcrypt('password'),
            'role' => 'it_manager',
            'is_active' => true,
        ])->assignRole('it_manager');

        User::create([
            'name' => 'Helpdesk',
            'email' => 'helpdesk@netmon.local',
            'password' => bcrypt('password'),
            'role' => 'helpdesk',
            'is_active' => true,
        ])->assignRole('helpdesk');

        // Create device types
        $deviceTypes = [
            ['name' => 'Router', 'icon' => 'router', 'description' => 'Network router device'],
            ['name' => 'Switch', 'icon' => 'switch', 'description' => 'Network switch device'],
            ['name' => 'Firewall', 'icon' => 'firewall', 'description' => 'Firewall security device'],
            ['name' => 'Access Point', 'icon' => 'wifi', 'description' => 'Wireless access point'],
            ['name' => 'Server', 'icon' => 'server', 'description' => 'Server device'],
            ['name' => 'Other', 'icon' => 'device', 'description' => 'Other network device'],
        ];

        foreach ($deviceTypes as $type) {
            DeviceType::create($type);
        }

        // Create default settings
        Setting::setValue('monitoring_interval', '60', 'monitoring');
        Setting::setValue('ping_timeout', '5', 'monitoring');
        Setting::setValue('snmp_timeout', '10', 'monitoring');

        Setting::setValue('alert_email_enabled', '1', 'email');
        Setting::setValue('mail_host', 'smtp.gmail.com', 'email');
        Setting::setValue('mail_port', '587', 'email');
        Setting::setValue('mail_encryption', 'tls', 'email');
        Setting::setValue('mail_username', '', 'email');
        Setting::setValue('mail_password', '', 'email');
        Setting::setValue('mail_from_address', '', 'email');
        Setting::setValue('mail_from_name', 'NetMon Enterprise', 'email');

        Setting::setValue('alert_telegram_enabled', '0', 'telegram');
        Setting::setValue('telegram_bot_token', '', 'telegram');
        Setting::setValue('telegram_chat_id', '', 'telegram');
    }
}
