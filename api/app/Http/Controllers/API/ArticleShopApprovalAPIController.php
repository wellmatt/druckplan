<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateArticleShopApprovalAPIRequest;
use App\Http\Requests\API\UpdateArticleShopApprovalAPIRequest;
use App\Models\ArticleShopApproval;
use App\Repositories\ArticleShopApprovalRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ArticleShopApprovalController
 * @package App\Http\Controllers\API
 */

class ArticleShopApprovalAPIController extends AppBaseController
{
    /** @var  ArticleShopApprovalRepository */
    private $articleShopApprovalRepository;

    public function __construct(ArticleShopApprovalRepository $articleShopApprovalRepo)
    {
        $this->articleShopApprovalRepository = $articleShopApprovalRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleShopApprovals",
     *      summary="Get a listing of the ArticleShopApprovals.",
     *      tags={"ArticleShopApproval"},
     *      description="Get all ArticleShopApprovals",
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
     *                  @SWG\Items(ref="#/definitions/ArticleShopApproval")
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
        $this->articleShopApprovalRepository->pushCriteria(new RequestCriteria($request));
        $this->articleShopApprovalRepository->pushCriteria(new LimitOffsetCriteria($request));
        $articleShopApprovals = $this->articleShopApprovalRepository->all();

        return $this->sendResponse($articleShopApprovals->toArray(), 'Article Shop Approvals retrieved successfully');
    }

    /**
     * @param CreateArticleShopApprovalAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/articleShopApprovals",
     *      summary="Store a newly created ArticleShopApproval in storage",
     *      tags={"ArticleShopApproval"},
     *      description="Store ArticleShopApproval",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleShopApproval that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleShopApproval")
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
     *                  ref="#/definitions/ArticleShopApproval"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateArticleShopApprovalAPIRequest $request)
    {
        $input = $request->all();

        $articleShopApprovals = $this->articleShopApprovalRepository->create($input);

        return $this->sendResponse($articleShopApprovals->toArray(), 'Article Shop Approval saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/articleShopApprovals/{id}",
     *      summary="Display the specified ArticleShopApproval",
     *      tags={"ArticleShopApproval"},
     *      description="Get ArticleShopApproval",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleShopApproval",
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
     *                  ref="#/definitions/ArticleShopApproval"
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
        /** @var ArticleShopApproval $articleShopApproval */
        $articleShopApproval = $this->articleShopApprovalRepository->find($id);

        if (empty($articleShopApproval)) {
            return $this->sendError('Article Shop Approval not found');
        }

        return $this->sendResponse($articleShopApproval->toArray(), 'Article Shop Approval retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateArticleShopApprovalAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/articleShopApprovals/{id}",
     *      summary="Update the specified ArticleShopApproval in storage",
     *      tags={"ArticleShopApproval"},
     *      description="Update ArticleShopApproval",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleShopApproval",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticleShopApproval that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticleShopApproval")
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
     *                  ref="#/definitions/ArticleShopApproval"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateArticleShopApprovalAPIRequest $request)
    {
        $input = $request->all();

        /** @var ArticleShopApproval $articleShopApproval */
        $articleShopApproval = $this->articleShopApprovalRepository->find($id);

        if (empty($articleShopApproval)) {
            return $this->sendError('Article Shop Approval not found');
        }

        $articleShopApproval = $this->articleShopApprovalRepository->update($input, $id);

        return $this->sendResponse($articleShopApproval->toArray(), 'ArticleShopApproval updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/articleShopApprovals/{id}",
     *      summary="Remove the specified ArticleShopApproval from storage",
     *      tags={"ArticleShopApproval"},
     *      description="Delete ArticleShopApproval",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticleShopApproval",
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
        /** @var ArticleShopApproval $articleShopApproval */
        $articleShopApproval = $this->articleShopApprovalRepository->find($id);

        if (empty($articleShopApproval)) {
            return $this->sendError('Article Shop Approval not found');
        }

        $articleShopApproval->delete();

        return $this->sendResponse($id, 'Article Shop Approval deleted successfully');
    }
}
