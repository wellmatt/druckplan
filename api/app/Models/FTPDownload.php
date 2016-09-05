<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FTPDownload",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ftp_cust_id",
 *          description="ftp_cust_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ftp_orgname",
 *          description="ftp_orgname",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ftp_hash",
 *          description="ftp_hash",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ftp_status",
 *          description="ftp_status",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class FTPDownload extends Model
{

    public $table = 'ftpdownloads';
    
    public $timestamps = false;



    public $fillable = [
        'ftp_cust_id',
        'ftp_orgname',
        'ftp_hash',
        'ftp_status',
        'ftp_conf_step'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'ftp_cust_id' => 'integer',
        'ftp_orgname' => 'string',
        'ftp_hash' => 'string',
        'ftp_status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
