<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crée les permissions
    Permission::create(['name' => 'voir dashboard']);
    Permission::create(['name' => 'gérer utilisateurs']);

    // Crée un rôle Admin avec toutes les permissions
    $admin = Role::create(['name' => 'admin']);
    $admin->givePermissionTo(Permission::all());

    // Optionnel : créer un rôle utilisateur simple
    $user = Role::create(['name' => 'utilisateur']);
    $user->givePermissionTo(['voir dashboard']);
    }
}
