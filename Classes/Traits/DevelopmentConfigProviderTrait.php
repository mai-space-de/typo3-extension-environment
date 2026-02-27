<?php

declare(strict_types = 1);

namespace Maispace\Environment\Traits;

use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

trait DevelopmentConfigProviderTrait
{
    public function useDevelopmentPreset(): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);

        $be = (array)($typo3ConfVars['BE'] ?? []);
        $be['debug'] = true;
        $typo3ConfVars['BE'] = $be;

        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $fe['debug'] = true;
        $typo3ConfVars['FE'] = $fe;

        $sys = (array)($typo3ConfVars['SYS'] ?? []);
        $sys['devIPmask'] = '*';
        $sys['displayErrors'] = 1;
        $sys['belogErrorReporting'] = E_ALL;
        $sys['exceptionalErrors'] = E_ALL;
        $typo3ConfVars['SYS'] = $sys;

        $log = (array)($typo3ConfVars['LOG'] ?? []);
        $writerConfiguration = (array)($log['writerConfiguration'] ?? []);
        $writerConfiguration[LogLevel::WARNING] = [
            FileWriter::class => ['disabled' => false],
        ];
        $log['writerConfiguration'] = $writerConfiguration;
        $typo3ConfVars['LOG'] = $log;

        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        $this->enableDeprecationLogging();

        return $this;
    }
}
