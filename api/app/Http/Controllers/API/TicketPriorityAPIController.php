<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTicketPriorityAPIRequest;
use App\Http\Requests\API\UpdateTicketPriorityAPIRequest;
use App\Models\TicketPriority;
use App\Repositories\TicketPriorityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TicketPriorityController
 * @package App\Http\Controllers\API
 */

class TicketPriorityAPIController extends AppBaseController
{
    /** @var  TicketPriorityRepository */
    private $ticketPriorityRepository;

    public function __construct(TicketPriorityRepository $ticketPriorityRepo)
    {
        $this->ticketPriorityRepository = $ticketPriorityRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketPriorities",
     *      summary="Get a listing of the TicketPriorities.",
     *      tags={"TicketPriority"},
     *      description="Get all TicketPriorities",
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
     *                  @SWG\Items(ref="#/definitions/TicketPriority")
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
        $this->ticketPriorityRepository->pushCriteria(new RequestCriteria($request));
        $this->ticketPriorityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $ticketPriorities = $this->ticketPriorityRepository->all();

        return $this->sendResponse($ticketPriorities->toArray(), 'Ticket Priorities retrieved successfully');
    }

    /**
     * @param CreateTicketPriorityAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/ticketPriorities",
     *      summary="Store a newly created TicketPriority in storage",
     *      tags={"TicketPriority"},
     *      description="Store TicketPriority",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketPriority that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketPriority")
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
     *                  ref="#/definitions/TicketPriority"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTicketPriorityAPIRequest $request)
    {
        $input = $request->all();

        $ticketPriorities = $this->ticketPriorityRepository->create($input);

        return $this->sendResponse($ticketPriorities->toArray(), 'Ticket Priority saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketPriorities/{id}",
     *      summary="Display the specified TicketPriority",
     *      tags={"TicketPriority"},
     *      description="Get TicketPriority",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketPriority",
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
     *                  ref="#/definitions/TicketPriority"
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
        /** @var TicketPriority $ticketPriority */
        $ticketPriority = $this->ticketPriorityRepository->find($id);

        if (empty($ticketPriority)) {
            return $this->sendError('Ticket Priority not found');
        }

        return $this->sendResponse($ticketPriority->toArray(), 'Ticket Priority retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTicketPriorityAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/ticketPriorities/{id}",
     *      summary="Update the specified TicketPriority in storage",
     *      tags={"TicketPriority"},
     *      description="Update TicketPriority",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketPriority",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketPriority that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketPriority")
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
     *                  ref="#/definitions/TicketPriority"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTicketPriorityAPIRequest $request)
    {
        $input = $request->all();

        /** @var TicketPriority $ticketPriority */
        $ticketPriority = $this->ticketPriorityRepository->find($id);

        if (empty($ticketPriority)) {
            return $this->sendError('Ticket Priority not found');
        }

        $ticketPriority = $this->ticketPriorityRepository->update($input, $id);

        return $this->sendResponse($ticketPriority->toArray(), 'TicketPriority updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/ticketPriorities/{id}",
     *      summary="Remove the specified TicketPriority from storage",
     *      tags={"TicketPriority"},
     *      description="Delete TicketPriority",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketPriority",
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
        /** @var TicketPriority $ticketPriority */
        $ticketPriority = $this->ticketPriorityRepository->find($id);

        if (empty($ticketPriority)) {
            return $this->sendError('Ticket Priority not found');
        }

        $ticketPriority->delete();

        return $this->sendResponse($id, 'Ticket Priority deleted successfully');
    }
}
