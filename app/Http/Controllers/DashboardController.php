<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Error;
use App\Traits\StatsTrait;

class DashboardController extends Controller
{
    use StatsTrait;

    public function index()
    {
        $user = auth()->user();
        $cardsData = $this->getCardsData($user->organization);
        $chartData = $this->getChartData($user->organization->id);

        return Inertia::render('Dashboard/Index', [
            'cardsData' => $cardsData,
            'chartData' => $chartData,
        ]);
    }

    /**
     *  Returns the data for the stat cards
     *
     * @param \App\Models\Organization $organization
     * @return array
     */
    private function getCardsData($organization): array
    {
        $projectCount = $organization->projects()->count();
        $errorCount = $organization->projects()->withCount('errors')->get()->sum('errors_count');
        $errorCount24 = $organization->projects()->withCount(['errors' => function ($query) {
            $query->whereBetween('timestamp', [now()->subDays(1), now()]);
        }])->get()->sum('errors_count');

        return [
            'projectCount' => $projectCount,
            'errorCount' => $errorCount,
            'error24' => [
                'count' => $errorCount24,
                'percentage' => '+' . $this->calculatePercentage($errorCount24, ($errorCount - $errorCount24)),
            ]
        ];
    }

    /**
     * Returns the data for the chart
     *
     * @param int $organizationId
     *
     * @return array
     */
    private function getChartData($organizationId): array
    {
        $result = [
            "allErrors" => [],
            "projectErrors" => [],
        ];

        $projects = Project::with(['errors' => function ($query) {
            $query->whereBetween('timestamp', [now()->subDays(6), now()])->orderBy('timestamp', 'asc');
        }])->where('organization_id', $organizationId)->get();

        foreach ($projects as $project) {
            $errors = $project['errors']->groupBy(function ($error) {
                return Carbon::parse($error->timestamp)->format('d-m-Y');
            })->map(function ($group) {
                return count($group);
            })->toArray();

            foreach ($errors as $date => $count) {
                if (array_key_exists($date, $result["allErrors"])) {
                    $result["allErrors"][$date] += $count;
                } else {
                    $result["allErrors"][$date] = $count;
                }
            }

            $result["projectErrors"][] = [
                'project' => $project['name'],
                'errors' => $errors,
            ];
        }

        $dateRange = $this->generateDateRange(7);
        $result = $this->addMissingDays($result, 'allErrors', $dateRange);
        foreach ($result['projectErrors'] as &$projectError) {
            $projectError = $this->addMissingDays($projectError, 'errors', $dateRange);
        }

        return $result;
    }
}