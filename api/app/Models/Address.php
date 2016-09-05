<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Address",
 *      required={"active","businesscontact","name1","name2","address1","address2","zip","city","country","fax","phone","mobile","shoprel","is_default"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="businesscontact",
 *          description="businesscontact",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name1",
 *          description="name1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name2",
 *          description="name2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address1",
 *          description="address1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address2",
 *          description="address2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="zip",
 *          description="zip",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="city",
 *          description="city",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="country",
 *          description="country",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fax",
 *          description="fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="phone",
 *          description="phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="mobile",
 *          description="mobile",
 *          type="string"
 *      )
 * )
 */
class Address extends Model
{

    public $table = 'address';
    
    public $timestamps = false;



    public $fillable = [
        'active',
        'businesscontact',
        'name1',
        'name2',
        'address1',
        'address2',
        'zip',
        'city',
        'country',
        'fax',
        'phone',
        'mobile',
        'shoprel',
        'is_default'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'businesscontact' => 'integer',
        'name1' => 'string',
        'name2' => 'string',
        'address1' => 'string',
        'address2' => 'string',
        'zip' => 'string',
        'city' => 'string',
        'country' => 'integer',
        'fax' => 'string',
        'phone' => 'string',
        'mobile' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
