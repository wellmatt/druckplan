<?php

use Faker\Factory as Faker;
use App\Models\CollectiveinvoiceAttribute;
use App\Repositories\CollectiveinvoiceAttributeRepository;

trait MakeCollectiveinvoiceAttributeTrait
{
    /**
     * Create fake instance of CollectiveinvoiceAttribute and save it in database
     *
     * @param array $collectiveinvoiceAttributeFields
     * @return CollectiveinvoiceAttribute
     */
    public function makeCollectiveinvoiceAttribute($collectiveinvoiceAttributeFields = [])
    {
        /** @var CollectiveinvoiceAttributeRepository $collectiveinvoiceAttributeRepo */
        $collectiveinvoiceAttributeRepo = App::make(CollectiveinvoiceAttributeRepository::class);
        $theme = $this->fakeCollectiveinvoiceAttributeData($collectiveinvoiceAttributeFields);
        return $collectiveinvoiceAttributeRepo->create($theme);
    }

    /**
     * Get fake instance of CollectiveinvoiceAttribute
     *
     * @param array $collectiveinvoiceAttributeFields
     * @return CollectiveinvoiceAttribute
     */
    public function fakeCollectiveinvoiceAttribute($collectiveinvoiceAttributeFields = [])
    {
        return new CollectiveinvoiceAttribute($this->fakeCollectiveinvoiceAttributeData($collectiveinvoiceAttributeFields));
    }

    /**
     * Get fake data of CollectiveinvoiceAttribute
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCollectiveinvoiceAttributeData($collectiveinvoiceAttributeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'collectiveinvoice_id' => $fake->randomDigitNotNull,
            'attribute_id' => $fake->randomDigitNotNull,
            'item_id' => $fake->randomDigitNotNull,
            'value' => $fake->word,
            'inputvalue' => $fake->word
        ], $collectiveinvoiceAttributeFields);
    }
}
