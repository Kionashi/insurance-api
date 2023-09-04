<?php

namespace App\Services;

use App\DTOs\CarInsuranceDTO;
use App\Exceptions\InvalidInsuranceDataException;
use App\Services\Interfaces\InsuranceServiceConnectionInterface;
use SimpleXMLElement;

class FooInsuranceService implements InsuranceServiceConnectionInterface {

    // Validate carInsuranceData 
    public function validateInsuranceData(CarInsuranceDTO $carInsuranceData): bool
    {
        $isValid = true;
        $errorMsg = [];
        if ($carInsuranceData->holder === 'CONDUCTOR_PRINCIPAL' &&
        (
            $carInsuranceData->holder_birthDate || 
            $carInsuranceData->holder_civilStatus ||
            $carInsuranceData->holder_id ||
            $carInsuranceData->holder_idType ||
            $carInsuranceData->holder_licenseDate ||
            $carInsuranceData->holder_profession ||
            $carInsuranceData->holder_sex
        ))
        {
            $isValid = false;
            $errorMsg['holder'] = ["If the holder is the main driver, the rest of holder data must be empty, since it will be taken from the driver's data"];
        }
        if (!$isValid){
            $errorMsg = json_encode($errorMsg);
            throw new InvalidInsuranceDataException($errorMsg);
        }
        return $isValid;
    }
    public function getTariffPrice(CarInsuranceDTO $carInsuranceData)
    {
        $this->validateInsuranceData($carInsuranceData);

        // Generate array with the data mapped.
        $map = $this->mapDTO($carInsuranceData);
        
        // Generate XML data.
        $xml = $this->arrayToXml($map);
        
        // Send request to get the price from the insurance service
        $price = $this->connectToInsuranceCompany($xml);
        return $price;
    }

    /**
     *  Receives a CarInsuranceDTO and generates an array with the data mapped to be used by the Foo insurance company.
     */
    public function mapDTO(CarInsuranceDTO $carInsuranceData): array
    {
        $yearsInPrevInsurance = 0;
        if ($carInsuranceData->prevInsurance_exists === 'SI') {
            $prevInsuranceStartYear = date("Y", strtotime($carInsuranceData->prevInsurance_contractDate));
            $prevInsuranceEndYear = date("Y", strtotime($carInsuranceData->prevInsurance_expirationDate));
            $currentYear = date('Y');
            // Calculate the difference between the start year of the prev insurance and the expiration date or the current date if the contract hasn't expired yet.
            $yearsInPrevInsurance = $prevInsuranceEndYear > $currentYear ?  $currentYear - $prevInsuranceStartYear : $prevInsuranceEndYear - $prevInsuranceStartYear;

        }
        $map = [];
        $map['Cotizacion'] = 0;

        // Datos Generales
        $map['Datos']['DatosGenerales']['CondPpalEsTomador'] = $carInsuranceData->holder === 'CONDUCTOR_PRINCIPAL' ? 'S': 'N';
        $map['Datos']['DatosGenerales']['ConductorUnico'] = $carInsuranceData->occasionalDriver === 'NO' ? 'S' : 'N';
        $map['Datos']['DatosGenerales']['FecCot'] = date("Y-m-d\TH:i:s");
        $map['Datos']['DatosGenerales']['AnosSegAnte'] = $yearsInPrevInsurance;
        $map['Datos']['DatosGenerales']['NroCondOca'] = $carInsuranceData->occasionalDriver === 'NO' ? 0 : 1;
        $map['Datos']['DatosGenerales']['SeguroEnVigor'] = $carInsuranceData->prevInsurance_expirationDate && $carInsuranceData->prevInsurance_expirationDate > date('c') ? 'S': 'N';
        
        // Datos del conductor
        $map['Datos']['DatosConductor']['CodDocumento'] = strtoupper($carInsuranceData->driver_idType);
        $map['Datos']['DatosConductor']['FecCarnet'] = date("Y-m-d\TH:i:s",strtotime($carInsuranceData->driver_licenseDate));
        $map['Datos']['DatosConductor']['FecNacimiento'] = date("Y-m-d\TH:i:s",strtotime($carInsuranceData->driver_birthDate));
        $map['Datos']['DatosConductor']['Sexo'] = $carInsuranceData->driver_sex === 'MUJER'? 'M' : 'H';

        return $map;
    }

    /**
     * Convert nested arrays to XML string
     */
    public function arrayToXml($array, $rootElement = null, $xml = null): string 
    {
         
        // If there is no xml yet then is the first call to this method, so we create the SimpleXMLElement with the initial tag.
        if ($xml === null) {
            $xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<TarificacionThirdPartyRequest/>');
        }
         
        // Visit all key value pair
        foreach ($array as $key => $value) {
             
            // If there is nested array then
            if (is_array($value)) {
                // Call function for nested array
                $this->arrayToXml($value, $key, $xml->addChild($key));
            } else {
                // Simply add child element.
                $xml->addChild($key, $value);
            }
        }
        $xml->asXML('object.xml'); // Generates xml file just for checking purposes.
        return $xml->asXML();
    }

    /**
     *  @TODO: Implement this function
     */
    public function connectToInsuranceCompany(string $xmlString): int
    {
        return rand(0,100); 
    }

    public function test(){
        return 'Foo';
    }

}
