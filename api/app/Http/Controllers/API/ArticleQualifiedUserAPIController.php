<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateArticleQualifiedUserAPIRequest;
use App\Http\Requests\API\UpdateArticleQualifiedUserAPIRequest;
use App\Models\ArticleQualifiedUser;
use App\Repositories\ArticleQualifiedUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ArticleQualifiedUserController
 * @package App\Http\Controllers\API
 */

class ArticleQualifiedUserAPIController extends AppBaseController
{
    /** @var  ArticleQualifiedUserRepository */
    private $articleQualifiedUserRepository;

    public function __construct(ArticleQualifiedUserRepository $articleQualifiedUserRepo)
    {
        $this->articleQualifiedUserRepository = $articleQualifiedUserRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleQualifiedUsers",
     *      summary="Get a listing of the ArticleQualifiedUsers.",
     *      tags={"ArticleQualifiedUser"},
     *      description="Get all ArticleQualifiedUsers",
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
     *                  @SWG\Items(ref="#/definitions/ArticleQualifiedUser")
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
        $this->articleQualifiedUserRepository->pushCriteria(new RequestCriteria($request));
        $this->articleQualifiedUserRepository->pushCriteria(new LimitOffsetCriteria($request));
        $articleQualifiedUsers = $this->articleQualifiedUserRepository->all();

        return $this->sendResponse($articleQualifiedUsers->toArray(), 'Article Qualified Users retrieved successfully');
    }

    /**
     * @param CreateArticleQualifiedUserAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/articleQualifiedUsers",
     *      summary="Store a newly created ArticleQualifiedUser in storage",
     *      tags={"ArticleQualifiedUser"},
     *      description="Store ArticleQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleQualifiedUser that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleQualifiedUser")
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
     *                  ref="#/definitions/ArticleQualifiedUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateArticleQualifiedUserAPIRequest $request)
    {
        $input = $request->all();

        $articleQualifiedUsers = $this->articleQualifiedUserRepository->create($input);

        return $this->sendResponse($articleQualifiedUsers->toArray(), 'Article Qualified User saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleQualifiedUsers/{id}",
     *      summary="Display the specified ArticleQualifiedUser",
     *      tags={"ArticleQualifiedUser"},
     *      description="Get ArticleQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleQualifiedUser",
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
     *                  ref="#/definitions/ArticleQualifiedUser"
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
        /** @var ArticleQualifiedUser $articleQualifiedUser */
        $articleQualifiedUser = $this->articleQualifiedUserRepository->find($id);

        if (empty($articleQualifiedUser)) {
            return $this->sendError('Article Qualified User not found');
        }

        return $this->sendResponse($articleQualifiedUser->toArray(), 'Article Qualified User retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateArticleQualifiedUserAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/articleQualifiedUsers/{id}",
     *      summary="Update the specified ArticleQualifiedUser in storage",
     *      tags={"ArticleQualifiedUser"},
     *      description="Update ArticleQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleQualifiedUser",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleQualifiedUser that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleQualifiedUser")
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
     *                  ref="#/definitions/ArticleQualifiedUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateArticleQualifiedUserAPIRequest $request)
    {
        $input = $request->all();

        /** @var ArticleQualifiedUser $articleQualifiedUser */
        $articleQualifiedUser = $this->articleQualifiedUserRepository->find($id);

        if (empty($articleQualifiedUser)) {
            return $this->sendError('Article Qualified User not found');
        }

        $articleQualifiedUser = $this->articleQualifiedUserRepository->update($input, $id);

        return $this->sendResponse($articleQualifiedUser->toArray(), 'ArticleQualifiedUser updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/articleQualifiedUsers/{id}",
     *      summary="Remove the specified ArticleQualifiedUser from storage",
     *      tags={"ArticleQualifiedUser"},
     *      description="Delete ArticleQualifiedUser",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleQualifiedUser",
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
        /** @var ArticleQualifiedUser $articleQualifiedUser */
        $articleQualifiedUser = $this->articleQualifiedUserRepository->find($id);

        if (empty($articleQualifiedUser)) {
            return $this->sendError('Article Qualified User not found');
        }

        $articleQualifiedUser->delete();

        return $this->sendResponse($id, 'Article Qualified User deleted successfully');
    }
}
