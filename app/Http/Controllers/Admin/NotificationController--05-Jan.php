<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiNotification;
use Input;
use App\User;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = Input::get('title');
        //$status = Input::get('status');

        $notifications = ApiNotification::orderBy('id', 'desc');

        if (!empty($title)){
            $notifications->where('title', 'LIKE', '%' . $title . '%');
        }

        $notifications = $notifications->get();

        return view('admin.notification.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.notification.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
        ]);

        $inputs = $request->only('title','description','image','date');

        date_default_timezone_set('Asia/Kolkata');

        $current_date = date('Y-m-d');
        $current_time = date('H:m:i');

        $inputs['date'] = $current_date. ' ' .$current_time; 

        if (Input::hasfile('image')){
            $inputs['image'] = $this->uploadImage(Input::file('image'));
        }        

        $notification = ApiNotification::create($inputs);
        
        $user = User::select('gsm_token')->where('role_id', '!=', '1')->get();

        $load = array();
        $load['title'] = $notification->title;
        $load['description'] = $notification->description;
        if(!empty($notification->image)){
            $load['image'] = asset('laravel/public/notification/'.$notification->image);
        }else{
            $load['image'] = asset('laravel/public/images/test-image.png');
        }
        $load['date'] = $notification->date;
		$load['status'] = NULL;
        $load['type'] = 'general';

        $token = [];

        if(count($user) > 0){
            foreach ($user as $key => $value) {
                if(!empty($value->gsm_token)){
                    $token[] = $value->gsm_token;
                }
            }
        }

        $this->android_notification($token, $load);    

        if ($notification->save()) {
            return redirect()->route('admin.notification.index')->with('success', 'Notification Added Successfully');
        } else {
            return redirect()->route('admin.notification.index')->with('error', 'Something Went Wrong !');
        }
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
        $notification = ApiNotification::find($id);
        return view('admin.notification.edit', compact('notification'));
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
        $validatedData = $request->validate([
            'title' => 'required',
        ]);

        $notification = ApiNotification::where('id', $id)->first();

        $inputs = $request->only('title','description','image');

        if (Input::hasfile('image')){
            $this->RemoveNotificaton($notification->image);
            $inputs['image'] = $this->uploadImage(Input::file('image'));
        }       

        if ($notification->update($inputs)) {
            return redirect()->route('admin.notification.index')->with('success', 'Notification Updated Successfully');
        } else {
            return redirect()->route('admin.notification.index')->with('error', 'Something Went Wrong !');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = ApiNotification::find($id);

        if($notification->image){
            $this->RemoveNotificaton($notification->image);
        }

        if ($notification->delete()) {
            return redirect()->back()->with('success', 'Notification Deleted Successfully');
        } else {
            return redirect()->route('admin.notification.index')->with('error', 'Something Went Wrong !');
        }
    }

    public function uploadImage($image){
        $drive = public_path(DIRECTORY_SEPARATOR . 'notification' . DIRECTORY_SEPARATOR);
        $extension = $image->getClientOriginalExtension();
        $imagename = uniqid() . '.' . $extension;    
        $newImage = $drive . $imagename;
        $imgResource = $image->move($drive, $imagename);
        return $imagename;

    }

    public function RemoveNotificaton($image) {
        $drive = public_path(DIRECTORY_SEPARATOR . 'notification' . DIRECTORY_SEPARATOR);
        $old_image = $drive . $image;
        if (\File::exists($old_image)) {
            \File::delete($old_image);
        }
    }
}
