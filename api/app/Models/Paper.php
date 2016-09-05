<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Paper",
 *      required={"status","name","comment","type","pricebase","dilivermat","glue","thickness","totalweight","price_100kg","price_1qm","rolle","volume"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dilivermat",
 *          description="dilivermat",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glue",
 *          description="glue",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="thickness",
 *          description="thickness",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="totalweight",
 *          description="totalweight",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price_100kg",
 *          description="price_100kg",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price_1qm",
 *          description="price_1qm",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="volume",
 *          description="volume",
 *          type="string"
 *      )
 * )
 */
class Paper extends Model
{

    public $table = 'papers';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'name',
        'comment',
        'type',
        'pricebase',
        'dilivermat',
        'glue',
        'thickness',
        'totalweight',
        'price_100kg',
        'price_1qm',
        'rolle',
        'volume'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'comment' => 'string',
        'dilivermat' => 'string',
        'glue' => 'string',
        'thickness' => 'string',
        'totalweight' => 'string',
        'price_100kg' => 'string',
        'price_1qm' => 'string',
        'volume' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'paperprices',
        'papersizes',
        'papersuppliers',
        'paperweights',
    );

    /**
     * @return mixed
     */
    public function paperprices()
    {
        return $this->hasMany('App\Models\PaperPrice', 'paper_id', 'id');
    }

    /**
     * @return mixed
     */
    public function papersizes()
    {
        return $this->hasMany('App\Models\PaperSize', 'paper_id', 'id');
    }

    /**
     * @return mixed
     */
    public function papersuppliers()
    {
        return $this->hasMany('App\Models\PaperSupplier', 'paper_id', 'id');
    }

    /**
     * @return mixed
     */
    public function paperweights()
    {
        return $this->hasMany('App\Models\PaperWeight', 'paper_id', 'id');
    }

    
}
