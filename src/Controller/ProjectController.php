<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    public function __construct(private WalletService $walletService)
    {

    }

    #[Route('/project', name: 'project')]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }

    #[Route('/project/show/{projectId}', name: 'project_show', methods: ['GET'])]
    public function show(): Response
    {

    }

    #[Route('/project/create/{templateType}', name: 'project_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        string $templateType
    ): Response
    {
        // TODO: Implement this as ArrayAccess
        $wallets = [];
        // TODO: Extend this to recipes
        switch ($templateType) {
            case 'single-wallet':
                $wallet = $this->walletService->createWallet();
                $wallets[] = [
                    'function' => 'simple-wallet',
                    'seed' => $wallet->getSeed(),
                    'address' => $wallet->getAddress()
                ];
                break;
            case 'loyalty-points':
                break;
            case 'token':
                break;
            default:
                // TODO: Throw Error Response
                break;
        }

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }


        return $this->render('project/show.html.twig', [
            'project' => [
                'id' => $randomString,
                'type' => 'Loyalty Points',
                'wallets' => $wallets
            ]
        ]);
    }

    #[Route('/project/edit/{projectId}', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {

    }
}
