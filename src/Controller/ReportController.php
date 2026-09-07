<?php

namespace App\Controller;

use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportController extends AbstractController
{
    public function __construct(
        private ReportService $reportService
    ) {}

    #[Route('/report/top-authors', name: 'app_report_top_authors')]
    public function topAuthors(Request $request): Response
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        $reportData = $this->reportService->getTopAuthorsReport($year);

        return $this->render('report/top_authors.html.twig', $reportData);
    }
}
