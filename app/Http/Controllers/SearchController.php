<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;

class SearchController extends Controller
{
    public function search($search)
    {
        $categories = Category::select('id', 'name')->where('name', 'like', "%$search%");
        $users = User::select('id', 'name')->where('name', 'like', "%$search%");
        // $parameters = Parameter::where('name', 'like', "%$search%");

        // $results = $categories->union($users)->union($parameters)->get();
        $results = $categories->union($users)->get();

        return response()->json($results);
    }
}
