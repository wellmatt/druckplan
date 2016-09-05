<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMachineDifficultyAPIRequest;
use App\Http\Requests\API\UpdateMachineDifficultyAPIRequest;
use App\Models\MachineDifficulty;
use App\Repositories\MachineDifficultyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MachineDifficultyController
 * @package App\Http\Controllers\API
 */

class MachineDifficultyAPIController extends AppBaseController
{
    /** @var  MachineDifficultyRepository */
    private $machineDifficultyRepository;

    public function __construct(MachineDifficultyRepository $machineDifficultyRepo)
    {
        $this->machineDifficultyRepository = $machineDifficultyRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineDifficulties",
     *      summary="Get a listing of the MachineDifficulties.",
     *      tags={"MachineDifficulty"},
     *      description="Get all MachineDifficulties",
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
     *                  @SWG\Items(ref="#/definitions/MachineDifficulty")
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
        $this->machineDifficultyRepository->pushCriteria(new RequestCriteria($request));
        $this->machineDifficultyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $machineDifficulties = $this->machineDifficultyRepository->all();

        return $this->sendResponse($machineDifficulties->toArray(), 'Machine Difficulties retrieved successfully');
    }

    /**
     * @param CreateMachineDifficultyAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/machineDifficulties",
     *      summary="Store a newly created MachineDifficulty in storage",
     *      tags={"MachineDifficulty"},
     *      description="Store MachineDifficulty",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineDifficulty that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineDifficulty")
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
     *                  ref="#/definitions/MachineDifficulty"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMachineDifficultyAPIRequest $request)
    {
        $input = $request->all();

        $machineDifficulties = $this->machineDifficultyRepository->create($input);

        return $this->sendResponse($machineDifficulties->toArray(), 'Machine Difficulty saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/machineDifficulties/{id}",
     *      summary="Display the specified MachineDifficulty",
     *      tags={"MachineDifficulty"},
     *      description="Get MachineDifficulty",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineDifficulty",
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
     *                  ref="#/definitions/MachineDifficulty"
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
        /** @var MachineDifficulty $machineDifficulty */
        $machineDifficulty = $this->machineDifficultyRepository->find($id);

        if (empty($machineDifficulty)) {
            return $this->sendError('Machine Difficulty not found');
        }

        return $this->sendResponse($machineDifficulty->toArray(), 'Machine Difficulty retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMachineDifficultyAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/machineDifficulties/{id}",
     *      summary="Update the specified MachineDifficulty in storage",
     *      tags={"MachineDifficulty"},
     *      description="Update MachineDifficulty",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineDifficulty",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MachineDifficulty that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MachineDifficulty")
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
     *                  ref="#/definitions/MachineDifficulty"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMachineDifficultyAPIRequest $request)
    {
        $input = $request->all();

        /** @var MachineDifficulty $machineDifficulty */
        $machineDifficulty = $this->machineDifficultyRepository->find($id);

        if (empty($machineDifficulty)) {
            return $this->sendError('Machine Difficulty not found');
        }

        $machineDifficulty = $this->machineDifficultyRepository->update($input, $id);

        return $this->sendResponse($machineDifficulty->toArray(), 'MachineDifficulty updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/machineDifficulties/{id}",
     *      summary="Remove the specified MachineDifficulty from storage",
     *      tags={"MachineDifficulty"},
     *      description="Delete MachineDifficulty",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MachineDifficulty",
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
        /** @var MachineDifficulty $machineDifficulty */
        $machineDifficulty = $this->machineDifficultyRepository->find($id);

        if (empty($machineDifficulty)) {
            return $this->sendError('Machine Difficulty not found');
        }

        $machineDifficulty->delete();

        return $this->sendResponse($id, 'Machine Difficulty deleted successfully');
    }
}
