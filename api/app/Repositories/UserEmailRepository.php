<?php

namespace App\Repositories;

use App\Models\UserEmail;
use InfyOm\Generator\Common\BaseRepository;

class UserEmailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return UserEmail::class;
    }
}
