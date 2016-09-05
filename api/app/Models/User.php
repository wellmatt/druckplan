<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="User",
 *      required={""},
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
 *          property="login",
 *          description="login",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="password",
 *          description="password",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_firstname",
 *          description="user_firstname",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_lastname",
 *          description="user_lastname",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_level",
 *          description="user_level",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_email",
 *          description="user_email",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_phone",
 *          description="user_phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_signature",
 *          description="user_signature",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_lang",
 *          description="user_lang",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="telefon_ip",
 *          description="telefon_ip",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="w_mo",
 *          description="w_mo",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="w_tu",
 *          description="w_tu",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="w_we",
 *          description="w_we",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="w_th",
 *          description="w_th",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="w_fr",
 *          description="w_fr",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="w_sa",
 *          description="w_sa",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="w_su",
 *          description="w_su",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="w_month",
 *          description="w_month",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="avatar",
 *          description="avatar",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="homepage",
 *          description="homepage",
 *          type="string"
 *      )
 * )
 */
class User extends Model
{

    public $table = 'user';
    
    public $timestamps = false;



    public $fillable = [
        'client',
        'login',
        'password',
        'user_firstname',
        'user_lastname',
        'user_level',
        'user_email',
        'user_phone',
        'user_signature',
        'user_lang',
        'user_active',
        'user_forwardmail',
        'telefon_ip',
        'cal_birthdays',
        'cal_tickets',
        'cal_orders',
        'w_mo',
        'w_tu',
        'w_we',
        'w_th',
        'w_fr',
        'w_sa',
        'w_su',
        'w_month',
        'avatar',
        'homepage',
        'BCshowOnlyOverview'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'client' => 'integer',
        'login' => 'string',
        'password' => 'string',
        'user_firstname' => 'string',
        'user_lastname' => 'string',
        'user_level' => 'integer',
        'user_email' => 'string',
        'user_phone' => 'string',
        'user_signature' => 'string',
        'user_lang' => 'integer',
        'telefon_ip' => 'string',
        'w_mo' => 'float',
        'w_tu' => 'float',
        'w_we' => 'float',
        'w_th' => 'float',
        'w_fr' => 'float',
        'w_sa' => 'float',
        'w_su' => 'float',
        'w_month' => 'float',
        'avatar' => 'string',
        'homepage' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'useremails',
        'usergroups',
    );

    /**
     * @return mixed
     */
    public function useremails()
    {
        return $this->hasMany('App\Models\UserEmail', 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function usergroups()
    {
        return $this->hasMany('App\Models\UserGroup', 'user_id', 'id');
    }

    
}
