<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationSeperationApiTest extends TestCase
{
    use MakePersonalizationSeperationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePersonalizationSeperation()
    {
        $personalizationSeperation = $this->fakePersonalizationSeperationData();
        $this->json('POST', '/api/v1/personalizationSeperations', $personalizationSeperation);

        $this->assertApiResponse($personalizationSeperation);
    }

    /**
     * @test
     */
    public function testReadPersonalizationSeperation()
    {
        $personalizationSeperation = $this->makePersonalizationSeperation();
        $this->json('GET', '/api/v1/personalizationSeperations/'.$personalizationSeperation->id);

        $this->assertApiResponse($personalizationSeperation->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePersonalizationSeperation()
    {
        $personalizationSeperation = $this->makePersonalizationSeperation();
        $editedPersonalizationSeperation = $this->fakePersonalizationSeperationData();

        $this->json('PUT', '/api/v1/personalizationSeperations/'.$personalizationSeperation->id, $editedPersonalizationSeperation);

        $this->assertApiResponse($editedPersonalizationSeperation);
    }

    /**
     * @test
     */
    public function testDeletePersonalizationSeperation()
    {
        $personalizationSeperation = $this->makePersonalizationSeperation();
        $this->json('DELETE', '/api/v1/personalizationSeperations/'.$personalizationSeperation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/personalizationSeperations/'.$personalizationSeperation->id);

        $this->assertResponseStatus(404);
    }
}
