<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PrivatContact",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="crtuser",
 *          description="crtuser",
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
 *          property="title",
 *          description="title",
 *          type="string"
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
 *          property="phone",
 *          description="phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="mobil",
 *          description="mobil",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fax",
 *          description="fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email",
 *          description="email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="web",
 *          description="web",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="birthdate",
 *          description="birthdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="alt_title",
 *          description="alt_title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_name1",
 *          description="alt_name1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_name2",
 *          description="alt_name2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_address1",
 *          description="alt_address1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_address2",
 *          description="alt_address2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_zip",
 *          description="alt_zip",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_city",
 *          description="alt_city",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_country",
 *          description="alt_country",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="alt_email",
 *          description="alt_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_phone",
 *          description="alt_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_mobil",
 *          description="alt_mobil",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_fax",
 *          description="alt_fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_web",
 *          description="alt_web",
 *          type="string"
 *      )
 * )
 */
class PrivatContact extends Model
{

    public $table = 'privatecontacts';
    
    public $timestamps = false;



    public $fillable = [
        'crtuser',
        'active',
        'businesscontact',
        'title',
        'name1',
        'name2',
        'address1',
        'address2',
        'zip',
        'city',
        'country',
        'phone',
        'mobil',
        'fax',
        'email',
        'web',
        'comment',
        'birthdate',
        'alt_title',
        'alt_name1',
        'alt_name2',
        'alt_address1',
        'alt_address2',
        'alt_zip',
        'alt_city',
        'alt_country',
        'alt_email',
        'alt_phone',
        'alt_mobil',
        'alt_fax',
        'alt_web'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'crtuser' => 'integer',
        'businesscontact' => 'integer',
        'title' => 'string',
        'name1' => 'string',
        'name2' => 'string',
        'address1' => 'string',
        'address2' => 'string',
        'zip' => 'string',
        'city' => 'string',
        'country' => 'integer',
        'phone' => 'string',
        'mobil' => 'string',
        'fax' => 'string',
        'email' => 'string',
        'web' => 'string',
        'comment' => 'string',
        'birthdate' => 'integer',
        'alt_title' => 'string',
        'alt_name1' => 'string',
        'alt_name2' => 'string',
        'alt_address1' => 'string',
        'alt_address2' => 'string',
        'alt_zip' => 'string',
        'alt_city' => 'string',
        'alt_country' => 'integer',
        'alt_email' => 'string',
        'alt_phone' => 'string',
        'alt_mobil' => 'string',
        'alt_fax' => 'string',
        'alt_web' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'privatcontactaccess',
    );

    /**
     * @return mixed
     */
    public function privatcontactaccess()
    {
        return $this->hasMany('App\Models\PrivatContactAccess', 'prvtc_id', 'id');
    }

    
}
