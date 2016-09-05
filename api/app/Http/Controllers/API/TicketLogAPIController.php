<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTicketLogAPIRequest;
use App\Http\Requests\API\UpdateTicketLogAPIRequest;
use App\Models\TicketLog;
use App\Repositories\TicketLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TicketLogController
 * @package App\Http\Controllers\API
 */

class TicketLogAPIController extends AppBaseController
{
    /** @var  TicketLogRepository */
    private $ticketLogRepository;

    public function __construct(TicketLogRepository $ticketLogRepo)
    {
        $this->ticketLogRepository = $ticketLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketLogs",
     *      summary="Get a listing of the TicketLogs.",
     *      tags={"TicketLog"},
     *      description="Get all TicketLogs",
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
     *                  @SWG\Items(ref="#/definitions/TicketLog")
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
        $this->ticketLogRepository->pushCriteria(new RequestCriteria($request));
        $this->ticketLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $ticketLogs = $this->ticketLogRepository->all();

        return $this->sendResponse($ticketLogs->toArray(), 'Ticket Logs retrieved successfully');
    }

    /**
     * @param CreateTicketLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/ticketLogs",
     *      summary="Store a newly created TicketLog in storage",
     *      tags={"TicketLog"},
     *      description="Store TicketLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketLog")
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
     *                  ref="#/definitions/TicketLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTicketLogAPIRequest $request)
    {
        $input = $request->all();

        $ticketLogs = $this->ticketLogRepository->create($input);

        return $this->sendResponse($ticketLogs->toArray(), 'Ticket Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketLogs/{id}",
     *      summary="Display the specified TicketLog",
     *      tags={"TicketLog"},
     *      description="Get TicketLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketLog",
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
     *                  ref="#/definitions/TicketLog"
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
        /** @var TicketLog $ticketLog */
        $ticketLog = $this->ticketLogRepository->find($id);

        if (empty($ticketLog)) {
            return $this->sendError('Ticket Log not found');
        }

        return $this->sendResponse($ticketLog->toArray(), 'Ticket Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTicketLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/ticketLogs/{id}",
     *      summary="Update the specified TicketLog in storage",
     *      tags={"TicketLog"},
     *      description="Update TicketLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketLog")
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
     *                  ref="#/definitions/TicketLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTicketLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var TicketLog $ticketLog */
        $ticketLog = $this->ticketLogRepository->find($id);

        if (empty($ticketLog)) {
            return $this->sendError('Ticket Log not found');
        }

        $ticketLog = $this->ticketLogRepository->update($input, $id);

        return $this->sendResponse($ticketLog->toArray(), 'TicketLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/ticketLogs/{id}",
     *      summary="Remove the specified TicketLog from storage",
     *      tags={"TicketLog"},
     *      description="Delete TicketLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketLog",
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
        /** @var TicketLog $ticketLog */
        $ticketLog = $this->ticketLogRepository->find($id);

        if (empty($ticketLog)) {
            return $this->sendError('Ticket Log not found');
        }

        $ticketLog->delete();

        return $this->sendResponse($id, 'Ticket Log deleted successfully');
    }
}
