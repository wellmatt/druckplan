<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CountryApiTest extends TestCase
{
    use MakeCountryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCountry()
    {
        $country = $this->fakeCountryData();
        $this->json('POST', '/api/v1/countries', $country);

        $this->assertApiResponse($country);
    }

    /**
     * @test
     */
    public function testReadCountry()
    {
        $country = $this->makeCountry();
        $this->json('GET', '/api/v1/countries/'.$country->id);

        $this->assertApiResponse($country->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCountry()
    {
        $country = $this->makeCountry();
        $editedCountry = $this->fakeCountryData();

        $this->json('PUT', '/api/v1/countries/'.$country->id, $editedCountry);

        $this->assertApiResponse($editedCountry);
    }

    /**
     * @test
     */
    public function testDeleteCountry()
    {
        $country = $this->makeCountry();
        $this->json('DELETE', '/api/v1/countries/'.$country->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/countries/'.$country->id);

        $this->assertResponseStatus(404);
    }
}
