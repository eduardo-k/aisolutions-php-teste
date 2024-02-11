<?php

namespace App\Http\Controllers;

use App\Exceptions\ImportValidationException;
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
        try {
            $request->validate(['file' => 'required|file|mimetypes:application/json']);

            $fileAsArray = $this->importService->convertFileToArray($request->file('file'));

            $this->importService->validateData($fileAsArray);
            $this->importService->validateBusinessRules($fileAsArray);

            foreach($fileAsArray['documentos'] as $documentAsArray) {
                $category = $this->categoryService->firstOrCreateByName($documentAsArray['categoria']);
                $documentAsArray['category_id'] = $category->id;

                $document = $this->importService->createDocument($documentAsArray);

                ProcessDocuments::dispatch($document);
            }

            return view('import/dispatch');
        } 
        catch (ImportValidationException $e) {
            return view('import/import')->with('alerts', unserialize($e->getMessage()));
        }
        catch (\Exception $e) {
            return view('import/import')->with('alerts', [$e->getMessage()]);
        }
    }

    public function dispatch()
    {
        try {
            Artisan::call('queue:work --stop-when-empty', []);

            return view('import/import')->with('message', 'Fila processada!');
        }
        catch (\Exception $e) {
            return view('import/import')->with('alerts', [$e->getMessage()]);
        }
    }
}
