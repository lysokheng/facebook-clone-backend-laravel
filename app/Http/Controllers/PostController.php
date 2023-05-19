<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /// get posts with likes count and comments count 
        $posts = Post::withCount('likes')->withCount('comments')->with('user')->get();
        /// order date 
     
        $baseUrlImage = request()->getSchemeAndHttpHost() . '/images';
        foreach ($posts as $post) {
            $post->image = $baseUrlImage . '/' . $post->image;
        }
      
        
        foreach($posts as $post){
            $post['liked'] = $post->likes->contains('user_id', auth()->user()->id);
            /// get first user liked each posts
            $post['first_user_liked'] = $post->user()->first();
        }
        /// get first user liked post 
      

        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /// create posts 
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //create posts 
        $data= $request->all();
        /// validate image 
        $request->validate([
            'title' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        /// upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $data['image'] = $name;
        }
        /// get current user from token 
        $data['user_id'] = auth()->user()->id;
        $post = Post::create($data);
        $baseUrlImage = request()->getSchemeAndHttpHost() . '/images';
        $post->image = $baseUrlImage.'/'.$data['image'];

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // show post
        $post = Post::find($id);
        return response()->json($post);
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
        
        $data = $request->all();
        $post = Post::find($id);
           /// check if user is owner of post
           if($post->user_id != auth()->user()->id){
            return response()->json(['message' => 'you are not allowed to delete this post'],403);
        }
        if($post){
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                $image->move($destinationPath, $name);
                $data['image'] = $name;
                // delete old image
                $oldImage = public_path('/images/').$post->image;
                if(file_exists($oldImage)){
                    @unlink($oldImage);
                }

            }

            $post->update($data);
            $baseUrlImage = request()->getSchemeAndHttpHost() . '/images';
            $post->image = $baseUrlImage.'/'.$data['image'];
            return response()->json($post);

        }
        return response()->json(['message' => 'post not found'],404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        /// check if user is owner of post
        if($post->user_id != auth()->user()->id){
            return response()->json(['message' => 'you are not allowed to delete this post'],403);
        }
        if($post){
            $post->delete();
            return response()->json(['message' => 'post deleted successfully']);
        }
        return response()->json(['message' => 'post not found'],404);
    }
}
