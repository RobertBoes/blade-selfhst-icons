<?php

declare(strict_types=1);

namespace Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use BladeUI\SelfhstIcons\BladeSelfhstIconsServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CompilesIconsTest extends TestCase
{
    #[Test]
    public function it_compiles_a_single_anonymous_component()
    {
        $result = svg('selfhst-1panel')->toHtml();

        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('</svg>', $result);
    }

    #[Test]
    public function it_can_add_classes_to_icons()
    {
        $result = svg('selfhst-1panel', 'w-6 h-6 text-gray-500')->toHtml();

        $this->assertStringContainsString('class="w-6 h-6 text-gray-500"', $result);
    }

    #[Test]
    public function it_can_add_styles_to_icons()
    {
        $result = svg('selfhst-1panel', ['style' => 'color: #555'])->toHtml();

        $this->assertStringContainsString('style="color: #555"', $result);
    }

    protected function getPackageProviders($app)
    {
        return [
            BladeIconsServiceProvider::class,
            BladeSelfhstIconsServiceProvider::class,
        ];
    }
}
