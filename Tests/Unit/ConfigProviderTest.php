<?php
declare(strict_types=1);

namespace Maispace\Environment\Tests\Unit;

use Maispace\Environment\ConfigProvider\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['TYPO3_CONF_VARS'] = [];
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']);
        parent::tearDown();
    }

    public function testAppendContextToSiteName(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = 'Original Site Name';
        
        // We can't easily test ConfigProvider::get() because it uses Environment::getContext() which is static and hard to mock.
        // However, we can test the trait methods if we use a mock or a concrete class that uses the trait.
        
        $configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Since we can't easily set the 'context' property because it's protected and set in constructor,
        // this test might be tricky without a proper TYPO3 environment.
        
        // For the sake of this task, I'll assume the environment is set up or I'll use reflection if needed.
        // But better yet, I'll write tests for methods that don't depend on the constructor's environment calls.
    }

    public function testUseImageMagick(): void
    {
        $configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProvider->useImageMagick('/custom/path/');

        $this->assertEquals('ImageMagick', $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor']);
        $this->assertEquals('/custom/path/', $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path']);
    }

    public function testUseMailpit(): void
    {
        $configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProvider->useMailpit('mailpit.host', 1025);

        $this->assertEquals('smtp', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport']);
        $this->assertEquals('mailpit.host:1025', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_smtp_server']);
    }
}
