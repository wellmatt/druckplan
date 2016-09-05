<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePrivatContactAccessAPIRequest;
use App\Http\Requests\API\UpdatePrivatContactAccessAPIRequest;
use App\Models\PrivatContactAccess;
use App\Repositories\PrivatContactAccessRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PrivatContactAccessController
 * @package App\Http\Controllers\API
 */

class PrivatContactAccessAPIController extends AppBaseController
{
    /** @var  PrivatContactAccessRepository */
    private $privatContactAccessRepository;

    public function __construct(PrivatContactAccessRepository $privatContactAccessRepo)
    {
        $this->privatContactAccessRepository = $privatContactAccessRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/privatContactAccesses",
     *      summary="Get a listing of the PrivatContactAccesses.",
     *      tags={"PrivatContactAccess"},
     *      description="Get all PrivatContactAccesses",
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
     *                  @SWG\Items(ref="#/definitions/PrivatContactAccess")
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
        $this->privatContactAccessRepository->pushCriteria(new RequestCriteria($request));
        $this->privatContactAccessRepository->pushCriteria(new LimitOffsetCriteria($request));
        $privatContactAccesses = $this->privatContactAccessRepository->all();

        return $this->sendResponse($privatContactAccesses->toArray(), 'Privat Contact Accesses retrieved successfully');
    }

    /**
     * @param CreatePrivatContactAccessAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/privatContactAccesses",
     *      summary="Store a newly created PrivatContactAccess in storage",
     *      tags={"PrivatContactAccess"},
     *      description="Store PrivatContactAccess",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PrivatContactAccess that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PrivatContactAccess")
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
     *                  ref="#/definitions/PrivatContactAccess"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePrivatContactAccessAPIRequest $request)
    {
        $input = $request->all();

        $privatContactAccesses = $this->privatContactAccessRepository->create($input);

        return $this->sendResponse($privatContactAccesses->toArray(), 'Privat Contact Access saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/privatContactAccesses/{id}",
     *      summary="Display the specified PrivatContactAccess",
     *      tags={"PrivatContactAccess"},
     *      description="Get PrivatContactAccess",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrivatContactAccess",
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
     *                  ref="#/definitions/PrivatContactAccess"
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
        /** @var PrivatContactAccess $privatContactAccess */
        $privatContactAccess = $this->privatContactAccessRepository->find($id);

        if (empty($privatContactAccess)) {
            return $this->sendError('Privat Contact Access not found');
        }

        return $this->sendResponse($privatContactAccess->toArray(), 'Privat Contact Access retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePrivatContactAccessAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/privatContactAccesses/{id}",
     *      summary="Update the specified PrivatContactAccess in storage",
     *      tags={"PrivatContactAccess"},
     *      description="Update PrivatContactAccess",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrivatContactAccess",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PrivatContactAccess that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PrivatContactAccess")
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
     *                  ref="#/definitions/PrivatContactAccess"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePrivatContactAccessAPIRequest $request)
    {
        $input = $request->all();

        /** @var PrivatContactAccess $privatContactAccess */
        $privatContactAccess = $this->privatContactAccessRepository->find($id);

        if (empty($privatContactAccess)) {
            return $this->sendError('Privat Contact Access not found');
        }

        $privatContactAccess = $this->privatContactAccessRepository->update($input, $id);

        return $this->sendResponse($privatContactAccess->toArray(), 'PrivatContactAccess updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/privatContactAccesses/{id}",
     *      summary="Remove the specified PrivatContactAccess from storage",
     *      tags={"PrivatContactAccess"},
     *      description="Delete PrivatContactAccess",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrivatContactAccess",
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
        /** @var PrivatContactAccess $privatContactAccess */
        $privatContactAccess = $this->privatContactAccessRepository->find($id);

        if (empty($privatContactAccess)) {
            return $this->sendError('Privat Contact Access not found');
        }

        $privatContactAccess->delete();

        return $this->sendResponse($id, 'Privat Contact Access deleted successfully');
    }
}
