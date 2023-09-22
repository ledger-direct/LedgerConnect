<?php declare(strict_types=1);

namespace App\Service;

use XRPL_PHP\Client\JsonRpcClient;
use XRPL_PHP\Core\Networks;
use XRPL_PHP\Wallet\Wallet;

class WalletService {

    private ConfigurationService $configurationService;

    private TransactionService $transactionService;

    private JsonRpcClient $client;

    public function __construct(
        ConfigurationService $configurationService,
        TransactionService $transactionService,
    ) {
        $this->configurationService = $configurationService;
        $networkUrl = $this->configurationService->getNetworkUrl();
        $this->client = new JsonRpcClient($networkUrl);

        $this->transactionService = $transactionService;
    }

    public function createWallet(): Wallet
    {
        return $this->client->fundWallet();
    }

    public function handleRecipe(mixed $recipe)
    {
        $short = [];
        $wallets = [];
        foreach ($recipe['wallets'] as $walletDefinition) {
            $ref = $walletDefinition['ref'];
            $wallet = $this->createWallet();
            $wallets[$ref] = $wallet;
            $short[$ref] = [
                'function' => $walletDefinition['role'],
                'seed' => $wallet->getSeed(),
                'address' => $wallet->getAddress()
            ];
        }

        if (count($recipe['scripts'])) {
            sleep (1);
            $this->transactionService->setWallets($wallets);
        }

        foreach ($recipe['scripts'] as $script) {
            $txPayload = $this->transactionService->createTxPayload($script['transaction']);
            $this->transactionService->transact($wallets[$script['transaction']['wallet']], $txPayload);
        }

        return $short;
    }
}