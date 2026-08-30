<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\VerificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $logs = VerificationLog::where('company_id', $request->user()->company_id)
            ->with(['university', 'student', 'verificationDocument'])
            ->latest('created_at')
            ->paginate(25);

        return view('company.history', compact('logs'));
    }
}
