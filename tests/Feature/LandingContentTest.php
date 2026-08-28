<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Support\LandingContent;
use PHPUnit\Framework\Attributes\Test;

final class LandingContentTest extends \Tests\TestCase
{
    #[Test]
    public function features_returns_six_items_with_required_keys(): void
    {
        $features = LandingContent::features();

        $this->assertCount(6, $features);
        foreach ($features as $f) {
            $this->assertArrayHasKey('icon', $f);
            $this->assertArrayHasKey('title', $f);
            $this->assertArrayHasKey('desc', $f);
            $this->assertArrayHasKey('accent', $f);
            $this->assertContains($f['accent'], ['teal', 'violet', 'cyan']);
        }
    }

    #[Test]
    public function how_it_works_returns_three_numbered_steps(): void
    {
        $steps = LandingContent::howItWorks();

        $this->assertCount(3, $steps);
        $this->assertSame('01', $steps[0]['number']);
        $this->assertSame('02', $steps[1]['number']);
        $this->assertSame('03', $steps[2]['number']);
    }

    #[Test]
    public function pricing_packages_returns_three_tiers_with_starter_active(): void
    {
        $packages = LandingContent::pricingPackages();

        $this->assertCount(3, $packages);
        $this->assertSame('Kasiva Starter', $packages[0]['name']);
        $this->assertFalse($packages[0]['comingSoon']);
        $this->assertTrue($packages[1]['comingSoon']);
        $this->assertTrue($packages[2]['comingSoon']);
    }

    #[Test]
    public function faqs_returns_at_least_ten_items_with_unique_slugs(): void
    {
        $faqs = LandingContent::faqs();

        $this->assertGreaterThanOrEqual(10, count($faqs));

        $slugs = array_column($faqs, 'slug');
        $this->assertSame($slugs, array_values(array_unique($slugs)), 'FAQ slugs must be unique');

        foreach ($faqs as $f) {
            $this->assertNotEmpty($f['q']);
            $this->assertNotEmpty($f['a']);
            $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $f['slug']);
        }
    }

    #[Test]
    public function testimonials_marks_all_as_coming_soon(): void
    {
        foreach (LandingContent::testimonials() as $t) {
            $this->assertTrue($t['comingSoon']);
            $this->assertNotEmpty($t['initials']);
        }
    }

    #[Test]
    public function platforms_lists_web_as_available_and_others_as_upcoming(): void
    {
        $platforms = LandingContent::platforms();
        $this->assertGreaterThanOrEqual(4, count($platforms));

        $byName = array_column($platforms, 'status', 'name');
        $this->assertSame('Tersedia', $byName['Web PWA']);
        $this->assertSame('Segera', $byName['Android']);
        $this->assertSame('Segera', $byName['iOS']);
        $this->assertSame('Segera', $byName['Desktop']);
    }

    #[Test]
    public function metrics_returns_non_empty_quartet(): void
    {
        $metrics = LandingContent::metrics();
        $this->assertCount(4, $metrics);
        foreach ($metrics as $m) {
            $this->assertNotEmpty($m['value']);
            $this->assertNotEmpty($m['label']);
        }
    }
}
