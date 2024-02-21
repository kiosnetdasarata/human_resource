<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;
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

    public function getPartnership($id)
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
        return $this->partnership->create($request);
    }

    public function updatePartnership($partnership, $request)
    {
        return $this->partnership->update($partnership, $request);
    }

    public function deletePartnership($partnership)
    {
        return $this->partnership->delete($partnership);
    }

    public function getFilePartnership($idParnership)
    {
        $idParnership = $this->partnership->find($idParnership);
        $data = $idParnership->filePartnership[0];
        if(!$data || $data->date_expired < now()) {
            throw new ModelNotFoundException('file mitra tidak ditemukan atau kadaluarsa', 404);
        }
        return $data;
    }

    public function getFilePartnerships($id)
    {
        return $this->getPartnership($id)->filePartnership;
    }

    public function createFilePartnership($partnership, $request)
    {
        $nama = $partnership->mitra;
        $filePartnership = collect($request)->merge([
            'mitra_id' => $partnership->id,
            'date_expired' => Carbon::parse($request['date_start'])->addMonths($request['durasi']),
            'file_mou' => uploadToGCS($request['file_cv'],$nama.'file_mou','partnership/'. $nama),
            'file_moa' => uploadToGCS($request['file_cv'],$nama.'file_mou','partnership/'. $nama),
        ]);

        return $this->filePartnership->create($filePartnership->all());
    }

    public function updateFilePartnership($mitraId, $request)
    {
        return DB::transaction(function () use ($mitraId, $request) {
            $fileParnership = $this->getFilePartnership($mitraId);
            $data = collect($request)->diffAssoc($fileParnership);
            if (isset($data['durasi']))
                $this->filePartnership->update($fileParnership, [
                    'durasi' => $data['durasi'],
                    'date_expired' => Carbon::parse($data['date_start'])->addMonths($data['durasi'])
                ]);
            if (isset($data['date_start']))
                $fileParnership = $this->getFilePartnership($mitraId);
                $this->filePartnership->update($fileParnership, [
                    'date_start' => $data['date_start'],
                    'date_expired' => Carbon::parse($data['date_start'])->addMonths($fileParnership['durasi'])
                ]);     
        });
    }

    public function deleteFilePartnership($mitraId)
    {
        $fileParnership = $this->getFilePartnership($mitraId);
        return $this->filePartnership->delete($fileParnership);
    }
}
