<?php

namespace App\Services;

use App\DTOs\CarInsuranceDTO;
use App\Services\Interfaces\InsuranceServiceConnectionInterface;

class BarInsuranceService implements InsuranceServiceConnectionInterface {

    public function getTariffPrice(CarInsuranceDTO $carInsuranceData)
    {
        return 'XML but Bar D:';
    }

    public function test(){
        return 'Bar';
    }

}
