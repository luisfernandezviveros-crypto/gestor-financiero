@if (session('success'))
    <div class="mb-4 rounded-md border border-green-300 bg-green-50 p-4 text-green-800" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-md border border-red-300 bg-red-50 p-4 text-red-800" role="alert">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-md border border-red-300 bg-red-50 p-4 text-red-800" role="alert">
        <p class="font-semibold">Revisa los siguientes errores:</p>
        <ul class="mt-1 list-inside list-disc text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
