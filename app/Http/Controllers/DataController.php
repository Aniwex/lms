<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;

class DataController extends Controller
{
    public function getAllData() {
        $data = Data::get()->toJson(JSON_PRETTY_PRINT);
    return response($data, 200);
      }
  
      public function createData(Request $request) {
        $data = new Data;
        $data->date = $request->date;
        $data->call = $request->call;
        $data->commentManager = $request->commentManager;
        $data->source = $request->source;
        $data->user = $request->user;
        $data->tag = $request->tag;
        $data->commentClient = $request->commentClient;
        $data->manager = $request->manager;
        $data->client = $request->client;
    $data->save();

    return response()->json([
        "message" => "data record created"
    ], 201);
      }
  
      public function getData($id) {
        if (Data::where('id', $id)->exists()) {
            $data = Data::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($data, 200);
          } else {
            return response()->json([
              "message" => "data not found"
            ], 404);
          }
      }
  
      public function updateData(Request $request, $id) {
        if (Data::where('id', $id)->exists()) {
            $data = Data::find($id);
            $data->date = is_null($request->date) ? $data->date : $request->date;
            $data->call = is_null($request->call) ? $data->call : $request->call;
            $data->commentManager = is_null($request->commentManager) ? $data->commentManager : $request->commentManager;
            $data->source = is_null($request->source) ? $data->source : $request->source;
            $data->user = is_null($request->user) ? $data->user : $request->user;
            $data->tag = is_null($request->tag) ? $data->tag : $request->tag;
            $data->commentClient = is_null($request->commentClient) ? $data->commentClient : $request->commentClient;
            $data->manager = is_null($request->manager) ? $data->manager : $request->manager;
            $data->client = is_null($request->client) ? $data->client : $request->client;
            $data->save();
    
            return response()->json([
                "message" => "records updated successfully"
            ], 200);
            } else {
            return response()->json([
                "message" => "data not found"
            ], 404);
            
        }
      }
  
      public function deleteData ($id) {
        if(Data::where('id', $id)->exists()) {
            $data = Data::find($id);
            $data->delete();
    
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
