<?php 

namespace App\Repositories\Internship;

use App\Models\FilePartnership;
use App\Interfaces\Internship\FilePartnershipRepositoryInterface;
use App\Models\Partnership;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FilePartnershipRepository implements FilePartnershipRepositoryInterface
{
    public function __construct(
        private FilePartnership $filePartnership,
        private Partnership $partnership
    ) { }

    public function getAll($idPartnership)
    {
        $partnership = $this->partnership
                        ->with('filePartnership')
                        ->where('id', $idPartnership)
                        ->firstOrFail();
        return $partnership->filePartnership;
    }

    public function find($id)
    {
        $partnership = $this->partnership
                        ->with(['filePartnership' => fn($query) =>
                            $query->where('is_expired', 0)
                                  ->where('date_expired', '>', now()
                        )])
                        ->where('id', $id)
                        ->firstOrFail();
        return $partnership->filePartnership->first();
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