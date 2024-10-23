<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidateRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function ___contructor(){
        $this->middleware('auth:api', ['except'=>['allReviews']]);
     }

     public function submitReviews(Request $request){
      
     $request->validate([
        'movie_id'=>'required|integer',
        'rating'=>'required|integer|max:5',
        'comment'=>'required|string',
      //   'user_id'=>'required|integer'
     ]);

     $review= new Review();
     $review->movie_id=$request->movie_id;
     $review->rating=$request->rating;
     $review->user_id= Auth::user()->id;  //auth()->user->id;
     $review->comment=$request->comment;
     $review->status = Review::pending;
    

     if($review->save()){
         $admins = User::where('user_type','admin')->get();
         foreach($admins as $admin){
            Mail::to($admin->email)->send(new ReviewNotification($review));
         }
       return response()->json(['message'=>'review submitted successfully']);
     }

   }
    
   public function allReviews(){
         $reviews= new Review();
         $all_reviews=$reviews->get();
         if(!$all_reviews){
            return response()->json(['message'=>'No review found']);
         }else{
            return response()->json(['review'=>$all_reviews]);
         }
   }
 
   

   public function approveReview(Request $request,$id){
      $request->validate([
        'status'=>'required|string' //approved || rejected
        ]);
      $review = Review::find($id);
      $admin = Auth::user();
      if($admin->user_type === 'admin'){
        $review->status = Str::lower($request->status);
        $review->save();
        return response()->json(['message'=>'review has been '.$request->status]);
       }
      return response()->json(['message'=>'Only admin can approve review']);
    
    }
}
