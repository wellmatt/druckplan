<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTicketSourceAPIRequest;
use App\Http\Requests\API\UpdateTicketSourceAPIRequest;
use App\Models\TicketSource;
use App\Repositories\TicketSourceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TicketSourceController
 * @package App\Http\Controllers\API
 */

class TicketSourceAPIController extends AppBaseController
{
    /** @var  TicketSourceRepository */
    private $ticketSourceRepository;

    public function __construct(TicketSourceRepository $ticketSourceRepo)
    {
        $this->ticketSourceRepository = $ticketSourceRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketSources",
     *      summary="Get a listing of the TicketSources.",
     *      tags={"TicketSource"},
     *      description="Get all TicketSources",
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
     *                  @SWG\Items(ref="#/definitions/TicketSource")
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
        $this->ticketSourceRepository->pushCriteria(new RequestCriteria($request));
        $this->ticketSourceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $ticketSources = $this->ticketSourceRepository->all();

        return $this->sendResponse($ticketSources->toArray(), 'Ticket Sources retrieved successfully');
    }

    /**
     * @param CreateTicketSourceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/ticketSources",
     *      summary="Store a newly created TicketSource in storage",
     *      tags={"TicketSource"},
     *      description="Store TicketSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketSource that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketSource")
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
     *                  ref="#/definitions/TicketSource"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTicketSourceAPIRequest $request)
    {
        $input = $request->all();

        $ticketSources = $this->ticketSourceRepository->create($input);

        return $this->sendResponse($ticketSources->toArray(), 'Ticket Source saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketSources/{id}",
     *      summary="Display the specified TicketSource",
     *      tags={"TicketSource"},
     *      description="Get TicketSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketSource",
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
     *                  ref="#/definitions/TicketSource"
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
        /** @var TicketSource $ticketSource */
        $ticketSource = $this->ticketSourceRepository->find($id);

        if (empty($ticketSource)) {
            return $this->sendError('Ticket Source not found');
        }

        return $this->sendResponse($ticketSource->toArray(), 'Ticket Source retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTicketSourceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/ticketSources/{id}",
     *      summary="Update the specified TicketSource in storage",
     *      tags={"TicketSource"},
     *      description="Update TicketSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketSource",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketSource that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketSource")
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
     *                  ref="#/definitions/TicketSource"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTicketSourceAPIRequest $request)
    {
        $input = $request->all();

        /** @var TicketSource $ticketSource */
        $ticketSource = $this->ticketSourceRepository->find($id);

        if (empty($ticketSource)) {
            return $this->sendError('Ticket Source not found');
        }

        $ticketSource = $this->ticketSourceRepository->update($input, $id);

        return $this->sendResponse($ticketSource->toArray(), 'TicketSource updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/ticketSources/{id}",
     *      summary="Remove the specified TicketSource from storage",
     *      tags={"TicketSource"},
     *      description="Delete TicketSource",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketSource",
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
        /** @var TicketSource $ticketSource */
        $ticketSource = $this->ticketSourceRepository->find($id);

        if (empty($ticketSource)) {
            return $this->sendError('Ticket Source not found');
        }

        $ticketSource->delete();

        return $this->sendResponse($id, 'Ticket Source deleted successfully');
    }
}
