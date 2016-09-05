<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChromaticityAPIRequest;
use App\Http\Requests\API\UpdateChromaticityAPIRequest;
use App\Models\Chromaticity;
use App\Repositories\ChromaticityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChromaticityController
 * @package App\Http\Controllers\API
 */

class ChromaticityAPIController extends AppBaseController
{
    /** @var  ChromaticityRepository */
    private $chromaticityRepository;

    public function __construct(ChromaticityRepository $chromaticityRepo)
    {
        $this->chromaticityRepository = $chromaticityRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chromaticities",
     *      summary="Get a listing of the Chromaticities.",
     *      tags={"Chromaticity"},
     *      description="Get all Chromaticities",
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
     *                  @SWG\Items(ref="#/definitions/Chromaticity")
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
        $this->chromaticityRepository->pushCriteria(new RequestCriteria($request));
        $this->chromaticityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chromaticities = $this->chromaticityRepository->all();

        return $this->sendResponse($chromaticities->toArray(), 'Chromaticities retrieved successfully');
    }

    /**
     * @param CreateChromaticityAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chromaticities",
     *      summary="Store a newly created Chromaticity in storage",
     *      tags={"Chromaticity"},
     *      description="Store Chromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Chromaticity that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Chromaticity")
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
     *                  ref="#/definitions/Chromaticity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChromaticityAPIRequest $request)
    {
        $input = $request->all();

        $chromaticities = $this->chromaticityRepository->create($input);

        return $this->sendResponse($chromaticities->toArray(), 'Chromaticity saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chromaticities/{id}",
     *      summary="Display the specified Chromaticity",
     *      tags={"Chromaticity"},
     *      description="Get Chromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Chromaticity",
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
     *                  ref="#/definitions/Chromaticity"
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
        /** @var Chromaticity $chromaticity */
        $chromaticity = $this->chromaticityRepository->find($id);

        if (empty($chromaticity)) {
            return $this->sendError('Chromaticity not found');
        }

        return $this->sendResponse($chromaticity->toArray(), 'Chromaticity retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateChromaticityAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chromaticities/{id}",
     *      summary="Update the specified Chromaticity in storage",
     *      tags={"Chromaticity"},
     *      description="Update Chromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Chromaticity",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Chromaticity that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Chromaticity")
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
     *                  ref="#/definitions/Chromaticity"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChromaticityAPIRequest $request)
    {
        $input = $request->all();

        /** @var Chromaticity $chromaticity */
        $chromaticity = $this->chromaticityRepository->find($id);

        if (empty($chromaticity)) {
            return $this->sendError('Chromaticity not found');
        }

        $chromaticity = $this->chromaticityRepository->update($input, $id);

        return $this->sendResponse($chromaticity->toArray(), 'Chromaticity updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chromaticities/{id}",
     *      summary="Remove the specified Chromaticity from storage",
     *      tags={"Chromaticity"},
     *      description="Delete Chromaticity",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Chromaticity",
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
        /** @var Chromaticity $chromaticity */
        $chromaticity = $this->chromaticityRepository->find($id);

        if (empty($chromaticity)) {
            return $this->sendError('Chromaticity not found');
        }

        $chromaticity->delete();

        return $this->sendResponse($id, 'Chromaticity deleted successfully');
    }
}
