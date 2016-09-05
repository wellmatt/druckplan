<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAttachmentAPIRequest;
use App\Http\Requests\API\UpdateAttachmentAPIRequest;
use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AttachmentController
 * @package App\Http\Controllers\API
 */

class AttachmentAPIController extends AppBaseController
{
    /** @var  AttachmentRepository */
    private $attachmentRepository;

    public function __construct(AttachmentRepository $attachmentRepo)
    {
        $this->attachmentRepository = $attachmentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/attachments",
     *      summary="Get a listing of the Attachments.",
     *      tags={"Attachment"},
     *      description="Get all Attachments",
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
     *                  @SWG\Items(ref="#/definitions/Attachment")
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
        $this->attachmentRepository->pushCriteria(new RequestCriteria($request));
        $this->attachmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $attachments = $this->attachmentRepository->all();

        return $this->sendResponse($attachments->toArray(), 'Attachments retrieved successfully');
    }

    /**
     * @param CreateAttachmentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/attachments",
     *      summary="Store a newly created Attachment in storage",
     *      tags={"Attachment"},
     *      description="Store Attachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Attachment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Attachment")
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
     *                  ref="#/definitions/Attachment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAttachmentAPIRequest $request)
    {
        $input = $request->all();

        $attachments = $this->attachmentRepository->create($input);

        return $this->sendResponse($attachments->toArray(), 'Attachment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/attachments/{id}",
     *      summary="Display the specified Attachment",
     *      tags={"Attachment"},
     *      description="Get Attachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Attachment",
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
     *                  ref="#/definitions/Attachment"
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
        /** @var Attachment $attachment */
        $attachment = $this->attachmentRepository->find($id);

        if (empty($attachment)) {
            return $this->sendError('Attachment not found');
        }

        return $this->sendResponse($attachment->toArray(), 'Attachment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAttachmentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/attachments/{id}",
     *      summary="Update the specified Attachment in storage",
     *      tags={"Attachment"},
     *      description="Update Attachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Attachment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Attachment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Attachment")
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
     *                  ref="#/definitions/Attachment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAttachmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var Attachment $attachment */
        $attachment = $this->attachmentRepository->find($id);

        if (empty($attachment)) {
            return $this->sendError('Attachment not found');
        }

        $attachment = $this->attachmentRepository->update($input, $id);

        return $this->sendResponse($attachment->toArray(), 'Attachment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/attachments/{id}",
     *      summary="Remove the specified Attachment from storage",
     *      tags={"Attachment"},
     *      description="Delete Attachment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Attachment",
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
        /** @var Attachment $attachment */
        $attachment = $this->attachmentRepository->find($id);

        if (empty($attachment)) {
            return $this->sendError('Attachment not found');
        }

        $attachment->delete();

        return $this->sendResponse($id, 'Attachment deleted successfully');
    }
}
