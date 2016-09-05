<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttributeItemApiTest extends TestCase
{
    use MakeAttributeItemTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAttributeItem()
    {
        $attributeItem = $this->fakeAttributeItemData();
        $this->json('POST', '/api/v1/attributeItems', $attributeItem);

        $this->assertApiResponse($attributeItem);
    }

    /**
     * @test
     */
    public function testReadAttributeItem()
    {
        $attributeItem = $this->makeAttributeItem();
        $this->json('GET', '/api/v1/attributeItems/'.$attributeItem->id);

        $this->assertApiResponse($attributeItem->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAttributeItem()
    {
        $attributeItem = $this->makeAttributeItem();
        $editedAttributeItem = $this->fakeAttributeItemData();

        $this->json('PUT', '/api/v1/attributeItems/'.$attributeItem->id, $editedAttributeItem);

        $this->assertApiResponse($editedAttributeItem);
    }

    /**
     * @test
     */
    public function testDeleteAttributeItem()
    {
        $attributeItem = $this->makeAttributeItem();
        $this->json('DELETE', '/api/v1/attributeItems/'.$attributeItem->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/attributeItems/'.$attributeItem->id);

        $this->assertResponseStatus(404);
    }
}
