<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateArticlePictureAPIRequest;
use App\Http\Requests\API\UpdateArticlePictureAPIRequest;
use App\Models\ArticlePicture;
use App\Repositories\ArticlePictureRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ArticlePictureController
 * @package App\Http\Controllers\API
 */

class ArticlePictureAPIController extends AppBaseController
{
    /** @var  ArticlePictureRepository */
    private $articlePictureRepository;

    public function __construct(ArticlePictureRepository $articlePictureRepo)
    {
        $this->articlePictureRepository = $articlePictureRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/articlePictures",
     *      summary="Get a listing of the ArticlePictures.",
     *      tags={"ArticlePicture"},
     *      description="Get all ArticlePictures",
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
     *                  @SWG\Items(ref="#/definitions/ArticlePicture")
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
        $this->articlePictureRepository->pushCriteria(new RequestCriteria($request));
        $this->articlePictureRepository->pushCriteria(new LimitOffsetCriteria($request));
        $articlePictures = $this->articlePictureRepository->all();

        return $this->sendResponse($articlePictures->toArray(), 'Article Pictures retrieved successfully');
    }

    /**
     * @param CreateArticlePictureAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/articlePictures",
     *      summary="Store a newly created ArticlePicture in storage",
     *      tags={"ArticlePicture"},
     *      description="Store ArticlePicture",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticlePicture that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticlePicture")
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
     *                  ref="#/definitions/ArticlePicture"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateArticlePictureAPIRequest $request)
    {
        $input = $request->all();

        $articlePictures = $this->articlePictureRepository->create($input);

        return $this->sendResponse($articlePictures->toArray(), 'Article Picture saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/articlePictures/{id}",
     *      summary="Display the specified ArticlePicture",
     *      tags={"ArticlePicture"},
     *      description="Get ArticlePicture",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticlePicture",
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
     *                  ref="#/definitions/ArticlePicture"
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
        /** @var ArticlePicture $articlePicture */
        $articlePicture = $this->articlePictureRepository->find($id);

        if (empty($articlePicture)) {
            return $this->sendError('Article Picture not found');
        }

        return $this->sendResponse($articlePicture->toArray(), 'Article Picture retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateArticlePictureAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/articlePictures/{id}",
     *      summary="Update the specified ArticlePicture in storage",
     *      tags={"ArticlePicture"},
     *      description="Update ArticlePicture",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticlePicture",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ArticlePicture that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ArticlePicture")
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
     *                  ref="#/definitions/ArticlePicture"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateArticlePictureAPIRequest $request)
    {
        $input = $request->all();

        /** @var ArticlePicture $articlePicture */
        $articlePicture = $this->articlePictureRepository->find($id);

        if (empty($articlePicture)) {
            return $this->sendError('Article Picture not found');
        }

        $articlePicture = $this->articlePictureRepository->update($input, $id);

        return $this->sendResponse($articlePicture->toArray(), 'ArticlePicture updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/articlePictures/{id}",
     *      summary="Remove the specified ArticlePicture from storage",
     *      tags={"ArticlePicture"},
     *      description="Delete ArticlePicture",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ArticlePicture",
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
        /** @var ArticlePicture $articlePicture */
        $articlePicture = $this->articlePictureRepository->find($id);

        if (empty($articlePicture)) {
            return $this->sendError('Article Picture not found');
        }

        $articlePicture->delete();

        return $this->sendResponse($id, 'Article Picture deleted successfully');
    }
}
