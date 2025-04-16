<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::all(); // Fetch all customers
        return response()->json($customers); // Return as JSON
    }
}
