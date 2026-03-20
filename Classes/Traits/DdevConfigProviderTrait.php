<?php

declare(strict_types = 1);

namespace Maispace\MaiEnvironment\Traits;

trait DdevConfigProviderTrait
{
    public function useDDEVConfiguration(?string $dbHost = null): self
    {
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $sys = (array)($typo3ConfVars['SYS'] ?? []);
        $sys['trustedHostsPattern'] = '.*.*';
        $typo3ConfVars['SYS'] = $sys;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

        $this
            ->initializeDatabaseConnection(
                [
                    'charset'  => 'utf8mb4',
                    'dbname'   => 'db',
                    'driver'   => 'mysqli',
                    'host'     => 'db',
                    'password' => 'db',
                    'port'     => 3306,
                    'user'     => 'db',
                ]
            )
            ->useImageMagick();

        $mailhogSmtpBindAddr = getenv('MH_SMTP_BIND_ADDR');
        if (is_string($mailhogSmtpBindAddr) && '' !== $mailhogSmtpBindAddr) {
            $this->useMailpit($mailhogSmtpBindAddr);
        }

        return $this;
    }
}
