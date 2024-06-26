<?php

namespace App\Http\Controllers\Starter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pkt\StarterKit\Helpers\GlobalSearch;

class GlobalSearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $qurey = new GlobalSearch();
        $results = $qurey->search($request->search);

        return response()->json([
            'status' => true,
            'data' => $results,
        ], 200);
    }
}
