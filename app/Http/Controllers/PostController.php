<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $post = Post::with(['images'])->orderBy('created_at', 'desc')->get();
        return response()->json([
            'tasks'    => $post,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id'        => 'required',
            'title'          => 'required|max:255',
        ]);

        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('public/images');
                PostImage::create([
                    'post_id' => $post->id,
                    'image' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post created successfully',
        ], 200);
    }

    public function show(Post $post)
    {
        $post = Post::with(['images'])->where('id', $post->id)->first();
        return response()->json([
            'post'    => $post,
        ], 200);
    }

    public function update(Request $request, Post $post)
    {
        $this->validate($request, [
            'post_id'        => 'required',
            'title'          => 'required|max:255',
        ]);

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('public/images');
                PostImage::create([
                    'post_id' => $post->id,
                    'image' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post updated successfully',
        ], 200);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }




}
