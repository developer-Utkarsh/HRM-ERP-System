<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Meta;
use App\Category;
use App\News;
use Session;
use App\NewsTags;
use App\User;
use DB;  
use Hash;


class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){

        return redirect()->route('login');

    }

    public function check_user(Request $request){
        $mobile=$request->mobile;
        $emp_code=$request->emp_code;
        $query_check=$request->query_check;
        $user=User::where('mobile', $mobile)->where('register_id', $emp_code)->where('status', 1)->where('is_deleted','0');
        if($query_check=='checkuser'){
            $user=$user->first();
            if(!empty($user)){
                $otp_gen=substr(str_shuffle("0123456789"), 0, 6);
                $user->update(['login_otp'=>$otp_gen]);
                $msg="Use ".$otp_gen. " as one time password(OTP). From Utkarsh Classes";
                $message_content=urlencode($msg);
                $url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx?User=nirmal.gahlot&passwd=k2X0d8eQV4UK&mobilenumber={$mobile}&message={$message_content}&sid=UTKRSH&mtype=N&DR=Y";
                $ch=curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_exec($ch);
                curl_close($ch);
                return $this->returnResponse(200,true, "Details correct");
            }
        }else if($query_check=='checkotp'){
            $user=$user->where("login_otp",$request->frg_otp)->first();
            if(!empty($user)){
              return $this->returnResponse(200,true, "Details correct");  
            }else{
                return $this->returnResponse(200,false, "In correct Otp");
            }
        }else if($query_check=='resetpassword'){
            $user=$user->where("login_otp",$request->frg_otp)->first();
            if(!empty($user)){
                $password = Hash::make($request->password);
                $user->update(['password'=>$password,'last_password_update'=>date('Y-m-d')]);
                return $this->returnResponse(200,true, "Password Updated Successfully");  
            }else{
                return $this->returnResponse(200,false, "Something wnet wrong...try again");
            }  
        }

        return $this->returnResponse(200, false, "Details Incorrect");
    }

     public function index_old(){

       // return redirect()->route('login');

        Meta::set('title', 'Home');
        Meta::set('keywords', 'latest news, breaking news, India news, Sangri Times, sangritimesnews , News Analysis, sangri times hindi, Rajasthan News, current headlines, Sangri times india, news online, breaking news online, latest news headlines, live news online, hot topics, science, technology, lifestyle, world, business, photos, entertainment,local news, jaipur news');
        Meta::set('description', 'Sangri Times is News Media website which covers the latest news in National, Politics, Rajasthan and many more categories.');

        //$blogCategories = Category::with(['get_category_news'])->where('status', '1')->orderBy('id','desc')->get();

        //$news = News::with(['article_category'])->where('is_published', '1')->orderBy('id', 'desc')->limit(3)->get();

        $latest_news = News::with([
            'news_category',
        ])->where('status', '1')->orderBy('id', 'desc')->paginate(5);

        $first_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(4);
            },
        ])->where('slug', 'national')->first();

        //print_r($first_cat_news->toArray()); die;

        $second_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(6);
            },
        ])->where('slug', 'sports')->first();

        $third_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(5);
            },
        ])->where('slug', 'rajasthan')->first();

        $fourth_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(5);
            },
        ])->where('slug', 'business')->first();

        $five_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(5);
            },
        ])->where('slug', 'technology')->first();

        $six_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(5);
            },
        ])->where('slug', 'fashion')->first();

        $seven_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(5);
            },
        ])->where('slug', 'entertainment')->first();

        $eight_cat_news = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc')->limit(4);
            },
        ])->where('slug', 'politics')->first();

        $latest_news_bottom = News::with([
            'news_category','user'
        ])->where('status', '1')->orderBy('id', 'desc')->paginate(4);

        $random_news = News::with([
            'news_category','user'
        ])->where('status', '1')->orderByRaw('RAND()')->limit(2)->get();

        $popular_news = News::with([
            'news_category','user'
        ])->where('status', '1')->orderBy('hit_count', 'desc')->limit(4)->get(); 

        
        
        return view('index', compact('latest_news','first_cat_news','second_cat_news','third_cat_news','fourth_cat_news','five_cat_news','six_cat_news','seven_cat_news', 'eight_cat_news','latest_news_bottom','random_news','popular_news'));
    }



    public function news_detail($slug1 = null, $slug2 = null){

        //$blogCategories = BlogCategory::with(['get_category_news'])->where('is_active', '1')->orderBy('id','desc')->get();

        if(isset($slug1) && !empty($slug2)){

            $get_category = Category::where('slug', $slug1)->first();
            
            if (!$get_category) {
				abort(404);
			}

            $news = News::with(['news_category', 'has_tags'])->where('slug', $slug2)->first();
            
            if (!$news) {
				abort(404);
			}

            $post_id = $news->id;

            $newskey = 'news' . $post_id;

            if(!Session::has($newskey)){
                $news->increment('hit_count');
                Session::put($newskey, 1);
            }

            $related_news = Category::with([
                'get_category_news' => function($q) use ($post_id){
                    $q->where('status', '1')->where('news_id', '!=', $post_id)->orderBy('id', 'desc')->limit(4);
                },
            ])->where('slug', $get_category->slug)->first();                 

            Meta::set('title', $news->title);
            Meta::set('keywords', 'latest news, breaking news, India news, Sangri Times, sangritimesnews , News Analysis, sangri times hindi, Rajasthan News, current headlines, Sangri times india, news online, breaking news online, latest news headlines, live news online, hot topics, science, technology, lifestyle, world, business, photos, entertainment,local news, jaipur news');
            Meta::set('description', 'Sangri Times is News Media website which covers the latest news in National, Politics, Rajasthan and many more categories.');

            $popular_news = News::with([
                'news_category',
            ])->where('status', '1')->orderBy('hit_count', 'desc')->limit(4)->get();

            // $recent_news = Article::with([
            //     'article_category',
            // ])->where('id', '!=', $post_id)->where('is_published', '1')->orderBy('id', 'desc')->limit(5)->get();

            // $most_read = Article::with([
            //     'article_category',
            // ])->where('id', '!=', $post_id)->where('is_published', '1')->orderByRaw('RAND()')->limit(5)->get();

            $breadcums = array(
                'Home' => \URL::to('/'),
                $get_category->name => \URL::to($get_category->slug),
                $news->title => '',
            );            
        }

        return view('single', compact('news', 'related_news', 'breadcums', 'popular_news'));       
    }

    public function show_category($slug = null){

        $slug = \Request::segment(1);       

        $get_all_news_category = Category::with([
            'get_category_news' => function($q){
                $q->where('status', '1')->orderBy('id', 'desc');
            }, 
        ])->where('slug', $slug)->first();
        
         if (!$get_all_news_category) {
				abort(404);
			}

        $popular_news = News::with([
            'news_category',
        ])->where('status', '1')->orderBy('hit_count', 'desc')->limit(4)->get();     

        Meta::set('title', $get_all_news_category->name);
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            $get_all_news_category->name => '',
        );

        return view('category', compact('breadcums','get_all_news_category', 'popular_news'));
    }

    public function show_tags($slug = null){

        $tags = NewsTags::where('tags', $slug)->get();
        
        if (!$tags) {
				abort(404);
			}

        $news_ids = [];

        foreach($tags as $value){

            $news_ids[] = $value->news_id;
        }

        if(isset($news_ids) && is_array($news_ids)){
            $get_all_news_by_tags = News::with(['news_category', 'has_tags'])->where('status', '1')->whereIn('id', $news_ids)->get();    
        }       

        $popular_news = News::with([
            'news_category',
        ])->where('status', '1')->orderBy('hit_count', 'desc')->limit(4)->get();     
        
        Meta::set('title', $slug);
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'Topic' => '',
        );
        
        return view('tags', compact('slug', 'get_all_news_by_tags', 'breadcums','popular_news'));
    }

    public function show_user_news($slug = null){

        $user = User::where('username', $slug)->first();
        
        if (!$user) {
				abort(404);
			}

        if(isset($user) && !empty($user)){
            $get_all_news_by_user = News::with(['news_category', 'has_tags'])->where('status', '1')->where('user_id', $user->id)->get();    
        }
        
        if (!$get_all_news_by_user) {
				abort(404);
			}

        $popular_news = News::with([
            'news_category',
        ])->where('status', '1')->orderBy('hit_count', 'desc')->limit(4)->get();     
        
        Meta::set('title', $user->name);
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            $user->name => '',
        );
        
        return view('user-news', compact('user', 'get_all_news_by_user', 'breadcums','popular_news'));
    }

    public function about_us(){ 

        die('dddd');         

        Meta::set('title', 'About Us');
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'About Us' => '',
        );
        
        return view('about', compact('breadcums'));
    }

    public function contact_us(){          

        Meta::set('title', 'Contact Us');
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'Contact Us' => '',
        );
        
        return view('contact', compact('breadcums'));
    }

    public function team(){          

        Meta::set('title', 'Our Team');
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'Our Team' => '',
        );
        
        return view('team', compact('breadcums'));
    }

    public function join_us(){          

        Meta::set('title', 'Join Us');
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'Join Us' => '',
        );
        
        return view('joinus', compact('breadcums'));
    }

    public function privacy(){          

        Meta::set('title', 'Privacy Policy');
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'Privacy Policy' => '',
        );
        
        return view('privacy', compact('breadcums'));
    }

    public function terms(){          

        Meta::set('title', 'Terms & Condition');
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'Terms & Condition' => '',
        );
        
        return view('terms', compact('breadcums'));
    }

    public function legal_info(){          

        Meta::set('title', 'Legal Info');
        Meta::set('keywords', '');
        Meta::set('description', '');

        $breadcums = array(
            'Home' => \URL::to('/'),
            'Legal Info' => '',
        );
        
        return view('legal', compact('breadcums'));
    }

    public function rss_feed(){

        $posts = News::with(['news_category'])->where('status', '1')->orderBy('id', 'desc')->get();
        return response()->view('rss', [
            'posts' => $posts,
        ])->header('Content-Type', 'text/xml');
    }
}
