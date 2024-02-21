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
        $age = Carbon::parse($request['tanggal_lahir'])->diffInYears(now());
        
        if (now() > $jobVacancy['close_date'] || now() < $jobVacancy['open_date']) throw new \Exception('vacancy belum dibuka / sudah ditutup', 403);
        if ($age > $jobVacancy['max_umur'] || $age < $jobVacancy['min_umur']) throw new \Exception('umur tidak valid', 422);
        
        $slug = $this->generateTraineeshipSlug($request['nama_lengkap']);        
        $traineeship = collect($request)->merge([
            'nama_lengkap'      => Str::title($request['nama_lengkap']),
            'slug'              => $slug,
            'tanggal_lamaran'   => now(),
            'file_cv'           => uploadToGCS($request['file_cv'], $slug .'_'. $jobVacancy['role']['nama_jabatan'] . '_cv','traineeship/cv')
        ]);

        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] >= date('Y')) {
            throw new \Exception('tahun lulus tidak valid', 422);
        }
        
        return $this->traineeship->create($traineeship->all());
    }

    public function updateStatus($id, $status)
    {
        $old = $this->traineeship->find($id);
        if ($old)
        return DB::transaction(function () use ($old, $status) {
            $oldStatus = $old->status_tahap;
            if ($status == 'Assesment' && $oldStatus != 'FU') {
                throw new \Exception ('status jobApplicant tidak valid', 422);
            } else if ($status == 'Lolos') {
                if (!$old->hr_point_id) throw new \Exception ('hr point dari traineeship tidak ditemukan', 404);
                $this->createInternship($old);
            }
            $this->traineeship->update($old, ['status_tahap' => $status]);

            if ($status == 'Lolos' || $status == 'Tolak') {
                if ($old->interviewPoint) $this->interviewPoint->delete($old->interviewPoint);
                $this->traineeship->delete($old);
            }
        });            
    }

    public function updateTraineeship($id, $request) 
    {
        $old = $this->traineeship->find($id);
        return DB::transaction(function() use ($old, $request){            
            $jobApplicant = collect($request)->diffAssoc($old);
            if (isset($jobApplicant['nama_lengkap'])) {
                $jobApplicant->put('nama_lengkap', Str::title($jobApplicant['nama_lengkap']))->put('slug', $this->generateTraineeshipSlug($jobApplicant['nama_lengkap']));
            }
            if (isset($jobApplicant['file_cv'])) {
                $link = uploadToGCS($request['file_cv'], $old->slug .'_'. $old->role->nama_jabatan . '_cv', 'traineeship/file_cv');
                $jobApplicant->put('file_cv', $link);
            }
            
            $this->traineeship->update($old, $jobApplicant->all());
        });
    }

    public function addInterviewPoint($id, $request)
    {
        return DB::transaction(function ()  use ($id, $request) {
            $traineeship = $this->traineeship->find($id);
            if ($traineeship->hr_point_id) {
                throw new \Exception('job Applicant ini sudah memiliki interview point dengan id '. $traineeship->hr_point_id,422);
            } elseif ($traineeship->status_tahap != 'Assesment') {
                throw new \Exception('job Applicant harus pada tahap Assesment',422);
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
        if (!$poin) {
            throw new ModelNotFoundException('Traineeship ini belum memiliki interview point',404);
        }
        return $this->interviewPoint->update($poin, $request);
    }

    public function deleteInterviewPoint($idTraineenship)
    {
        return DB::transaction(function () use ($idTraineenship) {
            $traineeship = $this->traineeship->find($idTraineenship);
            if (!$traineeship->interviewPoint) {
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

    public function createInternship($traineeship)
    {
        return DB::Transaction(function() use ($traineeship) {
            $this->updateStatus($traineeship, ['status_tahap' => 'Lolos']);

            $internship = collect($traineeship)->merge([
                    'id'                => Uuid::uuid4()->getHex(),
                    'internship_nip'    => $this->generateNip($traineeship->jk),
                    'slug'              => $this->generateInternshipSlug($traineeship->nama_lengkap),
                    'file_cv'           => $traineeship->file_cv,
                    'no_tlpn'           => $traineeship->nomor_telepone,
                    'role_id'           => $traineeship->jobVacancy->role_id,
                    'tanggal_masuk'     => now()->format('Y-m-d'),
                ]);
            return $this->internship->create($internship->all());
        });
    }

    public function updateInternship($uuid, $request)
    {
        $old = $this->findInternship($uuid);
        $internship = collect($request)->diffAssoc($old);

        if (isset($internship["nama_lengkap"])) {
            $internship = $internship->merge([
                'nama_lengkap' => Str::title($internship['nama_lengkap']),
                'slug' => $this->generateInternshipSlug($internship['nama_lengkap']),
            ]);
        }
        
        if (isset($internship['supervisor']) && $this->employee->find($internship['supervisor'], 'nip') == null)
            throw new \Exception('supervisor tidak ditemukan atau bukan karyawan aktif', 404);

        return $this->internship->update($old, $internship->all());
    }

    public function deleteInternship($uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $internship = $this->findInternship($uuid);
            $this->deleteInternshipContract($uuid);
            $this->internship->delete($internship);
        });
    }

    public function getInternshipContracts($id)
    {
        return $this->findInternship($id)->internshipContract;
    }

    public function getInternshipContract($id)
    {
        $data = $this->findInternship($id)->internshipContract[0];
        if (!$data || $data->date_expired < now()) {
            throw new ModelNotFoundException('file mitra tidak ditemukan atau kadaluarsa', 404);
        }
        return $data;
    }

    public function createInternshipContract($id,$request)
    {
        return DB::transaction(function () use ($id, $request) {
            $internship = $this->findInternship($id);
            if (!$internship) {
                throw new \Exception('internship tidak ditemukan',404);
            }
            $contract = collect($request)->merge([
                'id'                => Uuid::uuid4()->getHex(),
                'internship_nip_id' => $internship->internship_nip,
                'role_internship'   => $internship->role_id,
                'date_expired'      => Carbon::parse($request['date_start'])->addMonths($request['durasi_kontrak']),
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
                $durasi = $data['durasi_kontrak'];
                $this->internshipContract->update($contract, [
                    'durasi_kontrak'    => $durasi,
                    'date_expired'      => Carbon::parse($data['date_start'])->addMonths($data['durasi_kontrak']),
                ]);
            }
            if (isset($data['date_start'])){
                $contract = $this->getInternshipContract($id);
                $this->internshipContract->update($contract, [
                    'date_start'    => $data['date_start'],
                    'date_expired'  => Carbon::parse($data['date_start'])->addMonths($contract['durasi_kontrak'])
                ]);
            }
        });
    }

    public function deleteInternshipContract($uuid)
    {
        $contract = $this->getInternshipContract($uuid);
        if ($contract) 
            return $this->internshipContract->delete($contract);
        throw new \Exception('Data kontrak tidak ditemukan', 404);
    }

    private function generateNip($jk)
    {
        $ke = $this->internship->getAllThisYear() + 1;
        if ((int)($ke/10 == 0)) $ke = '00'.$ke;
        else if ((int)($ke/100 == 0)) $ke = '0'.$ke;

        return (string) ( 
            '2' 
            . (string) now()->format('Ym')
            . ($jk == 'Laki-Laki' ? '1':'0')
            . $ke
        );
    }

    private function generateTraineeshipSlug($name)
    {
        $list = $this->findTraineeshipSlug($name);
        
        $slug = Str::slug($name,'_');
        if (count($list)) {
            $int = $list->sortBy('slug')->last()->slug;
            $int = explode('_', $int);
            $slug = $slug . '_' . (int) end($int) + 1;
        }
        return $slug;
    }

    private function generateInternshipSlug($name)
    {
        $list = $this->findInternship($name);
        
        $slug = Str::slug($name,'_');
        if (count($list)) {
            $int = $list->sortBy('slug')->last()->slug;
            $int = explode('_', $int);
            $slug = $slug . '_' . (int) end($int) + 1;
        }
    }
}