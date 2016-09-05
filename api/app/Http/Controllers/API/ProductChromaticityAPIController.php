<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductChromaticityAPIRequest;
use App\Http\Requests\API\UpdateProductChromaticityAPIRequest;
use App\Models\ProductChromaticity;
use App\Repositories\ProductChromaticityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProductChromaticityController
 * @package App\Http\Controllers\API
 */

class ProductChromaticityAPIController extends AppBaseController
{
    /** @var  ProductChromaticityRepository */
    private $productChromaticityRepository;

    public function __construct(ProductChromaticityRepository $productChromaticityRepo)
    {
        $this->productChromaticityRepository = $productChromaticityRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/productChromaticities",
     *      summary="Get a listing of the ProductChromaticities.",
     *      tags={"ProductChromaticity"},
     *      description="Get all ProductChromaticities",
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
     *                  @SWG\Items(ref="#/definitions/ProductChromaticity")
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
        $this->productChromaticityRepository->pushCriteria(new RequestCriteria($request));
        $this->productChromaticityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $productChromaticities = $this->productChromaticityRepository->all();

        return $this->sendResponse($productChromaticities->toArray(), 'Product Chromaticities retrieved successfully');
    }

    /**
     * @param CreateProductChromaticityAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/productChromaticities",
     *      summary="Store a newly created ProductChromaticity in storage",
     *      tags={"ProductChromaticity"},
     *      description="Store ProductChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductChromaticity that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductChromaticity")
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
     *                  ref="#/definitions/ProductChromaticity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProductChromaticityAPIRequest $request)
    {
        $input = $request->all();

        $productChromaticities = $this->productChromaticityRepository->create($input);

        return $this->sendResponse($productChromaticities->toArray(), 'Product Chromaticity saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/productChromaticities/{id}",
     *      summary="Display the specified ProductChromaticity",
     *      tags={"ProductChromaticity"},
     *      description="Get ProductChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductChromaticity",
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
     *                  ref="#/definitions/ProductChromaticity"
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
        /** @var ProductChromaticity $productChromaticity */
        $productChromaticity = $this->productChromaticityRepository->find($id);

        if (empty($productChromaticity)) {
            return $this->sendError('Product Chromaticity not found');
        }

        return $this->sendResponse($productChromaticity->toArray(), 'Product Chromaticity retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateProductChromaticityAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/productChromaticities/{id}",
     *      summary="Update the specified ProductChromaticity in storage",
     *      tags={"ProductChromaticity"},
     *      description="Update ProductChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductChromaticity",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductChromaticity that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductChromaticity")
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
     *                  ref="#/definitions/ProductChromaticity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProductChromaticityAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductChromaticity $productChromaticity */
        $productChromaticity = $this->productChromaticityRepository->find($id);

        if (empty($productChromaticity)) {
            return $this->sendError('Product Chromaticity not found');
        }

        $productChromaticity = $this->productChromaticityRepository->update($input, $id);

        return $this->sendResponse($productChromaticity->toArray(), 'ProductChromaticity updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/productChromaticities/{id}",
     *      summary="Remove the specified ProductChromaticity from storage",
     *      tags={"ProductChromaticity"},
     *      description="Delete ProductChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductChromaticity",
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
        /** @var ProductChromaticity $productChromaticity */
        $productChromaticity = $this->productChromaticityRepository->find($id);

        if (empty($productChromaticity)) {
            return $this->sendError('Product Chromaticity not found');
        }

        $productChromaticity->delete();

        return $this->sendResponse($id, 'Product Chromaticity deleted successfully');
    }
}
