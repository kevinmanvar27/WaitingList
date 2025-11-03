<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use App\Models\User;
use App\Models\Page;

class PageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_view_active_pages()
    {
        $page = Page::create([
            'title' => 'Public Page',
            'slug' => 'public-page',
            'content' => 'This is public content.',
            'is_active' => true,
        ]);

        $response = $this->get('/pages/public-page');

        $response->assertStatus(200);
        $response->assertSee('Public Page');
        $response->assertSee('This is public content.');
    }

    /** @test */
    public function users_cannot_view_inactive_pages()
    {
        $page = Page::create([
            'title' => 'Inactive Page',
            'slug' => 'inactive-page',
            'content' => 'This is inactive content.',
            'is_active' => false,
        ]);

        $response = $this->get('/pages/inactive-page');

        $response->assertNotFound();
    }

    /** @test */
    public function page_content_is_rendered_correctly()
    {
        $page = Page::create([
            'title' => 'HTML Content Page',
            'slug' => 'html-content',
            'content' => '<h1>Welcome</h1><p>This is <strong>HTML</strong> content.</p>',
            'is_active' => true,
        ]);

        $response = $this->get('/pages/html-content');

        $response->assertStatus(200);
        $response->assertSee('Welcome');
        $response->assertSee('HTML Content Page'); // Check for the title
    }
}