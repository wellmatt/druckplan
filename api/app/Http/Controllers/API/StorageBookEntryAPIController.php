<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStorageBookEntryAPIRequest;
use App\Http\Requests\API\UpdateStorageBookEntryAPIRequest;
use App\Models\StorageBookEntry;
use App\Repositories\StorageBookEntryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class StorageBookEntryController
 * @package App\Http\Controllers\API
 */

class StorageBookEntryAPIController extends AppBaseController
{
    /** @var  StorageBookEntryRepository */
    private $storageBookEntryRepository;

    public function __construct(StorageBookEntryRepository $storageBookEntryRepo)
    {
        $this->storageBookEntryRepository = $storageBookEntryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageBookEntries",
     *      summary="Get a listing of the StorageBookEntries.",
     *      tags={"StorageBookEntry"},
     *      description="Get all StorageBookEntries",
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
     *                  @SWG\Items(ref="#/definitions/StorageBookEntry")
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
        $this->storageBookEntryRepository->pushCriteria(new RequestCriteria($request));
        $this->storageBookEntryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $storageBookEntries = $this->storageBookEntryRepository->all();

        return $this->sendResponse($storageBookEntries->toArray(), 'Storage Book Entries retrieved successfully');
    }

    /**
     * @param CreateStorageBookEntryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/storageBookEntries",
     *      summary="Store a newly created StorageBookEntry in storage",
     *      tags={"StorageBookEntry"},
     *      description="Store StorageBookEntry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageBookEntry that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageBookEntry")
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
     *                  ref="#/definitions/StorageBookEntry"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStorageBookEntryAPIRequest $request)
    {
        $input = $request->all();

        $storageBookEntries = $this->storageBookEntryRepository->create($input);

        return $this->sendResponse($storageBookEntries->toArray(), 'Storage Book Entry saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/storageBookEntries/{id}",
     *      summary="Display the specified StorageBookEntry",
     *      tags={"StorageBookEntry"},
     *      description="Get StorageBookEntry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageBookEntry",
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
     *                  ref="#/definitions/StorageBookEntry"
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
        /** @var StorageBookEntry $storageBookEntry */
        $storageBookEntry = $this->storageBookEntryRepository->find($id);

        if (empty($storageBookEntry)) {
            return $this->sendError('Storage Book Entry not found');
        }

        return $this->sendResponse($storageBookEntry->toArray(), 'Storage Book Entry retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateStorageBookEntryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/storageBookEntries/{id}",
     *      summary="Update the specified StorageBookEntry in storage",
     *      tags={"StorageBookEntry"},
     *      description="Update StorageBookEntry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageBookEntry",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StorageBookEntry that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StorageBookEntry")
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
     *                  ref="#/definitions/StorageBookEntry"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStorageBookEntryAPIRequest $request)
    {
        $input = $request->all();

        /** @var StorageBookEntry $storageBookEntry */
        $storageBookEntry = $this->storageBookEntryRepository->find($id);

        if (empty($storageBookEntry)) {
            return $this->sendError('Storage Book Entry not found');
        }

        $storageBookEntry = $this->storageBookEntryRepository->update($input, $id);

        return $this->sendResponse($storageBookEntry->toArray(), 'StorageBookEntry updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/storageBookEntries/{id}",
     *      summary="Remove the specified StorageBookEntry from storage",
     *      tags={"StorageBookEntry"},
     *      description="Delete StorageBookEntry",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StorageBookEntry",
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
        /** @var StorageBookEntry $storageBookEntry */
        $storageBookEntry = $this->storageBookEntryRepository->find($id);

        if (empty($storageBookEntry)) {
            return $this->sendError('Storage Book Entry not found');
        }

        $storageBookEntry->delete();

        return $this->sendResponse($id, 'Storage Book Entry deleted successfully');
    }
}
