<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Solutions Test - Import Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="px-4 py-5 my-5 text-center">
        <h1 class="display-5 fw-bold text-body-emphasis">AI Solutions</h1>
        <h3 class="display-8 fw-bold text-body-emphasis">PHP Laravel Test</h3>
        <div class="col-lg-6 mx-auto">
            <p class="mb-2 mt-4">Selecione o arquivo JSON com as informações dos documentos para que sejam inseridos na fila:</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <form action="/import" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                    <input type="file" name="file">
                    <button type="submit" class="btn btn-primary btn-lg px-4 gap-2">Importar</button>
                </form>
            </div>
        </div>
    </div>

    @if(isset($message))
        <div class="col-md-10 mx-auto col-lg-5 text-center">
            <div class="alert alert-success">
                {{ $message }}
            </div>
        </div>
    @endif

    @if(isset($alerts))
        <div class="col-md-10 mx-auto col-lg-5 text-center">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($alerts as $alert)
                    <li>{{ $alert }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>