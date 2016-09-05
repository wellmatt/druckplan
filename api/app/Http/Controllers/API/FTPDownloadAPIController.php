<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFTPDownloadAPIRequest;
use App\Http\Requests\API\UpdateFTPDownloadAPIRequest;
use App\Models\FTPDownload;
use App\Repositories\FTPDownloadRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FTPDownloadController
 * @package App\Http\Controllers\API
 */

class FTPDownloadAPIController extends AppBaseController
{
    /** @var  FTPDownloadRepository */
    private $fTPDownloadRepository;

    public function __construct(FTPDownloadRepository $fTPDownloadRepo)
    {
        $this->fTPDownloadRepository = $fTPDownloadRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fTPDownloads",
     *      summary="Get a listing of the FTPDownloads.",
     *      tags={"FTPDownload"},
     *      description="Get all FTPDownloads",
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
     *                  @SWG\Items(ref="#/definitions/FTPDownload")
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
        $this->fTPDownloadRepository->pushCriteria(new RequestCriteria($request));
        $this->fTPDownloadRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fTPDownloads = $this->fTPDownloadRepository->all();

        return $this->sendResponse($fTPDownloads->toArray(), 'F T P Downloads retrieved successfully');
    }

    /**
     * @param CreateFTPDownloadAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fTPDownloads",
     *      summary="Store a newly created FTPDownload in storage",
     *      tags={"FTPDownload"},
     *      description="Store FTPDownload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FTPDownload that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FTPDownload")
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
     *                  ref="#/definitions/FTPDownload"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFTPDownloadAPIRequest $request)
    {
        $input = $request->all();

        $fTPDownloads = $this->fTPDownloadRepository->create($input);

        return $this->sendResponse($fTPDownloads->toArray(), 'F T P Download saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fTPDownloads/{id}",
     *      summary="Display the specified FTPDownload",
     *      tags={"FTPDownload"},
     *      description="Get FTPDownload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FTPDownload",
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
     *                  ref="#/definitions/FTPDownload"
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
        /** @var FTPDownload $fTPDownload */
        $fTPDownload = $this->fTPDownloadRepository->find($id);

        if (empty($fTPDownload)) {
            return $this->sendError('F T P Download not found');
        }

        return $this->sendResponse($fTPDownload->toArray(), 'F T P Download retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFTPDownloadAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fTPDownloads/{id}",
     *      summary="Update the specified FTPDownload in storage",
     *      tags={"FTPDownload"},
     *      description="Update FTPDownload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FTPDownload",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FTPDownload that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FTPDownload")
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
     *                  ref="#/definitions/FTPDownload"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFTPDownloadAPIRequest $request)
    {
        $input = $request->all();

        /** @var FTPDownload $fTPDownload */
        $fTPDownload = $this->fTPDownloadRepository->find($id);

        if (empty($fTPDownload)) {
            return $this->sendError('F T P Download not found');
        }

        $fTPDownload = $this->fTPDownloadRepository->update($input, $id);

        return $this->sendResponse($fTPDownload->toArray(), 'FTPDownload updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fTPDownloads/{id}",
     *      summary="Remove the specified FTPDownload from storage",
     *      tags={"FTPDownload"},
     *      description="Delete FTPDownload",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FTPDownload",
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
        /** @var FTPDownload $fTPDownload */
        $fTPDownload = $this->fTPDownloadRepository->find($id);

        if (empty($fTPDownload)) {
            return $this->sendError('F T P Download not found');
        }

        $fTPDownload->delete();

        return $this->sendResponse($id, 'F T P Download deleted successfully');
    }
}
