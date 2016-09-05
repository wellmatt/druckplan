<?php

namespace App\Repositories;

use App\Models\FTPDownload;
use InfyOm\Generator\Common\BaseRepository;

class FTPDownloadRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ftp_cust_id',
        'ftp_orgname',
        'ftp_hash',
        'ftp_status',
        'ftp_conf_step'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FTPDownload::class;
    }
}
