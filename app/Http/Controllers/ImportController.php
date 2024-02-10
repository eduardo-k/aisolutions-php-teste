<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDocuments;
use App\Services\CategoryService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ImportController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
        private ImportService $importService
    ) {}

    public function import() 
    {
        return view('import/import');
    }

    public function processImport(Request $request) 
    {
        $request->validate(['file' => 'required|file|mimetypes:application/json']);

        $fileAsArray = $this->importService->convertFileToArray($request->file('file'));
        $validator = $this->importService->validateData($fileAsArray);

        if ($validator->fails()) {
            return view('import/import')->with('errors', $validator->messages());
        }

        foreach($fileAsArray['documentos'] as $documentAsArray) {
            $category = $this->categoryService->firstOrCreate($documentAsArray['categoria']);
            $documentAsArray['category_id'] = $category->id;

            $document = $this->importService->createDocument($documentAsArray);

            ProcessDocuments::dispatch($document);
        }

        return view('import/dispatch');
    }

    public function dispatch()
    {
        Artisan::call('queue:work --stop-when-empty', []);

        return view('import/import')->with('message', 'Fila processada!');
    }
}
