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

Route::resource('articles', 'ArticleAPIController');

Route::resource('article_pricescales', 'ArticlePricescaleAPIController');

Route::resource('article_orderamounts', 'ArticleOrderamountAPIController');

Route::resource('article_pictures', 'ArticlePictureAPIController');

Route::resource('article_qualified_users', 'ArticleQualifiedUserAPIController');

Route::resource('users', 'UserAPIController');

Route::resource('article_shop_approvals', 'ArticleShopApprovalAPIController');

Route::resource('businesscontacts', 'BusinesscontactAPIController');

Route::resource('contactpeople', 'ContactpersonAPIController');

Route::resource('article_tags', 'ArticleTagAPIController');

Route::resource('tradegroups', 'TradegroupAPIController');

Route::resource('orders', 'OrderAPIController');

Route::resource('countries', 'CountryAPIController');

Route::resource('languages', 'LanguageAPIController');

Route::resource('paymentterms', 'PaymenttermAPIController');

Route::resource('attachments', 'AttachmentAPIController');

Route::resource('attributes', 'AttributeAPIController');

Route::resource('attribute_items', 'AttributeItemAPIController');

Route::resource('businesscontact_attributes', 'BusinesscontactAttributeAPIController');

Route::resource('chromaticities', 'ChromaticityAPIController');

Route::resource('clients', 'ClientAPIController');

Route::resource('collectiveinvoices', 'CollectiveinvoiceAPIController');

Route::resource('addresses', 'AddressAPIController');

Route::resource('collectiveinvoice_attributes', 'CollectiveinvoiceAttributeAPIController');

Route::resource('collectiveinvoice_orderpositions', 'CollectiveinvoiceOrderpositionAPIController');

Route::resource('deliveryterms', 'DeliverytermAPIController');

Route::resource('documents', 'DocumentAPIController');

Route::resource('events', 'EventAPIController');

Route::resource('event_participants', 'EventParticipantAPIController');

Route::resource('finishings', 'FinishingAPIController');

Route::resource('foldtypes', 'FoldtypeAPIController');

Route::resource('formats', 'FormatsAPIController');

Route::resource('f_t_p_custuploads', 'FTPCustuploadAPIController');



Route::resource('groups', 'GroupAPIController');

Route::resource('f_t_p_downloads', 'FTPDownloadAPIController');

Route::resource('invoiceemissions', 'InvoiceemissionAPIController');

Route::resource('invoicereverts', 'InvoicerevertAPIController');

Route::resource('invoicetemplates', 'InvoicetemplateAPIController');

Route::resource('machine_groups', 'MachineGroupAPIController');

Route::resource('machines', 'MachineAPIController');

Route::resource('machine_chromaticities', 'MachineChromaticityAPIController');

Route::resource('machine_difficulties', 'MachineDifficultyAPIController');

Route::resource('machine_locks', 'MachineLockAPIController');

Route::resource('machine_qualified_users', 'MachineQualifiedUserAPIController');

Route::resource('machine_unit_per_hours', 'MachineUnitPerHourAPIController');

Route::resource('machine_work_times', 'MachineWorkTimeAPIController');

Route::resource('order_calculations', 'OrderCalculationAPIController');

Route::resource('order_machines', 'OrderMachineAPIController');

Route::resource('papers', 'PaperAPIController');

Route::resource('paper_prices', 'PaperPriceAPIController');

Route::resource('paper_sizes', 'PaperSizeAPIController');

Route::resource('paper_suppliers', 'PaperSupplierAPIController');

Route::resource('paper_weights', 'PaperWeightAPIController');

Route::resource('parts_lists', 'PartsListAPIController');

Route::resource('parts_list_items', 'PartsListItemAPIController');

Route::resource('personalizations', 'PersonalizationAPIController');

Route::resource('personalization_items', 'PersonalizationItemAPIController');

Route::resource('personalization_order_items', 'PersonalizationOrderItemAPIController');

Route::resource('personalization_orders', 'PersonalizationOrderAPIController');

Route::resource('personalization_seperations', 'PersonalizationSeperationAPIController');

Route::resource('planning_jobs', 'PlanningJobAPIController');

Route::resource('privat_contacts', 'PrivatContactAPIController');

Route::resource('privat_contact_accesses', 'PrivatContactAccessAPIController');

Route::resource('products', 'ProductAPIController');

Route::resource('product_chromaticities', 'ProductChromaticityAPIController');

Route::resource('product_formats', 'ProductFormatAPIController');

Route::resource('product_machines', 'ProductMachineAPIController');

Route::resource('product_papers', 'ProductPaperAPIController');

Route::resource('storage_areas', 'StorageAreaAPIController');

Route::resource('storage_book_entries', 'StorageBookEntryAPIController');

Route::resource('storage_positions', 'StoragePositionAPIController');

Route::resource('storage_goods', 'StorageGoodAPIController');

Route::resource('storage_good_positions', 'StorageGoodPositionAPIController');

Route::resource('sup_orders', 'SupOrderAPIController');

Route::resource('sup_order_positions', 'SupOrderPositionAPIController');

Route::resource('tickets', 'TicketAPIController');

Route::resource('ticket_categories', 'TicketCategoryAPIController');

Route::resource('ticket_logs', 'TicketLogAPIController');

Route::resource('ticket_priorities', 'TicketPriorityAPIController');

Route::resource('ticket_sources', 'TicketSourceAPIController');

Route::resource('ticket_states', 'TicketStateAPIController');

Route::resource('user_emails', 'UserEmailAPIController');

Route::resource('user_groups', 'UserGroupAPIController');