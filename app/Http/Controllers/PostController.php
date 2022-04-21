<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllPosts()
    {   
        $user = auth()->user();
        $posts = Post::all()->where('user_id', $user->id);
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addPost(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'finishDue' => 'required|date',
        ]);

        $new_post = [...$request->only('title', 'description', 'finishDue'), "user_id" => $user->id];
        return Post::create($new_post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postDetail($id)
    {
        $user = auth()->user();
        // dd($user->id);
        // return Post::first();
        try {
            $post = Post::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            return new PostResource($post);
        } catch (Exception $exception) {
            return response()->json(["Post with id:".$id." not found."], 404);
        }
        // $post = Post::find($id)->where('user_id', $user->id);
        // dd(Post::find($id));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePost(Request $request, $id)
    {
        // $post = Post::find($id);
        // $post->update($request->all());
        // return new PostResource($post);
        $user = auth()->user();

        try {
            $post = Post::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $post->update($request->all());
            return new PostResource($post);
        } catch (Exception $exception) {
            return response()->json(["message" => "Post with id: ".$id." is not found."], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePost($id)
    {
        $user = auth()->user();

        try {
            $post = Post::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $post->delete();
            return response()->json([
                "success" => true
            ]);
        } catch (Exception $exception) {
            return response()->json(["message" => "Post with id: ".$id." is not found."], 404);
        }
    }
}
