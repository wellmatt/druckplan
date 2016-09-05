<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePrivatContactAPIRequest;
use App\Http\Requests\API\UpdatePrivatContactAPIRequest;
use App\Models\PrivatContact;
use App\Repositories\PrivatContactRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PrivatContactController
 * @package App\Http\Controllers\API
 */

class PrivatContactAPIController extends AppBaseController
{
    /** @var  PrivatContactRepository */
    private $privatContactRepository;

    public function __construct(PrivatContactRepository $privatContactRepo)
    {
        $this->privatContactRepository = $privatContactRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/privatContacts",
     *      summary="Get a listing of the PrivatContacts.",
     *      tags={"PrivatContact"},
     *      description="Get all PrivatContacts",
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
     *                  @SWG\Items(ref="#/definitions/PrivatContact")
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
        $this->privatContactRepository->pushCriteria(new RequestCriteria($request));
        $this->privatContactRepository->pushCriteria(new LimitOffsetCriteria($request));
        $privatContacts = $this->privatContactRepository->all();

        return $this->sendResponse($privatContacts->toArray(), 'Privat Contacts retrieved successfully');
    }

    /**
     * @param CreatePrivatContactAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/privatContacts",
     *      summary="Store a newly created PrivatContact in storage",
     *      tags={"PrivatContact"},
     *      description="Store PrivatContact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PrivatContact that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PrivatContact")
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
     *                  ref="#/definitions/PrivatContact"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePrivatContactAPIRequest $request)
    {
        $input = $request->all();

        $privatContacts = $this->privatContactRepository->create($input);

        return $this->sendResponse($privatContacts->toArray(), 'Privat Contact saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/privatContacts/{id}",
     *      summary="Display the specified PrivatContact",
     *      tags={"PrivatContact"},
     *      description="Get PrivatContact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrivatContact",
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
     *                  ref="#/definitions/PrivatContact"
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
        /** @var PrivatContact $privatContact */
        $privatContact = $this->privatContactRepository->find($id);

        if (empty($privatContact)) {
            return $this->sendError('Privat Contact not found');
        }

        return $this->sendResponse($privatContact->toArray(), 'Privat Contact retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePrivatContactAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/privatContacts/{id}",
     *      summary="Update the specified PrivatContact in storage",
     *      tags={"PrivatContact"},
     *      description="Update PrivatContact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrivatContact",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PrivatContact that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PrivatContact")
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
     *                  ref="#/definitions/PrivatContact"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePrivatContactAPIRequest $request)
    {
        $input = $request->all();

        /** @var PrivatContact $privatContact */
        $privatContact = $this->privatContactRepository->find($id);

        if (empty($privatContact)) {
            return $this->sendError('Privat Contact not found');
        }

        $privatContact = $this->privatContactRepository->update($input, $id);

        return $this->sendResponse($privatContact->toArray(), 'PrivatContact updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/privatContacts/{id}",
     *      summary="Remove the specified PrivatContact from storage",
     *      tags={"PrivatContact"},
     *      description="Delete PrivatContact",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrivatContact",
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
        /** @var PrivatContact $privatContact */
        $privatContact = $this->privatContactRepository->find($id);

        if (empty($privatContact)) {
            return $this->sendError('Privat Contact not found');
        }

        $privatContact->delete();

        return $this->sendResponse($id, 'Privat Contact deleted successfully');
    }
}
