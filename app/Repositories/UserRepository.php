<?php 

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
    
    public function create($request)
    {
        return $this->user->create([
            'karyawan_nip' => $request['karyawan_nip'],
            'is_leader' => $request['is_leader'],
            'password' => Hash::make($request['password']),
        ]);
    }
    
    public function update($employee, $request)
    {
        
    }
    
    public function delete($employee)
    {
        
    }
    
}

?>