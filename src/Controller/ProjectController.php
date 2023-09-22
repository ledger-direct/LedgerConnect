<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class ProjectController extends AbstractController
{
    public const RANDOM_CHARACTER_LIST = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private array $recipes = [];

    public function __construct(private readonly WalletService $walletService)
    {
        $dir = getcwd() . '/../src/Recipes/';
        $recipeFiles = scandir($dir);
        foreach ($recipeFiles as $file) {
            $key = str_replace('.yaml', '', $file);
            if ($file !== '.' && $file !== '..') {
                $this->recipes[$key] = Yaml::parseFile($dir . $file);
            }
        }
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
        $recipe = $this->recipes[$templateType] ?? null;
        if (!$recipe) {
            // TODO Throw Exception
        }

        $wallets = $this->walletService->handleRecipe($recipe);

        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $index = rand(0, strlen(self::RANDOM_CHARACTER_LIST) - 1);
            $randomString .= self::RANDOM_CHARACTER_LIST[$index];
        }

        return $this->render('project/show.html.twig', [
            'project' => [
                'id' => $randomString,
                'type' => $recipe['label'],
                'wallets' => $wallets
            ]
        ]);
    }

    #[Route('/project/edit/{projectId}', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {

    }
}
