<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaperSizeAPIRequest;
use App\Http\Requests\API\UpdatePaperSizeAPIRequest;
use App\Models\PaperSize;
use App\Repositories\PaperSizeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaperSizeController
 * @package App\Http\Controllers\API
 */

class PaperSizeAPIController extends AppBaseController
{
    /** @var  PaperSizeRepository */
    private $paperSizeRepository;

    public function __construct(PaperSizeRepository $paperSizeRepo)
    {
        $this->paperSizeRepository = $paperSizeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperSizes",
     *      summary="Get a listing of the PaperSizes.",
     *      tags={"PaperSize"},
     *      description="Get all PaperSizes",
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
     *                  @SWG\Items(ref="#/definitions/PaperSize")
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
        $this->paperSizeRepository->pushCriteria(new RequestCriteria($request));
        $this->paperSizeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paperSizes = $this->paperSizeRepository->all();

        return $this->sendResponse($paperSizes->toArray(), 'Paper Sizes retrieved successfully');
    }

    /**
     * @param CreatePaperSizeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paperSizes",
     *      summary="Store a newly created PaperSize in storage",
     *      tags={"PaperSize"},
     *      description="Store PaperSize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperSize that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperSize")
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
     *                  ref="#/definitions/PaperSize"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaperSizeAPIRequest $request)
    {
        $input = $request->all();

        $paperSizes = $this->paperSizeRepository->create($input);

        return $this->sendResponse($paperSizes->toArray(), 'Paper Size saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paperSizes/{id}",
     *      summary="Display the specified PaperSize",
     *      tags={"PaperSize"},
     *      description="Get PaperSize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperSize",
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
     *                  ref="#/definitions/PaperSize"
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
        /** @var PaperSize $paperSize */
        $paperSize = $this->paperSizeRepository->find($id);

        if (empty($paperSize)) {
            return $this->sendError('Paper Size not found');
        }

        return $this->sendResponse($paperSize->toArray(), 'Paper Size retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaperSizeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paperSizes/{id}",
     *      summary="Update the specified PaperSize in storage",
     *      tags={"PaperSize"},
     *      description="Update PaperSize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperSize",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaperSize that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaperSize")
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
     *                  ref="#/definitions/PaperSize"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaperSizeAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaperSize $paperSize */
        $paperSize = $this->paperSizeRepository->find($id);

        if (empty($paperSize)) {
            return $this->sendError('Paper Size not found');
        }

        $paperSize = $this->paperSizeRepository->update($input, $id);

        return $this->sendResponse($paperSize->toArray(), 'PaperSize updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paperSizes/{id}",
     *      summary="Remove the specified PaperSize from storage",
     *      tags={"PaperSize"},
     *      description="Delete PaperSize",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaperSize",
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
        /** @var PaperSize $paperSize */
        $paperSize = $this->paperSizeRepository->find($id);

        if (empty($paperSize)) {
            return $this->sendError('Paper Size not found');
        }

        $paperSize->delete();

        return $this->sendResponse($id, 'Paper Size deleted successfully');
    }
}
