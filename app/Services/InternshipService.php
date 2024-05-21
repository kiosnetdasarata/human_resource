<?php
namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Helpers\FileHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Internship\InternshipRepositoryInterface;

class InternshipService
{
    public function __construct(
        private InternshipRepositoryInterface $internship,
        private InternshipContractService $internshipContract,
        private TraineeshipService $traineeship,
        private FileHelper $file,
    )
    {
        //
    }

    public function get()
    {
        $data =  $this->internship->getAll();
        return $data;
    }

    public function find($item, $category = null)
    {
        return $category == 'slug' ?
            $this->internship->findBySlug(Str::slug($item,'_')) : $this->internship->find($item);
    }

    public function create($idTraineenship, $request)
    {
        return DB::Transaction(function() use ($idTraineenship, $request) {
            $traineeship = $this->traineeship->find($idTraineenship);

            $data = collect($traineeship)->merge($request)->merge([
                'id'                => Uuid::uuid4()->getHex(),
                'internship_nip'    => $this->generateNip($traineeship->jk),
                'slug'              => $this->generateInternshipSlug($traineeship->nama_lengkap),
                'no_tlpn'           => $traineeship->nomor_telepone,
                'role_id'           => $traineeship->jobVacancy->role_id,
                'tanggal_masuk'     => now()->format('Y-m-d'),
            ])->all();

            $this->internship->create($data);

            $this->traineeship->updateStatus($idTraineenship, 'Lolos');
        });
    }

    public function update($uuid, $request)
    {
        $old = $this->find($uuid);
        $data = collect($request)->diffAssoc($old);

        if (isset($data["nama_lengkap"])) {
            $data->put('nama_lengkap', Str::title($data['nama_lengkap']))
                 ->put('slug', $this->generateInternshipSlug($data['nama_lengkap']));
        }

        $this->internship->update($old, $data->all());
        return $this->find($uuid);
    }

    public function delete($uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $this->internshipContract->deleteExistingContract($uuid);
            $this->internship->delete($this->find($uuid));
        });
    }

    private function generateNip($jk)
    {
        $count = $this->internship->getAllThisYear() + 1;
        if ($count < 10) $count = '00'.$count;
        else if ($count < 100) $count = '0'.$count;

        return (string) (
            '2'
            . (string) now()->format('Ym')
            . ($jk == 'Laki-Laki' ? '1':'0')
            . $count
        );
    }

    private function generateInternshipSlug($name)
    {
        $list = $this->find($name, 'slug');

        $slug = Str::slug($name,'_');
        if (count($list)) {
            $int    = $list->sortBy('slug')->last()->slug;
            $int    = explode('_', $int);
            $slug   = $slug . '_' . (int) end($int) + 1;
        }
        return $slug;
    }
}
