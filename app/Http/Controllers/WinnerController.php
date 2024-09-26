<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Winner;
use App\Jobs\SetWinnerJob;

class WinnerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $sortby = $request->query('sortby') ?? 'points';
            $order = $request->query('order') ?? 'desc';

            SetWinnerJob::dispatch();
            $winners = Winner::with('user')->orderBy($sortby, $order)->get();

            return response()->json([
                'error' => false,
                'message' => 'Winners retrieved successfully',
                'data' => $winners
            ]);
        } catch (\Exception $e) {
            \Log::error('Error retrieving winners: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Oops! Something went wrong. Please try again later.',
            ], 500);
        }
    }
}
