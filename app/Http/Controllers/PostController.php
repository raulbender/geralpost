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
}
