<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateArticlePricescaleAPIRequest;
use App\Http\Requests\API\UpdateArticlePricescaleAPIRequest;
use App\Models\ArticlePricescale;
use App\Repositories\ArticlePricescaleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ArticlePricescaleController
 * @package App\Http\Controllers\API
 */

class ArticlePricescaleAPIController extends AppBaseController
{
    /** @var  ArticlePricescaleRepository */
    private $articlePricescaleRepository;

    public function __construct(ArticlePricescaleRepository $articlePricescaleRepo)
    {
        $this->articlePricescaleRepository = $articlePricescaleRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/articlePricescales",
     *      summary="Get a listing of the ArticlePricescales.",
     *      tags={"ArticlePricescale"},
     *      description="Get all ArticlePricescales",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/ArticlePricescale")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->articlePricescaleRepository->pushCriteria(new RequestCriteria($request));
        $this->articlePricescaleRepository->pushCriteria(new LimitOffsetCriteria($request));
        $articlePricescales = $this->articlePricescaleRepository->all();

        return $this->sendResponse($articlePricescales->toArray(), 'Article Pricescales retrieved successfully');
    }

    /**
     * @param CreateArticlePricescaleAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/articlePricescales",
     *      summary="Store a newly created ArticlePricescale in storage",
     *      tags={"ArticlePricescale"},
     *      description="Store ArticlePricescale",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticlePricescale that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticlePricescale")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ArticlePricescale"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateArticlePricescaleAPIRequest $request)
    {
        $input = $request->all();

        $articlePricescales = $this->articlePricescaleRepository->create($input);

        return $this->sendResponse($articlePricescales->toArray(), 'Article Pricescale saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/articlePricescales/{id}",
     *      summary="Display the specified ArticlePricescale",
     *      tags={"ArticlePricescale"},
     *      description="Get ArticlePricescale",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticlePricescale",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ArticlePricescale"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var ArticlePricescale $articlePricescale */
        $articlePricescale = $this->articlePricescaleRepository->find($id);

        if (empty($articlePricescale)) {
            return $this->sendError('Article Pricescale not found');
        }

        return $this->sendResponse($articlePricescale->toArray(), 'Article Pricescale retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateArticlePricescaleAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/articlePricescales/{id}",
     *      summary="Update the specified ArticlePricescale in storage",
     *      tags={"ArticlePricescale"},
     *      description="Update ArticlePricescale",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticlePricescale",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticlePricescale that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticlePricescale")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ArticlePricescale"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateArticlePricescaleAPIRequest $request)
    {
        $input = $request->all();

        /** @var ArticlePricescale $articlePricescale */
        $articlePricescale = $this->articlePricescaleRepository->find($id);

        if (empty($articlePricescale)) {
            return $this->sendError('Article Pricescale not found');
        }

        $articlePricescale = $this->articlePricescaleRepository->update($input, $id);

        return $this->sendResponse($articlePricescale->toArray(), 'ArticlePricescale updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/articlePricescales/{id}",
     *      summary="Remove the specified ArticlePricescale from storage",
     *      tags={"ArticlePricescale"},
     *      description="Delete ArticlePricescale",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticlePricescale",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var ArticlePricescale $articlePricescale */
        $articlePricescale = $this->articlePricescaleRepository->find($id);

        if (empty($articlePricescale)) {
            return $this->sendError('Article Pricescale not found');
        }

        $articlePricescale->delete();

        return $this->sendResponse($id, 'Article Pricescale deleted successfully');
    }
}
