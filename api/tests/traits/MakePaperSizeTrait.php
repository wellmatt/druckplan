<?php

use Faker\Factory as Faker;
use App\Models\PaperSize;
use App\Repositories\PaperSizeRepository;

trait MakePaperSizeTrait
{
    /**
     * Create fake instance of PaperSize and save it in database
     *
     * @param array $paperSizeFields
     * @return PaperSize
     */
    public function makePaperSize($paperSizeFields = [])
    {
        /** @var PaperSizeRepository $paperSizeRepo */
        $paperSizeRepo = App::make(PaperSizeRepository::class);
        $theme = $this->fakePaperSizeData($paperSizeFields);
        return $paperSizeRepo->create($theme);
    }

    /**
     * Get fake instance of PaperSize
     *
     * @param array $paperSizeFields
     * @return PaperSize
     */
    public function fakePaperSize($paperSizeFields = [])
    {
        return new PaperSize($this->fakePaperSizeData($paperSizeFields));
    }

    /**
     * Get fake data of PaperSize
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaperSizeData($paperSizeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paper_id' => $fake->randomDigitNotNull,
            'width' => $fake->randomDigitNotNull,
            'height' => $fake->randomDigitNotNull
        ], $paperSizeFields);
    }
}
