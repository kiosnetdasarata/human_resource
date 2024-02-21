<?php 

namespace App\Repositories\Internship;

use App\Models\FilePartnership;
use App\Interfaces\Internship\FilePartnershipRepositoryInterface;
use App\Models\Partnership;

class FilePartnershipRepository implements FilePartnershipRepositoryInterface
{
    public function __construct(
        private FilePartnership $filePartnership,
        private Partnership $partnership
    ) { }

    public function getAll($idPartnership)
    {
        $partnership = $this->partnership->with(['file' => function($query) {
            $query->sortBy('created_at');
        }])->firstOrFail();
        return $partnership->file;
    }

    public function find($id)
    {
        $partnership = $this->partnership->with(['file' => function($query) {
            $query->sortBy('created_at')->last();
        }])->firstOrFail();
        return $partnership->file;
    }

    public function findByMitra($mitraid)
    {
        return $this->filePartnership->where('mitra_id', $mitraid);
    }

    public function create($request)
    {
        return $this->filePartnership->create($request);
    }

    public function update($filePartnership, $request)
    {
        return $filePartnership->update($request);
    }

    public function delete($filePartnership)
    {
        return $filePartnership->delete();
    }

}