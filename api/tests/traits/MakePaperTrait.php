<?php

use Faker\Factory as Faker;
use App\Models\Paper;
use App\Repositories\PaperRepository;

trait MakePaperTrait
{
    /**
     * Create fake instance of Paper and save it in database
     *
     * @param array $paperFields
     * @return Paper
     */
    public function makePaper($paperFields = [])
    {
        /** @var PaperRepository $paperRepo */
        $paperRepo = App::make(PaperRepository::class);
        $theme = $this->fakePaperData($paperFields);
        return $paperRepo->create($theme);
    }

    /**
     * Get fake instance of Paper
     *
     * @param array $paperFields
     * @return Paper
     */
    public function fakePaper($paperFields = [])
    {
        return new Paper($this->fakePaperData($paperFields));
    }

    /**
     * Get fake data of Paper
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaperData($paperFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'name' => $fake->word,
            'comment' => $fake->text,
            'type' => $fake->word,
            'pricebase' => $fake->word,
            'dilivermat' => $fake->word,
            'glue' => $fake->word,
            'thickness' => $fake->word,
            'totalweight' => $fake->word,
            'price_100kg' => $fake->word,
            'price_1qm' => $fake->word,
            'rolle' => $fake->word,
            'volume' => $fake->word
        ], $paperFields);
    }
}
