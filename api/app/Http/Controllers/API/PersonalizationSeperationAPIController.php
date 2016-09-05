<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePersonalizationSeperationAPIRequest;
use App\Http\Requests\API\UpdatePersonalizationSeperationAPIRequest;
use App\Models\PersonalizationSeperation;
use App\Repositories\PersonalizationSeperationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PersonalizationSeperationController
 * @package App\Http\Controllers\API
 */

class PersonalizationSeperationAPIController extends AppBaseController
{
    /** @var  PersonalizationSeperationRepository */
    private $personalizationSeperationRepository;

    public function __construct(PersonalizationSeperationRepository $personalizationSeperationRepo)
    {
        $this->personalizationSeperationRepository = $personalizationSeperationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationSeperations",
     *      summary="Get a listing of the PersonalizationSeperations.",
     *      tags={"PersonalizationSeperation"},
     *      description="Get all PersonalizationSeperations",
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
     *                  @SWG\Items(ref="#/definitions/PersonalizationSeperation")
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
        $this->personalizationSeperationRepository->pushCriteria(new RequestCriteria($request));
        $this->personalizationSeperationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $personalizationSeperations = $this->personalizationSeperationRepository->all();

        return $this->sendResponse($personalizationSeperations->toArray(), 'Personalization Seperations retrieved successfully');
    }

    /**
     * @param CreatePersonalizationSeperationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/personalizationSeperations",
     *      summary="Store a newly created PersonalizationSeperation in storage",
     *      tags={"PersonalizationSeperation"},
     *      description="Store PersonalizationSeperation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationSeperation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationSeperation")
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
     *                  ref="#/definitions/PersonalizationSeperation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePersonalizationSeperationAPIRequest $request)
    {
        $input = $request->all();

        $personalizationSeperations = $this->personalizationSeperationRepository->create($input);

        return $this->sendResponse($personalizationSeperations->toArray(), 'Personalization Seperation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizationSeperations/{id}",
     *      summary="Display the specified PersonalizationSeperation",
     *      tags={"PersonalizationSeperation"},
     *      description="Get PersonalizationSeperation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationSeperation",
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
     *                  ref="#/definitions/PersonalizationSeperation"
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
        /** @var PersonalizationSeperation $personalizationSeperation */
        $personalizationSeperation = $this->personalizationSeperationRepository->find($id);

        if (empty($personalizationSeperation)) {
            return $this->sendError('Personalization Seperation not found');
        }

        return $this->sendResponse($personalizationSeperation->toArray(), 'Personalization Seperation retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePersonalizationSeperationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/personalizationSeperations/{id}",
     *      summary="Update the specified PersonalizationSeperation in storage",
     *      tags={"PersonalizationSeperation"},
     *      description="Update PersonalizationSeperation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationSeperation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PersonalizationSeperation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PersonalizationSeperation")
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
     *                  ref="#/definitions/PersonalizationSeperation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePersonalizationSeperationAPIRequest $request)
    {
        $input = $request->all();

        /** @var PersonalizationSeperation $personalizationSeperation */
        $personalizationSeperation = $this->personalizationSeperationRepository->find($id);

        if (empty($personalizationSeperation)) {
            return $this->sendError('Personalization Seperation not found');
        }

        $personalizationSeperation = $this->personalizationSeperationRepository->update($input, $id);

        return $this->sendResponse($personalizationSeperation->toArray(), 'PersonalizationSeperation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/personalizationSeperations/{id}",
     *      summary="Remove the specified PersonalizationSeperation from storage",
     *      tags={"PersonalizationSeperation"},
     *      description="Delete PersonalizationSeperation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PersonalizationSeperation",
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
        /** @var PersonalizationSeperation $personalizationSeperation */
        $personalizationSeperation = $this->personalizationSeperationRepository->find($id);

        if (empty($personalizationSeperation)) {
            return $this->sendError('Personalization Seperation not found');
        }

        $personalizationSeperation->delete();

        return $this->sendResponse($id, 'Personalization Seperation deleted successfully');
    }
}
