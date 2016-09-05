<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttributeApiTest extends TestCase
{
    use MakeAttributeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAttribute()
    {
        $attribute = $this->fakeAttributeData();
        $this->json('POST', '/api/v1/attributes', $attribute);

        $this->assertApiResponse($attribute);
    }

    /**
     * @test
     */
    public function testReadAttribute()
    {
        $attribute = $this->makeAttribute();
        $this->json('GET', '/api/v1/attributes/'.$attribute->id);

        $this->assertApiResponse($attribute->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAttribute()
    {
        $attribute = $this->makeAttribute();
        $editedAttribute = $this->fakeAttributeData();

        $this->json('PUT', '/api/v1/attributes/'.$attribute->id, $editedAttribute);

        $this->assertApiResponse($editedAttribute);
    }

    /**
     * @test
     */
    public function testDeleteAttribute()
    {
        $attribute = $this->makeAttribute();
        $this->json('DELETE', '/api/v1/attributes/'.$attribute->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/attributes/'.$attribute->id);

        $this->assertResponseStatus(404);
    }
}
