<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserGroupAPIRequest;
use App\Http\Requests\API\UpdateUserGroupAPIRequest;
use App\Models\UserGroup;
use App\Repositories\UserGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UserGroupController
 * @package App\Http\Controllers\API
 */

class UserGroupAPIController extends AppBaseController
{
    /** @var  UserGroupRepository */
    private $userGroupRepository;

    public function __construct(UserGroupRepository $userGroupRepo)
    {
        $this->userGroupRepository = $userGroupRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/userGroups",
     *      summary="Get a listing of the UserGroups.",
     *      tags={"UserGroup"},
     *      description="Get all UserGroups",
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
     *                  @SWG\Items(ref="#/definitions/UserGroup")
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
        $this->userGroupRepository->pushCriteria(new RequestCriteria($request));
        $this->userGroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userGroups = $this->userGroupRepository->all();

        return $this->sendResponse($userGroups->toArray(), 'User Groups retrieved successfully');
    }

    /**
     * @param CreateUserGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/userGroups",
     *      summary="Store a newly created UserGroup in storage",
     *      tags={"UserGroup"},
     *      description="Store UserGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserGroup that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserGroup")
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
     *                  ref="#/definitions/UserGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserGroupAPIRequest $request)
    {
        $input = $request->all();

        $userGroups = $this->userGroupRepository->create($input);

        return $this->sendResponse($userGroups->toArray(), 'User Group saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/userGroups/{id}",
     *      summary="Display the specified UserGroup",
     *      tags={"UserGroup"},
     *      description="Get UserGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserGroup",
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
     *                  ref="#/definitions/UserGroup"
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
        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->find($id);

        if (empty($userGroup)) {
            return $this->sendError('User Group not found');
        }

        return $this->sendResponse($userGroup->toArray(), 'User Group retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateUserGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/userGroups/{id}",
     *      summary="Update the specified UserGroup in storage",
     *      tags={"UserGroup"},
     *      description="Update UserGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserGroup",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserGroup that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserGroup")
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
     *                  ref="#/definitions/UserGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->find($id);

        if (empty($userGroup)) {
            return $this->sendError('User Group not found');
        }

        $userGroup = $this->userGroupRepository->update($input, $id);

        return $this->sendResponse($userGroup->toArray(), 'UserGroup updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/userGroups/{id}",
     *      summary="Remove the specified UserGroup from storage",
     *      tags={"UserGroup"},
     *      description="Delete UserGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserGroup",
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
        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->find($id);

        if (empty($userGroup)) {
            return $this->sendError('User Group not found');
        }

        $userGroup->delete();

        return $this->sendResponse($id, 'User Group deleted successfully');
    }
}
