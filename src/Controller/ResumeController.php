<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AiService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResumeController extends AbstractController
{
    private AiService $aiService;
    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }
    #[Route('/api/analyze', methods: ['POST'], name: 'app_ai')]
    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $jd = $data['jd'] ?? null;
        $resume = $data['resume'] ?? null;
        if (!$jd || !$resume) {
            return new JsonResponse(['error' => 'JD and Resume are required'], 400);
        }
        try {
            $feedback = $this->aiService->resumeAnalyze($jd, $resume);
            return $this->json([
                'feedback' => $feedback,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}