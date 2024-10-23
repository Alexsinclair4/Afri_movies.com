<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
//use Illuminate\Foundation\Validation\ValidateRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\MovieUpdate;

class MovieController extends Controller
{
    public function ___contructor(){
        $this->middleware('auth:api', ['except'=>['allMovie','searchBytitle','searchBygenre']]);
     }

     public function allMovie(){
        $movie= new Movie();
        $all_movies=$movie->get();
        if(!$all_movies){
            return response()->json(['message'=>'No movie found']);
        }else{
            return response()->json(['movie'=>$all_movies]);
        }
     }
     
     public function searchBytitle(Request $request){
        $request->validate([
         'movie_title'=>'require|string',
        ]);
       $result = Movie::where('movie_title', 'like', '%'.$request->movie_title.'%')->first();
       if($result){
        return response()->json($result);
       }else{
        return response()->json(['message'=>'movie not found'], 404);
       }       
     }

      public function searchBygenre(Request $request){
        $request->validate([
         'genre'=>'require|string',
        ]);
       $result = Movie::where('genre', 'like', '%'.$request->genre.'%')->first();
       if($result){
        return response()->json($result);
       }else{
        return response()->json(['message'=>'movie not found'], 404);
       }       
     }


     public function addMovie(Request $request){
      $request->validate([
       'movie_title'=>'required|string',
       'description'=>'required|string',
       'thumbnail'=>'required|image|mimes:jpg|max:50000',
       'genre'=>'required|string',
       'release_date'=>'required|date'
      ]);

     $photo_name = time() ." ". $request->thumbnail->extension();
     $request->thumbnail->move(public_path('movie_photo'), $photo_name);
     $movie = new Movie();
     $movie->movie_title = $request->movie_title;
     $movie->description = $request->description;
     $movie->thumbnail = 'public/' . " " . $photo_name;
     $movie->genre  = $request->genre;
     $movie->release_date = $request->release_date;
     

     if($movie->save()){
      $users = User::all();
      foreach($users as $user){
     Mail::to($user->email)->send(new MovieUpdate($movie));
      }
     return response()->json(['message'=>'movie uploaded successfully']);
     }else{
        return response()->json(['message'=>'Fail to Upload']);
     }
     }


     public function movieRating(){
     $movies = DB::table('movies')->join('reviews','movies.id','=','reviews.movie_id')->get();
     //$movies = $all_movies->reviews()->with('movies')->get();
     $movieRating = $movies->map(function($movie){
    //  $averageRating = array_reduce($arr_movie,function($carry,$item){
    //     $carry += (int)$item['rating'];
    //     return $carry;
    //   },0); 
      $avgRating = $movie->rating/count((array)$movie->rating);
      //$ty = gettype($movie);
      return[
        'movie_id'=>$movie->id,
        'title'=>$movie->movie_title,
       'average_rating'=>$avgRating,
      ];
     });
      return response()->json(['movies'=>$movieRating]); 

     }
}

