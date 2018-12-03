<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DemoRequest;

class UsersController extends Controller
{
    public function create(DemoRequest $request)
    {
	return view('users.create');
    }	    
}
