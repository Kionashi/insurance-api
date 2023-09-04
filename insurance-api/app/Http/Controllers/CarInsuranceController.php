<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\DTOs\CarInsuranceDTO;
use App\Exceptions\InvalidInsuranceDataException;
use App\Services\FooInsuranceService;
use App\Services\Interfaces\InsuranceServiceConnectionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CarInsuranceController extends Controller
{
    private InsuranceServiceConnectionInterface $insuranceService;
    
    public function __construct(FooInsuranceService $fooInsuranceService)
    {
        $this->insuranceService = $fooInsuranceService;
    }

    public function checkTariffPrice(Request $request, Response $response): Response 
    {
        $validator = Validator::make($request->all(),[
            'holder' => 'required|string',
            'occasionalDriver' => 'required|in:SI,NO',
            'prevInsurance_contractDate' => 'nullable|date_format:Y-m-d',
            'prevInsurance_expirationDate' => 'nullable|date_format:Y-m-d',
            'prevInsurance_exists' => 'required|string|max:2',
            'driver_licenseDate' => 'required|date_format:Y-m-d',
            'driver_birthDate' => 'required|date_format:Y-m-d',
            'driver_idType' => 'required|in:dni,cif,nif',
            'driver_sex' => 'required|in:MUJER,HOMBRE'
        ]);
        if($validator->fails()) {
            throw new HttpException(400,$validator->errors());
        }
        try {
            $carInsuranceData = new CarInsuranceDTO($request->all());
            $price = $this->insuranceService->getTariffPrice($carInsuranceData);
        } catch (InvalidInsuranceDataException $e) {
            return $response->setContent($e->getMessage())->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        return $response->setContent($price)->setStatusCode(Response::HTTP_OK);
    }

}
