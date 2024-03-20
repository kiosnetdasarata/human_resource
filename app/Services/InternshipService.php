<?php
namespace App\Services;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Helpers\FileHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\ArchiveJobApplicant;
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
        private ArchiveJobApplicant $archiveJobApplicant,
        private PartnershipRepositoryInterface $partnership,
        private InterviewPointRepositoryInterface $interviewPoint,
        private EmployeeRepositoryInterface $employee,
        private JobVacancyRepositoryInterface $jobVacancy,
        private FileHelper $file,
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

    public function findByVacancy($id) 
    {
        return $this->jobVacancy->getTraineeships($id);
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
            'tanggal_lamaran'   => now()->format('Y-m-d'),
            'file_cv'           => $this->file->uploadToGCS($request['file_cv'], $slug .'_'. $jobVacancy['role']['nama_jabatan'] . '_cv','traineeship/cv')
        ]);

        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] >= date('Y')) {
            throw new \Exception('tahun lulus tidak valid', 422);
        }
        
        return $this->traineeship->create($traineeship->all());
    }

    public function updateStatus($id, $status)
    {
        $old = $this->traineeship->find($id);
        if (!$old) throw new ModelNotFoundException('traineeship tidak ditemukan');
        return DB::transaction(function () use ($old, $status) {
            $oldStatus = $old->status_tahap;
            if ($status == 'Assesment' && $oldStatus != 'FU') {
                throw new \Exception ('status jobApplicant tidak valid', 422);
            }
            $this->traineeship->update($old, ['status_tahap' => $status]);

            if ($status == 'Tolak' ||$status == 'Lolos') {
                $this->deleteTraineeship($old);
            }
        });            
    }

    private function deleteTraineeship($traineeship)
    {
        $jobVacancy = $this->jobVacancy->find($traineeship['vacancy_id']);
        $data = collect($traineeship)->merge([
            'tanggal_lamaran' => $traineeship->created_at,
            'keterangan' => 'dihapus karena job vacancy terhapus',
            'status_lamaran' => $traineeship->status_tahap,
            'is_intern' => 1,
            'no_tlpn' => $traineeship->nomor_telepone,
            'role_id' => $jobVacancy->role_id,
        ]);
        $this->archiveJobApplicant->create($data->all());
        $this->traineeship->delete($traineeship);
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
                $link = $this->file->uploadToGCS($request['file_cv'], $old->slug .'_'. $old->role->nama_jabatan . '_cv', 'traineeship/file_cv');
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
            throw new ModelNotFoundException('poin tidak ditemukan');
        }
        return $this->interviewPoint->update($poin, $request);
    }

    public function deleteInterviewPoint($idTraineenship)
    {
        return DB::transaction(function () use ($idTraineenship) {
            $traineeship = $this->traineeship->find($idTraineenship);
            if (!$traineeship->interviewPoint) {
                throw new ModelNotFoundException('poin tidak ditemukan');
            }
            $this->traineeship->update($traineeship, ['hr_point_id' => null]);
            $this->interviewPoint->delete($traineeship->interviewPoint);
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
            $traineeship = $this->findTraineeship($idTraineenship);
            $internship = collect($traineeship)->merge($request)->merge([
                    'id'                => Uuid::uuid4()->getHex(),
                    'internship_nip'    => $this->generateNip($traineeship->jk),
                    'slug'              => $this->generateInternshipSlug($traineeship->nama_lengkap),
                    'no_tlpn'           => $traineeship->nomor_telepone,
                    'role_id'           => $traineeship->jobVacancy->role_id,
                    'tanggal_masuk'     => now()->format('Y-m-d'),
                ]);
            $this->internship->create($internship->all());
            $this->updateStatus($idTraineenship, 'Lolos');
        });
    }

    public function updateInternship($uuid, $request)
    {
        $old = $this->findInternship($uuid);
        $internship = collect($request)->diffAssoc($old);

        if (isset($internship["nama_lengkap"])) {
            $internship = $internship->merge([
                'nama_lengkap'  => Str::title($internship['nama_lengkap']),
                'slug'          => $this->generateInternshipSlug($internship['nama_lengkap']),
            ]);
        }
        
        if (isset($internship['supervisor']) && $this->employee->find($internship['supervisor'], 'nip') == null)
            throw new \Exception('supervisor tidak ditemukan atau bukan karyawan aktif', 404);
        else {
            $this->internship->update($old, $internship->all());
            return $this->findInternship($uuid);
        }
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
        return $this->internshipContract->getAll($id);
    }

    public function getInternshipContract($id)
    {
        return $this->internshipContract->find($id);
    }

    public function createInternshipContract($id,$request)
    {
        return DB::transaction(function () use ($id, $request) {
            $internship = $this->findInternship($id);
            if (!$internship) {
                throw new ModelNotFoundException("internship tidak ditemukan");
            }
            $internshipContract = $this->internshipContract->find($id);
            if ($internshipContract) {
                $this->internshipContract->update($internshipContract, ['is_expired' => 1]);
                $this->internshipContract->delete($internshipContract);
            }
            $date_expired = Carbon::parse($request['date_start'])->addMonths($request['durasi_kontrak']);
            $contract = collect($request)->merge([
                'id'                => Uuid::uuid4()->getHex(),
                'internship_nip_id' => $internship->internship_nip,
                'role_internship'   => $internship->role_id,
                'date_expired'      => $date_expired,
                'is_expired'        => $date_expired < now() ? 1 : 0,
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
                $date_expired = Carbon::parse($request['date_start'])->addMonths($request['durasi_kontrak']);
                $data = $data->merge([
                    'durasi_kontrak' => $data['durasi_kontrak'],
                    'date_expired'   => $date_expired,
                    'is_expired'     => $date_expired < now() ? 1 : 0,
                ]);
            }
            if (isset($data['date_start'])){
                $date_expired = Carbon::parse($request['date_start'])->addMonths($request['durasi_kontrak']);
                $data = $data->merge([
                    'date_start'    => $data['date_start'],
                    'date_expired'  => $date_expired,
                    'is_expired'    => $date_expired < now() ? 1 : 0,
                ]);
            }
            $this->internshipContract->update($contract, $data->all());
            if ($data['is_expired']) $this->internshipContract->delete($contract);
        });
    }

    public function deleteInternshipContract($uuid)
    {
        $contract = $this->getInternshipContract($uuid);
        if ($contract) 
            return $this->internshipContract->delete($contract);
        throw new ModelNotFoundException('kontrak tidak ditemukan');
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
        $list = $this->findInternship($name, 'slug');
        
        $slug = Str::slug($name,'_');
        if (count($list)) {
            $int = $list->sortBy('slug')->last()->slug;
            $int = explode('_', $int);
            $slug = $slug . '_' . (int) end($int) + 1;
        }
        return $slug;
    }
}