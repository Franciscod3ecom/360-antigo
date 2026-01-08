<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title') - Meu Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .btn-meli {
            background-color: #300570;
            color: #ffe600;
            font-weight: bold;
        }
        .btn-meli:hover {
            background-color: #4a1cc1;
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">
    @yield('content')
</body>
</html>
