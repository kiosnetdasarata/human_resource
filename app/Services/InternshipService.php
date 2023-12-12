<?php
namespace App\Services;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\Employee\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Internship\InternshipRepositoryInterface;
use App\Interfaces\Internship\PartnershipRepositoryInterface;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use App\Interfaces\Internship\InterviewPointRepositoryInterface;
use App\Interfaces\Internship\InternshipContractRepositoryInterface;

class InternshipService
{
    public function __construct(
        private InternshipRepositoryInterface $internship,
        private InternshipContractRepositoryInterface $internshipContract,
        private TraineeshipRepositoryInterface $traineeship,
        private RoleRepositoryInterface $role,
        private PartnershipRepositoryInterface $partnership,
        private InterviewPointRepositoryInterface $interviewPoint,
        private EmployeeRepositoryInterface $employee,
        private JobVacancyRepositoryInterface $jobVacancy
        )
    {
    }

    public function getAllTraineeship()
    {
        return $this->traineeship->getAll();
    }

    public function findTraineeship($id, $withtrashes = false)
    {
        return $withtrashes ? $this->traineeship->findWithTrashes($id) : $this->traineeship->find($id);
    }

    public function findTraineeshipSlug($name) 
    {
        return $this->traineeship->findBySlug(Str::slug($name, '_'));
    }
    
    public function createTraineeship($request)
    {
        $jobVacancy = $this->jobVacancy->find($request['vacancy_id']);
        $age = Carbon::parse($request['tanggal_lahir'])->diffInYears(Carbon::now());
        
        if (Carbon::now() > $jobVacancy['close_date'] || Carbon::now() < $jobVacancy['open_date'])
            throw new \Exception('vacancy belum dibuka / sudah ditutup', 403);
        if ($age > $jobVacancy['max_umur'] || $age < $jobVacancy['min_umur'])
            throw new \Exception('umur tidak valid', 422);
        
        $list = $this->findTraineeshipSlug($request['nama_lengkap']);
        $slug = Str::slug($request['nama_lengkap']) .
                    count($list) > 0 ?? (int) end(explode('_', end($list->slug))) +1;
        
        $traineeship = collect($request)->merge([
            'file_cv' => 'filenya ada',
            'tanggal_lamaran' => Carbon::now(),
            'slug' => $slug
        ]);

        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] >= date('Y')) {
            throw new \Exception('tahun lulus tidak valid', 422);
        }
        
        $traineeship->put('file_cv', uploadToGCS($request->file['file_cv'], $traineeship['slug']. '_cv','traineeship/cv'));
        return $this->traineeship->create($traineeship->all());
    }

    public function updateTraineeship($slug, $request) 
    {
        return DB::transaction(function() use ($slug, $request) {
            $old = $this->findTraineeship($slug);
            $traineeship = collect($request)->diffAssoc($old);
            if (isset($traineeship['file_cv'])) {
                $traineeship->put('file_cv', 'test_cv');
                $traineeship->put('file_cv', uploadToGCS($request->file['file_cv'],$traineeship->id,'traineeship/cv'));
            }
            if (isset($traineeship['nama_lengkap'])) {
                $list = $this->findTraineeshipSlug($request['nama_lengkap']);
                $additionalSlug = '';
                if (count($list) > 0) {
                    $explodedSlug = explode('_', $list->last()->slug);
                    $counter = (int) end($explodedSlug);
                    $additionalSlug = '_' . ($counter + 1);
                }
                $slug = Str::slug($traineeship['nama_lengkap'],'_') . $additionalSlug;
                
                $traineeship->put('slug', $slug);
            }
            if (isset($request['status_tahap'])) {
                $oldStatus = $old->status_tahap;
                $newStatus = $request['status_tahap'];
                if ($newStatus == 'Asesment' && $oldStatus != 'FU') {
                    throw new \Exception ('status traineeship tidak valid', 422);
                } elseif ($newStatus == 'Lolos') {
                    if ($old->hr_point_id == null)
                        throw new \Exception ('hr point dari traineeship tidak ditemukan', 404);
                    $this->createInternship($old->id, $request);
                }
            }
            $data = $this->traineeship->update($old, $traineeship->all());

            if ($data->status_tahap == 'Tolak' || $data->status_tahap == 'Lolos') {
                $this->traineeship->delete($old);
            }
        });
    }

    public function deleteTraineeship($slug)
    {
        return DB::transaction(function () use ($slug) {
            $traineeship = $this->traineeship->find($slug);
            $this->interviewPoint->delete($traineeship->interviewPoint);
            $this->traineeship->delete($traineeship);
        });
    }

    public function addInterviewPoint($id, $request)
    {
        return DB::transaction(function ()  use ($id, $request) {
            $traineeship = $this->traineeship->find($id);
            if ($traineeship->hr_point_id != null) {
                throw new \Exception('job aplicant ini sudah memiliki interview point dengan id '. $traineeship->hr_point_id,422);
            } elseif ($traineeship->status_tahap != 'Assessment') {
                throw new \Exception('job aplicant harus pada tahap Assessment',422);
            }
            $poin = $this->interviewPoint->create($request);
            $this->traineeship->update($traineeship, ['hr_point_id' => $poin->id]);
        });
    }
    
    public function showInterviewPoint($id)
    {
        return $this->findTraineeship($id)->interviewPoint;
    }

    public function updateInterviewPoint($idTraineenship, $request) 
    {
        $poin = $this->traineeship->find($idTraineenship)->interviewPoint;
        if ($poin == null) {
            throw new ModelNotFoundException('Traineeship ini belum memiliki interview point',404);
        }
        return $this->interviewPoint->update($poin, $request);
    }

    public function deleteInterviewPoint($idTraineenship)
    {
        return DB::transaction(function () use ($idTraineenship) {
            $traineeship = $this->traineeship->find($idTraineenship);
            if ($traineeship->interviewPoint == null) {
                throw new ModelNotFoundException('Traineeship ini belum memiliki interview point',404);
            }
            $this->interviewPoint->delete($traineeship->interviewPoint);
            $this->traineeship->update($traineeship, ['hr_point_id' => null]);
        });
    }

    public function getAllInternship()
    {
        return $this->internship->getAll();
    }

    public function findInternship($item, $category = null)
    {
        return $category == 'slug' ?
            $this->internship->findBySlug(Str::slug($item,'_')) : $this->internship->find($item);
    }

    public function createInternship($idTraineenship, $request)
    {
        return DB::Transaction(function() use ($idTraineenship, $request) {
            if (isset($internship['mitra_id'])) {
                $file = $this->partnership->find($internship['mitra_id'])->filePartnership[0];
                if ($file == null || $file->date_expired < Carbon::now())
                    throw new \Exception('file mitra tidak ditemukan atau kadaluarsa',404);
            }
            
            $traineeship = $this->findTraineeship($idTraineenship, true);
            $list = $this->findInternship($request['nama_lengkap'],'slug');
            $slug = Str::slug($traineeship['nama_lengkap']) .
                        count($list) > 0 ?? (int) end(explode('_', end($list->slug))) +1;
            $internship = collect($traineeship)->merge([
                    'id' => Uuid::uuid4()->getHex(),
                    'internship_nip' => $this->generateNip($request['tanggal_masuk'], $traineeship->jk),
                    'slug' => $slug,
                    'file_cv' => $traineeship->file_cv,
                    'no_tlpn' => $traineeship->nomor_telepone,
                    'role_id' => $traineeship->jobVacancy->role_id,
                ])->merge($request);
            
            $internship->put('file_cv', uploadToGCS($internship['file_cv'],$internship->id.'_cv','internship/cv'));
            $this->internship->create($internship->all());
            $this->traineeship->delete($traineeship);
        });
    }

    public function updateInternship($uuid, $request)
    {
        $old = $this->findInternship($uuid);
        $internship = collect($request)->diffAssoc($old);

        if (isset($internship["nama_lengkap"]))
            $list = $this->findInternship($request['nama_lengkap'],'slug');
            $slug = Str::slug($internship['nama_lengkap']) .
                        count($list) > 0 ?? (int) end(explode('_', end($list->slug))) +1;
            
            $internship->put('slug', $slug);

        if (isset($internship['supervisor']) && $this->employee->find($internship['supervisor'], 'nip') == null)
            throw new \Exception('supervisor tidak ditemuka atau bukan karyawan aktif', 404);

        return $this->internship->update($old, $internship->all());
    }

    public function deleteInternship($uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $internship = $this->findInternship($uuid);
            $this->internship->delete($internship);
            $this->deleteInternshipContract($uuid);
        });
    }

    public function getInternshipContracts($id)
    {
        return $this->findInternship($id)->internshipContract;
    }

    public function getInternshipContract($id)
    {
        $data = $this->findInternship($id)->internshipContract[0];
        if ($data == null || $data->date_expired < Carbon::now()) {
            throw new ModelNotFoundException('file mitra tidak ditemukan atau kadaluarsa', 404);
        }
        return $data;
    }

    public function createInternshipContract($id,$request)
    {
        return DB::transaction(function () use ($id, $request) {
            $internship = $this->findInternship($id);
            if ($internship == null) {
                throw new \Exception('internship tidak ditemukan',404);
            }
            $contract = collect($request)->merge([
                'id' => Uuid::uuid4()->getHex(),
                'internship_nip_id' => $internship->internship_nip,
                'role_internship' => $internship->role_id,
                'date_expired' => Carbon::parse($request['date_start'])->addMonth($request['durasi_kontrak']),
            ]);

            $this->internshipContract->create($contract->all());
            $this->internship->update($internship, ['durasi' => $contract['durasi_kontrak']]);
        });
    }

    public function updateInternshipContract($id, $request) 
    {
        return DB::transaction(function () use ($id, $request) {
            $contract = $this->getInternshipContract($id);
            $data = collect($request)->diffAssoc($contract);
            if (isset($data['durasi_kontrak'])){
                $this->internshipContract->update($contract, [
                    'durasi_kontrak' => $data['durasi_kontrak'],
                    'date_expired' => Carbon::parse($data['date_start'])->addMonth($data['durasi_kontrak'])
                ]);
            }
            if (isset($data['date_start'])){
                $contract = $this->getInternshipContract($id);
                $this->internshipContract->update($contract, [
                    'date_start' => $data['date_start'],
                    'date_expired' => Carbon::parse($data['date_start'])->addMonth($contract['durasi_kontrak'])
                ]);
            }
        });
    }

    public function deleteInternshipContract($uuid)
    {
        $contract = $this->getInternshipContract($uuid);
        if ($contract != null) 
            return $this->internshipContract->delete($contract);
        throw new \Exception('Data kontrak tidak ditemukan', 404);
    }

    private function generateNip($tglKerja, $jk)
    {
        $ke = $this->internship->getAllThisYear() + 1;
        if ((int)($ke/10 == 0)) $ke = '00'.$ke;
        else if ((int)($ke/100 == 0)) $ke = '0'.$ke;

        return (string) ( 
            '2' 
            . (string) date_create_from_format('Y-m-d', $tglKerja)->format('Ym')
            . ($jk == 'Laki-Laki' ? '1':'0')
            . $ke
        );
    }
}

?>