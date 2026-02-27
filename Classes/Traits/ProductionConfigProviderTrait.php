<?php

declare(strict_types = 1);

namespace Maispace\Environment\Traits;

use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

trait ProductionConfigProviderTrait
{
    public function useProductionPreset(): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);

        $be = (array)($typo3ConfVars['BE'] ?? []);
        $be['debug'] = false;
        $typo3ConfVars['BE'] = $be;

        $fe = (array)($typo3ConfVars['FE'] ?? []);
        $fe['debug'] = false;
        $typo3ConfVars['FE'] = $fe;

        $sys = (array)($typo3ConfVars['SYS'] ?? []);
        $sys['devIPmask'] = '';
        $sys['displayErrors'] = -1;
        $sys['belogErrorReporting'] = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
        $sys['exceptionalErrors'] = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
        $typo3ConfVars['SYS'] = $sys;

        $log = (array)($typo3ConfVars['LOG'] ?? []);
        $log['writerConfiguration'] = array_replace_recursive(
            [
                LogLevel::DEBUG => [
                    FileWriter::class => ['disabled' => true],
                ],
                LogLevel::INFO => [
                    FileWriter::class => ['disabled' => true],
                ],
                LogLevel::WARNING => [
                    FileWriter::class => ['disabled' => true],
                ],
                LogLevel::ERROR => [
                    FileWriter::class => ['disabled' => false],
                ],
            ],
            (array)($log['writerConfiguration'] ?? [])
        );
        $typo3ConfVars['LOG'] = $log;

        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        $this->disableDeprecationLogging();

        return $this;
    }
}
