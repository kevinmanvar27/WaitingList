<?php
require_once 'vendor/autoload.php';

use App\Models\Page;

// Create a new Laravel application instance
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the Page model
$page = Page::where('slug', 'test-page')->first();

if ($page) {
    echo "Page found: " . $page->title . "\n";
    echo "Content: " . $page->content . "\n";
} else {
    echo "Page not found\n";
}