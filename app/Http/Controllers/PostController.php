<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Post;

class PostController extends Controller {

    public function create(): View {
        return view('posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse {

        $validated = $request->validated();

        $request->user()->posts()->create($validated);

        return back()->with('status', 'Post scheduled successfully on the radar!');
    }

    public function index(): View {
        $posts = Post::forFeed()->latest()->get();
        return view('welcome', compact('posts'));
    }

    public function adminIndex(): View {
        $pendingPosts = Post::onlyRevised()->latest()->get();

        return view('posts.admin', compact('pendingPosts'));
    }

    public function approve(Post $post): RedirectResponse {
        $post->update([
            'status' => 'published',
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'News article approved successfully and published to the global feed! 🚀');
    }

    public function discard(Post $post): RedirectResponse {
        $post->update([
            'status' => 'discarded',
        ]);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'News article discarded successfully. 🗑️');
    }
}
