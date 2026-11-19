@if (session('success'))
    <div class="mb-4 rounded-md bg-green-100 p-3 text-sm text-green-800">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-md bg-red-100 p-3 text-sm text-red-800">{{ session('error') }}</div>
@endif