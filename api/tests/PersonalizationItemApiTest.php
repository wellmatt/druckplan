<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PersonalizationItemApiTest extends TestCase
{
    use MakePersonalizationItemTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePersonalizationItem()
    {
        $personalizationItem = $this->fakePersonalizationItemData();
        $this->json('POST', '/api/v1/personalizationItems', $personalizationItem);

        $this->assertApiResponse($personalizationItem);
    }

    /**
     * @test
     */
    public function testReadPersonalizationItem()
    {
        $personalizationItem = $this->makePersonalizationItem();
        $this->json('GET', '/api/v1/personalizationItems/'.$personalizationItem->id);

        $this->assertApiResponse($personalizationItem->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePersonalizationItem()
    {
        $personalizationItem = $this->makePersonalizationItem();
        $editedPersonalizationItem = $this->fakePersonalizationItemData();

        $this->json('PUT', '/api/v1/personalizationItems/'.$personalizationItem->id, $editedPersonalizationItem);

        $this->assertApiResponse($editedPersonalizationItem);
    }

    /**
     * @test
     */
    public function testDeletePersonalizationItem()
    {
        $personalizationItem = $this->makePersonalizationItem();
        $this->json('DELETE', '/api/v1/personalizationItems/'.$personalizationItem->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/personalizationItems/'.$personalizationItem->id);

        $this->assertResponseStatus(404);
    }
}
