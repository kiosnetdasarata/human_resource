<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\AllowanceService;
use Illuminate\Http\Request;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Validator;

class AllowanceLevelController extends Controller
{
    public function __construct(
        private AllowanceService $allowance,
        private ResponseHelper $response
    )
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index($uuid)
    {
        try {
            $data = $this->allowance->findByEmployee($uuid);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function get($uuid)
    {
        try {
            $data = $this->allowance->eligibleAllowance($uuid);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($uuid, Request $request)
    {
        try {
            $data = $request->validate($request->all(), [
                '*.id' => 'required|exists:allowances_categories,id',
                '*.tanggal_mulai' => 'required|date_format:Y-m-d'
            ]);

            $formattedData = collect($data)->mapWithKeys(function ($item) {
                return [$item['id'] => $item['tanggal_mulai']];
            })->all();

            $this->allowance->createEmployee($uuid, $formattedData);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid, Request $request)
    {
        try {
            $this->allowance->delete($request->only('id'), $uuid);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
}
}
