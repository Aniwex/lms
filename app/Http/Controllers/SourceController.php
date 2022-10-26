<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Source;
class SourceController extends Controller
{
    public function getAllSources() {
        $sources = Source::get()->toJson(JSON_PRETTY_PRINT);
    return response($sources, 200);
      }
  
      public function createSources(Request $request) {
        $sources = new Source;
        $sources->integration = $request->integration;
        $sources->name = $request->name;
        $sources->code = $request->code;
        $sources->source_data = $request->source_data;
        $sources->save();
        return response()->json([
            "message" => "sources record created"
        ], 201);
      }
  
      public function getSources($id) {
        if (Source::where('id', $id)->exists()) {
            $sources = Source::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($sources, 200);
          } else {
            return response()->json([
              "message" => "Source not found"
            ], 404);
          }
      }
  
      public function updateSources(Request $request, $id) {
        if (Source::where('id', $id)->exists()) {
            $sources = Source::find($id);
            $sources->integration = is_null($request->integration) ? $sources->integration : $request->integration;
            $sources->name = is_null($request->name) ? $sources->name : $request->name;
            $sources->code = is_null($request->code) ? $sources->code : $request->code;
            $sources->source_data = is_null($request->source_data) ? $sources->source_data : $request->source_data;
            $sources->save();
    
            return response()->json([
                "message" => "records updated successfully"
            ], 200);
            } else {
            return response()->json([
                "message" => "Source not found"
            ], 404);
            
        }
      }
  
      public function deleteSources ($id) {
        if(Source::where('id', $id)->exists()) {
            $sources = Source::find($id);
            $sources->delete();
    
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
