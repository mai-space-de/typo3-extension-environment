<?php

declare(strict_types = 1);

namespace Maispace\MaiEnvironment\ConfigProvider;

use Maispace\MaiEnvironment\Traits\ConfigProviderTraitInterface;
use Maispace\MaiEnvironment\Traits\DdevConfigProviderTrait;
use Maispace\MaiEnvironment\Traits\DefaultConfigProviderTrait;
use Maispace\MaiEnvironment\Traits\DevelopmentConfigProviderTrait;
use Maispace\MaiEnvironment\Traits\ProductionConfigProviderTrait;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;

class ConfigProvider implements ConfigProviderInterface, ConfigProviderTraitInterface
{
    use DefaultConfigProviderTrait;
    use DdevConfigProviderTrait;
    use DevelopmentConfigProviderTrait;
    use ProductionConfigProviderTrait;

    protected ApplicationContext $context;
    protected Typo3Version $version;
    protected string $configPath;
    protected string $varPath;
    private bool $ddevEnvironment = false;
    private static ?self $instance = null;

    protected function __construct()
    {
        $this->context = Environment::getContext();
        $this->version = new Typo3Version();
        $this->configPath = Environment::getConfigPath();
        $this->varPath = Environment::getVarPath();
        $this->ddevEnvironment = 'true' === getenv('IS_DDEV_PROJECT');
    }

    public static function initialize(bool $applyDefaults = true): self
    {
        self::$instance = new self();
        if ($applyDefaults) {
            self::$instance->applyDefaults();
        }

        return self::$instance;
    }

    public static function get(): self
    {
        if (!self::$instance instanceof self) {
            return self::initialize();
        }

        return self::$instance;
    }

    public function applyDefaults(): self
    {
        $this
            ->appendContextToSiteName()
            ->forbidInvalidCacheHashQueryParameter()
            ->forbidNoCacheQueryParameter();

        if ($this->context->isDevelopment() || $this->context->isTesting()) {
            $this->useDevelopmentPreset();
            if ($this->ddevEnvironment) {
                $this->useDDEVConfiguration();
            }
        } elseif ($this->context->isProduction()) {
            $this->useProductionPreset();
        }

        $this->includeContextDependentConfigurationFiles();

        return $this;
    }
}
