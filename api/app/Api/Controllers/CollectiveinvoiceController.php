<?php namespace App\Api\Controllers;

use App\Api\Controllers\Controller;
use App\Models\Collectiveinvoice;
use App\Api\Transformers\CollectiveinvoiceTransformer;

class CollectiveinvoiceController extends Controller
{
    /**
     * Eloquent model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function model()
    {
        return new Collectiveinvoice;
    }

    /**
     * Transformer for the current model.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    protected function transformer()
    {
        return new CollectiveinvoiceTransformer;
    }
}
