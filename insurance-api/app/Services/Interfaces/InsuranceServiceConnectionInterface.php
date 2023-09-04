<?php
namespace App\Services\Interfaces;

use App\DTOs\CarInsuranceDTO;

interface InsuranceServiceConnectionInterface {
    public function getTariffPrice(CarInsuranceDTO $carInsuranceData);

    public function test();
}