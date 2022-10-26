<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tags;
class TagsController extends Controller
{
    public function getAllTags() {
        $tags = Tags::get()->toJson(JSON_PRETTY_PRINT);
    return response($tags, 200);
      }
  
      public function createTags(Request $request) {
        $tags = new Tags;
        $tags->name = $request->name;
        $tags->type = $request->type;
        $tags->plus_words_client = $request->plus_words_client;
        $tags->minus_words_client = $request->minus_words_client;
        $tags->plus_words_operator = $request->plus_words_operator;
        $tags->minus_words_operator = $request->minus_words_operator;
        $tags->save();
        return response()->json([
            "message" => "tags record created"
        ], 201);
      }
  
      public function getTags($id) {
        if (Tags::where('id', $id)->exists()) {
            $tags = Tags::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($tags, 200);
          } else {
            return response()->json([
              "message" => "Tags not found"
            ], 404);
          }
      }
  
      public function updateTags(Request $request, $id) {
        if (Tags::where('id', $id)->exists()) {
            $tags = Tags::find($id);
            $tags->name = is_null($request->name) ? $tags->name : $request->name;
            $tags->type = is_null($request->type) ? $tags->type : $request->type;
            $tags->plus_words_client = is_null($request->plus_words_client) ? $tags->plus_words_client : $request->plus_words_client;
            $tags->minus_words_client = is_null($request->minus_words_client) ? $tags->minus_words_client : $request->minus_words_client;
            $tags->plus_words_operator = is_null($request->plus_words_operator) ? $tags->plus_words_operator : $request->plus_words_operator;
            $tags->minus_words_operator = is_null($request->minus_words_operator) ? $tags->minus_words_operator : $request->minus_words_operator;
            $tags->save();
    
            return response()->json([
                "message" => "records updated successfully"
            ], 200);
            } else {
            return response()->json([
                "message" => "Tags not found"
            ], 404);
            
        }
      }
  
      public function deleteTags ($id) {
        if(Tags::where('id', $id)->exists()) {
            $tags = Tags::find($id);
            $tags->delete();
    
            return response()->json([
              "message" => "records deleted"
            ], 202);
          } else {
            return response()->json([
              "message" => "Student not found"
            ], 404);
          }
      }
}
