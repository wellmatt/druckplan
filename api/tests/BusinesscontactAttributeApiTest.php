<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinesscontactAttributeApiTest extends TestCase
{
    use MakeBusinesscontactAttributeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->fakeBusinesscontactAttributeData();
        $this->json('POST', '/api/v1/businesscontactAttributes', $businesscontactAttribute);

        $this->assertApiResponse($businesscontactAttribute);
    }

    /**
     * @test
     */
    public function testReadBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->makeBusinesscontactAttribute();
        $this->json('GET', '/api/v1/businesscontactAttributes/'.$businesscontactAttribute->id);

        $this->assertApiResponse($businesscontactAttribute->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->makeBusinesscontactAttribute();
        $editedBusinesscontactAttribute = $this->fakeBusinesscontactAttributeData();

        $this->json('PUT', '/api/v1/businesscontactAttributes/'.$businesscontactAttribute->id, $editedBusinesscontactAttribute);

        $this->assertApiResponse($editedBusinesscontactAttribute);
    }

    /**
     * @test
     */
    public function testDeleteBusinesscontactAttribute()
    {
        $businesscontactAttribute = $this->makeBusinesscontactAttribute();
        $this->json('DELETE', '/api/v1/businesscontactAttributes/'.$businesscontactAttribute->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/businesscontactAttributes/'.$businesscontactAttribute->id);

        $this->assertResponseStatus(404);
    }
}
