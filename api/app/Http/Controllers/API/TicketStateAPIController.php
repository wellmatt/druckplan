<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTicketStateAPIRequest;
use App\Http\Requests\API\UpdateTicketStateAPIRequest;
use App\Models\TicketState;
use App\Repositories\TicketStateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TicketStateController
 * @package App\Http\Controllers\API
 */

class TicketStateAPIController extends AppBaseController
{
    /** @var  TicketStateRepository */
    private $ticketStateRepository;

    public function __construct(TicketStateRepository $ticketStateRepo)
    {
        $this->ticketStateRepository = $ticketStateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketStates",
     *      summary="Get a listing of the TicketStates.",
     *      tags={"TicketState"},
     *      description="Get all TicketStates",
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
     *                  @SWG\Items(ref="#/definitions/TicketState")
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
        $this->ticketStateRepository->pushCriteria(new RequestCriteria($request));
        $this->ticketStateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $ticketStates = $this->ticketStateRepository->all();

        return $this->sendResponse($ticketStates->toArray(), 'Ticket States retrieved successfully');
    }

    /**
     * @param CreateTicketStateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/ticketStates",
     *      summary="Store a newly created TicketState in storage",
     *      tags={"TicketState"},
     *      description="Store TicketState",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketState that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketState")
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
     *                  ref="#/definitions/TicketState"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTicketStateAPIRequest $request)
    {
        $input = $request->all();

        $ticketStates = $this->ticketStateRepository->create($input);

        return $this->sendResponse($ticketStates->toArray(), 'Ticket State saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketStates/{id}",
     *      summary="Display the specified TicketState",
     *      tags={"TicketState"},
     *      description="Get TicketState",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketState",
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
     *                  ref="#/definitions/TicketState"
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
        /** @var TicketState $ticketState */
        $ticketState = $this->ticketStateRepository->find($id);

        if (empty($ticketState)) {
            return $this->sendError('Ticket State not found');
        }

        return $this->sendResponse($ticketState->toArray(), 'Ticket State retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTicketStateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/ticketStates/{id}",
     *      summary="Update the specified TicketState in storage",
     *      tags={"TicketState"},
     *      description="Update TicketState",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketState",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketState that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketState")
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
     *                  ref="#/definitions/TicketState"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTicketStateAPIRequest $request)
    {
        $input = $request->all();

        /** @var TicketState $ticketState */
        $ticketState = $this->ticketStateRepository->find($id);

        if (empty($ticketState)) {
            return $this->sendError('Ticket State not found');
        }

        $ticketState = $this->ticketStateRepository->update($input, $id);

        return $this->sendResponse($ticketState->toArray(), 'TicketState updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/ticketStates/{id}",
     *      summary="Remove the specified TicketState from storage",
     *      tags={"TicketState"},
     *      description="Delete TicketState",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketState",
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
        /** @var TicketState $ticketState */
        $ticketState = $this->ticketStateRepository->find($id);

        if (empty($ticketState)) {
            return $this->sendError('Ticket State not found');
        }

        $ticketState->delete();

        return $this->sendResponse($id, 'Ticket State deleted successfully');
    }
}
