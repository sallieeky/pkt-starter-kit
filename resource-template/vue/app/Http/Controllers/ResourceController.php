<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\DxAdapter;
use App\Models\ModelName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class ModelNameController extends Controller
{
    public function managePage(Request $request)
    {
        return Inertia::render('ModelName/ModelNameManage');
    }

    public function dataProcessing(Request $request)
    {
        $loadData = ModelName::query()->select('*');
        $loadDatais = DxAdapter::load($loadData);
        $data = $loadDatais->paginate($request->take ?? $loadDatais->count());
        return response()->json([
            'status' => true,
            'data' => $data->items(),
            'totalCount' => $data->total(),
        ], 200);
    }
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->all();
            $modelName = ModelName::create($validated);

            DB::commit();
            return redirect()->back()->with('message','Success to create ModelLabel');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message'=>'Failed to create ModelLabel'
            ]);
        }
    }
    public function update(ModelName $modelName, Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->all();
            $modelName->update($validated);

            DB::commit();
            return redirect()->back()->with('message','Success to update ModelLabel');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message'=>'Failed to update ModelLabel'
            ]);
        }
    }
    public function delete(ModelName $user, Request $request)
    {
        $user->delete();
        return redirect()->back()->with('message','Success to delete ModelLabel');
    }
}
