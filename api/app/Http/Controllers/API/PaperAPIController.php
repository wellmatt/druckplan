<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaperAPIRequest;
use App\Http\Requests\API\UpdatePaperAPIRequest;
use App\Models\Paper;
use App\Repositories\PaperRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaperController
 * @package App\Http\Controllers\API
 */

class PaperAPIController extends AppBaseController
{
    /** @var  PaperRepository */
    private $paperRepository;

    public function __construct(PaperRepository $paperRepo)
    {
        $this->paperRepository = $paperRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/papers",
     *      summary="Get a listing of the Papers.",
     *      tags={"Paper"},
     *      description="Get all Papers",
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
     *                  @SWG\Items(ref="#/definitions/Paper")
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
        $this->paperRepository->pushCriteria(new RequestCriteria($request));
        $this->paperRepository->pushCriteria(new LimitOffsetCriteria($request));
        $papers = $this->paperRepository->all();

        return $this->sendResponse($papers->toArray(), 'Papers retrieved successfully');
    }

    /**
     * @param CreatePaperAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/papers",
     *      summary="Store a newly created Paper in storage",
     *      tags={"Paper"},
     *      description="Store Paper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Paper that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Paper")
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
     *                  ref="#/definitions/Paper"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaperAPIRequest $request)
    {
        $input = $request->all();

        $papers = $this->paperRepository->create($input);

        return $this->sendResponse($papers->toArray(), 'Paper saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/papers/{id}",
     *      summary="Display the specified Paper",
     *      tags={"Paper"},
     *      description="Get Paper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Paper",
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
     *                  ref="#/definitions/Paper"
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
        /** @var Paper $paper */
        $paper = $this->paperRepository->find($id);

        if (empty($paper)) {
            return $this->sendError('Paper not found');
        }

        return $this->sendResponse($paper->toArray(), 'Paper retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaperAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/papers/{id}",
     *      summary="Update the specified Paper in storage",
     *      tags={"Paper"},
     *      description="Update Paper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Paper",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Paper that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Paper")
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
     *                  ref="#/definitions/Paper"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaperAPIRequest $request)
    {
        $input = $request->all();

        /** @var Paper $paper */
        $paper = $this->paperRepository->find($id);

        if (empty($paper)) {
            return $this->sendError('Paper not found');
        }

        $paper = $this->paperRepository->update($input, $id);

        return $this->sendResponse($paper->toArray(), 'Paper updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/papers/{id}",
     *      summary="Remove the specified Paper from storage",
     *      tags={"Paper"},
     *      description="Delete Paper",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Paper",
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
        /** @var Paper $paper */
        $paper = $this->paperRepository->find($id);

        if (empty($paper)) {
            return $this->sendError('Paper not found');
        }

        $paper->delete();

        return $this->sendResponse($id, 'Paper deleted successfully');
    }
}
