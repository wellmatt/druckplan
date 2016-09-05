<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTicketCategoryAPIRequest;
use App\Http\Requests\API\UpdateTicketCategoryAPIRequest;
use App\Models\TicketCategory;
use App\Repositories\TicketCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TicketCategoryController
 * @package App\Http\Controllers\API
 */

class TicketCategoryAPIController extends AppBaseController
{
    /** @var  TicketCategoryRepository */
    private $ticketCategoryRepository;

    public function __construct(TicketCategoryRepository $ticketCategoryRepo)
    {
        $this->ticketCategoryRepository = $ticketCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketCategories",
     *      summary="Get a listing of the TicketCategories.",
     *      tags={"TicketCategory"},
     *      description="Get all TicketCategories",
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
     *                  @SWG\Items(ref="#/definitions/TicketCategory")
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
        $this->ticketCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->ticketCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $ticketCategories = $this->ticketCategoryRepository->all();

        return $this->sendResponse($ticketCategories->toArray(), 'Ticket Categories retrieved successfully');
    }

    /**
     * @param CreateTicketCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/ticketCategories",
     *      summary="Store a newly created TicketCategory in storage",
     *      tags={"TicketCategory"},
     *      description="Store TicketCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketCategory")
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
     *                  ref="#/definitions/TicketCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTicketCategoryAPIRequest $request)
    {
        $input = $request->all();

        $ticketCategories = $this->ticketCategoryRepository->create($input);

        return $this->sendResponse($ticketCategories->toArray(), 'Ticket Category saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketCategories/{id}",
     *      summary="Display the specified TicketCategory",
     *      tags={"TicketCategory"},
     *      description="Get TicketCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketCategory",
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
     *                  ref="#/definitions/TicketCategory"
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
        /** @var TicketCategory $ticketCategory */
        $ticketCategory = $this->ticketCategoryRepository->find($id);

        if (empty($ticketCategory)) {
            return $this->sendError('Ticket Category not found');
        }

        return $this->sendResponse($ticketCategory->toArray(), 'Ticket Category retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTicketCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/ticketCategories/{id}",
     *      summary="Update the specified TicketCategory in storage",
     *      tags={"TicketCategory"},
     *      description="Update TicketCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketCategory")
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
     *                  ref="#/definitions/TicketCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTicketCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var TicketCategory $ticketCategory */
        $ticketCategory = $this->ticketCategoryRepository->find($id);

        if (empty($ticketCategory)) {
            return $this->sendError('Ticket Category not found');
        }

        $ticketCategory = $this->ticketCategoryRepository->update($input, $id);

        return $this->sendResponse($ticketCategory->toArray(), 'TicketCategory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/ticketCategories/{id}",
     *      summary="Remove the specified TicketCategory from storage",
     *      tags={"TicketCategory"},
     *      description="Delete TicketCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketCategory",
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
        /** @var TicketCategory $ticketCategory */
        $ticketCategory = $this->ticketCategoryRepository->find($id);

        if (empty($ticketCategory)) {
            return $this->sendError('Ticket Category not found');
        }

        $ticketCategory->delete();

        return $this->sendResponse($id, 'Ticket Category deleted successfully');
    }
}
