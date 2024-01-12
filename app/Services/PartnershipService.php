<?php
namespace App\Services;

use Carbon\Carbon;
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

    public function createPartnership($request)
    {
        return $this->partnership->create($request);
    }

    public function updatePartnership($partnershipId, $request)
    {
        return $this->partnership->update($this->getPartnership($partnershipId), $request);
    }

    public function deletePartnership($partnershipId)
    {
        return $this->partnership->delete($this->getPartnership($partnershipId));
    }

    public function getFilePartnership($mitraId)
    {
        $data = $this->getPartnership($mitraId)->filePartnership[0];
        if($data == null || $data->date_expired < now()) {
            throw new ModelNotFoundException('file mitra tidak ditemukan atau kadaluarsa', 404);
        }
        return $data;
    }

    public function getFilePartnerships($id)
    {
        return $this->getPartnership($id)->filePartnership;
    }

    public function createFilePartnership($mitraId, $request)
    {
        $mitraId = $this->getPartnership($mitraId);
        $filePartnership = collect($request)->merge([
            'mitra_id' => $mitraId->id,
            'date_expired' => Carbon::parse($request['date_start'])->addMonth($request['durasi']),
            'file_mou' => 'aaa',
            'file_moa' => 'aaa',
        ]);

        // $filePartnership = collect($request)->merge([
        //     'file_mou' => $request->file['file_mou']->storeAs('partnership/'.$mitraId, $mitraId.'_mou_'.Carbon::now().'.pdf', 'gcs'),
        //     'file_moa' => $request->file['file_moa']->storeAs('partnership/'.$mitraId, $mitraId.'_moa_'.Carbon::now().'.pdf', 'gcs'),
        // ]);
        
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
                    'date_expired' => Carbon::parse($data['date_start'])->addMonth($data['durasi'])
                ]);
            if (isset($data['date_start']))
                $fileParnership = $this->getFilePartnership($mitraId);
                $this->filePartnership->update($fileParnership, [
                    'date_start' => $data['date_start'],
                    'date_expired' => Carbon::parse($data['date_start'])->addMonth($fileParnership['durasi'])
                ]);
            // $data['file_mou']->storeAs('partnership/'.$mitraId, $mitraId.'_mou_'.Carbon::now().'.pdf', 'gcs');
            // $data['file_moa']->storeAs('partnership/'.$mitraId, $mitraId.'_moa_'.Carbon::now().'.pdf', 'gcs');
        });
    }

    public function deleteFilePartnership($mitraId)
    {
        $fileParnership = $this->getFilePartnership($mitraId);
        return $this->filePartnership->delete($fileParnership);
    }
}

?>