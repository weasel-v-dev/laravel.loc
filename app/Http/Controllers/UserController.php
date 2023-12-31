<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(User $users) {
        return view('users', ['users' => $users::paginate(5)]);
    }

    public function json(User $users) {
        return $users::paginate(5);
    }
}
