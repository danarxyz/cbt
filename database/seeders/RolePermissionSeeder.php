<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat permissions jika belum ada
        $permissions = [
            'view course',
            'create course',
            'edit course',
            'delete course'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Membuat role teacher jika belum ada, dan memberikan permission
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        
        $teacherRole->syncPermissions($permissions); // Menggunakan syncPermissions agar sinkron dengan array permissions di atas
        
        // Membuat role student jika belum ada, dan memberikan permission
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        
        $studentRole->syncPermissions(['view course']); // Memberikan hanya permission "view course" ke student

        // Membuat user super admin jika belum ada, dan memberikan role teacher
        $user = User::firstOrCreate(
            ['email' => 'danar@teacher.com'], // Cek berdasarkan email
            [
                'name' => 'Danar',
                'password' => bcrypt('123123123'),
            ]
        );

        $user->assignRole($teacherRole);
    }
}
