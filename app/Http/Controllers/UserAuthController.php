<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserAuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('authToken')->accessToken;
            return response(['user' => auth()->user(), 'access_token' => $token]);
        }

        return response(['message' => 'Invalid Credentials'], 401);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response(['message' => 'Logged out']);
    }
    /// register user
    public function register(Request $request)
    {
        // dd(request()->getSchemeAndHttpHost());
        $baseUrlImage = request()->getSchemeAndHttpHost() . '/images';
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        /// validate image

        $data = $request->all();

        /// if user already exists
        if (User::where('email', $data['email'])->exists()) {
            /// status user already ex
            return response(['message' => 'User already exists'],);
        }
        /// upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $data['image'] = $name;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'image' => $baseUrlImage.'/'.$data['image']
        ]);

        $token = $user->createToken('authToken')->accessToken;
        $user->image = $baseUrlImage.'/'.$data['image'];
        return response(['user' => $user, 'access_token' => $token]);
    }
    /// update user
    public function update(Request $request,$id)
    {
        $data = $request->all();

        /// find user if exists
        $user = User::find($id);
        if (!$user) {
            return response(['message' => 'User not found']);
        }

    if($user){
        /// update user image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $data['image'] = $name;

            /// remove old image
            $oldImage = public_path('/images/').$user->image;
            if (file_exists($oldImage)) {
                @unlink($oldImage);
            }
        }

        $user->update($data);
        $baseUrlImage = request()->getSchemeAndHttpHost() . '/images';
        $user->image = $baseUrlImage.'/'.$data['image'];

        return response(['user' => $user]);

    }


    }

    /// delete user
    public function delete()
    {
        $user = User::find(auth()->user()->id);
        $user->delete();
        return response(['message' => 'User deleted']);
    }

    /// get current user logged
    public function user()
    {
        return response(['user' => auth()->user()]);
    }
    /// get all users
    public function getAllUsers(){
        $users = User::all();
        return response()->json(['users' => $users]);
    }


    /// upload multiple images
    public function uploadImages(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $images = [];
        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                $image->move($destinationPath, $name);
                $images[] = $name;
            }
        }
        return response()->json(['images' => $images]);
    }

}
