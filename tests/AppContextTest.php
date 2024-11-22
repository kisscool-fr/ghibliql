<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GhibliQL\AppContext;

final class AppContextTest extends TestCase
{
    protected static $appContext;

    public static function setUpBeforeClass(): void
    {
        self::$appContext = new AppContext();
        self::$appContext->viewer = '';
        self::$appContext->rootUrl = 'https://localhost:8080';
        self::$appContext->request = $_REQUEST;
    }

    public function testRootUrl(): void
    {
        $this->assertSame('https://localhost:8080', self::$appContext->rootUrl);
    }

    public function testViewer(): void
    {
        $this->assertSame('', self::$appContext->viewer);
    }

    public function testRequest(): void
    {
        $this->assertIsArray(self::$appContext->request);
    }
}
