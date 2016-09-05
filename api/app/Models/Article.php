<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="Article",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="number",
 *          description="number",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tradegroup",
 *          description="tradegroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shoprel",
 *          description="shoprel",
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
 *          property="crtdate",
 *          description="crtdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uptuser",
 *          description="uptuser",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uptdate",
 *          description="uptdate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="picture",
 *          description="picture",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="tax",
 *          description="tax",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="minorder",
 *          description="minorder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="maxorder",
 *          description="maxorder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="orderunit",
 *          description="orderunit",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="orderunitweight",
 *          description="orderunitweight",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="shop_customer_rel",
 *          description="shop_customer_rel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shop_customer_id",
 *          description="shop_customer_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchcode",
 *          description="matchcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="orderid",
 *          description="orderid",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Article extends Model
{

    public $table = 'article';
    
    public $timestamps = false;



    public $fillable = [
        'status',
        'title',
        'description',
        'number',
        'tradegroup',
        'shoprel',
        'crtuser',
        'crtdate',
        'uptuser',
        'uptdate',
        'picture',
        'tax',
        'minorder',
        'maxorder',
        'orderunit',
        'orderunitweight',
        'shop_customer_rel',
        'shop_customer_id',
        'isworkhourart',
        'show_shop_price',
        'shop_needs_upload',
        'matchcode',
        'orderid',
        'usesstorage'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'number' => 'string',
        'tradegroup' => 'integer',
        'shoprel' => 'integer',
        'crtuser' => 'integer',
        'crtdate' => 'integer',
        'uptuser' => 'integer',
        'uptdate' => 'integer',
        'picture' => 'string',
        'tax' => 'float',
        'minorder' => 'integer',
        'maxorder' => 'integer',
        'orderunit' => 'integer',
        'orderunitweight' => 'float',
        'shop_customer_rel' => 'integer',
        'shop_customer_id' => 'integer',
        'matchcode' => 'string',
        'orderid' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    protected $with = array(
        'pricescales',
        'orderamounts',
        'pictures',
        'qualifiedusers',
        'shopapprovals',
        'tags'
    );

    /**
     * @return mixed
     */
    public function pricescales()
    {
        return $this->hasMany('App\Models\ArticlePricescale', 'article', 'id');
    }

    /**
     * @return mixed
     */
    public function orderamounts()
    {
        return $this->hasMany('App\Models\ArticleOrderamount', 'article_id', 'id');
    }

    /**
     * @return mixed
     */
    public function pictures()
    {
        return $this->hasMany('App\Models\ArticlePicture', 'articleid', 'id');
    }

    /**
     * @return mixed
     */
    public function qualifiedusers()
    {
        return $this->hasMany('App\Models\ArticleQualifiedUser', 'article', 'id');
    }

    /**
     * @return mixed
     */
    public function shopapprovals()
    {
        return $this->hasMany('App\Models\ArticleShopApproval', 'article', 'id');
    }

    /**
     * @return mixed
     */
    public function tags()
    {
        return $this->hasMany('App\Models\ArticleTag', 'article', 'id');
    }

}
