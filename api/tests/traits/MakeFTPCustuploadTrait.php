<?php

use Faker\Factory as Faker;
use App\Models\FTPCustupload;
use App\Repositories\FTPCustuploadRepository;

trait MakeFTPCustuploadTrait
{
    /**
     * Create fake instance of FTPCustupload and save it in database
     *
     * @param array $fTPCustuploadFields
     * @return FTPCustupload
     */
    public function makeFTPCustupload($fTPCustuploadFields = [])
    {
        /** @var FTPCustuploadRepository $fTPCustuploadRepo */
        $fTPCustuploadRepo = App::make(FTPCustuploadRepository::class);
        $theme = $this->fakeFTPCustuploadData($fTPCustuploadFields);
        return $fTPCustuploadRepo->create($theme);
    }

    /**
     * Get fake instance of FTPCustupload
     *
     * @param array $fTPCustuploadFields
     * @return FTPCustupload
     */
    public function fakeFTPCustupload($fTPCustuploadFields = [])
    {
        return new FTPCustupload($this->fakeFTPCustuploadData($fTPCustuploadFields));
    }

    /**
     * Get fake data of FTPCustupload
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFTPCustuploadData($fTPCustuploadFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'ftp_cust_id' => $fake->randomDigitNotNull,
            'ftp_orgname' => $fake->word,
            'ftp_hash' => $fake->word,
            'ftp_status' => $fake->randomDigitNotNull,
            'ftp_conf_step' => $fake->word,
            'ftp_filesize' => $fake->randomDigitNotNull,
            'ftp_crtdat' => $fake->randomDigitNotNull
        ], $fTPCustuploadFields);
    }
}
