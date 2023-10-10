<?php
namespace App\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\Internship\InternshipRepositoryInterface;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use App\Interfaces\Internship\InternshipContractRepositoryInterface;
use App\Interfaces\Internship\PartnershipRepositoryInterface;

class InternshipService
{
    public function __construct(
        private InternshipRepositoryInterface $internship,
        private InternshipContractRepositoryInterface $internshipContract,
        private TraineeshipRepositoryInterface $traineeship,
        private RoleRepositoryInterface $role,
        private PartnershipRepositoryInterface $partnership,
        )
    {
    }

    public function getAllTraineeship()
    {
        return $this->traineeship->getAll();
    }

    public function findTraineeship($name, $withtrashes = false)
    {
        $slug = Str::slug($name,'_');
        if ($withtrashes) return $this->traineeship->findWithTrashes($slug);
        return $this->traineeship->find($slug);
    }
    
    public function createTraineeship($request)
    {
        $traineeship = collect($request)->merge([
            'slug' => Str::slug($request['nama_lengkap'], '_') . (($count = count($this->findTraineeship($request['nama_lengkap']), true)) > 0 ? '_' . $count+1 : ''),
            'divisi_id' => $this->role->find($request['role_id'])->divisi_id,
            'file_cv' => 'dgjghdsdsfdsfgds',
        ]);
        
        // $traineeship->put('file_cv', $request['file_cv']->storeAs('traineeship/cv', $traineeship['slug'].'_cv.pdf', 'gcs'));

        $this->traineeship->create($traineeship->all());
        return true;
    }

    public function updateTraineeship($slug, $request) 
    {
        $old = $this->findInternship($slug);
        $traineeship = collect($request)->diffAssoc($old);

        if (isset($traineeship["nama_lengkap"]))
            $traineeship->put(
                'slug', Str::slug($traineeship["nama_lengkap"]) 
                    . (($count = count($this->findtraineeship($traineeship["nama_lengkap"], true))) > 1 ? '-' . $count + 1 : ''),
            );

        if ($traineeship->file('file_cv') == null) {
            // $traineeship->put('file_cv', $request->file['file_cv']->storeAs('traineeship/cv', $traineeship['uuid'].'.pdf', 'gcs'));
        }
        $this->traineeship->update($old, $traineeship->all());
        return true;
    }

    public function deleteTraineeship($slug)
    {
        return $this->traineeship->delete($this->findTraineeship($slug));;
    }

    public function getAllInternship()
    {
        return $this->internship->getAll();
    }

    public function findInternship($item, $category = null)
    {
        if ($category == 'slug') {
            return $this->internship->findBySlug(Str::slug($item,'_'));
        }
        return $this->internship->find($item);
    }

    public function createInternship($request)
    {     
        $internship = collect($request)->merge([
                'uuid' => Uuid::uuid4()->getHex(),
                'internship_nip' => $this->generateNip($request['tanggal_masuk']),
                'slug' => Str::slug($request['nama_lengkap'], '_') . (($count = count($this->findInternship($request['nama_lengkap'], 'slug'))) > 0 ? '_' . $count+1 : ''),
                'divisi' => $this->role->find($request['role_id'])->divisi_id,
                'durasi' => 0,
                'file_cv' => 'ffgui',
            ]);
        if(isset($internship['mitra_id']) && $this->partnership->find($internship['mitra_id'])->filePartnership() == null) {
            throw new \Exception('file mitra tidak ditemukan.');
        }
        // $internship->put('file_cv', $request->file['file_cv']->storeAs('internship/cv', $internship['uuid'].'.pdf', 'gcs'));

        $this->internship->create($internship->all());
        return true;
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

        if (isset($internship['file_cv'])) {
            $internship->put('file_cv', $request['file_cv']->storeAs('internship/cv', $internship['uuid'].'.pdf', 'gcs'));
        }
        $this->internship->update($old, $internship->all());
        return true;
    }

    public function deleteInternship($uuid)
    {
        $internship = $this->findInternship($uuid);
        $contract = $internship->internshipContract();
        if ($contract != null && $contract > Carbon::now())
            throw new \Exception('tidak bisa menghapus internship karena kontrak belum berakhir');

        DB::beginTransaction();

        try {
            $internship = $this->findInternship($uuid);
            
            $this->internship->delete($internship);
            $this->deleteInternshipContract($uuid);
            DB::commit();
            return true;
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function findInternshipContract($uuid)
    {
        return $this->internshipContract->find($uuid);
    }

    public function createInternshipContract($request)
    {
        DB::beginTransaction();
        $internship = $this->findInternship($request['internship_id'], 'id');

        try {
            $contract = collect($request)->merge([
                'divisi_internship' => $internship->divisi_id,
                'role_internship' => $internship->role_id,
                'date_expired' => Carbon::parse($request['date_start'])->addMonth($request['durasi_kontrak']),
            ]);
            $this->internshipContract->create($request);
            $this->internship->update($internship, ['durasi' => $contract['durasi_kontrak']]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteInternshipContract($uuid)
    {
        $contract = $this->findInternshipContract($uuid);
        if ($contract->date_expired < Carbon::now()) {
            throw new \Exception('tidak bisa menghapus karena kontrak belum berakhir');
        }
        
        $this->internshipContract->delete($contract);
        return true;
    }

    private function generateNip($tglKerja)
    {
        return (int) (
            date_create_from_format('Y-m-d', $tglKerja)->format('ym')
            . count($this->getAllInternship())
        );
    }
}

?>