<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymenttermAPIRequest;
use App\Http\Requests\API\UpdatePaymenttermAPIRequest;
use App\Models\Paymentterm;
use App\Repositories\PaymenttermRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymenttermController
 * @package App\Http\Controllers\API
 */

class PaymenttermAPIController extends AppBaseController
{
    /** @var  PaymenttermRepository */
    private $paymenttermRepository;

    public function __construct(PaymenttermRepository $paymenttermRepo)
    {
        $this->paymenttermRepository = $paymenttermRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentterms",
     *      summary="Get a listing of the Paymentterms.",
     *      tags={"Paymentterm"},
     *      description="Get all Paymentterms",
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
     *                  @SWG\Items(ref="#/definitions/Paymentterm")
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
        $this->paymenttermRepository->pushCriteria(new RequestCriteria($request));
        $this->paymenttermRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentterms = $this->paymenttermRepository->all();

        return $this->sendResponse($paymentterms->toArray(), 'Paymentterms retrieved successfully');
    }

    /**
     * @param CreatePaymenttermAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paymentterms",
     *      summary="Store a newly created Paymentterm in storage",
     *      tags={"Paymentterm"},
     *      description="Store Paymentterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Paymentterm that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Paymentterm")
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
     *                  ref="#/definitions/Paymentterm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymenttermAPIRequest $request)
    {
        $input = $request->all();

        $paymentterms = $this->paymenttermRepository->create($input);

        return $this->sendResponse($paymentterms->toArray(), 'Paymentterm saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentterms/{id}",
     *      summary="Display the specified Paymentterm",
     *      tags={"Paymentterm"},
     *      description="Get Paymentterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Paymentterm",
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
     *                  ref="#/definitions/Paymentterm"
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
        /** @var Paymentterm $paymentterm */
        $paymentterm = $this->paymenttermRepository->find($id);

        if (empty($paymentterm)) {
            return $this->sendError('Paymentterm not found');
        }

        return $this->sendResponse($paymentterm->toArray(), 'Paymentterm retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaymenttermAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paymentterms/{id}",
     *      summary="Update the specified Paymentterm in storage",
     *      tags={"Paymentterm"},
     *      description="Update Paymentterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Paymentterm",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Paymentterm that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Paymentterm")
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
     *                  ref="#/definitions/Paymentterm"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymenttermAPIRequest $request)
    {
        $input = $request->all();

        /** @var Paymentterm $paymentterm */
        $paymentterm = $this->paymenttermRepository->find($id);

        if (empty($paymentterm)) {
            return $this->sendError('Paymentterm not found');
        }

        $paymentterm = $this->paymenttermRepository->update($input, $id);

        return $this->sendResponse($paymentterm->toArray(), 'Paymentterm updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paymentterms/{id}",
     *      summary="Remove the specified Paymentterm from storage",
     *      tags={"Paymentterm"},
     *      description="Delete Paymentterm",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Paymentterm",
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
        /** @var Paymentterm $paymentterm */
        $paymentterm = $this->paymenttermRepository->find($id);

        if (empty($paymentterm)) {
            return $this->sendError('Paymentterm not found');
        }

        $paymentterm->delete();

        return $this->sendResponse($id, 'Paymentterm deleted successfully');
    }
}
