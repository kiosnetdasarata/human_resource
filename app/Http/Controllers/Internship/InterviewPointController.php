<?php

namespace App\Http\Controllers\Internship;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\InterviewPoint\StoreInterviewPointRequest;
use App\Http\Requests\InterviewPoint\UpdateInterviewPointRequest;
use App\Services\InterviewPointService;

class InterviewPointController extends Controller
{
    public function __construct(
        private InterviewPointService $interviewPoint,
        private ResponseHelper $response
    ) 
    { 
        //
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store($applicantType, $id, StoreInterviewPointRequest $request)
    {
        try {           
            $this->interviewPoint->store($id,$request->validated(), $applicantType);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($applicantType, $id)
    {
        try {
            $data = $this->interviewPoint->find($id, $applicantType);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($applicantType, UpdateInterviewPointRequest $request, string $id)
    {
        try {
            $this->interviewPoint->update($id, $request->validated(), $applicantType);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
