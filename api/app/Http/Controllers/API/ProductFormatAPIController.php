<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductFormatAPIRequest;
use App\Http\Requests\API\UpdateProductFormatAPIRequest;
use App\Models\ProductFormat;
use App\Repositories\ProductFormatRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProductFormatController
 * @package App\Http\Controllers\API
 */

class ProductFormatAPIController extends AppBaseController
{
    /** @var  ProductFormatRepository */
    private $productFormatRepository;

    public function __construct(ProductFormatRepository $productFormatRepo)
    {
        $this->productFormatRepository = $productFormatRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/productFormats",
     *      summary="Get a listing of the ProductFormats.",
     *      tags={"ProductFormat"},
     *      description="Get all ProductFormats",
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
     *                  @SWG\Items(ref="#/definitions/ProductFormat")
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
        $this->productFormatRepository->pushCriteria(new RequestCriteria($request));
        $this->productFormatRepository->pushCriteria(new LimitOffsetCriteria($request));
        $productFormats = $this->productFormatRepository->all();

        return $this->sendResponse($productFormats->toArray(), 'Product Formats retrieved successfully');
    }

    /**
     * @param CreateProductFormatAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/productFormats",
     *      summary="Store a newly created ProductFormat in storage",
     *      tags={"ProductFormat"},
     *      description="Store ProductFormat",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductFormat that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductFormat")
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
     *                  ref="#/definitions/ProductFormat"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProductFormatAPIRequest $request)
    {
        $input = $request->all();

        $productFormats = $this->productFormatRepository->create($input);

        return $this->sendResponse($productFormats->toArray(), 'Product Format saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/productFormats/{id}",
     *      summary="Display the specified ProductFormat",
     *      tags={"ProductFormat"},
     *      description="Get ProductFormat",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductFormat",
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
     *                  ref="#/definitions/ProductFormat"
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
        /** @var ProductFormat $productFormat */
        $productFormat = $this->productFormatRepository->find($id);

        if (empty($productFormat)) {
            return $this->sendError('Product Format not found');
        }

        return $this->sendResponse($productFormat->toArray(), 'Product Format retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateProductFormatAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/productFormats/{id}",
     *      summary="Update the specified ProductFormat in storage",
     *      tags={"ProductFormat"},
     *      description="Update ProductFormat",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductFormat",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductFormat that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductFormat")
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
     *                  ref="#/definitions/ProductFormat"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProductFormatAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductFormat $productFormat */
        $productFormat = $this->productFormatRepository->find($id);

        if (empty($productFormat)) {
            return $this->sendError('Product Format not found');
        }

        $productFormat = $this->productFormatRepository->update($input, $id);

        return $this->sendResponse($productFormat->toArray(), 'ProductFormat updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/productFormats/{id}",
     *      summary="Remove the specified ProductFormat from storage",
     *      tags={"ProductFormat"},
     *      description="Delete ProductFormat",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductFormat",
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
        /** @var ProductFormat $productFormat */
        $productFormat = $this->productFormatRepository->find($id);

        if (empty($productFormat)) {
            return $this->sendError('Product Format not found');
        }

        $productFormat->delete();

        return $this->sendResponse($id, 'Product Format deleted successfully');
    }
}
