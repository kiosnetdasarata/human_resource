<?php 

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function __construct(private User $user)
    {
    }

    public function getAll()
    {
        
    }

    public function find($id)
    {
        
    }

    public function findByNIP($nip)
    {
        return $this->user->where('nip_id', $nip)->firstOrFail();
    }
    
    public function create($request)
    {
        return $this->user->create($request);
    }
    
    public function setIsactive($user, $status)
    {
        return $user->update(['is_active' => $status == 0 ? 0:1]);
    }
    
    public function update($user, $request) {
        return $user->update($request);
    }
    
}

?>