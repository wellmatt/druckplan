<?php

namespace App\Repositories;

use App\Models\UserGroup;
use InfyOm\Generator\Common\BaseRepository;

class UserGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'group_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserGroup::class;
    }
}
