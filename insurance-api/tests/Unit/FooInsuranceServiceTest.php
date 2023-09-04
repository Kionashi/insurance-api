<?php

namespace Tests\Unit;
use App\DTOs\CarInsuranceDTO;
use App\Exceptions\InvalidInsuranceDataException;
use App\Services\FooInsuranceService;

use PHPUnit\Framework\TestCase;
use DateTime;

class FooInsuranceServiceTest extends TestCase
{
    private CarInsuranceDTO $carInsuranceData;
    private FooInsuranceService $fooInsuranceService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a carInsuranceData with default valid data.
        $this->carInsuranceData = new CarInsuranceDTO();
        $this->carInsuranceData->holder = 'CONDUCTOR_PRINCIPAL';
        $this->carInsuranceData->occasionalDriver = 'NO';
        $this->carInsuranceData->prevInsurance_exists = 'SI';
        $this->carInsuranceData->prevInsurance_contractDate = '2003-03-08';
        $this->carInsuranceData->prevInsurance_expirationDate = '2008-06-08';
        $this->carInsuranceData->driver_idType = 'dni';
        $this->carInsuranceData->driver_licenseDate = '2023-03-08';
        $this->carInsuranceData->driver_birthDate = '1980-03-08';
        $this->carInsuranceData->driver_sex = 'MUJER';

        $this->fooInsuranceService = new FooInsuranceService();
    }

    public function testValidateInsuranceDataReturnsTrueWithValidData() 
    {
        $isValid = $this->fooInsuranceService->validateInsuranceData($this->carInsuranceData);
        $this->assertTrue($isValid);
    }

    public function testValidateInsuranceDataThrowsExceptionWithInvalidData() 
    {
        $this->expectException(InvalidInsuranceDataException::class);
        $this->carInsuranceData->holder = 'CONDUCTOR_PRINCIPAL';
        $this->carInsuranceData->holder_profession = 'Generador de excepciones';
        $this->fooInsuranceService->validateInsuranceData($this->carInsuranceData);

    }

    public function testMapdtoReturnsArray()
    {
        $map = $this->fooInsuranceService->mapDTO($this->carInsuranceData);
        $this->assertEquals(is_array($map),True);
    }

    public function testMapdtoMappingsAreCorrect()
    {
        $map = $this->fooInsuranceService->mapDTO($this->carInsuranceData);
        $this->assertEquals($map['Datos']['DatosGenerales']['CondPpalEsTomador'],'S');
        $this->assertEquals($map['Datos']['DatosGenerales']['ConductorUnico'],'S');
        $this->assertEquals($map['Datos']['DatosGenerales']['AnosSegAnte'],5); // 2008 - 2003 = 5 years
        $this->assertEquals($map['Datos']['DatosGenerales']['NroCondOca'],0);
        $this->assertEquals($map['Datos']['DatosGenerales']['SeguroEnVigor'],'N');

        $this->assertEquals($map['Datos']['DatosConductor']['CodDocumento'],'DNI');
        $this->assertEquals($map['Datos']['DatosConductor']['FecCarnet'],'2023-03-08T00:00:00');
        $this->assertEquals($map['Datos']['DatosConductor']['Sexo'],'M');
    }

    public function testMapdtoUsesCurrentDateWhenPrevInsuranceStillActive()
    {
        $currentDate = new DateTime();
        $expectedDifference = $currentDate->format('Y') - 2003;
        // Add 2 years to the current date so the previous insurance will always be active
        $futureDate = $currentDate->modify('+2 years')->format('Y-m-d');
        $this->carInsuranceData->prevInsurance_expirationDate = $futureDate; 

        $map = $this->fooInsuranceService->mapDTO($this->carInsuranceData);
        $this->assertEquals($map['Datos']['DatosGenerales']['AnosSegAnte'],$expectedDifference); // currentYear - 2003
    }

    public function testArraytoxmlReturnsCorrectXmlString() 
    {
        $array = [
            'Cotizacion' => 0,
            'Datos' => [
                'DatosGenerales' => [
                    'CondPpalEsTomador' => 'S',
                    'ConductorUnico' => 'N'
                ],
                'DatosConductor' => [
                    'CodDocumento' => 'DNI',
                    'FecNacimiento' => '1990-15-06T00:00:00'
                ]
            ]
        ];
        $expectedXml = '<?xml version="1.0"?>
        <TarificacionThirdPartyRequest>
            <Cotizacion>0</Cotizacion>
            <Datos>
                <DatosGenerales>
                    <CondPpalEsTomador>S</CondPpalEsTomador>
                    <ConductorUnico>N</ConductorUnico>
                </DatosGenerales>
                <DatosConductor>
                    <CodDocumento>DNI</CodDocumento>
                    <FecNacimiento>1990-15-06T00:00:00</FecNacimiento>
                </DatosConductor>
            </Datos>
        </TarificacionThirdPartyRequest>';
        $resultXml = $this->fooInsuranceService->arrayToXml($array);
        $this->assertXmlStringEqualsXmlString($expectedXml, $resultXml);
    }

}
