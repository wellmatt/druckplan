<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePersonalizationAPIRequest;
use App\Http\Requests\API\UpdatePersonalizationAPIRequest;
use App\Models\Personalization;
use App\Repositories\PersonalizationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PersonalizationController
 * @package App\Http\Controllers\API
 */

class PersonalizationAPIController extends AppBaseController
{
    /** @var  PersonalizationRepository */
    private $personalizationRepository;

    public function __construct(PersonalizationRepository $personalizationRepo)
    {
        $this->personalizationRepository = $personalizationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizations",
     *      summary="Get a listing of the Personalizations.",
     *      tags={"Personalization"},
     *      description="Get all Personalizations",
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
     *                  @SWG\Items(ref="#/definitions/Personalization")
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
        $this->personalizationRepository->pushCriteria(new RequestCriteria($request));
        $this->personalizationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $personalizations = $this->personalizationRepository->all();

        return $this->sendResponse($personalizations->toArray(), 'Personalizations retrieved successfully');
    }

    /**
     * @param CreatePersonalizationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/personalizations",
     *      summary="Store a newly created Personalization in storage",
     *      tags={"Personalization"},
     *      description="Store Personalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Personalization that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Personalization")
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
     *                  ref="#/definitions/Personalization"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePersonalizationAPIRequest $request)
    {
        $input = $request->all();

        $personalizations = $this->personalizationRepository->create($input);

        return $this->sendResponse($personalizations->toArray(), 'Personalization saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/personalizations/{id}",
     *      summary="Display the specified Personalization",
     *      tags={"Personalization"},
     *      description="Get Personalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Personalization",
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
     *                  ref="#/definitions/Personalization"
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
        /** @var Personalization $personalization */
        $personalization = $this->personalizationRepository->find($id);

        if (empty($personalization)) {
            return $this->sendError('Personalization not found');
        }

        return $this->sendResponse($personalization->toArray(), 'Personalization retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePersonalizationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/personalizations/{id}",
     *      summary="Update the specified Personalization in storage",
     *      tags={"Personalization"},
     *      description="Update Personalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Personalization",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Personalization that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Personalization")
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
     *                  ref="#/definitions/Personalization"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePersonalizationAPIRequest $request)
    {
        $input = $request->all();

        /** @var Personalization $personalization */
        $personalization = $this->personalizationRepository->find($id);

        if (empty($personalization)) {
            return $this->sendError('Personalization not found');
        }

        $personalization = $this->personalizationRepository->update($input, $id);

        return $this->sendResponse($personalization->toArray(), 'Personalization updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/personalizations/{id}",
     *      summary="Remove the specified Personalization from storage",
     *      tags={"Personalization"},
     *      description="Delete Personalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Personalization",
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
        /** @var Personalization $personalization */
        $personalization = $this->personalizationRepository->find($id);

        if (empty($personalization)) {
            return $this->sendError('Personalization not found');
        }

        $personalization->delete();

        return $this->sendResponse($id, 'Personalization deleted successfully');
    }
}
