<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateArticleTagAPIRequest;
use App\Http\Requests\API\UpdateArticleTagAPIRequest;
use App\Models\ArticleTag;
use App\Repositories\ArticleTagRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ArticleTagController
 * @package App\Http\Controllers\API
 */

class ArticleTagAPIController extends AppBaseController
{
    /** @var  ArticleTagRepository */
    private $articleTagRepository;

    public function __construct(ArticleTagRepository $articleTagRepo)
    {
        $this->articleTagRepository = $articleTagRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleTags",
     *      summary="Get a listing of the ArticleTags.",
     *      tags={"ArticleTag"},
     *      description="Get all ArticleTags",
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
     *                  @SWG\Items(ref="#/definitions/ArticleTag")
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
        $this->articleTagRepository->pushCriteria(new RequestCriteria($request));
        $this->articleTagRepository->pushCriteria(new LimitOffsetCriteria($request));
        $articleTags = $this->articleTagRepository->all();

        return $this->sendResponse($articleTags->toArray(), 'Article Tags retrieved successfully');
    }

    /**
     * @param CreateArticleTagAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/articleTags",
     *      summary="Store a newly created ArticleTag in storage",
     *      tags={"ArticleTag"},
     *      description="Store ArticleTag",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleTag that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleTag")
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
     *                  ref="#/definitions/ArticleTag"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateArticleTagAPIRequest $request)
    {
        $input = $request->all();

        $articleTags = $this->articleTagRepository->create($input);

        return $this->sendResponse($articleTags->toArray(), 'Article Tag saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleTags/{id}",
     *      summary="Display the specified ArticleTag",
     *      tags={"ArticleTag"},
     *      description="Get ArticleTag",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleTag",
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
     *                  ref="#/definitions/ArticleTag"
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
        /** @var ArticleTag $articleTag */
        $articleTag = $this->articleTagRepository->find($id);

        if (empty($articleTag)) {
            return $this->sendError('Article Tag not found');
        }

        return $this->sendResponse($articleTag->toArray(), 'Article Tag retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateArticleTagAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/articleTags/{id}",
     *      summary="Update the specified ArticleTag in storage",
     *      tags={"ArticleTag"},
     *      description="Update ArticleTag",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleTag",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleTag that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleTag")
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
     *                  ref="#/definitions/ArticleTag"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateArticleTagAPIRequest $request)
    {
        $input = $request->all();

        /** @var ArticleTag $articleTag */
        $articleTag = $this->articleTagRepository->find($id);

        if (empty($articleTag)) {
            return $this->sendError('Article Tag not found');
        }

        $articleTag = $this->articleTagRepository->update($input, $id);

        return $this->sendResponse($articleTag->toArray(), 'ArticleTag updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/articleTags/{id}",
     *      summary="Remove the specified ArticleTag from storage",
     *      tags={"ArticleTag"},
     *      description="Delete ArticleTag",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleTag",
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
        /** @var ArticleTag $articleTag */
        $articleTag = $this->articleTagRepository->find($id);

        if (empty($articleTag)) {
            return $this->sendError('Article Tag not found');
        }

        $articleTag->delete();

        return $this->sendResponse($id, 'Article Tag deleted successfully');
    }
}
