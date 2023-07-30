<?php 

namespace App\Services;

use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\EmployeeRepositoryInterface;


class EmployeeService
{
    public function __construct(private EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
    }

    public function getAll()
    {
        return $this->employeeRepositoryInterface->getAll();
    }

    public function find($id)
    {
        return $this->employeeRepositoryInterface->find($id);
    }

    public function store($request)
    {
        $request['tgl_lahir'] = date_format(date_create_from_format('d/m/Y', $request['tgl_lahir']), 'Y-m-d');

        $validate = Validator::make($request, [
            'nik' => 'unique|employees',
            'no_tlpn' => 'unique|employees'
        ]);
        
        return $this->employeeRepositoryInterface->create($request);
        
    }

    public function update($id, EmployeeRequest $request)
    {
        $employee = $this->employeeRepositoryInterface->find($id);
        $request['tgl_lahir'] = date_format(date_create_from_format('d/m/Y', $request['tgl_lahir']), 'Y-m-d');
        
        return $this->employeeRepositoryInterface->update($employee, $request);
    }

    public function delete($data)
    {
        return $this->employeeRepositoryInterface->delete($data);
    }
}

?>