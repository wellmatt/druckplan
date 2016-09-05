<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBusinesscontactAPIRequest;
use App\Http\Requests\API\UpdateBusinesscontactAPIRequest;
use App\Models\Businesscontact;
use App\Repositories\BusinesscontactRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BusinesscontactController
 * @package App\Http\Controllers\API
 */

class BusinesscontactAPIController extends AppBaseController
{
    /** @var  BusinesscontactRepository */
    private $businesscontactRepository;

    public function __construct(BusinesscontactRepository $businesscontactRepo)
    {
        $this->businesscontactRepository = $businesscontactRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/businesscontacts",
     *      summary="Get a listing of the Businesscontacts.",
     *      tags={"Businesscontact"},
     *      description="Get all Businesscontacts",
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
     *                  @SWG\Items(ref="#/definitions/Businesscontact")
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
        $this->businesscontactRepository->pushCriteria(new RequestCriteria($request));
        $this->businesscontactRepository->pushCriteria(new LimitOffsetCriteria($request));
        $businesscontacts = $this->businesscontactRepository->all();

        return $this->sendResponse($businesscontacts->toArray(), 'Businesscontacts retrieved successfully');
    }

    /**
     * @param CreateBusinesscontactAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/businesscontacts",
     *      summary="Store a newly created Businesscontact in storage",
     *      tags={"Businesscontact"},
     *      description="Store Businesscontact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Businesscontact that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Businesscontact")
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
     *                  ref="#/definitions/Businesscontact"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBusinesscontactAPIRequest $request)
    {
        $input = $request->all();

        $businesscontacts = $this->businesscontactRepository->create($input);

        return $this->sendResponse($businesscontacts->toArray(), 'Businesscontact saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/businesscontacts/{id}",
     *      summary="Display the specified Businesscontact",
     *      tags={"Businesscontact"},
     *      description="Get Businesscontact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Businesscontact",
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
     *                  ref="#/definitions/Businesscontact"
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
        /** @var Businesscontact $businesscontact */
        $businesscontact = $this->businesscontactRepository->find($id);

        if (empty($businesscontact)) {
            return $this->sendError('Businesscontact not found');
        }

        return $this->sendResponse($businesscontact->toArray(), 'Businesscontact retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBusinesscontactAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/businesscontacts/{id}",
     *      summary="Update the specified Businesscontact in storage",
     *      tags={"Businesscontact"},
     *      description="Update Businesscontact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Businesscontact",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Businesscontact that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Businesscontact")
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
     *                  ref="#/definitions/Businesscontact"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBusinesscontactAPIRequest $request)
    {
        $input = $request->all();

        /** @var Businesscontact $businesscontact */
        $businesscontact = $this->businesscontactRepository->find($id);

        if (empty($businesscontact)) {
            return $this->sendError('Businesscontact not found');
        }

        $businesscontact = $this->businesscontactRepository->update($input, $id);

        return $this->sendResponse($businesscontact->toArray(), 'Businesscontact updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/businesscontacts/{id}",
     *      summary="Remove the specified Businesscontact from storage",
     *      tags={"Businesscontact"},
     *      description="Delete Businesscontact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Businesscontact",
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
        /** @var Businesscontact $businesscontact */
        $businesscontact = $this->businesscontactRepository->find($id);

        if (empty($businesscontact)) {
            return $this->sendError('Businesscontact not found');
        }

        $businesscontact->delete();

        return $this->sendResponse($id, 'Businesscontact deleted successfully');
    }
}
