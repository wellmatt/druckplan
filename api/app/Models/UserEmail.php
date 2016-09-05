<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="UserEmail",
 *      required={"status","user_id","login","address","password","type","host","port","signature","use_imap","use_ssl"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="login",
 *          description="login",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address",
 *          description="address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          description="password",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="host",
 *          description="host",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="port",
 *          description="port",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="signature",
 *          description="signature",
 *          type="string"
 *      )
 * )
 */
class UserEmail extends Model
{

    public $table = 'user_emailaddress';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'user_id',
        'login',
        'address',
        'password',
        'type',
        'host',
        'port',
        'signature',
        'use_imap',
        'use_ssl'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'login' => 'string',
        'address' => 'string',
        'password' => 'string',
        'host' => 'string',
        'signature' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
