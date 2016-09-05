<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFormatsAPIRequest;
use App\Http\Requests\API\UpdateFormatsAPIRequest;
use App\Models\Formats;
use App\Repositories\FormatsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FormatsController
 * @package App\Http\Controllers\API
 */

class FormatsAPIController extends AppBaseController
{
    /** @var  FormatsRepository */
    private $formatsRepository;

    public function __construct(FormatsRepository $formatsRepo)
    {
        $this->formatsRepository = $formatsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/formats",
     *      summary="Get a listing of the Formats.",
     *      tags={"Formats"},
     *      description="Get all Formats",
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
     *                  @SWG\Items(ref="#/definitions/Formats")
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
        $this->formatsRepository->pushCriteria(new RequestCriteria($request));
        $this->formatsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $formats = $this->formatsRepository->all();

        return $this->sendResponse($formats->toArray(), 'Formats retrieved successfully');
    }

    /**
     * @param CreateFormatsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/formats",
     *      summary="Store a newly created Formats in storage",
     *      tags={"Formats"},
     *      description="Store Formats",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Formats that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Formats")
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
     *                  ref="#/definitions/Formats"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFormatsAPIRequest $request)
    {
        $input = $request->all();

        $formats = $this->formatsRepository->create($input);

        return $this->sendResponse($formats->toArray(), 'Formats saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/formats/{id}",
     *      summary="Display the specified Formats",
     *      tags={"Formats"},
     *      description="Get Formats",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Formats",
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
     *                  ref="#/definitions/Formats"
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
        /** @var Formats $formats */
        $formats = $this->formatsRepository->find($id);

        if (empty($formats)) {
            return $this->sendError('Formats not found');
        }

        return $this->sendResponse($formats->toArray(), 'Formats retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFormatsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/formats/{id}",
     *      summary="Update the specified Formats in storage",
     *      tags={"Formats"},
     *      description="Update Formats",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Formats",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Formats that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Formats")
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
     *                  ref="#/definitions/Formats"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFormatsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Formats $formats */
        $formats = $this->formatsRepository->find($id);

        if (empty($formats)) {
            return $this->sendError('Formats not found');
        }

        $formats = $this->formatsRepository->update($input, $id);

        return $this->sendResponse($formats->toArray(), 'Formats updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/formats/{id}",
     *      summary="Remove the specified Formats from storage",
     *      tags={"Formats"},
     *      description="Delete Formats",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Formats",
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
        /** @var Formats $formats */
        $formats = $this->formatsRepository->find($id);

        if (empty($formats)) {
            return $this->sendError('Formats not found');
        }

        $formats->delete();

        return $this->sendResponse($id, 'Formats deleted successfully');
    }
}
