<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');

        $recipes = Recipe::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->orWhere('ingredients', 'like', "%$query%")
            ->orWhere('instructions', 'like', "%$query%")
            ->orWhereJsonContains('tags', $query)
            ->get();

        return view('search.index', [
            'recipes' => $recipes,
            'query' => $query,
        ]);
    }
}
