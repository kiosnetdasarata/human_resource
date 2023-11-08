<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Interfaces\JobAplicantRepositoryInterface;

class JobAplicantService
{
    public function __construct(private JobAplicantRepositoryInterface $jobApplicant) 
    {
    }

    public function get()
    {
        return $this->jobApplicant->getAll();
    }

    public function create($request)
    {
        if (Carbon::parse($request['tanggal_lahir'])->diffYears(Carbon::now()));
        return true;
        $aplicant = collect($request)->merge([
            'file_cv' => 'filenya ada',
            'date' => Carbon::now(),
            'status_tahap' => 'Screening',
        ]);
        
        // $traineeship->put('file_cv', $request['file_cv']->storeAs('traineeship/cv', $traineeship['slug'].'_cv.pdf', 'gcs'));

        return $this->jobApplicant->create($aplicant->all());
        
    }

    public function findJobAplicant($id) 
    {
        return $this->jobApplicant->find($id);
    }

    public function update($id, $request) 
    {
        return DB::transaction(function() use ($id, $request){
            $old = $this->findJobAplicant($id);
            $traineeship = collect($request)->diffAssoc($old);
            if (isset($traineeship['file_cv'])) {
                $traineeship->put('file_cv', 'test_cv');
                // $traineeship->put('file_cv', $request->file['file_cv']->storeAs('traineeship/cv', $traineeship['uuid'].'.pdf', 'gcs'));
            }
            if (isset($request['status_tahap'])) {
                $oldStatus = $old->status_tahap;
                $newStatus = $request['status_tahap'];
                $arr = ['Screening','FU','Assesment'];
                if ($newStatus == 'Tolak'){
                    $this->jobApplicant->delete($old);
                } elseif ($oldStatus == 'Assesment' && $newStatus == 'Lolos') {

                } elseif ($newStatus == 'Assesment' && $oldStatus == null) {
                    throw new \Exception('traineeship tidak memiliki hr point');
                } elseif ($oldStatus == 'Assesment' && $newStatus == 'Lolos'){

                } elseif (array_search($oldStatus, $arr) + 1 != array_search($newStatus, $arr)) {
                    throw new \Exception ('status traineeship tidak valid');
                } 
            
            }
            $this->jobApplicant->update($old, $traineeship->all());
        });
    }

}
?>