<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductPaperAPIRequest;
use App\Http\Requests\API\UpdateProductPaperAPIRequest;
use App\Models\ProductPaper;
use App\Repositories\ProductPaperRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProductPaperController
 * @package App\Http\Controllers\API
 */

class ProductPaperAPIController extends AppBaseController
{
    /** @var  ProductPaperRepository */
    private $productPaperRepository;

    public function __construct(ProductPaperRepository $productPaperRepo)
    {
        $this->productPaperRepository = $productPaperRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/productPapers",
     *      summary="Get a listing of the ProductPapers.",
     *      tags={"ProductPaper"},
     *      description="Get all ProductPapers",
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
     *                  @SWG\Items(ref="#/definitions/ProductPaper")
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
        $this->productPaperRepository->pushCriteria(new RequestCriteria($request));
        $this->productPaperRepository->pushCriteria(new LimitOffsetCriteria($request));
        $productPapers = $this->productPaperRepository->all();

        return $this->sendResponse($productPapers->toArray(), 'Product Papers retrieved successfully');
    }

    /**
     * @param CreateProductPaperAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/productPapers",
     *      summary="Store a newly created ProductPaper in storage",
     *      tags={"ProductPaper"},
     *      description="Store ProductPaper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductPaper that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductPaper")
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
     *                  ref="#/definitions/ProductPaper"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProductPaperAPIRequest $request)
    {
        $input = $request->all();

        $productPapers = $this->productPaperRepository->create($input);

        return $this->sendResponse($productPapers->toArray(), 'Product Paper saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/productPapers/{id}",
     *      summary="Display the specified ProductPaper",
     *      tags={"ProductPaper"},
     *      description="Get ProductPaper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductPaper",
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
     *                  ref="#/definitions/ProductPaper"
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
        /** @var ProductPaper $productPaper */
        $productPaper = $this->productPaperRepository->find($id);

        if (empty($productPaper)) {
            return $this->sendError('Product Paper not found');
        }

        return $this->sendResponse($productPaper->toArray(), 'Product Paper retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateProductPaperAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/productPapers/{id}",
     *      summary="Update the specified ProductPaper in storage",
     *      tags={"ProductPaper"},
     *      description="Update ProductPaper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductPaper",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductPaper that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductPaper")
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
     *                  ref="#/definitions/ProductPaper"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProductPaperAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductPaper $productPaper */
        $productPaper = $this->productPaperRepository->find($id);

        if (empty($productPaper)) {
            return $this->sendError('Product Paper not found');
        }

        $productPaper = $this->productPaperRepository->update($input, $id);

        return $this->sendResponse($productPaper->toArray(), 'ProductPaper updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/productPapers/{id}",
     *      summary="Remove the specified ProductPaper from storage",
     *      tags={"ProductPaper"},
     *      description="Delete ProductPaper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductPaper",
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
        /** @var ProductPaper $productPaper */
        $productPaper = $this->productPaperRepository->find($id);

        if (empty($productPaper)) {
            return $this->sendError('Product Paper not found');
        }

        $productPaper->delete();

        return $this->sendResponse($id, 'Product Paper deleted successfully');
    }
}
