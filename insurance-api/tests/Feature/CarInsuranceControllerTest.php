<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\FooInsuranceService;
use App\Http\Controllers\CarInsuranceController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CarInsuranceControllerTest extends TestCase
{
    private Request $request;
    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->response = new Response();
        // Create base request with valid data
        $this->request = new Request();
        $this->request->merge([
            'holder' => 'CONDUCTOR_PRINCIPAL',
            'occasionalDriver' => 'NO',
            'prevInsurance_exists' => 'NO',
            'driver_licenseDate' => '2020-01-01',
            'driver_birthDate' => '1990-06-15',
            'driver_idType' => 'dni',
            'driver_sex' => 'HOMBRE'
        ]);

    }

    public function testCheckTariffPriceReturnsOkWithValidData(): void
    {
        $service = $this->mock(FooInsuranceService::class, function (MockInterface $service) {
            $service->shouldReceive('getTariffPrice')->once()->andReturn(100);
        });

        $controller = new CarInsuranceController($service);
        
        $response = $controller->checkTariffPrice($this->request, $this->response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(100, $response->getContent());
    }
    
    public function testCheckTariffPriceThrowsExceptionOnEmptyRequest(): void
    {
        $service = $this->mock(FooInsuranceService::class, function (MockInterface $service) {
            $service->shouldReceive('getTariffPrice')->never();
        });
        $controller = new CarInsuranceController($service);
        $response = new Response();
        $request = new Request();
        $this->expectExceptionMessage('The holder field is required.');
        $this->expectExceptionMessage('The occasional driver field is required.');
        $this->expectExceptionMessage('The prev insurance exists field is required.');
        $this->expectExceptionMessage('The driver license date field is required.');
        $this->expectExceptionMessage('The driver birth date field is required.');
        $this->expectExceptionMessage('The driver id type field is required.');
        $this->expectExceptionMessage('The driver sex field is required.');
        $this->expectException(HttpException::class);
        
        $controller->checkTariffPrice($request, $response);
    }

    public function testCheckTariffPriceCapturesInvalidDates(): void
    {
        $service = $this->mock(FooInsuranceService::class, function (MockInterface $service) {
            $service->shouldReceive('getTariffPrice')->never();
        });
        $controller = new CarInsuranceController($service);
        $this->request->merge([
            'prevInsurance_contractDate' => '2020-15-06', 
            'prevInsurance_expirationDate' => '15/06/1990',
            'driver_licenseDate' => '2020-01-01T00:00:00',
            'driver_birthDate' => '1990-06-15UTC00:09:00',
        ]); 
        $this->expectExceptionMessage('The prev insurance contract date field must match the format Y-m-d.');
        $this->expectExceptionMessage('The prev insurance expiration date field must match the format Y-m-d.');
        $this->expectExceptionMessage('The driver licesnse date field must match the format Y-m-d.');
        $this->expectExceptionMessage('The driver birth date field must match the format Y-m-d.');
        $this->expectException(HttpException::class);
        
        $controller->checkTariffPrice($this->request, $this->response);
    }

    public function testCheckTariffPriceCapturesInvalidEnums(): void
    {
        $service = $this->mock(FooInsuranceService::class, function (MockInterface $service) {
            $service->shouldReceive('getTariffPrice')->never();
        });
        $controller = new CarInsuranceController($service);
        $this->request->merge([
            'occasionalDriver' => '2020-15-06', 
            'driver_idType' => 'passport',
            'driver_sex' => 'H',
        ]); 
        $this->expectExceptionMessage('The selected occasional driver is invalid.');
        $this->expectExceptionMessage('The selected driver id type is invalid.');
        $this->expectExceptionMessage('The selected driver sex is invalid.');
        $this->expectException(HttpException::class);
        
        $controller->checkTariffPrice($this->request, $this->response);
    }
}
