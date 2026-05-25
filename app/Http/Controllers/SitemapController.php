<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use App\Models\Course;
use App\Models\BlogPost;

class SitemapController extends Controller
{
    public function index()
    {
        // 1. Define Static Routes
        // These match the route names or paths defined in your web.php
        $staticRoutes = [
            'welcome',
            'features',
            'about',
            'pricing',
            'learning-paths',
            'ai-tutor',
            'help',
            'contact',
            'faq',
            'terms',
            'privacy',
            'teams',
            'cookies',
            'accessibility',
            'gdpr',
            'waeclanding',
            'jamblanding',
            'specializations.index',
            'courses.index',
            'enterprise',
            'blog.index',
            'community.index',
        ];

        $urls = [];

        // Process Static Routes
        foreach ($staticRoutes as $routeName) {
            // Check if route exists to prevent errors
            if (Route::has($routeName)) {
                $urls[] = [
                    'loc' => route($routeName),
                    'lastmod' => Carbon::now()->startOfMonth()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => $routeName === 'welcome' ? '1.0' : '0.8',
                ];
            }
        }

        // 2. Process Dynamic Routes (Courses)
        // Uncomment and adjust the Model class below based on your actual App structure

        $courses = \App\Models\Course::where('is_public', true)->get();
        foreach ($courses as $course) {
            $urls[] = [
                'loc' => route('courses.show', ['id' => $course->id, 'slug' => $course->slug]),
                'lastmod' => $course->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }


        // 3. Process Dynamic Routes (Blog Posts)
        // Uncomment and adjust the Model class below

        $posts = \App\Models\BlogPost::where('is_published', true)->get();
        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('blog.show', ['slug' => $post->slug]),
                'lastmod' => $post->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }


        // 4. Generate XML
        $xml = $this->generateXml($urls);

        // 5. Return Response with correct Content-Type
        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Helper to construct the XML structure
     */
    private function generateXml($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . $url['loc'] . '</loc>';
            $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
