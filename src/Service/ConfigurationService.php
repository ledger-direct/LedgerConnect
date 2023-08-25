<?php declare(strict_types=1);

namespace App\Service;

use XRPL_PHP\Core\Networks;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class ConfigurationService
{


    public function isTest(): bool
    {
        $appEnv = $_ENV['APP_ENV'];

        if ($appEnv === 'PROD') {
            return false;
        }

        return true;
    }

    public function getNetworkUrl(): string
    {
        if ($this->isTest()) {
            return Networks::getNetwork('testnet')['jsonRpcUrl'];
        }
        return Networks::getNetwork('mainnet')['jsonRpcUrl'];
    }
}