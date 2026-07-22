<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Battle</title>

    @viteReactRefresh
    @vite([
        'resources/css/app.css',
        'resources/js/app.jsx'
    ])
</head>

<body>
    <div
    id="battle-app"
    data-attack-url="{{ route('battle.attack') }}"
    data-end-url="{{ route('battle.end') }}"
></div>

<script
    id="battle-data"
    type="application/json"
>
    @json($battle)
</script>
</body>
</html>