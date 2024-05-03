<?php
namespace App\Services;

use Carbon\Carbon;
use LogicException;
use App\Helpers\FileHelper;
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
        private FileHelper $file,
    )
    {
        //
    }

    public function get()
    {
        return $this->partnership->getAll();
    }

    public function find($id)
    {
        return $this->partnership->find($id);
    }

    public function getInternship($id, $status)
    {
        if ($status != 'magang' && $status != 'internship') {
            throw new ModelNotFoundException();
        }

        $status = Str::title($status);
        return $this->partnership->getInternship($id,$status);
    }

    public function getInternshipArchive($id,$status)
    {
        if ($status != 'magang' && $status != 'internship') {
            throw new ModelNotFoundException();
        }

        return $this->partnership->getInternshipArchive($id,$status);
    }

    public function create($request)
    {
        $data = collect($request)->put('nama_mitra', Str::title($request['nama_mitra']))->all();
        return $this->partnership->create($data);
    }

    public function update($id, $request)
    {
        $old = $this->find($id);
        $data = collect($request)->diffAssoc($old);

        if (isset($data['nama_mitra'])) {
            $data->put('nama_mitra', Str::title($request['nama_mitra']))->all();
        }
        return $this->partnership->update($old, $data);
    }

    public function delete($partnership)
    {
        return $this->partnership->delete($partnership);
    }

    public function findFile($idParnership)
    {
        $data = $this->filePartnership->find($idParnership);

        if ($data->is_expired || $data->date_expired < now()) throw new ModelNotFoundException();
        return $data;
    }

    public function getFile($id)
    {
        $data = $this->filePartnership->getAll($id);
        return $data;
    }

    public function createFile($idPartnership, $request)
    {
        return DB::transaction(function () use ($idPartnership, $request) {
            if ($request['date_start'] > now()) {
                throw new LogicException('date_start tidak boleh tanggal yang akan datang');
            }

            $old = $this->filePartnership->find($idPartnership);
            if ($old) {
                $this->filePartnership->update($old, ['is_expired' => 1]);
            }

            $partnership = $this->partnership->find($idPartnership);
            $nama = $partnership->nama_mitra;
            $dateExpired = Carbon::parse($request['date_start'])->addMonths($request['durasi']);

            $filePartnership = collect($request)->merge([
                'mitra_id'      => $partnership->id,
                'date_expired'  => $dateExpired,
                'file_mou'      => $this->file->uploadToGCS($request['file_mou'],$nama.'file_mou','partnership/'. $nama),
                'file_moa'      => $this->file->uploadToGCS($request['file_moa'],$nama.'file_moa','partnership/'. $nama),
                'is_expired'    => $dateExpired < now() ? 1 : 0,
            ]);

            return $this->filePartnership->create($filePartnership->all());
        });
    }

    public function updateFile($idPartnership, $request)
    {
        return DB::transaction(function () use ($idPartnership, $request) {
            $old = $this->filePartnership->find($idPartnership);
            $data = collect($request)->diffAssoc($old);

            $date_start = isset($data['date_start']) ? $data['date_start'] : $old['date_start'];
            $durasi_kontrak = isset($data['durasi_kontrak']) ? $data['durasi_kontrak'] : $old['durasi_kontrak'];

            $date_expired = Carbon::parse($date_start)->addMonths($durasi_kontrak);

            $data->put('is_expired', $date_expired < now() ? 1 : 0);
            $data->put('date_expired', $date_expired);


            $this->filePartnership->update($old, $data);
        });
    }
}
