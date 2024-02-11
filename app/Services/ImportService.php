<?php

namespace App\Services;

use App\Exceptions\ImportValidationException;
use App\Helper\stringHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ImportService
{
    /**
     * Convert a json file to array.
     *
     * @param UploadedFile $file
     * @return array
     */
    public function convertFileToArray(UploadedFile $file): array
    {
        return json_decode(json: File::get($file), associative: true);
    }

    /**
     * Check input array validations.
     *
     * @param array $data
     * @return void|ImportValidationException
     */
    public function validateData(array $data): void 
    {
        $errors = [];

        $rules = [
            'documentos' => 'required|array',
            'documentos.*.categoria' => 'required|string',
            'documentos.*.titulo' => 'required|string',
            'documentos.*.conteúdo' => 'required|string|max:65535',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            foreach($validator->messages()->getMessages() as $messages) {
                $errors[] = $messages[0];
            }
        }

        if ($errors) {
            throw new ImportValidationException($errors);
        }
    }

    /**
     * Check input array business rules.
     *
     * @param array $data
     * @return void|ImportValidationException
     */
    public function validateBusinessRules(array $data): void
    {
        $errors = [];
        $documentos = $data['documentos'];
    
        for ($i = 0; $i < count($documentos); $i++) {
            if ($documentos[$i]['categoria'] === 'Remessa' && !strpos(strtolower($documentos[$i]['titulo']), 'semestre')) {
                $errors[] = "{$i}. Title must have 'semestre' when category is 'Remessa'";
            }
            if ($documentos[$i]['categoria'] === 'Remessa Parcial' && !stringHelper::contemMes($documentos[$i]['titulo'])) {
                $errors[] = "{$i}. Title must have month when category is 'Remessa Parcial'";
            }
        }

        if ($errors) {
            throw new ImportValidationException($errors);
        }
    }

    /**
     * Creates document type variable.
     *
     * @param array $document
     * @return array
     */
    public function createDocument(array $document): array
    {
        return [
            'title' => $document['titulo'],
            'contents' => $document['conteúdo'],
            'category_id' => $document['category_id'],
        ];
    }
}