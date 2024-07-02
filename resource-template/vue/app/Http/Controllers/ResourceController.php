<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\DxAdapter;
use App\Http\Requests\ModelName\CreateModelNameRequest;
use App\Http\Requests\ModelName\UpdateModelNameRequest;
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
    public function createPage(Request $request)
    {
        return Inertia::render('ModelName/ModelNameCreate');
    }
    public function create(CreateModelNameRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $modelName = ModelName::query()->create($validated);

            DB::commit();
            return redirect()->back()->with('message','Success to create ModelLabel');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message'=>'Failed to create ModelLabel'
            ]);
        }
    }
    public function updatePage(ModelName $modelName, Request $request)
    {
        return Inertia::render('ModelName/ModelNameUpdate', [
            'modelName' => $modelName
        ]);
    }
    public function update(ModelName $modelName, UpdateModelNameRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
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
    public function delete(ModelName $modelName, Request $request)
    {
        $modelName->delete();
        return redirect()->back()->with('message','Success to delete ModelLabel');
    }
}
