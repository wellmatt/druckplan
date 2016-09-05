<?php

use Faker\Factory as Faker;
use App\Models\Attribute;
use App\Repositories\AttributeRepository;

trait MakeAttributeTrait
{
    /**
     * Create fake instance of Attribute and save it in database
     *
     * @param array $attributeFields
     * @return Attribute
     */
    public function makeAttribute($attributeFields = [])
    {
        /** @var AttributeRepository $attributeRepo */
        $attributeRepo = App::make(AttributeRepository::class);
        $theme = $this->fakeAttributeData($attributeFields);
        return $attributeRepo->create($theme);
    }

    /**
     * Get fake instance of Attribute
     *
     * @param array $attributeFields
     * @return Attribute
     */
    public function fakeAttribute($attributeFields = [])
    {
        return new Attribute($this->fakeAttributeData($attributeFields));
    }

    /**
     * Get fake data of Attribute
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAttributeData($attributeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'state' => $fake->word,
            'title' => $fake->word,
            'comment' => $fake->text,
            'module' => $fake->randomDigitNotNull,
            'objectid' => $fake->randomDigitNotNull,
            'crtuser' => $fake->randomDigitNotNull,
            'crtdate' => $fake->randomDigitNotNull,
            'enable_customer' => $fake->word,
            'enable_contacts' => $fake->word,
            'enable_colinv' => $fake->word
        ], $attributeFields);
    }
}
