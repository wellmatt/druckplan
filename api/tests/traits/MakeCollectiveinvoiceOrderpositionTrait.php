<?php

use Faker\Factory as Faker;
use App\Models\CollectiveinvoiceOrderposition;
use App\Repositories\CollectiveinvoiceOrderpositionRepository;

trait MakeCollectiveinvoiceOrderpositionTrait
{
    /**
     * Create fake instance of CollectiveinvoiceOrderposition and save it in database
     *
     * @param array $collectiveinvoiceOrderpositionFields
     * @return CollectiveinvoiceOrderposition
     */
    public function makeCollectiveinvoiceOrderposition($collectiveinvoiceOrderpositionFields = [])
    {
        /** @var CollectiveinvoiceOrderpositionRepository $collectiveinvoiceOrderpositionRepo */
        $collectiveinvoiceOrderpositionRepo = App::make(CollectiveinvoiceOrderpositionRepository::class);
        $theme = $this->fakeCollectiveinvoiceOrderpositionData($collectiveinvoiceOrderpositionFields);
        return $collectiveinvoiceOrderpositionRepo->create($theme);
    }

    /**
     * Get fake instance of CollectiveinvoiceOrderposition
     *
     * @param array $collectiveinvoiceOrderpositionFields
     * @return CollectiveinvoiceOrderposition
     */
    public function fakeCollectiveinvoiceOrderposition($collectiveinvoiceOrderpositionFields = [])
    {
        return new CollectiveinvoiceOrderposition($this->fakeCollectiveinvoiceOrderpositionData($collectiveinvoiceOrderpositionFields));
    }

    /**
     * Get fake data of CollectiveinvoiceOrderposition
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCollectiveinvoiceOrderpositionData($collectiveinvoiceOrderpositionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'quantity' => $fake->randomDigitNotNull,
            'price' => $fake->randomDigitNotNull,
            'tax' => $fake->randomDigitNotNull,
            'comment' => $fake->text,
            'collectiveinvoice' => $fake->randomDigitNotNull,
            'type' => $fake->randomDigitNotNull,
            'inv_rel' => $fake->randomDigitNotNull,
            'object_id' => $fake->randomDigitNotNull,
            'rev_rel' => $fake->randomDigitNotNull,
            'file_attach' => $fake->randomDigitNotNull,
            'perso_order' => $fake->randomDigitNotNull
        ], $collectiveinvoiceOrderpositionFields);
    }
}
