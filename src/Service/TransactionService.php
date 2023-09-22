<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use XRPL_PHP\Client\JsonRpcClient;
use XRPL_PHP\Core\Networks;
use XRPL_PHP\Wallet\Wallet;

class TransactionService {

    private array $wallets = [];
    private ConfigurationService $configurationService;

    private JsonRpcClient $client;

    public function __construct(ConfigurationService $configurationService) {
        $this->configurationService = $configurationService;
        $networkUrl = $this->configurationService->getNetworkUrl();
        $this->client = new JsonRpcClient($networkUrl);
    }

    public function setWallets(array $wallets):void
    {
        $this->wallets = $wallets;
    }

    public function createTxPayload(array $definition): array
    {
        return match ($definition['type']) {
            'AccountSet' => $this->createAccountSetTx($definition['tx']),
            'Payment' => $this->createPaymentTx($definition['tx']),
            'TrustSet' => $this->createTrustSetTx($definition['tx']),
            default => throw new Exception('Transactor not defined')
        };
    }

    public function transact(Wallet $wallet, array$tx): array
    {
        return [];
    }

    private function createAccountSetTx(array $tx): array
    {
        return [];
    }

    private function createPaymentTx(array $tx): array
    {
        return [];
    }

    private function createTrustSetTx(array $tx): array
    {
        return [
            "TransactionType" => "TrustSet",
            "Account" => $this->getWalletAddress($tx['Account']),
            "LimitAmount" => [
                "currency" => $tx['LimitAmount']['currency'],
                "issuer" => $this->getWalletAddress($tx['LimitAmount']['issuer']),
                "value" => $tx['LimitAmount']['value']
            ]
        ];
    }

    private function getWalletAddress(string $ref): string
    {
        if (!isset($this->wallets[$ref])) {
            throw new Exception('Wallet not found in refs');
        }

        return $this->wallets[$ref]->getAddress();
    }
}