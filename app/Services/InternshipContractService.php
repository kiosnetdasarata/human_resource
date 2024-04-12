<?php

namespace App\Services;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Internship\InternshipContractRepositoryInterface;
use App\Interfaces\Internship\InternshipRepositoryInterface;

class InternshipContractService
{
    public function __construct(
        private InternshipContractRepositoryInterface $internshipContract,
        private InternshipRepositoryInterface $internship,
        private FileHelper $file,
    )
    {
        //
    }

    public function get($id)
    {
        return $this->internshipContract->getAll($id);
    }

    public function find($id)
    {
        $data = $this->internshipContract->find($id);
        if ($data->is_expired || $data->date_expired < now()) throw new ModelNotFoundException();
        return $data;
    }

    public function create($id,$request)
    {
        return DB::transaction(function () use ($id, $request) {
            $this->deleteExistingContract($id);

            $internship = $this->internship->find($id);

            $dateExpired = Carbon::parse($request['date_start'])->addMonths($request['durasi_kontrak']);
            $data = collect($request)->merge([
                'id'                => Uuid::uuid4()->getHex(),
                'internship_nip_id' => $internship->internship_nip,
                'role_internship'   => $internship->role_id,
                'date_expired'      => $dateExpired,
                'is_expired'        => $dateExpired < now() ? 1 : 0,
            ])->all();

            $this->internshipContract->create($data);
            $this->internship->update($internship, ['durasi' => $data['durasi_kontrak']]);
        });
    }

    public function update($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $old = $this->get($id);
            $data = collect($request)->diffAssoc($old);

            $date_start = isset($data['date_start']) ? $data['date_start'] : $old['date_start'];
            $durasi_kontrak = isset($data['durasi_kontrak']) ? $data['durasi_kontrak'] : $old['durasi_kontrak'];

            $date_expired = Carbon::parse($date_start)->addMonths($durasi_kontrak);

            $data = $data->merge([
                'date_expired' => $date_expired,
                'is_expired'   => $date_expired < now() ? 1 : 0,
            ])->all();

            $this->internshipContract->update($old, $data);

            if ($data['is_expired']) {
                $this->internshipContract->delete($old);
            }
        });
    }

    public function deleteExistingContract($id)
    {
        $internshipContract = $this->internshipContract->find($id);
        if ($internshipContract) {
            $this->internshipContract->update($internshipContract, ['is_expired' => 1]);
            $this->internshipContract->delete($internshipContract);
        }
    }
}
