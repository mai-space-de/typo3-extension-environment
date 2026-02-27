<?php

declare(strict_types = 1);

namespace Maispace\Environment\Traits;

use TYPO3\CMS\Core\Cache\Backend\RedisBackend;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;
use TYPO3\CMS\Core\Log\Writer\NullWriter;
use TYPO3\CMS\Core\Utility\ArrayUtility;

trait DefaultConfigProviderTrait
{
    public function includeContextDependentConfigurationFiles(): self
    {
        $orderedListOfContextNames = [];
        $currentContext = $this->context;
        do {
            $orderedListOfContextNames[] = (string)$currentContext;
        } while (($currentContext = $currentContext->getParent()));

        $orderedListOfContextNames = array_reverse($orderedListOfContextNames);
        foreach ($orderedListOfContextNames as $orderedListOfContextName) {
            $contextConfigFilePath = $this->configPath . '/system/' . strtolower($orderedListOfContextName) . '.php';
            if (file_exists($contextConfigFilePath)) {
                require $contextConfigFilePath;
            }
        }

        return $this;
    }

    public function appendContextToSiteName(bool $contextFirst = true, string $delimiter = ' ', bool $showInProduction = true): self
    {
        if (false === $showInProduction && $this->context->isProduction()) {
            return $this;
        }

        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $sys = (array)($typo3ConfVars['SYS'] ?? []);
        $siteName = $sys['sitename'] ?? '';
        $siteName = is_scalar($siteName) ? (string)$siteName : '';

        $context = (string)$this->context;
        $contextString = '[' . $context . ']';
        $contextPosition = $contextFirst ? 0 : 1;
        $sys['sitename'] = 0 === $contextPosition ? $contextString . $delimiter . $siteName : $siteName . $delimiter . $contextString;
        $typo3ConfVars['SYS'] = $sys;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    /**
     * @param array<string, mixed>|null $options
     */
    public function initializeDatabaseConnection(?array $options = null, string $connectionName = 'Default'): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $db = (array)($typo3ConfVars['DB'] ?? []);
        $connections = (array)($db['Connections'] ?? []);
        $connections[$connectionName] = array_replace_recursive(
            (array)($connections[$connectionName] ?? []),
            $options ?? []
        );

        if (empty($connections[$connectionName]['driver'])) {
            $connections[$connectionName]['driver'] = 'mysqli';
        }

        $db['Connections'] = $connections;
        $typo3ConfVars['DB'] = $db;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function useImageMagick(string $path = '/usr/bin/'): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $gfx = (array)($typo3ConfVars['GFX'] ?? []);
        $gfx['processor'] = 'ImageMagick';
        $gfx['processor_path'] = $path;
        $gfx['processor_path_lzw'] = $path;
        $typo3ConfVars['GFX'] = $gfx;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function useGraphicsMagick(string $path = '/usr/bin/'): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $gfx = (array)($typo3ConfVars['GFX'] ?? []);
        $gfx['processor'] = 'GraphicsMagick';
        $gfx['processor_path'] = $path;
        $gfx['processor_path_lzw'] = $path;
        $typo3ConfVars['GFX'] = $gfx;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function useMailpit(string $host = 'localhost', ?int $port = null): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $mail = (array)($typo3ConfVars['MAIL'] ?? []);
        $mail['transport'] = 'smtp';
        $mail['transport_smtp_encrypt'] = '';
        $mail['transport_smtp_password'] = '';
        $mail['transport_smtp_server'] = $host . ($port ? ':' . $port : '');
        $mail['transport_smtp_username'] = '';
        $typo3ConfVars['MAIL'] = $mail;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function useMailhog(string $host = 'localhost', ?int $port = null): self
    {
        return $this->useMailpit($host, $port);
    }

    public function allowNoCacheQueryParameter(): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $fe['disableNoCacheParameter'] = false;
        $typo3ConfVars['FE'] = $fe;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function forbidNoCacheQueryParameter(): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $fe['disableNoCacheParameter'] = true;
        $typo3ConfVars['FE'] = $fe;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function allowInvalidCacheHashQueryParameter(): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $fe['pageNotFoundOnCHashError'] = false;
        $typo3ConfVars['FE'] = $fe;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function forbidInvalidCacheHashQueryParameter(): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $fe['pageNotFoundOnCHashError'] = true;
        $typo3ConfVars['FE'] = $fe;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function excludeQueryParameterForCacheHashCalculation(string $queryParameter): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $cacheHash = (array)($fe['cacheHash'] ?? []);
        $excludedParameters = (array)($cacheHash['excludedParameters'] ?? []);
        $excludedParameters[] = $queryParameter;
        $cacheHash['excludedParameters'] = $excludedParameters;
        $fe['cacheHash'] = $cacheHash;
        $typo3ConfVars['FE'] = $fe;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    /**
     * @param array<int, string> $queryParameters
     */
    public function excludeQueryParametersForCacheHashCalculation(array $queryParameters): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $cacheHash = (array)($fe['cacheHash'] ?? []);
        $excludedParameters = (array)($cacheHash['excludedParameters'] ?? []);
        $cacheHash['excludedParameters'] = array_merge(
            $excludedParameters,
            $queryParameters
        );
        $fe['cacheHash'] = $cacheHash;
        $typo3ConfVars['FE'] = $fe;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function enableDeprecationLogging(): self
    {
        $path = 'TYPO3/CMS/deprecations/writerConfiguration/' . LogLevel::NOTICE . '/' . FileWriter::class . '/disabled';
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $log = (array)($typo3ConfVars['LOG'] ?? []);
        $log = ArrayUtility::setValueByPath($log, $path, false, '/');
        $typo3ConfVars['LOG'] = $log;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function disableDeprecationLogging(): self
    {
        $path = 'TYPO3/CMS/deprecations/writerConfiguration/' . LogLevel::NOTICE . '/' . FileWriter::class . '/disabled';
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $log = (array)($typo3ConfVars['LOG'] ?? []);
        $log = ArrayUtility::setValueByPath($log, $path, true, '/');
        $typo3ConfVars['LOG'] = $log;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function configureExceptionHandlers(string $productionExceptionHandlerClassName, string $debugExceptionHandlerClassName): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $sys = (array)($typo3ConfVars['SYS'] ?? []);
        $sys['productionExceptionHandler'] = $productionExceptionHandlerClassName;
        $sys['debugExceptionHandler'] = $debugExceptionHandlerClassName;
        $typo3ConfVars['SYS'] = $sys;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function autoconfigureSolrLogging(string $fileName = 'solr.log', ?string $forceLogLevel = null): self
    {
        if (null !== $forceLogLevel) {
            $logLevel = $forceLogLevel;
        } else {
            $logLevel = $this->context->isProduction() ? LogLevel::ERROR : LogLevel::DEBUG;
        }

        return $this->addFileLogger('ApacheSolrForTypo3\\Solr', $fileName, $logLevel);
    }

    public function addFileLogger(string $namespace, ?string $fileName = null, ?string $logLevel = null): self
    {
        $fileName ??= strtolower(str_replace('\\', '_', $namespace)) . '.log';
        if (null === $logLevel) {
            $logLevel = $this->context->isProduction() ? LogLevel::ERROR : LogLevel::DEBUG;
        }

        $logFile = $this->varPath . '/log/' . $fileName;
        $value = [
            'writerConfiguration' => [
                $logLevel => [
                    FileWriter::class => [
                        'logFile' => $logFile,
                    ],
                ],
            ],
        ];
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $log = (array)($typo3ConfVars['LOG'] ?? []);
        $log = ArrayUtility::setValueByPath($log, $namespace, $value, '\\');
        $typo3ConfVars['LOG'] = $log;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    public function setNullLogger(string $namespace, string $logLevel = LogLevel::DEBUG): self
    {
        $value = [
            'writerConfiguration' => [
                $logLevel => [
                    NullWriter::class => [],
                ],
            ],
        ];
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $log = (array)($typo3ConfVars['LOG'] ?? []);
        $log = ArrayUtility::setValueByPath($log, $namespace, $value, '\\');
        $typo3ConfVars['LOG'] = $log;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    /**
     * @param array<string, int>|null $caches
     * @param array<string, int>      $additionalCaches
     */
    public function initializeRedisCaching(
        ?array $caches = null,
        string $redisHost = '127.0.0.1',
        int $redisStartDb = 0,
        int $redisPort = 6379,
        ?string $alternativeCacheBackend = null,
        array $additionalCaches = []
    ): self {
        $isVersion12OrHigher = $this->version->getMajorVersion() >= 12;
        $cacheBackend = $alternativeCacheBackend ?? RedisBackend::class;
        $redisDb = $redisStartDb;
        $caches = array_merge(
            $caches ?? [
                'pages'       => 86400 * 30,
                'pagesection' => 86400 * 30,
                'hash'        => 86400 * 30,
                'rootline'    => 86400 * 30,
                'extbase'     => 0,
            ],
            $additionalCaches
        );

        if ($isVersion12OrHigher) {
            unset($caches['pagesection'], $caches['cache_pagesection']);
        }

        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $sys = (array)($typo3ConfVars['SYS'] ?? []);
        $caching = (array)($sys['caching'] ?? []);
        $cacheConfigurations = (array)($caching['cacheConfigurations'] ?? []);

        foreach ($caches as $key => $lifetime) {
            $cacheConfig = (array)($cacheConfigurations[$key] ?? []);
            $cacheConfig['backend'] = $cacheBackend;
            $cacheConfig['options'] = [
                'database'        => $redisDb++,
                'hostname'        => $redisHost,
                'port'            => $redisPort,
                'defaultLifetime' => $lifetime,
            ];
            $cacheConfigurations[$key] = $cacheConfig;
        }

        $caching['cacheConfigurations'] = $cacheConfigurations;
        $sys['caching'] = $caching;
        $typo3ConfVars['SYS'] = $sys;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    /**
     * @param array<int, string>|null $applyForCaches
     */
    public function setAlternativeCachePath(string $path, ?array $applyForCaches = null): self
    {
        $applyForCaches ??= [
            'cache_core',
            'fluid_template',
            'assets',
            'l10n',
        ];

        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $sys = (array)($typo3ConfVars['SYS'] ?? []);
        $caching = (array)($sys['caching'] ?? []);
        $cacheConfigurations = (array)($caching['cacheConfigurations'] ?? []);

        foreach ($applyForCaches as $applyForCache) {
            $cacheConfig = (array)($cacheConfigurations[$applyForCache] ?? []);
            $options = (array)($cacheConfig['options'] ?? []);
            $options['cacheDirectory'] = $path;
            $cacheConfig['options'] = $options;
            $cacheConfigurations[$applyForCache] = $cacheConfig;
        }

        $caching['cacheConfigurations'] = $cacheConfigurations;
        $sys['caching'] = $caching;
        $typo3ConfVars['SYS'] = $sys;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function setPhpSettings(array $settings): self
    {
        foreach ($settings as $key => $value) {
            try {
                if (function_exists('ini_set') && !ini_get($key)) {
                    $stringValue = is_scalar($value) ? (string)$value : '';
                    ini_set($key, $stringValue);
                } else {
                    error_log(sprintf('Unable to set PHP setting %s, ini_set is disabled or already set.', $key));
                }
            } catch (\ErrorException $e) {
                error_log(sprintf('Error setting PHP configuration for %s: ', $key) . $e->getMessage());
            }
        }

        return $this;
    }

    /**
     * @param array<string, mixed> $keyValuePairs
     */
    public function setConfigPathValues(string $configPath, array $keyValuePairs): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);

        $currentValue = ArrayUtility::getValueByPath($typo3ConfVars, $configPath, '/');
        $mergedValue = array_replace_recursive((array)($currentValue ?? []), $keyValuePairs);
        $typo3ConfVars = ArrayUtility::setValueByPath($typo3ConfVars, $configPath, $mergedValue, '/');

        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        return $this;
    }
}
