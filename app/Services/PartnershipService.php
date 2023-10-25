<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Internship\PartnershipRepositoryInterface;
use App\Interfaces\Internship\FilePartnershipRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

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

    public function getFilePartnerships($mitraId)
    {
        if(($data = $this->getPartnership($mitraId)->filePartnership) == null) {
            throw new ModelNotFoundException('tidak ada data yang ditemukan', 404);
        }
        return $data;
    }

    public function getFilePartnership($id, $type)
    {
        $filePartnership = $this->filePartnership->find($id);
        if ($type === 'moa') return $filePartnership->file_moa;

        if ($type === 'mou') return $filePartnership->file_mou;
        //2 tahunfull bulan 00/01 jk 3 angka internship tahun itu
        throw new InvalidArgumentException('tipe harus mou atau moa');
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
        $fileParnership = $this->getFilePartnerships($mitraId)->sortByDesc('tanggal_masuk')->first();
        return $fileParnership->update($request);
    }

    // public function deleteFilePartnership($mitraId,
}

?>