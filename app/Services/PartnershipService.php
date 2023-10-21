<?php
namespace App\Services;

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
        return $this->filePartnership->find($mitraId);
    }

    public function createFilePartnership($request)
    {
        $mitraId = $request['mitra_id'];
        $filePartnership = collect($request)->merge([
            'file_mou' => 'aaa',
            'file_moa' => 'aaa',
        ]);

        // $filePartnership = collect($request)->merge([
        //     'file_mou' => $request->file['file_mou']->storeAs('partnership/'.$mitraId, $mitraId.'_mou_'.Carbon::now().'.pdf', 'gcs'),
        //     'file_moa' => $request->file['file_moa']->storeAs('partnership/'.$mitraId, $mitraId.'_moa_'.Carbon::now().'.pdf', 'gcs'),
        // ]);
        
        return $this->partnership->create($filePartnership->all());
    }

    public function updateFilePartnership($mitraId, $request)
    {
        $fileParnership = $this->getFilePartnership($mitraId)->sortByDesc('tanggal_masuk')->first();
        return $fileParnership->update($request);
    }

    // public function deleteFilePartnership($mitraId,
}

?>