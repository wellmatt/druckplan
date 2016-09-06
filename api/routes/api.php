<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'oauth:articles'], function() {
    Route::resource('articles', 'ArticleAPIController');
    Route::resource('article_pricescales', 'ArticlePricescaleAPIController');
    Route::resource('article_orderamounts', 'ArticleOrderamountAPIController');
    Route::resource('article_pictures', 'ArticlePictureAPIController');
    Route::resource('article_qualified_users', 'ArticleQualifiedUserAPIController');
    Route::resource('article_shop_approvals', 'ArticleShopApprovalAPIController');
    Route::resource('article_tags', 'ArticleTagAPIController');
});

Route::group(['middleware' => 'oauth:users'], function() {
    Route::resource('users', 'UserAPIController');
    Route::resource('user_groups', 'UserGroupAPIController');
});

Route::group(['middleware' => 'oauth:useremails'], function() {
    Route::resource('user_emails', 'UserEmailAPIController');
});

Route::group(['middleware' => 'oauth:businesscontacts'], function() {
    Route::resource('businesscontacts', 'BusinesscontactAPIController');
});

Route::group(['middleware' => 'oauth:contactpersons'], function() {
    Route::resource('contactpersons', 'ContactpersonAPIController');
});

Route::group(['middleware' => 'oauth:tradegroups'], function() {
    Route::resource('tradegroups', 'TradegroupAPIController');
});

Route::group(['middleware' => 'oauth:orders'], function() {
    Route::resource('orders', 'OrderAPIController');
    Route::resource('order_calculations', 'OrderCalculationAPIController');
    Route::resource('order_machines', 'OrderMachineAPIController');
});

Route::group(['middleware' => 'oauth:countries'], function() {
    Route::resource('countries', 'CountryAPIController');
});

Route::group(['middleware' => 'oauth:languages'], function() {
    Route::resource('languages', 'LanguageAPIController');
});

Route::group(['middleware' => 'oauth:paymentterms'], function() {
    Route::resource('paymentterms', 'PaymenttermAPIController');
});

Route::group(['middleware' => 'oauth:attachments'], function() {
    Route::resource('attachments', 'AttachmentAPIController');
});

Route::group(['middleware' => 'oauth:attributes'], function() {
    Route::resource('attributes', 'AttributeAPIController');
    Route::resource('attribute_items', 'AttributeItemAPIController');
    Route::resource('businesscontact_attributes', 'BusinesscontactAttributeAPIController');
});

Route::group(['middleware' => 'oauth:chromaticities'], function() {
    Route::resource('chromaticities', 'ChromaticityAPIController');
});

Route::group(['middleware' => 'oauth:clients'], function() {
    Route::resource('clients', 'ClientAPIController');
});

Route::group(['middleware' => 'oauth:collectiveinvoices'], function() {
    Route::resource('collectiveinvoices', 'CollectiveinvoiceAPIController');
    Route::resource('collectiveinvoice_attributes', 'CollectiveinvoiceAttributeAPIController');
    Route::resource('collectiveinvoice_orderpositions', 'CollectiveinvoiceOrderpositionAPIController');
});

Route::group(['middleware' => 'oauth:addresses'], function() {
    Route::resource('addresses', 'AddressAPIController');
});

Route::group(['middleware' => 'oauth:deliveryterms'], function() {
    Route::resource('deliveryterms', 'DeliverytermAPIController');
});

Route::group(['middleware' => 'oauth:documents'], function() {
    Route::resource('documents', 'DocumentAPIController');
});

Route::group(['middleware' => 'oauth:events'], function() {
    Route::resource('events', 'EventAPIController');
    Route::resource('event_participants', 'EventParticipantAPIController');
});

Route::group(['middleware' => 'oauth:finishings'], function() {
    Route::resource('finishings', 'FinishingAPIController');
});

Route::group(['middleware' => 'oauth:foldtypes'], function() {
    Route::resource('foldtypes', 'FoldtypeAPIController');
});

Route::group(['middleware' => 'oauth:formats'], function() {
    Route::resource('formats', 'FormatsAPIController');
});

Route::group(['middleware' => 'oauth:ftpcustuploads'], function() {
    Route::resource('ftpcustuploads', 'FTPCustuploadAPIController');
});

Route::group(['middleware' => 'oauth:groups'], function() {
    Route::resource('groups', 'GroupAPIController');
});

Route::group(['middleware' => 'oauth:ftpdownloads'], function() {
    Route::resource('ftpdownloads', 'FTPDownloadAPIController');
});

Route::group(['middleware' => 'oauth:invoiceemissions'], function() {
    Route::resource('invoiceemissions', 'InvoiceemissionAPIController');
});

Route::group(['middleware' => 'oauth:invoicereverts'], function() {
    Route::resource('invoicereverts', 'InvoicerevertAPIController');
});

Route::group(['middleware' => 'oauth:invoicetemplates'], function() {
    Route::resource('invoicetemplates', 'InvoicetemplateAPIController');
});

Route::group(['middleware' => 'oauth:machines'], function() {
    Route::resource('machines', 'MachineAPIController');
    Route::resource('machine_groups', 'MachineGroupAPIController');
    Route::resource('machine_chromaticities', 'MachineChromaticityAPIController');
    Route::resource('machine_difficulties', 'MachineDifficultyAPIController');
    Route::resource('machine_locks', 'MachineLockAPIController');
    Route::resource('machine_qualified_users', 'MachineQualifiedUserAPIController');
    Route::resource('machine_unit_per_hours', 'MachineUnitPerHourAPIController');
    Route::resource('machine_work_times', 'MachineWorkTimeAPIController');
});

Route::group(['middleware' => 'oauth:papers'], function() {
    Route::resource('papers', 'PaperAPIController');
    Route::resource('paper_prices', 'PaperPriceAPIController');
    Route::resource('paper_sizes', 'PaperSizeAPIController');
    Route::resource('paper_suppliers', 'PaperSupplierAPIController');
    Route::resource('paper_weights', 'PaperWeightAPIController');
});

Route::group(['middleware' => 'oauth:partslists'], function() {
    Route::resource('parts_lists', 'PartsListAPIController');
    Route::resource('parts_list_items', 'PartsListItemAPIController');
});

Route::group(['middleware' => 'oauth:personalizations'], function() {
    Route::resource('personalizations', 'PersonalizationAPIController');
    Route::resource('personalization_items', 'PersonalizationItemAPIController');
    Route::resource('personalization_order_items', 'PersonalizationOrderItemAPIController');
    Route::resource('personalization_orders', 'PersonalizationOrderAPIController');
    Route::resource('personalization_seperations', 'PersonalizationSeperationAPIController');
});

Route::group(['middleware' => 'oauth:planningjobs'], function() {
    Route::resource('planning_jobs', 'PlanningJobAPIController');
});

Route::group(['middleware' => 'oauth:privatcontacts'], function() {
    Route::resource('privat_contacts', 'PrivatContactAPIController');
    Route::resource('privat_contact_accesses', 'PrivatContactAccessAPIController');
});

Route::group(['middleware' => 'oauth:products'], function() {
    Route::resource('products', 'ProductAPIController');
    Route::resource('product_chromaticities', 'ProductChromaticityAPIController');
    Route::resource('product_formats', 'ProductFormatAPIController');
    Route::resource('product_machines', 'ProductMachineAPIController');
    Route::resource('product_papers', 'ProductPaperAPIController');
});

Route::group(['middleware' => 'oauth:storageareas'], function() {
    Route::resource('storage_areas', 'StorageAreaAPIController');
    Route::resource('storage_book_entries', 'StorageBookEntryAPIController');
    Route::resource('storage_positions', 'StoragePositionAPIController');
});

Route::group(['middleware' => 'oauth:storagegoods'], function() {
    Route::resource('storage_goods', 'StorageGoodAPIController');
    Route::resource('storage_good_positions', 'StorageGoodPositionAPIController');
});

Route::group(['middleware' => 'oauth:suporders'], function() {
    Route::resource('sup_orders', 'SupOrderAPIController');
    Route::resource('sup_order_positions', 'SupOrderPositionAPIController');
});

Route::group(['middleware' => 'oauth:tickets'], function() {
    Route::resource('tickets', 'TicketAPIController');
    Route::resource('ticket_categories', 'TicketCategoryAPIController');
    Route::resource('ticket_logs', 'TicketLogAPIController');
    Route::resource('ticket_priorities', 'TicketPriorityAPIController');
    Route::resource('ticket_sources', 'TicketSourceAPIController');
    Route::resource('ticket_states', 'TicketStateAPIController');
});