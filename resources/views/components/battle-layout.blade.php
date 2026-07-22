<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
@vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Battle</title>
</head>
<body>
    <header class="bg-black">
        <div class="flex flex-row justify-between px-15 py-5">
            <h1 class="text-white">Battle</h1>
            <form action="{{route('logout')}}" class="text-white" method="POST">
            <button type="submit">
                Logout
            </button>    
            </form>
        </div>
    </header>
    <main class="flex min-h-screen bg-black p-5 text-white flex-row gap-10">
    {{$slot}}
    </main>
    <footer>

    </footer>
</body>
</html>