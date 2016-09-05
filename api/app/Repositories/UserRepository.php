<?php

namespace App\Repositories;

use App\Models\User;
use InfyOm\Generator\Common\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }
}
