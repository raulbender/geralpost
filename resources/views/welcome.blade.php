<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('GeralPost - Publications Radar') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 min-h-screen">
        
        <nav class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xl font-bold tracking-wider text-indigo-600 dark:text-indigo-400">⚓ GeralPost</span>
                </div>

                <div class="flex items-center space-x-4 gap-4">
                    @auth
                        <a href="{{ route('posts.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow text-sm transition">
                            ➕ {{ __('Create New Post') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">
                            {{__('Log In')}}
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-sm font-medium rounded-md transition">
                            {{__('Register')}}
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-extrabold tracking-tight mb-8">{{ __('Radar Feed') }}</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($posts as $post)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden p-6 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400">
                                    {{ $post->status }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $post->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <h2 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-100 line-clamp-2">
                                {{ $post->title }}
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-4 mb-4">
                                {{ $post->content }}
                            </p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-auto">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ __('By(id):') }} {{ $post->user_id }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow">
                        <p class="text-gray-500 dark:text-gray-400">{{ __('No posts found on the radar yet. Be the first to create one!') }}</p>
                    </div>
                @endforelse
            </div>
        </main>

    </body>
</html>