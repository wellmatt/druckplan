<?php

use Faker\Factory as Faker;
use App\Models\FTPDownload;
use App\Repositories\FTPDownloadRepository;

trait MakeFTPDownloadTrait
{
    /**
     * Create fake instance of FTPDownload and save it in database
     *
     * @param array $fTPDownloadFields
     * @return FTPDownload
     */
    public function makeFTPDownload($fTPDownloadFields = [])
    {
        /** @var FTPDownloadRepository $fTPDownloadRepo */
        $fTPDownloadRepo = App::make(FTPDownloadRepository::class);
        $theme = $this->fakeFTPDownloadData($fTPDownloadFields);
        return $fTPDownloadRepo->create($theme);
    }

    /**
     * Get fake instance of FTPDownload
     *
     * @param array $fTPDownloadFields
     * @return FTPDownload
     */
    public function fakeFTPDownload($fTPDownloadFields = [])
    {
        return new FTPDownload($this->fakeFTPDownloadData($fTPDownloadFields));
    }

    /**
     * Get fake data of FTPDownload
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFTPDownloadData($fTPDownloadFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'ftp_cust_id' => $fake->randomDigitNotNull,
            'ftp_orgname' => $fake->word,
            'ftp_hash' => $fake->word,
            'ftp_status' => $fake->randomDigitNotNull,
            'ftp_conf_step' => $fake->word
        ], $fTPDownloadFields);
    }
}
