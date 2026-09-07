<?php

namespace App\Service;

use App\Repository\AuthorRepository;

class ReportService
{
    public function __construct(
        private AuthorRepository $authorRepository
    ) {}

    /**
     * Возвращает структурированные данные для отчета "Топ Авторов".
     */
    public function getTopAuthorsReport(int $year): array
    {
        if ($year < 1000 || $year > 2030) {
            $year = (int) date('Y');
        }

        $results = $this->authorRepository->findTopAuthorsByYear($year);

        return [
            'results' => $results,
            'current_year' => $year
        ];
    }
}
