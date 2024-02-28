<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Internship\PartnershipRepositoryInterface;
use App\Interfaces\Internship\FilePartnershipRepositoryInterface;

class PartnershipService
{
    public function __construct(
        private FilePartnershipRepositoryInterface $filePartnership,
        private PartnershipRepositoryInterface $partnership,
        )
    {
    }

    public function getAllPartnership()
    {
        return $this->partnership->getAll();
    }

    public function findPartnership($id)
    {
        return $this->partnership->find($id);
    }

    public function getInternship($id,$status)
    {
        $status = Str::title($status);
        return $this->partnership->getInternship($id,$status);
    }

    public function getInternshipArchive($id,$status)
    {
        return $this->partnership->getInternshipArchive($id,$status);
    }

    public function createPartnership($request)
    {
        $data = collect($request)->put('nama_mitra', Str::title($request['nama_mitra']))->all();
        return $this->partnership->create($data);
    }

    public function updatePartnership($id, $request)
    {
        $old = $this->findPartnership($id);
        $data = collect($request)->diffAssoc($old)
                ->put('nama_mitra', Str::title($request['nama_mitra']))->all();
        return $this->partnership->update($old, $data);
    }

    public function deletePartnership($partnership)
    {
        return $this->partnership->delete($partnership);
    }

    public function getFilePartnership($idParnership)
    {
        return $this->filePartnership->find($idParnership);
    }

    public function getFilePartnerships($id)
    {
        return $this->filePartnership->getAll($id);
    }

    public function createFilePartnership($idPartnership, $request)
    {
        return DB::transaction(function () use ($idPartnership, $request) {
            if ($request['date_start'] > now()) { 
                throw new \Exception('date_start tidak boleh tanggal yang akan datang');
            }
            
            $filePartnership = $this->filePartnership->find($idPartnership);
            if ($filePartnership && $filePartnership->date_expired > now()) {
                $this->filePartnership->update($filePartnership, ['is_expired' => 1]);
            }

            $partnership = $this->partnership->find($idPartnership);
            $nama = $partnership->nama_mitra;
            $dateExpired = Carbon::parse($request['date_start'])->addMonths($request['durasi']);
            $filePartnership = collect($request)->merge([
                'mitra_id'      => $partnership->id,
                'date_expired'  => $dateExpired,
                'file_mou'      => uploadToGCS($request['file_mou'],$nama.'file_mou','partnership/'. $nama),
                'file_moa'      => uploadToGCS($request['file_moa'],$nama.'file_moa','partnership/'. $nama),
                'is_expired'    => $dateExpired < now() ? 1 : 0,
            ]);

            return $this->filePartnership->create($filePartnership->all());
        });
        
    }

    public function updateFilePartnership($idPartnership, $request)
    {
        return DB::transaction(function () use ($idPartnership, $request) {
            $old = $this->filePartnership->find($idPartnership);
            $data = collect($request)->diffAssoc($old);
            if (isset($data['durasi'])) {
                $dateExpired = Carbon::parse($data['date_start'])->addMonths($data['durasi']);
                $this->filePartnership->update($old, [
                    'durasi'        => $data['durasi'],
                    'date_expired'  => $dateExpired,                    
                    'is_expired'    => $dateExpired < now() ? 1 : 0,
                ]);
            }
            if (isset($data['date_start'])) {
                if ($request['date_start'] > now()) { 
                    throw new \Exception('date_start tidak boleh tanggal yang akan datang');
                }
                
                $dateExpired = Carbon::parse($data['date_start'])->addMonths($old['durasi']);
                $this->filePartnership->update($old, [
                    'date_start'    => $data['date_start'],
                    'date_expired'  => $dateExpired,                    
                    'is_expired'    => $dateExpired < now() ? 1 : 0,
                ]);
            }
        });
    }
}
