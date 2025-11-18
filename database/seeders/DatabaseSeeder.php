<?php
// database/seeders/DatabaseSeeder.php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $tutorRole = Role::create(['name' => 'tutor']);
        $studentRole = Role::create(['name' => 'student']);

        // Create permissions
        $permissions = [
            'manage_users',
            'manage_courses',
            'manage_content',
            'view_analytics',
            'grade_projects',
            'access_chat',
            'generate_content',
            'view_reports'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        $tutorRole->givePermissionTo(['manage_courses', 'manage_content', 'grade_projects', 'view_reports']);
        $studentRole->givePermissionTo(['access_chat']);

        // Seed exam boards
        \App\Models\ExamBoard::create([
            'name' => 'WAEC',
            'code' => 'waec',
            'country' => 'Nigeria',
            'description' => 'West African Examinations Council'
        ]);

        \App\Models\ExamBoard::create([
            'name' => 'NECO',
            'code' => 'neco',
            'country' => 'Nigeria',
            'description' => 'National Examinations Council'
        ]);

        \App\Models\ExamBoard::create([
            'name' => 'CFA',
            'code' => 'cfa',
            'country' => 'International',
            'description' => 'Chartered Financial Analyst'
        ]);

        // Seed AI providers
        \App\Models\AiProvider::create([
            'name' => 'OpenAI',
            'code' => 'openai',
            'available_models' => json_encode(['gpt-4', 'gpt-4-turbo', 'gpt-3.5-turbo']),
            'is_default' => true
        ]);

        \App\Models\AiProvider::create([
            'name' => 'DeepSeek',
            'code' => 'deepseek',
            'available_models' => json_encode(['deepseek-chat', 'deepseek-coder']),
            'is_default' => false
        ]);
    }
}
