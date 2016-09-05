<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUserEmailAPIRequest;
use App\Http\Requests\API\UpdateUserEmailAPIRequest;
use App\Models\UserEmail;
use App\Repositories\UserEmailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UserEmailController
 * @package App\Http\Controllers\API
 */

class UserEmailAPIController extends AppBaseController
{
    /** @var  UserEmailRepository */
    private $userEmailRepository;

    public function __construct(UserEmailRepository $userEmailRepo)
    {
        $this->userEmailRepository = $userEmailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/userEmails",
     *      summary="Get a listing of the UserEmails.",
     *      tags={"UserEmail"},
     *      description="Get all UserEmails",
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
     *                  @SWG\Items(ref="#/definitions/UserEmail")
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
        $this->userEmailRepository->pushCriteria(new RequestCriteria($request));
        $this->userEmailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userEmails = $this->userEmailRepository->all();

        return $this->sendResponse($userEmails->toArray(), 'User Emails retrieved successfully');
    }

    /**
     * @param CreateUserEmailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/userEmails",
     *      summary="Store a newly created UserEmail in storage",
     *      tags={"UserEmail"},
     *      description="Store UserEmail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserEmail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserEmail")
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
     *                  ref="#/definitions/UserEmail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserEmailAPIRequest $request)
    {
        $input = $request->all();

        $userEmails = $this->userEmailRepository->create($input);

        return $this->sendResponse($userEmails->toArray(), 'User Email saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/userEmails/{id}",
     *      summary="Display the specified UserEmail",
     *      tags={"UserEmail"},
     *      description="Get UserEmail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserEmail",
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
     *                  ref="#/definitions/UserEmail"
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
        /** @var UserEmail $userEmail */
        $userEmail = $this->userEmailRepository->find($id);

        if (empty($userEmail)) {
            return $this->sendError('User Email not found');
        }

        return $this->sendResponse($userEmail->toArray(), 'User Email retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateUserEmailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/userEmails/{id}",
     *      summary="Update the specified UserEmail in storage",
     *      tags={"UserEmail"},
     *      description="Update UserEmail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserEmail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserEmail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserEmail")
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
     *                  ref="#/definitions/UserEmail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserEmailAPIRequest $request)
    {
        $input = $request->all();

        /** @var UserEmail $userEmail */
        $userEmail = $this->userEmailRepository->find($id);

        if (empty($userEmail)) {
            return $this->sendError('User Email not found');
        }

        $userEmail = $this->userEmailRepository->update($input, $id);

        return $this->sendResponse($userEmail->toArray(), 'UserEmail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/userEmails/{id}",
     *      summary="Remove the specified UserEmail from storage",
     *      tags={"UserEmail"},
     *      description="Delete UserEmail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of UserEmail",
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
        /** @var UserEmail $userEmail */
        $userEmail = $this->userEmailRepository->find($id);

        if (empty($userEmail)) {
            return $this->sendError('User Email not found');
        }

        $userEmail->delete();

        return $this->sendResponse($id, 'User Email deleted successfully');
    }
}
