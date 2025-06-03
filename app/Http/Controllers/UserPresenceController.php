<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Services\UserPresenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPresenceController extends Controller
{
    protected $presenceService;

    public function __construct(UserPresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    /**
     * Compte le nombre de jours ouvrés (Lundi-Vendredi) entre deux dates
     */
    private function countWorkingDays(Carbon $startDate, Carbon $endDate)
    {
        $days = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // 0 = dimanche, 6 = samedi
            if (!in_array($currentDate->dayOfWeek, [0, 6])) {
                $days++;
            }
            $currentDate->addDay();
        }

        return $days;
    }
}
