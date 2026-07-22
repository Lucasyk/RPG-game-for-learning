<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Character</title>

    @viteReactRefresh
    @vite('resources/js/app.jsx')
</head>
<body>
    @if ($errors->any())
        <div class="bg-red-950 p-4 text-red-200">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div
        id="character-creation"
        data-action="{{ route('player.store') }}"
        data-csrf="{{ csrf_token() }}"
    ></div>
</body>
</html>