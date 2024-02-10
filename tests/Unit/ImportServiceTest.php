<?php

namespace Tests\Unit;

use App\Exceptions\ImportValidationException;
use App\Services\ImportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ImportServiceTest extends TestCase
{
    private $data  = [
        "exercicio" => 2023,
        "documentos" => [
            [
                "categoria" =>  "Remessa",
                "titulo" => "Eget mi proin sed libero enim",
                "conteúdo" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Mi sit amet mauris commodo quis. At elementum eu facilisis sed odio morbi. Nec ullamcorper sit amet risus nullam eget. Ultrices neque ornare aenean euismod elementum. Eget mi proin sed libero enim. Diam in arcu cursus euismod quis viverra nibh. Quis enim lobortis scelerisque fermentum dui. Erat imperdiet sed euismod nisi porta lorem mollis aliquam ut. Dolor sed viverra ipsum nunc. Sed adipiscing diam donec adipiscing tristique. Feugiat in fermentum posuere urna. Cursus in hac habitasse platea dictumst quisque sagittis purus sit. Interdum consectetur libero id faucibus nisl tincidunt eget nullam. Dui vivamus arcu felis bibendum ut tristique et egestas. Congue quisque egestas diam in arcu cursus euismod quis viverra. Sit amet consectetur adipiscing elit ut aliquam purus. At in tellus integer feugiat. Morbi non arcu risus quis varius quam. Bibendum enim facilisis gravida neque. Vulputate sapien nec sagittis aliquam malesuada. Volutpat ac tincidunt vitae semper quis lectus. Vulputate sapien nec sagittis aliquam. Pellentesque habitant morbi tristique senectus et netus et. Quis vel eros donec ac odio tempor."
            ],
            [
                "categoria" =>  "Remessa Parcial",
                "titulo" => "Interdum consectetur libero",
                "conteúdo" => "At quis risus sed vulputate odio. Nisi est sit amet facilisis magna etiam. Aliquam sem et tortor consequat id. Eget magna fermentum iaculis eu non. Feugiat pretium nibh ipsum consequat nisl vel pretium lectus quam. Turpis in eu mi bibendum neque egestas. Ac turpis egestas sed tempus urna. Facilisis gravida neque convallis a cras semper auctor. Quam adipiscing vitae proin sagittis nisl rhoncus mattis rhoncus urna. Laoreet id donec ultrices tincidunt. Leo duis ut diam quam nulla porttitor. Sagittis id consectetur purus ut faucibus pulvinar elementum integer. Ut faucibus pulvinar elementum integer enim. Interdum velit euismod in pellentesque massa placerat duis. Augue mauris augue neque gravida in fermentum et sollicitudin ac. Justo donec enim diam vulputate ut pharetra sit. Vulputate eu scelerisque felis imperdiet proin fermentum leo. Faucibus purus in massa tempor nec. Id leo in vitae turpis massa. Ipsum nunc aliquet bibendum enim. Sed odio morbi quis commodo. In est ante in nibh mauris. Quam nulla porttitor massa id neque aliquam vestibulum morbi."
            ],
            [
                "categoria" =>  "Remessa",
                "titulo" => "Eget mi proin sed libero enim",
                "conteúdo" => "Elementum integer enim neque volutpat ac. Enim nec dui nunc mattis enim ut tellus elementum sagittis. Facilisi morbi tempus iaculis urna. Lorem donec massa sapien faucibus et molestie ac. Nunc lobortis mattis aliquam faucibus purus in massa. Vitae purus faucibus ornare suspendisse sed nisi. Urna nunc id cursus metus aliquam eleifend. Accumsan tortor posuere ac ut consequat semper viverra nam libero. Mauris pharetra et ultrices neque ornare aenean euismod elementum nisi. Adipiscing diam donec adipiscing tristique risus. Blandit volutpat maecenas volutpat blandit aliquam etiam erat. Purus viverra accumsan in nisl nisi scelerisque eu. Euismod in pellentesque massa placerat duis. Cursus risus at ultrices mi. Non quam lacus suspendisse faucibus interdum. Mattis aliquam faucibus purus in massa tempor nec feugiat nisl. Est pellentesque elit ullamcorper dignissim cras tincidunt. Praesent semper feugiat nibh sed pulvinar proin gravida hendrerit. Turpis cursus in hac habitasse. Massa massa ultricies mi quis hendrerit dolor magna. Et tortor consequat id porta nibh venenatis cras sed."
            ]
        ]
    ];

    public function test_convertFileToArray_should_return_valid(): void
    {
        $importService = new ImportService;

        $path = base_path('tests/Fixtures/jsonFile.json');
        $name = File::name($path);
        $extension = File::extension($path);
        $originalName = $name.'.'.$extension;
        $mimeType = File::mimeType($path);
        $size = File::size($path);
        $error = false;
        $test = false;
        $object = new UploadedFile($path, $originalName, $mimeType, $size, $error, $test);

        $expectedArray = json_decode(json: File::get($path), associative: true);

        $array = $importService->convertFileToArray($object);

        $this->assertEquals($array, $expectedArray);
    }

    public function test_validateData_should_return_true(): void
    {
        $importService = new ImportService;

        $return = $importService->validateData($this->data);

        $this->assertEmpty($return);
    }

    public function test_validateData_without_documentos_should_return_false(): void
    {
        $importService = new ImportService;
        
        unset($this->data['documentos']);
        $errorMessages = ["The documentos field is required."];

        $this->expectException(ImportValidationException::class);
        $this->expectExceptionMessage(serialize($errorMessages));

        $importService->validateData($this->data);
    }

    public function test_validateData_without_documentos_fields_should_return_false(): void
    {
        $importService = new ImportService;

        unset($this->data['documentos'][0]['categoria']);
        unset($this->data['documentos'][0]['titulo']);
        unset($this->data['documentos'][0]['conteúdo']);

        $errorMessages = [
            "The documentos.0.categoria field is required.",
            "The documentos.0.titulo field is required.",
            "The documentos.0.conteúdo field is required.",
        ];

        $this->expectException(ImportValidationException::class);
        $this->expectExceptionMessage(serialize($errorMessages));

        $importService->validateData($this->data);
    }

    public function test_validateData_with_invalid_documentos_fields_should_return_false(): void
    {
        $importService = new ImportService;

        $this->data['documentos'][0]['categoria'] = 1234;
        $this->data['documentos'][0]['titulo'] = 1234;
        $this->data['documentos'][0]['conteúdo'] = 1234;

        $errorMessages = [
            "The documentos.0.categoria field must be a string.",
            "The documentos.0.titulo field must be a string.",
            "The documentos.0.conteúdo field must be a string.",
        ];

        $this->expectException(ImportValidationException::class);
        $this->expectExceptionMessage(serialize($errorMessages));

        $validator = $importService->validateData($this->data);
    }

    public function test_validateData_with_oversized_documentos_conteudo_should_return_false(): void
    {
        $importService = new ImportService;

        $this->data['documentos'][0]['conteúdo'] = str_repeat('a', 65536);

        $errorMessages = [
            "The documentos.0.conteúdo field must not be greater than 65535 characters.",
        ];

        $this->expectException(ImportValidationException::class);
        $this->expectExceptionMessage(serialize($errorMessages));

        $validator = $importService->validateData($this->data);
    }

    public function test_createDocument_should_return_valid()
    {
        $importService = new ImportService;

        $document = [
            'titulo' => 'Eget mi proin sed libero enim',
            'conteúdo' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Mi sit amet mauris commodo quis. At elementum eu facilisis sed odio morbi. Nec ullamcorper sit amet risus nullam eget. Ultrices neque ornare aenean euismod elementum. Eget mi proin sed libero enim. Diam in arcu cursus euismod quis viverra nibh. Quis enim lobortis scelerisque fermentum dui. Erat imperdiet sed euismod nisi porta lorem mollis aliquam ut. Dolor sed viverra ipsum nunc. Sed adipiscing diam donec adipiscing tristique. Feugiat in fermentum posuere urna. Cursus in hac habitasse platea dictumst quisque sagittis purus sit. Interdum consectetur libero id faucibus nisl tincidunt eget nullam. Dui vivamus arcu felis bibendum ut tristique et egestas. Congue quisque egestas diam in arcu cursus euismod quis viverra. Sit amet consectetur adipiscing elit ut aliquam purus. At in tellus integer feugiat. Morbi non arcu risus quis varius quam. Bibendum enim facilisis gravida neque. Vulputate sapien nec sagittis aliquam malesuada. Volutpat ac tincidunt vitae semper quis lectus. Vulputate sapien nec sagittis aliquam. Pellentesque habitant morbi tristique senectus et netus et. Quis vel eros donec ac odio tempor.',
            'category_id' => 1,
        ];

        $expectedDocument = [
            'title' => $document['titulo'],
            'contents' => $document['conteúdo'],
            'category_id' => $document['category_id'],
        ];

        $createdDocument = $importService->createDocument($document);
        $this->assertEquals($expectedDocument, $createdDocument);
    }
}
