<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/template', name: 'app_template')]
    public function index(): Response
    {
        // TODO: Revisit example here!

        $templates  = [
            [
                'id' => 0,
                'type' => 'single-wallet',
                'label' => 'Single Wallet',
                'shortDescription' => 'Create a single Wallet for testing purposes',
                'icon' => '',
                'image' => '',
            ], [
                'id' => 1,
                'type' => 'loyalty-points',
                'label' => 'Loyalty Points',
                'shortDescription' => 'Create loyalty points wallets',
                'icon' => '',
                'image' => '',
            ], [
                'id' => 2,
                'type' => 'token',
                'label' => 'CBDC / Token',
                'shortDescription' => 'Create custom token wallet collection with issuer, bank and customer wallets',
                'icon' => '',
                'image' => '',
            ]
        ];

        return $this->render('template/index.html.twig', [
            'controller_name' => 'TemplateController',
            'templates' => $templates
        ]);
    }

    #[Route('/template/show/{templateId}', name: 'app_template_show')]
    public function show(): Response
    {

    }

}
