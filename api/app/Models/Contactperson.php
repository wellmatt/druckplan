<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Contactperson",
 *      required={"active","businesscontact","title","name1","name2","address1","address2","zip","city","country","phone","mobil","fax","email","web","comment","main_contact","active_adress","alt_name1","alt_name2","alt_address1","alt_address2","alt_zip","alt_city","alt_country","alt_phone","alt_fax","alt_mobil","alt_email","priv_name1","priv_name2","priv_address1","priv_address2","priv_zip","priv_city","priv_country","priv_phone","priv_fax","priv_mobil","priv_email","shop_login","shop_pass","enabled_ticket","enabled_article","enabled_personalization","enabled_marketing","birthdate","notifymailadr"},
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
 *          property="alt_phone",
 *          description="alt_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_fax",
 *          description="alt_fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_mobil",
 *          description="alt_mobil",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="alt_email",
 *          description="alt_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_name1",
 *          description="priv_name1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_name2",
 *          description="priv_name2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_address1",
 *          description="priv_address1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_address2",
 *          description="priv_address2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_zip",
 *          description="priv_zip",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_city",
 *          description="priv_city",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_country",
 *          description="priv_country",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="priv_phone",
 *          description="priv_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_fax",
 *          description="priv_fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_mobil",
 *          description="priv_mobil",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="priv_email",
 *          description="priv_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shop_login",
 *          description="shop_login",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shop_pass",
 *          description="shop_pass",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="birthdate",
 *          description="birthdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="notifymailadr",
 *          description="notifymailadr",
 *          type="string"
 *      )
 * )
 */
class Contactperson extends Model
{

    public $table = 'contactperson';
    
    public $timestamps = false;



    public $fillable = [
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
        'main_contact',
        'active_adress',
        'alt_name1',
        'alt_name2',
        'alt_address1',
        'alt_address2',
        'alt_zip',
        'alt_city',
        'alt_country',
        'alt_phone',
        'alt_fax',
        'alt_mobil',
        'alt_email',
        'priv_name1',
        'priv_name2',
        'priv_address1',
        'priv_address2',
        'priv_zip',
        'priv_city',
        'priv_country',
        'priv_phone',
        'priv_fax',
        'priv_mobil',
        'priv_email',
        'shop_login',
        'shop_pass',
        'enabled_ticket',
        'enabled_article',
        'enabled_personalization',
        'enabled_marketing',
        'birthdate',
        'notifymailadr'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
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
        'alt_name1' => 'string',
        'alt_name2' => 'string',
        'alt_address1' => 'string',
        'alt_address2' => 'string',
        'alt_zip' => 'string',
        'alt_city' => 'string',
        'alt_country' => 'integer',
        'alt_phone' => 'string',
        'alt_fax' => 'string',
        'alt_mobil' => 'string',
        'alt_email' => 'string',
        'priv_name1' => 'string',
        'priv_name2' => 'string',
        'priv_address1' => 'string',
        'priv_address2' => 'string',
        'priv_zip' => 'string',
        'priv_city' => 'string',
        'priv_country' => 'integer',
        'priv_phone' => 'string',
        'priv_fax' => 'string',
        'priv_mobil' => 'string',
        'priv_email' => 'string',
        'shop_login' => 'string',
        'shop_pass' => 'string',
        'birthdate' => 'integer',
        'notifymailadr' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
