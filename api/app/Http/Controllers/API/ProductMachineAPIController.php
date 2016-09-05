<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductMachineAPIRequest;
use App\Http\Requests\API\UpdateProductMachineAPIRequest;
use App\Models\ProductMachine;
use App\Repositories\ProductMachineRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProductMachineController
 * @package App\Http\Controllers\API
 */

class ProductMachineAPIController extends AppBaseController
{
    /** @var  ProductMachineRepository */
    private $productMachineRepository;

    public function __construct(ProductMachineRepository $productMachineRepo)
    {
        $this->productMachineRepository = $productMachineRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/productMachines",
     *      summary="Get a listing of the ProductMachines.",
     *      tags={"ProductMachine"},
     *      description="Get all ProductMachines",
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
     *                  @SWG\Items(ref="#/definitions/ProductMachine")
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
        $this->productMachineRepository->pushCriteria(new RequestCriteria($request));
        $this->productMachineRepository->pushCriteria(new LimitOffsetCriteria($request));
        $productMachines = $this->productMachineRepository->all();

        return $this->sendResponse($productMachines->toArray(), 'Product Machines retrieved successfully');
    }

    /**
     * @param CreateProductMachineAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/productMachines",
     *      summary="Store a newly created ProductMachine in storage",
     *      tags={"ProductMachine"},
     *      description="Store ProductMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductMachine that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductMachine")
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
     *                  ref="#/definitions/ProductMachine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateProductMachineAPIRequest $request)
    {
        $input = $request->all();

        $productMachines = $this->productMachineRepository->create($input);

        return $this->sendResponse($productMachines->toArray(), 'Product Machine saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/productMachines/{id}",
     *      summary="Display the specified ProductMachine",
     *      tags={"ProductMachine"},
     *      description="Get ProductMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductMachine",
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
     *                  ref="#/definitions/ProductMachine"
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
        /** @var ProductMachine $productMachine */
        $productMachine = $this->productMachineRepository->find($id);

        if (empty($productMachine)) {
            return $this->sendError('Product Machine not found');
        }

        return $this->sendResponse($productMachine->toArray(), 'Product Machine retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateProductMachineAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/productMachines/{id}",
     *      summary="Update the specified ProductMachine in storage",
     *      tags={"ProductMachine"},
     *      description="Update ProductMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductMachine",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ProductMachine that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ProductMachine")
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
     *                  ref="#/definitions/ProductMachine"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateProductMachineAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProductMachine $productMachine */
        $productMachine = $this->productMachineRepository->find($id);

        if (empty($productMachine)) {
            return $this->sendError('Product Machine not found');
        }

        $productMachine = $this->productMachineRepository->update($input, $id);

        return $this->sendResponse($productMachine->toArray(), 'ProductMachine updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/productMachines/{id}",
     *      summary="Remove the specified ProductMachine from storage",
     *      tags={"ProductMachine"},
     *      description="Delete ProductMachine",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ProductMachine",
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
        /** @var ProductMachine $productMachine */
        $productMachine = $this->productMachineRepository->find($id);

        if (empty($productMachine)) {
            return $this->sendError('Product Machine not found');
        }

        $productMachine->delete();

        return $this->sendResponse($id, 'Product Machine deleted successfully');
    }
}
