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
    
    public function createTraineeship($request)
    {
        $jobVacancy = $this->jobVacancy->find($request['vacancy_id']);
        $age = Carbon::parse($request['tanggal_lahir'])->diffInYears(Carbon::now());
        
        if (Carbon::now() > $jobVacancy->close_date || Carbon::now() < $jobVacancy->open_date)
            throw new \Exception('vacancy belum dibuka / sudah ditutup');
        if ($age > $jobVacancy->max_umur || $age < $jobVacancy->min_umur)
            throw new \Exception('umur tidak valid');
        
        $traineeship = collect($request)->merge([
            'file_cv' => 'filenya ada',
            'tanggal_lamaran' => Carbon::now(),
            'status_traineeship' => 'Screening',
            'slug' => Str::slug($request['nama_lengkap'], '_'),
        ]);

        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] >= date('Y')) {
            throw new \Exception('tahun lulus tidak valid');
        }
        
        // $traineeship->put('file_cv', $request['file_cv']->storeAs('traineeship/cv', $traineeship['slug'].'_cv.pdf', 'gcs'));
        $this->traineeship->create($traineeship->all());
        return true;
    }

    public function updateTraineeship($slug, $request) 
    {
        return DB::transaction(function() use ($slug, $request) {
            $old = $this->findTraineeship($slug);
            $traineeship = collect($request)->diffAssoc($old);
            if (isset($traineeship['file_cv'])) {
                $traineeship->put('file_cv', 'test_cv');
                // $traineeship->put('file_cv', $request->file['file_cv']->storeAs('traineeship/cv', $traineeship['uuid'].'.pdf', 'gcs'));
            }
            if(isset($traineeship['nama_lengkap'])) {
                $traineeship->put('slug', Str::slug($traineeship['nama_lengkap']));
            }
            if (isset($request['status_tahap'])) {
                $oldStatus = $old->status_tahap;
                $newStatus = $request['status_tahap'];
                if ($newStatus == 'Assesment' && $oldStatus == null) {
                    throw new \Exception ('traineeship tidak memiliki hr point');
                } elseif ($newStatus == 'Lolos') {
                    if ($oldStatus != 'Assesment')
                        throw new \Exception ('status traineeship tidak valid');
                    $this->createInternship($old->id, $request);
                }
            }

            $this->traineeship->update($old, $traineeship->all());

            if ($newStatus == 'Tolak' || $newStatus == 'Lolos') {
                $this->traineeship->delete($old);
            }
        });
    }

    public function deleteTraineeship($slug)
    {
        return $this->traineeship->delete($this->findTraineeship($slug));;
    }

    public function addInterviewPoint($id, $request)
    {
        return DB::transaction(function ()  use ($id, $request) {
            $traineeship = $this->traineeship->find($id);
            if ($traineeship->hr_point_id != null) {
                throw new \Exception('Traineeship ini sudah memiliki interview point pada id '. $traineeship->hr_point_id);
            }
            $this->interviewPoint->create($request);
            $traineeship->update(['hr_point_id' => $this->interviewPoint->latest()->id]);
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
            throw new ModelNotFoundException('Traineeship ini belum memiliki interview point');
        }
        return $this->interviewPoint->update($poin, $request);
    }

    public function deleteInterviewPoint($idTraineenship)
    {
        $poin = $this->traineeship->find($idTraineenship)->interviewPoint;
        if ($poin == null) {
            throw new ModelNotFoundException('Traineeship ini belum memiliki interview point');
        }
        return $this->interviewPoint->delete($poin);
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
            $traineeship = $this->findTraineeship($idTraineenship, true);
            if ($traineeship['hr_point_id'] == null) {
                throw new \Exception('traineeship tidak memiliki interview point', 404);
            }
            $internship = collect($traineeship)->merge([
                    'id' => Uuid::uuid4()->getHex(),
                    'internship_nip' => $this->generateNip($request['tanggal_masuk'], $traineeship['jk']),
                    'slug' => Str::slug($traineeship['nama_lengkap'], '_') . (($count = count($this->findInternship($traineeship['nama_lengkap'], 'slug'))) > 0 ? '_' . $count+1 : ''),
                    'file_cv' => 'ffgui',
                    'no_tlpn' => $traineeship['nomor_telepone']
                ]);
            $internship = $internship->merge($request);
            if (isset($internship['mitra_id'])) {
                $file = $this->partnership->find($internship['mitra_id'])->filePartnership[0];
                if ($file == null || $file->date_expired < Carbon::now())
                    throw new \Exception('file mitra tidak ditemukan atau kadaluarsa');
            }
            // $internship->put('file_cv', $internship['file_cv']->storeAs('internship/cv', $internship['uuid'].'.pdf', 'gcs'));
            $this->internship->create($internship->all());
            $this->traineeship->delete($traineeship);
        });
    }

    public function updateInternship($uuid, $request)
    {

        $old = $this->findInternship($uuid);
        $internship = collect($request)->diffAssoc($old);

        if (isset($internship["nama_lengkap"]))
            $internship->put(
                'slug', Str::slug($internship["nama_lengkap"]) 
                    . (($count = count($this->findInternship($internship["nama_lengkap"], 'slug'))) > 1 ? '-' . $count + 1 : ''),
            );

        if (isset($internship['supervisor']) && $this->employee->find($internship['supervisor'], 'nip') == null)
            throw new \Exception('supervisor bukan karyawan aktif');

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
        if($data == null || $data->date_expired < Carbon::now()) {
            throw new ModelNotFoundException('file mitra tidak ditemukan atau kadaluarsa', 404);
        }
        return $data;
    }

    public function createInternshipContract($id,$request)
    {
        return DB::transaction(function () use ($id, $request) {
            $internship = $this->findInternship($id);
            if ($internship == null) {
                throw new \Exception('internship tidak ditemukan mungkin sudah dihapus');
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
    }

    private function generateNip($tglKerja, $jk)
    {
        $ke = $this->internship->getAllThisYear() + 1;
        if ((int)($ke/10) == 0) $ke = '00'.$ke;
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