<?php

namespace App\Repositories;

use App\Models\Document;
use InfyOm\Generator\Common\BaseRepository;

class DocumentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'doc_name',
        'doc_req_id',
        'doc_req_module',
        'doc_type',
        'doc_hash',
        'doc_sent',
        'doc_crtdat',
        'doc_crtusr',
        'doc_price_netto',
        'doc_price_brutto',
        'doc_payable',
        'doc_payed',
        'doc_warning_id',
        'doc_reverse',
        'doc_storno_date',
        'paper_order_pid'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Document::class;
    }
}
