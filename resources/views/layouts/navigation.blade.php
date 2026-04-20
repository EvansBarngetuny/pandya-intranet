<nav class="bg-white shadow mb-4">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between">

        <div class="font-bold text-blue-600">
            Pandya Intranet
        </div>

        <div class="space-x-4">
            <a href="{{ route('dashboard') }}" class="text-gray-700">Dashboard</a>
            <a href="{{ route('news.index') }}" class="text-gray-700">News</a>
            <a href="{{ route('memos.index') }}" class="text-gray-700">Memos</a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="text-red-600">Logout</button>
            </form>
        </div>

    </div>
</nav>
