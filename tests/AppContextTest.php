<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use GhibliQL\AppContext;

final class AppContextTest extends TestCase
{
    protected static $appContext;

    public static function setUpBeforeClass()
    {
        self::$appContext = new AppContext();
        self::$appContext->viewer = '';
        self::$appContext->rootUrl = 'https://localhost:8080';
        self::$appContext->request = '';
    }

    public function testRootUrl()
    {
        $this->assertSame('https://localhost:8080', self::$appContext->rootUrl);
    }

     public function testViewer()
    {
        $this->assertSame('', self::$appContext->viewer);
    }

    public function testRequest()
    {
        $this->assertSame('', self::$appContext->request);
    }
}
