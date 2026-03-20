<?php

declare(strict_types = 1);

namespace Maispace\MaiEnvironment\Tests\Unit;

use Maispace\MaiEnvironment\ConfigProvider\ConfigProvider;
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

        $reflection = new \ReflectionClass(ConfigProvider::class);
        $configProvider = $reflection->newInstanceWithoutConstructor();

        $context = $this->getMockBuilder(\TYPO3\CMS\Core\Core\ApplicationContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('isProduction')->willReturn(false);
        $context->method('__toString')->willReturn('Development');

        $contextProperty = $reflection->getProperty('context');
        $contextProperty->setAccessible(true);
        $contextProperty->setValue($configProvider, $context);

        $configProvider->appendContextToSiteName();

        $this->assertEquals('[Development] Original Site Name', $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']);
    }

    public function testUseImageMagick(): void
    {
        $reflection = new \ReflectionClass(ConfigProvider::class);
        $configProvider = $reflection->newInstanceWithoutConstructor();

        $configProvider->useImageMagick('/custom/path/');

        $this->assertEquals('ImageMagick', $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor']);
        $this->assertEquals('/custom/path/', $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path']);
    }

    public function testUseMailpit(): void
    {
        $reflection = new \ReflectionClass(ConfigProvider::class);
        $configProvider = $reflection->newInstanceWithoutConstructor();

        $configProvider->useMailpit('mailpit.host', 1025);

        $this->assertEquals('smtp', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport']);
        $this->assertEquals('mailpit.host:1025', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_smtp_server']);
    }
}
