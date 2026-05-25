<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1,  'name' => 'HR & Recruitment',                      'slug' => 'hr-recruitment'],
            ['id' => 2,  'name' => 'Learning & Development (L&D)',           'slug' => 'learning-development-ld'],
            ['id' => 3,  'name' => 'Education & Training Centers',           'slug' => 'education-training-centers'],
            ['id' => 4,  'name' => 'ExamPortal How-Tos & Tutorials',         'slug' => 'examportal-how-tos-tutorials'],
            ['id' => 5,  'name' => 'Product News & Updates',                 'slug' => 'product-news-updates'],
            ['id' => 6,  'name' => 'The Future of Assessment',               'slug' => 'the-future-of-assessment'],
            ['id' => 7,  'name' => 'Tips for Effective Testing',             'slug' => 'tips-for-effective-testing'],
            ['id' => 8,  'name' => 'Nigerian Business & Education Spotlight','slug' => 'nigerian-business-education-spotlight'],
            ['id' => 9,  'name' => 'Industry Insights',                      'slug' => 'industry-insights'],
            ['id' => 10, 'name' => 'Case Studies',                           'slug' => 'case-studies'],
            ['id' => 11, 'name' => 'EdTech Trends',                          'slug' => 'edtech-trends'],
            ['id' => 12, 'name' => 'Remote Proctoring',                      'slug' => 'remote-proctoring'],
            ['id' => 13, 'name' => 'Candidate Experience',                   'slug' => 'candidate-experience'],
            ['id' => 14, 'name' => 'Employee Upskilling',                    'slug' => 'employee-upskilling'],
            ['id' => 15, 'name' => 'Academic Integrity',                     'slug' => 'academic-integrity'],
            ['id' => 16, 'name' => 'FAQ',                                    'slug' => 'faq'],
        ];

        DB::table('categories')->upsert($categories, ['id'], ['name', 'slug']);
    }
}
