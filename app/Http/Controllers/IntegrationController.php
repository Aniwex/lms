<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integration;

class IntegrationController extends Controller
{
    public function getAllIntegrations() {
        $integrations = Integration::get()->toJson(JSON_PRETTY_PRINT);
    return response($integrations, 200);
      }
  
      public function createIntegrations(Request $request) {
        $integrations = new Integration;
        $integrations->name = $request->name;
        $integrations->code = $request->code;
        $integrations->save();
        return response()->json([
            "message" => "integrations record created"
        ], 201);
      }
  
      public function getIntegrations($id) {
        if (Integration::where('id', $id)->exists()) {
            $integrations = Integration::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($integrations, 200);
          } else {
            return response()->json([
              "message" => "Integration not found"
            ], 404);
          }
      }
  
      public function updateIntegrations(Request $request, $id) {
        if (Integration::where('id', $id)->exists()) {
            $integrations = Integration::find($id);
            $integrations->name = is_null($request->name) ? $integrations->name : $request->name;
            $integrations->code = is_null($request->code) ? $integrations->code : $request->code;
            $integrations->save();
    
            return response()->json([
                "message" => "records updated successfully"
            ], 200);
            } else {
            return response()->json([
                "message" => "Integration not found"
            ], 404);
            
        }
      }
  
      public function deleteIntegrations ($id) {
        if(Integration::where('id', $id)->exists()) {
            $integrations = Integration::find($id);
            $integrations->delete();
    
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
