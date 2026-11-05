<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\IncomeReportRequest;
use App\Models\Building;
use App\Services\IncomeReportService;
use Illuminate\View\View;

class IncomeReportController extends Controller
{
    public function index(IncomeReportRequest $request, IncomeReportService $reports): View
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $filters = $request->validated();

        return view('owner.reports.income', [
            'report' => $reports->build($user, $filters),
            'buildings' => Building::query()->where('owner_id', $user->id)->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }
}
