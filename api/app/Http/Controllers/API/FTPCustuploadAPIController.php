<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFTPCustuploadAPIRequest;
use App\Http\Requests\API\UpdateFTPCustuploadAPIRequest;
use App\Models\FTPCustupload;
use App\Repositories\FTPCustuploadRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FTPCustuploadController
 * @package App\Http\Controllers\API
 */

class FTPCustuploadAPIController extends AppBaseController
{
    /** @var  FTPCustuploadRepository */
    private $fTPCustuploadRepository;

    public function __construct(FTPCustuploadRepository $fTPCustuploadRepo)
    {
        $this->fTPCustuploadRepository = $fTPCustuploadRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fTPCustuploads",
     *      summary="Get a listing of the FTPCustuploads.",
     *      tags={"FTPCustupload"},
     *      description="Get all FTPCustuploads",
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
     *                  @SWG\Items(ref="#/definitions/FTPCustupload")
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
        $this->fTPCustuploadRepository->pushCriteria(new RequestCriteria($request));
        $this->fTPCustuploadRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fTPCustuploads = $this->fTPCustuploadRepository->all();

        return $this->sendResponse($fTPCustuploads->toArray(), 'F T P Custuploads retrieved successfully');
    }

    /**
     * @param CreateFTPCustuploadAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fTPCustuploads",
     *      summary="Store a newly created FTPCustupload in storage",
     *      tags={"FTPCustupload"},
     *      description="Store FTPCustupload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FTPCustupload that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FTPCustupload")
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
     *                  ref="#/definitions/FTPCustupload"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFTPCustuploadAPIRequest $request)
    {
        $input = $request->all();

        $fTPCustuploads = $this->fTPCustuploadRepository->create($input);

        return $this->sendResponse($fTPCustuploads->toArray(), 'F T P Custupload saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fTPCustuploads/{id}",
     *      summary="Display the specified FTPCustupload",
     *      tags={"FTPCustupload"},
     *      description="Get FTPCustupload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FTPCustupload",
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
     *                  ref="#/definitions/FTPCustupload"
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
        /** @var FTPCustupload $fTPCustupload */
        $fTPCustupload = $this->fTPCustuploadRepository->find($id);

        if (empty($fTPCustupload)) {
            return $this->sendError('F T P Custupload not found');
        }

        return $this->sendResponse($fTPCustupload->toArray(), 'F T P Custupload retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFTPCustuploadAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fTPCustuploads/{id}",
     *      summary="Update the specified FTPCustupload in storage",
     *      tags={"FTPCustupload"},
     *      description="Update FTPCustupload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FTPCustupload",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FTPCustupload that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FTPCustupload")
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
     *                  ref="#/definitions/FTPCustupload"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFTPCustuploadAPIRequest $request)
    {
        $input = $request->all();

        /** @var FTPCustupload $fTPCustupload */
        $fTPCustupload = $this->fTPCustuploadRepository->find($id);

        if (empty($fTPCustupload)) {
            return $this->sendError('F T P Custupload not found');
        }

        $fTPCustupload = $this->fTPCustuploadRepository->update($input, $id);

        return $this->sendResponse($fTPCustupload->toArray(), 'FTPCustupload updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fTPCustuploads/{id}",
     *      summary="Remove the specified FTPCustupload from storage",
     *      tags={"FTPCustupload"},
     *      description="Delete FTPCustupload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FTPCustupload",
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
        /** @var FTPCustupload $fTPCustupload */
        $fTPCustupload = $this->fTPCustuploadRepository->find($id);

        if (empty($fTPCustupload)) {
            return $this->sendError('F T P Custupload not found');
        }

        $fTPCustupload->delete();

        return $this->sendResponse($id, 'F T P Custupload deleted successfully');
    }
}
