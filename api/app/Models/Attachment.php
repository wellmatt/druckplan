<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Attachment",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="crtdate",
 *          description="crtdate",
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
 *          property="module",
 *          description="module",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="objectid",
 *          description="objectid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="filename",
 *          description="filename",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="orig_filename",
 *          description="orig_filename",
 *          type="string"
 *      )
 * )
 */
class Attachment extends Model
{

    public $table = 'attachments';
    
    public $timestamps = false;



    public $fillable = [
        'title',
        'crtdate',
        'crtuser',
        'state',
        'module',
        'objectid',
        'filename',
        'orig_filename'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'crtdate' => 'integer',
        'crtuser' => 'integer',
        'module' => 'string',
        'objectid' => 'integer',
        'filename' => 'string',
        'orig_filename' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
