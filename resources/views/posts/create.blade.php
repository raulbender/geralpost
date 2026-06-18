<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                
                {{-- Success Alert --}}
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg shadow">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('posts.store') }}" method="POST" class="space-y-6 max-w-xl">
                    @csrf

                    {{-- Field: Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Post Title')}}</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title')
                            <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Field: Content --}}
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Content')}}</label>
                        <textarea name="content" id="content" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content') }}</textarea>
                        @error('content')
                            <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Field: Schedule Date --}}
                    <div>
                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Schedule for (Optional)')}}</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('scheduled_at')
                            <span class="text-sm text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Field: Submit Button --}}
                    <div>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs uppercase tracking-widest rounded-md shadow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{__('Schedule Post')}}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>