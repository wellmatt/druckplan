<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEventParticipantAPIRequest;
use App\Http\Requests\API\UpdateEventParticipantAPIRequest;
use App\Models\EventParticipant;
use App\Repositories\EventParticipantRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EventParticipantController
 * @package App\Http\Controllers\API
 */

class EventParticipantAPIController extends AppBaseController
{
    /** @var  EventParticipantRepository */
    private $eventParticipantRepository;

    public function __construct(EventParticipantRepository $eventParticipantRepo)
    {
        $this->eventParticipantRepository = $eventParticipantRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/eventParticipants",
     *      summary="Get a listing of the EventParticipants.",
     *      tags={"EventParticipant"},
     *      description="Get all EventParticipants",
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
     *                  @SWG\Items(ref="#/definitions/EventParticipant")
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
        $this->eventParticipantRepository->pushCriteria(new RequestCriteria($request));
        $this->eventParticipantRepository->pushCriteria(new LimitOffsetCriteria($request));
        $eventParticipants = $this->eventParticipantRepository->all();

        return $this->sendResponse($eventParticipants->toArray(), 'Event Participants retrieved successfully');
    }

    /**
     * @param CreateEventParticipantAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/eventParticipants",
     *      summary="Store a newly created EventParticipant in storage",
     *      tags={"EventParticipant"},
     *      description="Store EventParticipant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EventParticipant that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EventParticipant")
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
     *                  ref="#/definitions/EventParticipant"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEventParticipantAPIRequest $request)
    {
        $input = $request->all();

        $eventParticipants = $this->eventParticipantRepository->create($input);

        return $this->sendResponse($eventParticipants->toArray(), 'Event Participant saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/eventParticipants/{id}",
     *      summary="Display the specified EventParticipant",
     *      tags={"EventParticipant"},
     *      description="Get EventParticipant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EventParticipant",
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
     *                  ref="#/definitions/EventParticipant"
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
        /** @var EventParticipant $eventParticipant */
        $eventParticipant = $this->eventParticipantRepository->find($id);

        if (empty($eventParticipant)) {
            return $this->sendError('Event Participant not found');
        }

        return $this->sendResponse($eventParticipant->toArray(), 'Event Participant retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateEventParticipantAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/eventParticipants/{id}",
     *      summary="Update the specified EventParticipant in storage",
     *      tags={"EventParticipant"},
     *      description="Update EventParticipant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EventParticipant",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EventParticipant that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EventParticipant")
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
     *                  ref="#/definitions/EventParticipant"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEventParticipantAPIRequest $request)
    {
        $input = $request->all();

        /** @var EventParticipant $eventParticipant */
        $eventParticipant = $this->eventParticipantRepository->find($id);

        if (empty($eventParticipant)) {
            return $this->sendError('Event Participant not found');
        }

        $eventParticipant = $this->eventParticipantRepository->update($input, $id);

        return $this->sendResponse($eventParticipant->toArray(), 'EventParticipant updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/eventParticipants/{id}",
     *      summary="Remove the specified EventParticipant from storage",
     *      tags={"EventParticipant"},
     *      description="Delete EventParticipant",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EventParticipant",
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
        /** @var EventParticipant $eventParticipant */
        $eventParticipant = $this->eventParticipantRepository->find($id);

        if (empty($eventParticipant)) {
            return $this->sendError('Event Participant not found');
        }

        $eventParticipant->delete();

        return $this->sendResponse($id, 'Event Participant deleted successfully');
    }
}
