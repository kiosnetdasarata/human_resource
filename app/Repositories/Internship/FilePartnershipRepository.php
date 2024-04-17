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
        return $this->partnership->find($idPartnership)->filesHistory;
    }

    public function find($id)
    {
        return $this->partnership->find($id)->file;
    }

    public function create($request)
    {
        return $this->filePartnership->create($request);
    }

    public function update($filePartnership, $request)
    {
        return $filePartnership->update($request);
    }
}
