<?php 

namespace App\Repositories\Internship;

use App\Models\FilePartnership;
use App\Interfaces\Internship\FilePartnershipRepositoryInterface;


class FilePartnershipRepository implements FilePartnershipRepositoryInterface
{
    public function __construct(private FilePartnership $filePartnership)
    {
    }

    public function getAll()
    {
        return $this->filePartnership->get();
    }

    public function find($mitraid)
    {
        return $this->filePartnership->where('mitra_id', $mitraid)->sortByDesc()->first();
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

?>