<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use    App\Models\Comment;
class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //create comment 
        $data= $request->all();
        // validate text and post_id
        $request->validate([
            'text' => 'required',
            'post_id' => 'required',
        ]);
        /// get current user from token
        $data['user_id'] = auth()->user()->id;
        $data['post_id'] = $request->post_id;
        $comment = Comment::create($data);
        return response()->json($comment);
    }

    /// show all comments for a post
    public function showComments($id)
    {
        $comments = Comment::with('user')->where('post_id', $id)->get();
        if(count($comments) == 0)
            return response()->json(['error' => 'No comments found.']);
            
        return response()->json($comments);
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
        //update comment 
        $data= $request->all();
        // validate text and post_id
        $request->validate([
            'text' => 'required',
            'post_id' => 'required',
        ]);
        /// get current user from token
        $data['user_id'] = auth()->user()->id;
        $data['post_id'] = $request->post_id;
        $comment = Comment::find($id);
        $comment->update($data);
        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /// check if user is owner of comment
        $comment = Comment::find($id);
        // dd($comment->user_id);
        if(auth()->user()->id != $comment->user_id){
            return response()->json(['error' => 'You can only delete your own comments.']);
        }
        $comment->delete();
        return response()->json(['success' => 'Comment deleted.']);
    }
}
