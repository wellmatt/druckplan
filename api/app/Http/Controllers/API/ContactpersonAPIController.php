<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateContactpersonAPIRequest;
use App\Http\Requests\API\UpdateContactpersonAPIRequest;
use App\Models\Contactperson;
use App\Repositories\ContactpersonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ContactpersonController
 * @package App\Http\Controllers\API
 */

class ContactpersonAPIController extends AppBaseController
{
    /** @var  ContactpersonRepository */
    private $contactpersonRepository;

    public function __construct(ContactpersonRepository $contactpersonRepo)
    {
        $this->contactpersonRepository = $contactpersonRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/contactpeople",
     *      summary="Get a listing of the Contactpeople.",
     *      tags={"Contactperson"},
     *      description="Get all Contactpeople",
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
     *                  @SWG\Items(ref="#/definitions/Contactperson")
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
        $this->contactpersonRepository->pushCriteria(new RequestCriteria($request));
        $this->contactpersonRepository->pushCriteria(new LimitOffsetCriteria($request));
        $contactpeople = $this->contactpersonRepository->all();

        return $this->sendResponse($contactpeople->toArray(), 'Contactpeople retrieved successfully');
    }

    /**
     * @param CreateContactpersonAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/contactpeople",
     *      summary="Store a newly created Contactperson in storage",
     *      tags={"Contactperson"},
     *      description="Store Contactperson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Contactperson that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Contactperson")
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
     *                  ref="#/definitions/Contactperson"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateContactpersonAPIRequest $request)
    {
        $input = $request->all();

        $contactpeople = $this->contactpersonRepository->create($input);

        return $this->sendResponse($contactpeople->toArray(), 'Contactperson saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/contactpeople/{id}",
     *      summary="Display the specified Contactperson",
     *      tags={"Contactperson"},
     *      description="Get Contactperson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Contactperson",
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
     *                  ref="#/definitions/Contactperson"
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
        /** @var Contactperson $contactperson */
        $contactperson = $this->contactpersonRepository->find($id);

        if (empty($contactperson)) {
            return $this->sendError('Contactperson not found');
        }

        return $this->sendResponse($contactperson->toArray(), 'Contactperson retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateContactpersonAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/contactpeople/{id}",
     *      summary="Update the specified Contactperson in storage",
     *      tags={"Contactperson"},
     *      description="Update Contactperson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Contactperson",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Contactperson that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Contactperson")
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
     *                  ref="#/definitions/Contactperson"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateContactpersonAPIRequest $request)
    {
        $input = $request->all();

        /** @var Contactperson $contactperson */
        $contactperson = $this->contactpersonRepository->find($id);

        if (empty($contactperson)) {
            return $this->sendError('Contactperson not found');
        }

        $contactperson = $this->contactpersonRepository->update($input, $id);

        return $this->sendResponse($contactperson->toArray(), 'Contactperson updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/contactpeople/{id}",
     *      summary="Remove the specified Contactperson from storage",
     *      tags={"Contactperson"},
     *      description="Delete Contactperson",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Contactperson",
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
        /** @var Contactperson $contactperson */
        $contactperson = $this->contactpersonRepository->find($id);

        if (empty($contactperson)) {
            return $this->sendError('Contactperson not found');
        }

        $contactperson->delete();

        return $this->sendResponse($id, 'Contactperson deleted successfully');
    }
}
