<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GeralPost - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 min-h-screen">

    <nav class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4 gap-2">
                <a href="{{ route('home') }}" class="text-xl font-bold text-gray-500 dark:text-gray-400 hover:text-indigo-600 transition">⚓ GeralPost </a>
                <span class="text-gray-300 dark:text-gray-600"> | </span>
                <span class="text-sm font-semibold tracking-wider text-amber-500"> 🛡️ Admin Dashboard</span>
            </div>
            <div>
                <span class="text-sm text-gray-500">Hello, Administrator</span>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight mb-2">AI Review Queue</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Here are the news articles captured and reviewed by the AI layers. Please provide human approval to publish to the global feed.</p>
        </div>

        <div class="space-y-4">
            @forelse($pendingPosts as $post)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700 md:flex md:items-center md:justify-between gap-6">

                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400">
                            {{ $post->status }}
                        </span>
                        <span class="text-xs text-gray-400">
                            Generated {{ $post->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">
                        {{ $post->title }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">
                        {{ $post->content }}
                    </p>
                </div>

                <div class="mt-4 md:mt-0 flex items-center space-x-3 shrink-0 gap-2">

                    <form action="{{ route('admin.posts.discard', $post) }}" method="POST">
                        @csrf
                        @method('PATCH')
                    <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium rounded-lg text-sm transition">
                        🗑️ Discard
                    </button>
                    </form>

                    <form action="{{ route('admin.posts.approve', $post) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-sm shadow transition">
                            ✅ Approve and Publish
                        </button>
                    </form>

                </div>

            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow">
                <p class="text-gray-500 dark:text-gray-400">The radar is clear! No new news articles waiting for approval at the moment.</p>
            </div>
            @endforelse
        </div>
    </main>

</body>

</html>