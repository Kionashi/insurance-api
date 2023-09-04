# Insurance API

This is a Laravel project that contains a single endpoint that after receiving some data as queryParams will map them into an XML format and use that to check the price of the tariff. So the end result will be the price of the tariff from the given params. 

## Getting Started

Follow these instructions to get the project up and running.

### Prerequisites

Before you begin, ensure you have met the following requirements:

- [PHP](https://php.net/) (version 8.1 or higher)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (for frontend assets, optional)
- [Git](https://git-scm.com/)

### Installation

1. Clone the repository and enter to the project directory:

   `git clone https://github.com/Kionashi/insurance-api.git`

   `cd insurance-api`

2. Install PHP dependencies using Composer:

    `composer install`

3. Copy the .env.example file to .env and configure your environment variables, in this case you wont need anything special, just make sure to have a .env file:
    cp .env.example .env

4. Generate a new application key:
    php artisan key:generate

### Running the Application

To start the Laravel development server, run:

`php artisan serve`

You can access the application in your web browser at http://localhost:8000.

### Running Tests

You can run tests using PHPUnit, Laravel's testing framework. To run the tests, execute the following command

`php artisan test`

This will execute all the tests in the tests directory.

### Features

Once started the project to access the main feature of consulting the price you can make a GET Request to the following endpoint (Postman is recommended): 

http://localhost:8000/api/insurance/foo/prices

For an organized Postmand friendly example of the possible queryparams you can send to the endpoint you can see a file named `example-request.txt` in the root of the repository.

Or you can also use this preloaded url if you want to test the endpoint in a web browser: 

http://localhost:8000/api/insurance/foo/prices?car_brand=SEAT&car_fuel=Gasolina&car_model=IBIZA&car_power=No estoy seguro&car_purchaseDate=&car_purchaseSituation=NUEVO&car_registrationDate=&car_version=00540140903&customer_journey_ok=1&driver_birthDate=2002-06-05&driver_birthPlace=ESP&driver_birthPlaceMain=ESP&driver_children=NO&driver_civilStatus=SOLTERO&driver_id=36714791Y&driver_idType=dni&driver_licenseDate=2020-12-15&driver_licensePlace=ESP&driver_licensePlaceMain=ESP&driver_profession=Estudiante&driver_sex=MUJER&holder=CONDUCTOR_PRINCIPAL&holder_birthDate=&holder_civilStatus=&holder_id=&holder_idType=&holder_licenseDate=&holder_profession=&holder_sex=&occasionalDriver=NO&occasionalDriver_birthDate=&occasionalDriver_civilStatus=&occasionalDriver_id=&occasionalDriver_idType=&occasionalDriver_licenseDate=&occasionalDriver_profession=&occasionalDriver_sex=&occasionalDriver_youngest=&prevInsurance_claims=&prevInsurance_claimsCount=0&prevInsurance_company=&prevInsurance_companyYear=&prevInsurance_contractDate=&prevInsurance_email=&prevInsurance_emailRequest=NO&prevInsurance_exists=NO&prevInsurance_expirationDate=&prevInsurance_fineAlcohol=&prevInsurance_fineOther=&prevInsurance_fineParking=&prevInsurance_fineSpeed=&prevInsurance_fines=&prevInsurance_modality=1&prevInsurance_years=&reference_code=1TT02TT11&use_carUse=OCASIONAL&use_kmsYear=6000&use_nightParking=CALLE&use_postalCode=28001'

The response of the request (if all the data is valid) will be an hipotetical price after consulting the price with the insurance company, so  in order to check if the XML is being generated correctly, there will be an xml file in the public folder of the project called `object.xml` that will contain the xml of the latest request to the endpoint so it will be easy to check.


Si tienes alguna pregunta o problema, no dudes en contactar conmigo.

tlf: 618918809
email: cardozo.anibal@gmail.com