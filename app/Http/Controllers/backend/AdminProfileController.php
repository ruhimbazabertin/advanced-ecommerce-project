<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Auth;


class AdminProfileController extends Controller
{
    public function adminProfile(){
      $adminData = Admin::find(1);
      return view('admin.admin_profile_view', compact('adminData'));
    }
    public function adminProfileEdit(){
       $editData = Admin::find(1);
      return view('admin.admin_profile_edit', compact('editData'));
    }
    public function adminProfileStore(Request $request){

      $data = Admin::find(1);
      $data->name = $request->name;
      $data->email = $request->email;

      if($request->file('profile_photo_path')){
        $file = $request->file('profile_photo_path');
        @unlink(public_path('upload/admin_images/'.$data->profile_photo_path));
        $filename = date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('upload/admin_images'),$filename);
        $data['profile_photo_path'] = $filename;

        $data->save();

        $notification = array(
          'message' => 'Admin Profile Updated Successfully' ,
          'alert-type' => 'success',
           );

        return redirect()->route('admin.profile')->with($notification);

      }
    }

    public function adminChangePassword(){

      return view('admin.admin_change_password');
    }

    public function adminUpdateChangePassword(Request $request){

      $validateData = $request->validate([
        'oldPassword' => 'required',
        'password' => 'required|confirmed',
      ]);

      $hashPassword = Admin::find(1)->password;

      if(Hash::check($request->oldPassword, $hashPassword)){
        $admin = Admin::find(1);
        $admin->password = Hash::make($request->password);

        $admin->save();

        Auth::logout();

        return redirect()->route('admin.logout');
      }else{

        $notification  = array(
          'message' => 'Password not match.',
          'alert-type' => 'error',
           );

        return redirect()->back()->with($notification);
      }
    }
}
