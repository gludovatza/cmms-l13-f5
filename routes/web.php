<?php

use App\Models\Document;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/documents/{document}/download', function (Document $document) {
    Gate::authorize('download', $document);

    return Storage::download(
        $document->attachment
    );
})->name('documents.download');
