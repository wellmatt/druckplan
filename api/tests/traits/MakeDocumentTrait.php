<?php

use Faker\Factory as Faker;
use App\Models\Document;
use App\Repositories\DocumentRepository;

trait MakeDocumentTrait
{
    /**
     * Create fake instance of Document and save it in database
     *
     * @param array $documentFields
     * @return Document
     */
    public function makeDocument($documentFields = [])
    {
        /** @var DocumentRepository $documentRepo */
        $documentRepo = App::make(DocumentRepository::class);
        $theme = $this->fakeDocumentData($documentFields);
        return $documentRepo->create($theme);
    }

    /**
     * Get fake instance of Document
     *
     * @param array $documentFields
     * @return Document
     */
    public function fakeDocument($documentFields = [])
    {
        return new Document($this->fakeDocumentData($documentFields));
    }

    /**
     * Get fake data of Document
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentData($documentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'doc_name' => $fake->word,
            'doc_req_id' => $fake->randomDigitNotNull,
            'doc_req_module' => $fake->word,
            'doc_type' => $fake->randomDigitNotNull,
            'doc_hash' => $fake->word,
            'doc_sent' => $fake->word,
            'doc_crtdat' => $fake->randomDigitNotNull,
            'doc_crtusr' => $fake->randomDigitNotNull,
            'doc_price_netto' => $fake->randomDigitNotNull,
            'doc_price_brutto' => $fake->randomDigitNotNull,
            'doc_payable' => $fake->randomDigitNotNull,
            'doc_payed' => $fake->randomDigitNotNull,
            'doc_warning_id' => $fake->randomDigitNotNull,
            'doc_reverse' => $fake->word,
            'doc_storno_date' => $fake->randomDigitNotNull,
            'paper_order_pid' => $fake->randomDigitNotNull
        ], $documentFields);
    }
}
