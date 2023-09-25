<?php declare(strict_types=1);

namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use XRPL_PHP\Client\JsonRpcClient;
use XRPL_PHP\Core\Networks;
use XRPL_PHP\Core\RippleBinaryCodec\Types\Amount;
use XRPL_PHP\Wallet\Wallet;

class TransactionService {

    private array $wallets = [];
    private ConfigurationService $configurationService;

    private JsonRpcClient $client;

    private ?LoggerInterface $logger;

    public function __construct(
        ConfigurationService $configurationService,
        ?LoggerInterface $logger
    ) {
        $this->configurationService = $configurationService;
        $networkUrl = $this->configurationService->getNetworkUrl();
        $this->client = new JsonRpcClient($networkUrl);
        $this->logger = $logger;
    }

    /**
     *
     *
     * @param array $wallets
     * @return void
     */
    public function setWallets(array $wallets):void
    {
        $this->wallets = $wallets;
    }

    /**
     *
     *
     * @param array $definition
     * @return array
     * @throws Exception
     */
    public function createTxPayload(array $definition): array
    {
        return match ($definition['type']) {
            'AccountSet' => $this->createAccountSetTx($definition['tx']),
            'Payment' => $this->createPaymentTx($definition['tx']),
            'TrustSet' => $this->createTrustSetTx($definition['tx']),
            default => throw new Exception('Transactor not defined')
        };
    }

    /**
     *
     *
     * @param Wallet $wallet
     * @param array $tx
     * @return array
     */
    public function transact(Wallet $wallet, array$tx): array
    {
        $autofilledTx = $this->client->autofill($tx);
        $signedTx = $wallet->sign($autofilledTx);

        $txResponse = $this->client->submitAndWait($signedTx['tx_blob']);
        return $txResponse->getResult();
    }

    /**
     *
     *
     * @param array $tx
     * @return array
     */
    private function createAccountSetTx(array $tx): array
    {
        return [];
    }

    /**
     *
     *
     * @param array $tx
     * @return array
     * @throws Exception
     */
    private function createPaymentTx(array $tx): array
    {
        //if (!Amount::isAmountValid($tx['Amount'])) {
        //    throw new Exception('Amount malformed');
        //}

        $amount = $tx['Amount'];
        if (is_array($tx['Amount'])) {
            // If amount is an IOU / token, the issuer address ref has to be transformed
            $amount['issuer'] = $this->getWalletAddress($amount['issuer']);
        }

        $payload = [
            "TransactionType" => "Payment",
            "Account" => $this->getWalletAddress($tx['Account']),
            "Destination" => $this->getWalletAddress($tx['Destination']),
            "Amount" => $amount
        ];

        if (isset($tx['DestinationTag'])) {
            $payload['DestinationTag'] = $tx['DestinationTag'];
        }

        return $payload;
    }

    /**
     *
     *
     * @param array $tx
     * @return array
     * @throws Exception
     */
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