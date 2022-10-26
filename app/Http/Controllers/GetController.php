<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class GetController extends Controller
{
    public function __invoke(){
        return User::get();
    }
}
