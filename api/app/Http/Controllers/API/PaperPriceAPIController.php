<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaperPriceAPIRequest;
use App\Http\Requests\API\UpdatePaperPriceAPIRequest;
use App\Models\PaperPrice;
use App\Repositories\PaperPriceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaperPriceController
 * @package App\Http\Controllers\API
 */

class PaperPriceAPIController extends AppBaseController
{
    /** @var  PaperPriceRepository */
    private $paperPriceRepository;

    public function __construct(PaperPriceRepository $paperPriceRepo)
    {
        $this->paperPriceRepository = $paperPriceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperPrices",
     *      summary="Get a listing of the PaperPrices.",
     *      tags={"PaperPrice"},
     *      description="Get all PaperPrices",
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
     *                  @SWG\Items(ref="#/definitions/PaperPrice")
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
        $this->paperPriceRepository->pushCriteria(new RequestCriteria($request));
        $this->paperPriceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paperPrices = $this->paperPriceRepository->all();

        return $this->sendResponse($paperPrices->toArray(), 'Paper Prices retrieved successfully');
    }

    /**
     * @param CreatePaperPriceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paperPrices",
     *      summary="Store a newly created PaperPrice in storage",
     *      tags={"PaperPrice"},
     *      description="Store PaperPrice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperPrice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperPrice")
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
     *                  ref="#/definitions/PaperPrice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaperPriceAPIRequest $request)
    {
        $input = $request->all();

        $paperPrices = $this->paperPriceRepository->create($input);

        return $this->sendResponse($paperPrices->toArray(), 'Paper Price saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperPrices/{id}",
     *      summary="Display the specified PaperPrice",
     *      tags={"PaperPrice"},
     *      description="Get PaperPrice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperPrice",
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
     *                  ref="#/definitions/PaperPrice"
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
        /** @var PaperPrice $paperPrice */
        $paperPrice = $this->paperPriceRepository->find($id);

        if (empty($paperPrice)) {
            return $this->sendError('Paper Price not found');
        }

        return $this->sendResponse($paperPrice->toArray(), 'Paper Price retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaperPriceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paperPrices/{id}",
     *      summary="Update the specified PaperPrice in storage",
     *      tags={"PaperPrice"},
     *      description="Update PaperPrice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperPrice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperPrice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperPrice")
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
     *                  ref="#/definitions/PaperPrice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaperPriceAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaperPrice $paperPrice */
        $paperPrice = $this->paperPriceRepository->find($id);

        if (empty($paperPrice)) {
            return $this->sendError('Paper Price not found');
        }

        $paperPrice = $this->paperPriceRepository->update($input, $id);

        return $this->sendResponse($paperPrice->toArray(), 'PaperPrice updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paperPrices/{id}",
     *      summary="Remove the specified PaperPrice from storage",
     *      tags={"PaperPrice"},
     *      description="Delete PaperPrice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperPrice",
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
        /** @var PaperPrice $paperPrice */
        $paperPrice = $this->paperPriceRepository->find($id);

        if (empty($paperPrice)) {
            return $this->sendError('Paper Price not found');
        }

        $paperPrice->delete();

        return $this->sendResponse($id, 'Paper Price deleted successfully');
    }
}
