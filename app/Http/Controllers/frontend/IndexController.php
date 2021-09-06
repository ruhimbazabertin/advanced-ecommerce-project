<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class IndexController extends Controller
{
    public function index(){

      return view('frontend.index');
    }

    public function userLogout(){
      Auth::logout();
      return Redirect()->route('login');
    }

    public function userProfile(){

      $id = Auth::user()->id;
      $user = User::find($id);

      return view('frontend.profile.user_profile', compact('user'));

    }

    public function userProfileStore(Request $request){
      $id = Auth::user()->id;
      $data = User::find($id);
      $data->name = $request->name;
      $data->email = $request->email;
      $data->phone = $request->phone;

      if($request->file('profile_photo_path')){
        $file = $request->file('profile_photo_path');
        @unlink(public_path('upload/user_images/'.$data->profile_photo_path));
        $filename = date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('upload/user_images'),$filename);
        $data['profile_photo_path'] = $filename;

        $data->save();

        $notification = array(
          'message' => 'User Profile Updated Successfully' ,
          'alert-type' => 'success',
           );

        return redirect()->route('dashboard')->with($notification);
    }
}

public function userChangePassword(){
      $id = Auth::user()->id;
      $user = User::find($id);
  return view('frontend.profile.change_password', compact('user'));
}

public function userPasswordUpdate(Request $request){

    $validateData = $request->validate([
        'oldPassword' => 'required',
        'password' => 'required|confirmed',
      ]);

      $hashPassword = Auth::user()->password;

      if(Hash::check($request->oldPassword, $hashPassword)){
        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->password);

        $user->save();

        Auth::logout();

        return redirect()->route('user.logout');
      }else{

        $notification  = array(
          'message' => 'Password not match.',
          'alert-type' => 'error',
           );

        return redirect()->back()->with($notification);
      }
}
}