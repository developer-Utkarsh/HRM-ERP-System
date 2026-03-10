<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Hash;
use Input;
use Image;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('faculty.index');
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
        //
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

    public function profile() {        
        return view('faculty.updateprofile');
    }

    public function profile_update(Request $request) {

        $user = User::findOrFail(Auth::user()->id);        
        $user->name = $request->name;
        if (Input::hasfile('image')){
            $this->RemoveProfileImage($user->image);
            $user->image = $this->uploadProfileImage(Input::file('image'));
        }
        $user->mobile = $request->mobile;

        if ($user->update()){
            return redirect()->route('faculty.profile')->with('success', 'Profile Updated Successfully.');
        } else {
            return redirect()->route('faculty.profile')->with('error', 'Profile Was Not Updated');
        }
    }

    public function change_password() {        
        return view('faculty.updatepassword');
    }


    public function update_password(Request $request) {        
        $user = User::findOrFail(Auth::user()->id);
        $input = $request->only('password');
        if ($request->cpass) {
            if (Hash::check($request->cpass, $user->password)) {

                if ($request->newpass == $request->renewpass) {
                    $input['password'] = Hash::make($request->newpass);
                } else {
                    return redirect()->route('faculty.password')->with('error', 'Confirm Password Does not match.');
                }
            } else {
                return redirect()->route('faculty.password')->with('error', 'Current Password Does not match.');
            }
        }
        
        $user->update($input);
        return redirect()->route('faculty.password')->with('success', 'Admin Password Updated Successfully.');       
    }

    /*Image Upload*/
    function uploadProfileImage($image){
        $extension = $image->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        $newImagename = Image::make($image);
        $newImagename->save(public_path('profile/' . $filename));
        return $filename;

    }

    
    /*Remove Image*/
    public function RemoveProfileImage($image) {
        $drive = public_path(DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR);
        $old_image = $drive . $image;
        if (\File::exists($old_image)) {
            \File::delete($old_image);
        }
    }
}
