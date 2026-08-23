<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('legal legacy URLs permanently redirect to their canonical routes', function (string $legacy, string $canonical) {
    $this->get($legacy)
        ->assertMovedPermanently()
        ->assertRedirect($canonical);
})->with([
    ['/pages/about', '/about'],
    ['/pages/privacy', '/privacy'],
    ['/pages/terms', '/terms'],
]);

test('public sitemap lists only canonical public URLs', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.route('landing').'</loc>', false)
        ->assertSee('<loc>'.route('about').'</loc>', false)
        ->assertSee('<loc>'.route('privacy').'</loc>', false)
        ->assertSee('<loc>'.route('terms').'</loc>', false)
        ->assertDontSee('/pages/');
});

test('offline shell requires authentication and sends noindex header', function () {
    $this->get('/app/pos')->assertRedirect('/login');

    $this->actingAs(User::factory()->create())
        ->get('/app/pos')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('admin aliases preserve authentication and redirect authorized users permanently', function () {
    $this->get('/admin/pos')->assertRedirect('/login');

    $this->actingAs(User::factory()->create())
        ->get('/admin/pos')
        ->assertMovedPermanently()
        ->assertRedirect('/pos');
});

test('robots declares the public sitemap and excludes private route prefixes', function () {
    expect(file_get_contents(public_path('robots.txt')))
        ->toContain('Disallow: /app/')
        ->toContain('Disallow: /pos/offline')
        ->toContain('Disallow: /admin/')
        ->toContain('Disallow: /offline-sync/')
        ->toContain('Disallow: /api/')
        ->toContain('Sitemap: /sitemap.xml');
});
