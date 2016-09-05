<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineChromaticityAPIRequest;
use App\Http\Requests\API\UpdateMachineChromaticityAPIRequest;
use App\Models\MachineChromaticity;
use App\Repositories\MachineChromaticityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineChromaticityController
 * @package App\Http\Controllers\API
 */

class MachineChromaticityAPIController extends AppBaseController
{
    /** @var  MachineChromaticityRepository */
    private $machineChromaticityRepository;

    public function __construct(MachineChromaticityRepository $machineChromaticityRepo)
    {
        $this->machineChromaticityRepository = $machineChromaticityRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineChromaticities",
     *      summary="Get a listing of the MachineChromaticities.",
     *      tags={"MachineChromaticity"},
     *      description="Get all MachineChromaticities",
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
     *                  @SWG\Items(ref="#/definitions/MachineChromaticity")
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
        $this->machineChromaticityRepository->pushCriteria(new RequestCriteria($request));
        $this->machineChromaticityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machineChromaticities = $this->machineChromaticityRepository->all();

        return $this->sendResponse($machineChromaticities->toArray(), 'Machine Chromaticities retrieved successfully');
    }

    /**
     * @param CreateMachineChromaticityAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machineChromaticities",
     *      summary="Store a newly created MachineChromaticity in storage",
     *      tags={"MachineChromaticity"},
     *      description="Store MachineChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineChromaticity that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineChromaticity")
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
     *                  ref="#/definitions/MachineChromaticity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineChromaticityAPIRequest $request)
    {
        $input = $request->all();

        $machineChromaticities = $this->machineChromaticityRepository->create($input);

        return $this->sendResponse($machineChromaticities->toArray(), 'Machine Chromaticity saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineChromaticities/{id}",
     *      summary="Display the specified MachineChromaticity",
     *      tags={"MachineChromaticity"},
     *      description="Get MachineChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineChromaticity",
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
     *                  ref="#/definitions/MachineChromaticity"
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
        /** @var MachineChromaticity $machineChromaticity */
        $machineChromaticity = $this->machineChromaticityRepository->find($id);

        if (empty($machineChromaticity)) {
            return $this->sendError('Machine Chromaticity not found');
        }

        return $this->sendResponse($machineChromaticity->toArray(), 'Machine Chromaticity retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineChromaticityAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machineChromaticities/{id}",
     *      summary="Update the specified MachineChromaticity in storage",
     *      tags={"MachineChromaticity"},
     *      description="Update MachineChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineChromaticity",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineChromaticity that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineChromaticity")
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
     *                  ref="#/definitions/MachineChromaticity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineChromaticityAPIRequest $request)
    {
        $input = $request->all();

        /** @var MachineChromaticity $machineChromaticity */
        $machineChromaticity = $this->machineChromaticityRepository->find($id);

        if (empty($machineChromaticity)) {
            return $this->sendError('Machine Chromaticity not found');
        }

        $machineChromaticity = $this->machineChromaticityRepository->update($input, $id);

        return $this->sendResponse($machineChromaticity->toArray(), 'MachineChromaticity updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machineChromaticities/{id}",
     *      summary="Remove the specified MachineChromaticity from storage",
     *      tags={"MachineChromaticity"},
     *      description="Delete MachineChromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineChromaticity",
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
        /** @var MachineChromaticity $machineChromaticity */
        $machineChromaticity = $this->machineChromaticityRepository->find($id);

        if (empty($machineChromaticity)) {
            return $this->sendError('Machine Chromaticity not found');
        }

        $machineChromaticity->delete();

        return $this->sendResponse($id, 'Machine Chromaticity deleted successfully');
    }
}
