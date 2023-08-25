<?php declare(strict_types=1);

namespace App\Service;

use XRPL_PHP\Client\JsonRpcClient;
use XRPL_PHP\Core\Networks;
use XRPL_PHP\Wallet\Wallet;

class WalletService {

    private ConfigurationService $configurationService;

    private JsonRpcClient $client;

    public function __construct(ConfigurationService $configurationService) {
        $this->configurationService = $configurationService;
        $networkUrl = $this->configurationService->getNetworkUrl();
        $this->client = new JsonRpcClient($networkUrl);
    }

    public function createWallet(): Wallet
    {
        return $this->client->fundWallet();
    }

    public function configureWallet(Wallet $wallet): bool
    {
        return false;
    }

    public function setTrustline(Wallet $wallet): bool
    {
        return false;
    }
}