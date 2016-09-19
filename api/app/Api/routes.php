<?php

Route::group(['prefix' => 'v1', 'namespace' => 'App\Api\Controllers', 'middleware' => 'oauth'], function () {
    Route::resource('collectiveinvoices', 'CollectiveinvoiceController');
});