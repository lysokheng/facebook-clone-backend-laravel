<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Get 

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
    
        //store like 
        $data = $request->all();
        // validate post_id
        $request->validate([
            'post_id' => 'required',
        ]);
        $data['user_id'] = auth()->user()->id;
        $data['post_id'] = $request->post_id;
        $like = Like::create($data);
        return response()->json($like);
    }
    // unlike
    public function unlike(Request $request)
    {
        //store like 
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $data['post_id'] = $request->post_id;
        $like = Like::where('user_id', $data['user_id'])->where('post_id', $data['post_id'])->delete();
        return response()->json($like);
    }

    /// get posts with users like 
    public function getPostLikes($id)
    {
      /// get post with likes and users 
        $posts = Post::with('likes.user')->where('id', $id)->get();
  
        return response()->json($posts);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
