<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller {
 
public function create(): View {
        return view('posts.create');
    }


    public function store(StorePostRequest $request): RedirectResponse {
        
        $validated = $request->validated();

        logger()->info('Post data successfully received:', $validated);

        return back()->with('status', 'Post successfully scheduled on the radar!');
    }
}
