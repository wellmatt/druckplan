<?php

use Faker\Factory as Faker;
use App\Models\Foldtype;
use App\Repositories\FoldtypeRepository;

trait MakeFoldtypeTrait
{
    /**
     * Create fake instance of Foldtype and save it in database
     *
     * @param array $foldtypeFields
     * @return Foldtype
     */
    public function makeFoldtype($foldtypeFields = [])
    {
        /** @var FoldtypeRepository $foldtypeRepo */
        $foldtypeRepo = App::make(FoldtypeRepository::class);
        $theme = $this->fakeFoldtypeData($foldtypeFields);
        return $foldtypeRepo->create($theme);
    }

    /**
     * Get fake instance of Foldtype
     *
     * @param array $foldtypeFields
     * @return Foldtype
     */
    public function fakeFoldtype($foldtypeFields = [])
    {
        return new Foldtype($this->fakeFoldtypeData($foldtypeFields));
    }

    /**
     * Get fake data of Foldtype
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFoldtypeData($foldtypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'status' => $fake->word,
            'name' => $fake->word,
            'beschreibung' => $fake->text,
            'vertical' => $fake->word,
            'horizontal' => $fake->word,
            'picture' => $fake->word,
            'breaks' => $fake->randomDigitNotNull
        ], $foldtypeFields);
    }
}
