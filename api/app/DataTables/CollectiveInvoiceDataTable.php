<?php

namespace App\DataTables;

use App\Models\CollectiveInvoice;
use Form;
use Yajra\Datatables\Services\DataTable;

class CollectiveInvoiceDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('action', 'collective_invoices.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $collectiveInvoices = CollectiveInvoice::query();

        return $this->applyScopes($collectiveInvoices);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->addAction(['width' => '10%'])
            ->ajax('')
            ->parameters([
                'dom' => 'Bfrtip',
                'scrollX' => false,
                'buttons' => [
                    'print',
                    'reset',
                    'reload',
                    [
                         'extend'  => 'collection',
                         'text'    => '<i class="fa fa-download"></i> Export',
                         'buttons' => [
                             'csv',
                             'excel',
                             'pdf',
                         ],
                    ],
                    'colvis'
                ]
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    private function getColumns()
    {
        return [
            'status' => ['name' => 'status', 'data' => 'status'],
            'title' => ['name' => 'title', 'data' => 'title'],
            'number' => ['name' => 'number', 'data' => 'number'],
            'deliverycosts' => ['name' => 'deliverycosts', 'data' => 'deliverycosts'],
            'comment' => ['name' => 'comment', 'data' => 'comment'],
            'client' => ['name' => 'client', 'data' => 'client'],
            'businesscontact' => ['name' => 'businesscontact', 'data' => 'businesscontact'],
            'deliveryterm' => ['name' => 'deliveryterm', 'data' => 'deliveryterm'],
            'paymentterm' => ['name' => 'paymentterm', 'data' => 'paymentterm'],
            'deliveryaddress' => ['name' => 'deliveryaddress', 'data' => 'deliveryaddress'],
            'invoiceaddress' => ['name' => 'invoiceaddress', 'data' => 'invoiceaddress'],
            'crtdate' => ['name' => 'crtdate', 'data' => 'crtdate'],
            'crtuser' => ['name' => 'crtuser', 'data' => 'crtuser'],
            'uptdate' => ['name' => 'uptdate', 'data' => 'uptdate'],
            'uptuser' => ['name' => 'uptuser', 'data' => 'uptuser'],
            'intent' => ['name' => 'intent', 'data' => 'intent'],
            'intern_contactperson' => ['name' => 'intern_contactperson', 'data' => 'intern_contactperson'],
            'cust_message' => ['name' => 'cust_message', 'data' => 'cust_message'],
            'cust_sign' => ['name' => 'cust_sign', 'data' => 'cust_sign'],
            'custContactperson' => ['name' => 'custContactperson', 'data' => 'custContactperson'],
            'needs_planning' => ['name' => 'needs_planning', 'data' => 'needs_planning'],
            'deliverydate' => ['name' => 'deliverydate', 'data' => 'deliverydate'],
            'rdyfordispatch' => ['name' => 'rdyfordispatch', 'data' => 'rdyfordispatch'],
            'ext_comment' => ['name' => 'ext_comment', 'data' => 'ext_comment'],
            'thirdparty' => ['name' => 'thirdparty', 'data' => 'thirdparty'],
            'thirdpartycomment' => ['name' => 'thirdpartycomment', 'data' => 'thirdpartycomment'],
            'ticket' => ['name' => 'ticket', 'data' => 'ticket'],
            'offer_header' => ['name' => 'offer_header', 'data' => 'offer_header'],
            'offer_footer' => ['name' => 'offer_footer', 'data' => 'offer_footer'],
            'offerconfirm_header' => ['name' => 'offerconfirm_header', 'data' => 'offerconfirm_header'],
            'offerconfirm_footer' => ['name' => 'offerconfirm_footer', 'data' => 'offerconfirm_footer'],
            'factory_header' => ['name' => 'factory_header', 'data' => 'factory_header'],
            'factory_footer' => ['name' => 'factory_footer', 'data' => 'factory_footer'],
            'delivery_header' => ['name' => 'delivery_header', 'data' => 'delivery_header'],
            'delivery_footer' => ['name' => 'delivery_footer', 'data' => 'delivery_footer'],
            'invoice_header' => ['name' => 'invoice_header', 'data' => 'invoice_header'],
            'invoice_footer' => ['name' => 'invoice_footer', 'data' => 'invoice_footer'],
            'revert_header' => ['name' => 'revert_header', 'data' => 'revert_header'],
            'revert_footer' => ['name' => 'revert_footer', 'data' => 'revert_footer']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'collectiveInvoices';
    }
}
