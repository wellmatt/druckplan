<?php

namespace App\Repositories;

use App\Models\FTPCustupload;
use InfyOm\Generator\Common\BaseRepository;

class FTPCustuploadRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ftp_cust_id',
        'ftp_orgname',
        'ftp_hash',
        'ftp_status',
        'ftp_conf_step',
        'ftp_filesize',
        'ftp_crtdat'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FTPCustupload::class;
    }
}
