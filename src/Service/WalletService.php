<?php declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use XRPL_PHP\Client\JsonRpcClient;
use XRPL_PHP\Wallet\Wallet;

class WalletService {

    private ConfigurationService $configurationService;

    private TransactionService $transactionService;

    private JsonRpcClient $client;

    private ?LoggerInterface $logger;

    public function __construct(
        ConfigurationService $configurationService,
        TransactionService $transactionService,
        ?LoggerInterface $logger
    ) {
        $this->configurationService = $configurationService;
        $networkUrl = $this->configurationService->getNetworkUrl();
        $this->client = new JsonRpcClient($networkUrl);
        $this->transactionService = $transactionService;
        $this->logger = $logger;
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
                'address' => $wallet->getAddress(),
                'ref' => $ref
            ];
            $this->logWallet($short[$ref]);
        }

        if (count($recipe['scripts'])) {
            sleep (1);
            $this->transactionService->setWallets($wallets);
        }

        foreach ($recipe['scripts'] as $script) {
            $txPayload = $this->transactionService->createTxPayload($script['transaction']);
            $this->logTxPayload($script, $txPayload);
            $result = $this->transactionService->transact($wallets[$script['transaction']['wallet']], $txPayload);
            $this->logTransactionResult($result);
        }

        return $short;
    }

    private function logWallet(array $shortInfo): void
    {
        $this->logger?->debug('Wallet: ', $shortInfo);
    }

    private function logTxPayload(array $script, array $txPayload): void
    {
        $this->logger?->debug('Transaction: ', [
            'id' => $script['id'],
            'tx' => print_r($txPayload, true)
        ]);
    }

    private function logTransactionResult(array $result): void
    {
        $this->logger?->debug('Result: ', [
            'res' => $result
        ]);
    }
}