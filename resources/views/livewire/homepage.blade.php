<div class="min-h-screen bg-gray-50">

    {{-- HEADER --}}
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-6 py-10">
            <h1 class="text-3xl font-bold">
                Welcome, {{ auth()->user()->name }}
            </h1>
            <p class="text-blue-100 mt-1">
                Pandya Memorial Hospital ERP System
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8 space-y-10">

        {{-- 🔥 LIVE STATS (3 COLUMN FIXED ROW) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white/70 backdrop-blur-xl border rounded-2xl p-6 shadow-sm">
                <p class="text-gray-500 text-sm">Total Staff</p>
                <h2 class="text-3xl font-bold text-blue-600">
                    {{ $stats['total_staff'] }}
                </h2>
            </div>

            <div class="bg-white/70 backdrop-blur-xl border rounded-2xl p-6 shadow-sm">
                <p class="text-gray-500 text-sm">Total Memos</p>
                <h2 class="text-3xl font-bold text-green-600">
                    {{ $stats['total_memos'] }}
                </h2>
            </div>

            {{-- 🔴 LIVE UNREAD (AUTO UPDATE) --}}
            <div wire:poll.5s="refreshData"
                 class="bg-white/70 backdrop-blur-xl border rounded-2xl p-6 shadow-sm relative">

                <p class="text-gray-500 text-sm">Unread Memos</p>

                <h2 class="text-3xl font-bold text-red-600">
                    {{ $unreadMemosCount }}
                </h2>

                @if($unreadMemosCount > 0)
                    <span class="absolute top-3 right-3 bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">
                        LIVE
                    </span>
                @endif
            </div>

        </div>

        {{-- 🧩 MODULE GRID (YOUR ICONS UPGRADED) --}}
        <div>

            <h2 class="text-lg font-semibold text-gray-700 mb-5">
                System Modules
            </h2>

           <div class="overflow-x-auto">

 <div class="w-full">

    <div class="flex flex-wrap md:flex-nowrap gap-4">

        @php
            $modules = [
                ['📝','Memos','/memos','blue'],
                ['📰','News','/news','green'],
                ['👨‍⚕️','Staff','/staff','purple'],
                ['🏥','Departments','/departments','yellow'],
                ['⏳','Approvals','/approvals','red'],
                ['📊','Reports','/reports','indigo'],
            ];
        @endphp

        @foreach($modules as $m)
            <a href="{{ $m[2] }}"
               class="group flex-1 min-w-[140px] bg-white/70 backdrop-blur-xl border rounded-2xl p-6 text-center
                      hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                {{-- BIG ICON --}}
                <div class="text-5xl md:text-6xl leading-none">
                    {{ $m[0] }}
                </div>

                {{-- LABEL --}}
                <p class="mt-3 text-sm md:text-base font-semibold text-gray-700 group-hover:text-{{ $m[3] }}-600">
                    {{ $m[1] }}
                </p>

            </a>
        @endforeach

    </div>

</div>

</div>
        </div>

        {{-- 📰 NEWS --}}
        <div class="bg-white/60 backdrop-blur-xl border rounded-2xl p-6">

            <h2 class="text-lg font-semibold mb-4">Latest Updates</h2>

            <div class="space-y-4">

                @forelse($recentNews as $news)
                    <div class="border-l-4 border-blue-500 pl-4">
                        <p class="font-semibold text-gray-800">{{ $news->title }}</p>
                        <p class="text-sm text-gray-500">
                            {{ \Illuminate\Support\Str::limit($news->content, 120) }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No news available</p>
                @endforelse

            </div>

        </div>

    </div>
</div>
