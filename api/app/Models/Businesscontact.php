<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Businesscontact",
 *      required={" "active","commissionpartner","customer","supplier","client","matchcode","name1","name2","address1","address2","zip","city","country","phone","fax","email","web","comment","language","payment_terms","discount","lector_id","shop_login","shop_pass","login_expire","ticket_enabled","personalization_enabled","branche","type","produkte","bedarf","priv_name1","priv_name2","priv_address1","priv_address2","priv_zip","priv_city","priv_country","priv_phone","priv_fax","priv_email","alt_name1","alt_name2","alt_address1","alt_address2","alt_zip","alt_city","alt_country","alt_phone","alt_fax","alt_email","cust_number","number_at_customer","enabled_article","debitor_number","kreditor_number","iban","bic","position_titles","notifymailadr","supervisor","tourmarker","notes","salesperson"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="client",
 *          description="client",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchcode",
 *          description="matchcode",
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
 *          property="language",
 *          description="language",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payment_terms",
 *          description="payment_terms",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="discount",
 *          description="discount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="lector_id",
 *          description="lector_id",
 *          type="integer",
 *          format="int32"
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
 *          property="login_expire",
 *          description="login_expire",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bedarf",
 *          description="bedarf",
 *          type="integer",
 *          format="int32"
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
 *          property="priv_email",
 *          description="priv_email",
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
 *          property="alt_email",
 *          description="alt_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="cust_number",
 *          description="cust_number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number_at_customer",
 *          description="number_at_customer",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="debitor_number",
 *          description="debitor_number",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="kreditor_number",
 *          description="kreditor_number",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="iban",
 *          description="iban",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bic",
 *          description="bic",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="position_titles",
 *          description="position_titles",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="notifymailadr",
 *          description="notifymailadr",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supervisor",
 *          description="supervisor",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tourmarker",
 *          description="tourmarker",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="notes",
 *          description="notes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salesperson",
 *          description="salesperson",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Businesscontact extends Model
{

    public $table = 'businesscontact';
    
    public $timestamps = false;



    public $fillable = [
        'active',
        'commissionpartner',
        'customer',
        'supplier',
        'client',
        'matchcode',
        'name1',
        'name2',
        'address1',
        'address2',
        'zip',
        'city',
        'country',
        'phone',
        'fax',
        'email',
        'web',
        'comment',
        'language',
        'payment_terms',
        'discount',
        'lector_id',
        'shop_login',
        'shop_pass',
        'login_expire',
        'ticket_enabled',
        'personalization_enabled',
        'branche',
        'type',
        'produkte',
        'bedarf',
        'priv_name1',
        'priv_name2',
        'priv_address1',
        'priv_address2',
        'priv_zip',
        'priv_city',
        'priv_country',
        'priv_phone',
        'priv_fax',
        'priv_email',
        'alt_name1',
        'alt_name2',
        'alt_address1',
        'alt_address2',
        'alt_zip',
        'alt_city',
        'alt_country',
        'alt_phone',
        'alt_fax',
        'alt_email',
        'cust_number',
        'number_at_customer',
        'enabled_article',
        'debitor_number',
        'kreditor_number',
        'iban',
        'bic',
        'position_titles',
        'notifymailadr',
        'supervisor',
        'tourmarker',
        'notes',
        'salesperson'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'client' => 'integer',
        'matchcode' => 'string',
        'name1' => 'string',
        'name2' => 'string',
        'address1' => 'string',
        'address2' => 'string',
        'zip' => 'string',
        'city' => 'string',
        'country' => 'integer',
        'phone' => 'string',
        'fax' => 'string',
        'email' => 'string',
        'web' => 'string',
        'comment' => 'string',
        'language' => 'integer',
        'payment_terms' => 'integer',
        'discount' => 'float',
        'lector_id' => 'integer',
        'shop_login' => 'string',
        'shop_pass' => 'string',
        'login_expire' => 'integer',
        'priv_name1' => 'string',
        'priv_name2' => 'string',
        'priv_address1' => 'string',
        'priv_address2' => 'string',
        'priv_zip' => 'string',
        'priv_city' => 'string',
        'priv_country' => 'integer',
        'priv_phone' => 'string',
        'priv_fax' => 'string',
        'priv_email' => 'string',
        'alt_name1' => 'string',
        'alt_name2' => 'string',
        'alt_address1' => 'string',
        'alt_address2' => 'string',
        'alt_zip' => 'string',
        'alt_city' => 'string',
        'alt_country' => 'integer',
        'alt_phone' => 'string',
        'alt_fax' => 'string',
        'alt_email' => 'string',
        'cust_number' => 'string',
        'number_at_customer' => 'string',
        'debitor_number' => 'integer',
        'kreditor_number' => 'integer',
        'iban' => 'string',
        'bic' => 'string',
        'position_titles' => 'string',
        'notifymailadr' => 'string',
        'supervisor' => 'integer',
        'tourmarker' => 'string',
        'notes' => 'string',
        'salesperson' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'businesscontactattributes',
    );

    /**
     * @return mixed
     */
    public function businesscontactattributes()
    {
        return $this->hasMany('App\Models\BusinesscontactAttribute', 'businesscontact_id', 'id');
    }

    
}
