<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateArticleOrderamountAPIRequest;
use App\Http\Requests\API\UpdateArticleOrderamountAPIRequest;
use App\Models\ArticleOrderamount;
use App\Repositories\ArticleOrderamountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ArticleOrderamountController
 * @package App\Http\Controllers\API
 */

class ArticleOrderamountAPIController extends AppBaseController
{
    /** @var  ArticleOrderamountRepository */
    private $articleOrderamountRepository;

    public function __construct(ArticleOrderamountRepository $articleOrderamountRepo)
    {
        $this->articleOrderamountRepository = $articleOrderamountRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleOrderamounts",
     *      summary="Get a listing of the ArticleOrderamounts.",
     *      tags={"ArticleOrderamount"},
     *      description="Get all ArticleOrderamounts",
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
     *                  @SWG\Items(ref="#/definitions/ArticleOrderamount")
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
        $this->articleOrderamountRepository->pushCriteria(new RequestCriteria($request));
        $this->articleOrderamountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $articleOrderamounts = $this->articleOrderamountRepository->all();

        return $this->sendResponse($articleOrderamounts->toArray(), 'Article Orderamounts retrieved successfully');
    }

    /**
     * @param CreateArticleOrderamountAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/articleOrderamounts",
     *      summary="Store a newly created ArticleOrderamount in storage",
     *      tags={"ArticleOrderamount"},
     *      description="Store ArticleOrderamount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleOrderamount that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleOrderamount")
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
     *                  ref="#/definitions/ArticleOrderamount"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateArticleOrderamountAPIRequest $request)
    {
        $input = $request->all();

        $articleOrderamounts = $this->articleOrderamountRepository->create($input);

        return $this->sendResponse($articleOrderamounts->toArray(), 'Article Orderamount saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleOrderamounts/{id}",
     *      summary="Display the specified ArticleOrderamount",
     *      tags={"ArticleOrderamount"},
     *      description="Get ArticleOrderamount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleOrderamount",
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
     *                  ref="#/definitions/ArticleOrderamount"
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
        /** @var ArticleOrderamount $articleOrderamount */
        $articleOrderamount = $this->articleOrderamountRepository->find($id);

        if (empty($articleOrderamount)) {
            return $this->sendError('Article Orderamount not found');
        }

        return $this->sendResponse($articleOrderamount->toArray(), 'Article Orderamount retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateArticleOrderamountAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/articleOrderamounts/{id}",
     *      summary="Update the specified ArticleOrderamount in storage",
     *      tags={"ArticleOrderamount"},
     *      description="Update ArticleOrderamount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleOrderamount",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleOrderamount that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleOrderamount")
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
     *                  ref="#/definitions/ArticleOrderamount"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateArticleOrderamountAPIRequest $request)
    {
        $input = $request->all();

        /** @var ArticleOrderamount $articleOrderamount */
        $articleOrderamount = $this->articleOrderamountRepository->find($id);

        if (empty($articleOrderamount)) {
            return $this->sendError('Article Orderamount not found');
        }

        $articleOrderamount = $this->articleOrderamountRepository->update($input, $id);

        return $this->sendResponse($articleOrderamount->toArray(), 'ArticleOrderamount updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/articleOrderamounts/{id}",
     *      summary="Remove the specified ArticleOrderamount from storage",
     *      tags={"ArticleOrderamount"},
     *      description="Delete ArticleOrderamount",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleOrderamount",
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
        /** @var ArticleOrderamount $articleOrderamount */
        $articleOrderamount = $this->articleOrderamountRepository->find($id);

        if (empty($articleOrderamount)) {
            return $this->sendError('Article Orderamount not found');
        }

        $articleOrderamount->delete();

        return $this->sendResponse($id, 'Article Orderamount deleted successfully');
    }
}
