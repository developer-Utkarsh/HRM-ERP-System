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

class KnowledgeBasedController extends Controller
{
    public function add_knowledge_based($emp_id,$id=NULL){ 
        $knowledge_based_result = '';
        $allCategory = DB::table('knowledge_based_category')->where('status', 'Active')->where('is_deleted', '0')->get();
        if(!empty($id)){
            $knowledge_based_result  = DB::table('knowledge_based')->where('id', $id)->where('emp_id', $emp_id)->first();
            if (empty($knowledge_based_result)) {
                return redirect()->route('knowledge-based', $emp_id)->with('error', 'Something Went Wrong !');
            }

        }
        return view('knowledge-based', compact('allCategory','emp_id','id','knowledge_based_result'));
    }

    public function storeKnowledgeBased(Request $request,$emp_id,$id=NULL){
        //dd($request->post());
        if(!empty($emp_id)){
            $validatedData = $request->validate([
                'category_name' => 'required',
                'title' => 'required',
            ]);

            if(!empty($id)){
                $knowledgeResult = DB::table('knowledge_based')->where('id', $id)->update([
                    'emp_id'         => $emp_id,
                    'cat_id'         => $request->category_name,
                    'title'          => $request->title,
                    'description'    => $request->description,
                    'reference_link' => $request->reference_link,
                    //'status'         => $request->status,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);

                if ($knowledgeResult) {
                    return redirect()->route('knowledge-based', [$emp_id, $id])->with('success', 'Knowledge Based Update Successfully');
                } else {
                    return redirect()->route('knowledge-based', [$emp_id, $id])->with('error', 'Something Went Wrong !');
                }

            }
            else{
                $knowledgeResult = DB::table('knowledge_based')->insertGetId([
                                    'emp_id'         => $emp_id,
                                    'cat_id'         => $request->category_name,
                                    'title'          => $request->title,
                                    'description'    => $request->description,
                                    'reference_link' => $request->reference_link,
                                    'status'         => 'Pending',
                                    'created_at'     => date('Y-m-d H:i:s'),
                                ]); 
                if ($knowledgeResult) {
                    return redirect()->route('knowledge-based', $emp_id)->with('success', 'Knowledge Based Added Successfully');
                } else {
                    return redirect()->route('knowledge-based', $emp_id)->with('error', 'Something Went Wrong !');
                }                
            }
            
        }
    }

    public function updateKnowledgeBased(Request $request,$emp_id,$id){
        //dd($request->post());
        if(!empty($emp_id) && !empty($id)){
            $validatedData = $request->validate([
                'category_name' => 'required',
                'title' => 'required',
            ]);

            $get_result = DB::table('knowledge_based')->where('id', $id)->first();
            if(!empty($get_result) && ($get_result->status == 'Pending' || $get_result->status == 'Reject')){
                $knowledgeResult = DB::table('knowledge_based')->where('id', $id)->update([
                                        'emp_id'         => $emp_id,
                                        'cat_id'         => $request->category_name,
                                        'title'          => $request->title,
                                        'description'    => $request->description,
                                        'reference_link' => $request->reference_link,
                                        //'status'         => $request->status,
                                        'created_at'     => date('Y-m-d H:i:s'),
                                    ]);
                
                
                if ($knowledgeResult) {
                    return redirect()->route('edit-knowledge-based', [$emp_id, $id])->with('success', 'Knowledge Based Update Successfully');
                } else {
                    return redirect()->route('edit-knowledge-based', [$emp_id, $id])->with('error', 'Something Went Wrong !');
                }
            }
            else{
                return redirect()->route('edit-knowledge-based', [$emp_id, $id])->with('error', 'Knowledge Based Are Approved');
            }

            
        }
    }
}
