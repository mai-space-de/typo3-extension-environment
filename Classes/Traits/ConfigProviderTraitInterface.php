<?php

declare(strict_types = 1);

namespace Maispace\Environment\Traits;

use TYPO3\CMS\Core\Log\LogLevel;

interface ConfigProviderTraitInterface
{
    public function includeContextDependentConfigurationFiles(): self;

    public function appendContextToSiteName(): self;

    /**
     * @param array<string, mixed>|null $options
     */
    public function initializeDatabaseConnection(?array $options = null, string $connectionName = 'Default'): self;

    public function useProductionPreset(): self;

    public function useDevelopmentPreset(): self;

    public function useImageMagick(string $path = '/usr/bin/'): self;

    public function useGraphicsMagick(string $path = '/usr/bin/'): self;

    public function useMailpit(string $host = 'localhost', ?int $port = null): self;

    public function useMailhog(string $host = 'localhost', ?int $port = null): self;

    public function allowNoCacheQueryParameter(): self;

    public function forbidNoCacheQueryParameter(): self;

    public function allowInvalidCacheHashQueryParameter(): self;

    public function forbidInvalidCacheHashQueryParameter(): self;

    public function excludeQueryParameterForCacheHashCalculation(string $queryParameter): self;

    /**
     * @param array<int, string> $queryParameters
     */
    public function excludeQueryParametersForCacheHashCalculation(array $queryParameters): self;

    public function enableDeprecationLogging(): self;

    public function disableDeprecationLogging(): self;

    public function configureExceptionHandlers(string $productionExceptionHandlerClassName, string $debugExceptionHandlerClassName): self;

    public function autoconfigureSolrLogging(string $fileName = 'solr.log', ?string $forceLogLevel = null): self;

    public function addFileLogger(string $namespace, ?string $fileName = null, ?string $logLevel = null): self;

    public function setNullLogger(string $namespace, string $logLevel = LogLevel::DEBUG): self;

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
    ): self;

    /**
     * @param array<int, string>|null $applyForCaches
     */
    public function setAlternativeCachePath(string $path, ?array $applyForCaches = null): self;

    /**
     * @param array<string, mixed> $settings
     */
    public function setPhpSettings(array $settings): self;

    /**
     * @param array<string, mixed> $keyValuePairs
     */
    public function setConfigPathValues(string $configPath, array $keyValuePairs): self;

    public function useDDEVConfiguration(?string $dbHost = null): self;
}
