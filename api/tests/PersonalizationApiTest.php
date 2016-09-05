<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationApiTest extends TestCase
{
    use MakePersonalizationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePersonalization()
    {
        $personalization = $this->fakePersonalizationData();
        $this->json('POST', '/api/v1/personalizations', $personalization);

        $this->assertApiResponse($personalization);
    }

    /**
     * @test
     */
    public function testReadPersonalization()
    {
        $personalization = $this->makePersonalization();
        $this->json('GET', '/api/v1/personalizations/'.$personalization->id);

        $this->assertApiResponse($personalization->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePersonalization()
    {
        $personalization = $this->makePersonalization();
        $editedPersonalization = $this->fakePersonalizationData();

        $this->json('PUT', '/api/v1/personalizations/'.$personalization->id, $editedPersonalization);

        $this->assertApiResponse($editedPersonalization);
    }

    /**
     * @test
     */
    public function testDeletePersonalization()
    {
        $personalization = $this->makePersonalization();
        $this->json('DELETE', '/api/v1/personalizations/'.$personalization->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/personalizations/'.$personalization->id);

        $this->assertResponseStatus(404);
    }
}
