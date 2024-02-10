<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorReturn;

class ImportService
{
    public function convertFileToArray(UploadedFile $file): array
    {
        return json_decode(json: File::get($file), associative: true);
    }

    public function validateData(array $data): ValidatorReturn
    {
        $rules = [
            'documentos' => 'required|array',
            'documentos.*.categoria' => 'required|string',
            'documentos.*.titulo' => 'required|string',
            'documentos.*.conteúdo' => 'required|string',
        ];

        return Validator::make($data, $rules);
    }

    public function createDocument(array $document): array
    {
        return [
            'title' => $document['titulo'],
            'contents' => $document['conteúdo'],
            'category_id' => $document['category_id'],
        ];
    }
}