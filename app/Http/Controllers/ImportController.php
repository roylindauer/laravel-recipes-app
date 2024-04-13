<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRecipeImport;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use QueryPath\QueryPath;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function create()
    {
        return view('import.create', [
            'recipe' => new Recipe()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        $recipe = Auth::user()->recipes()->create([
            'name' => $request->name,
            'import_url' => $request->import_url,
            'active' => false,
        ]);

        ProcessRecipeImport::dispatch($recipe);

        return redirect()->route('recipes.index')->with('status', 'Recipe queued for import');
    }

    private function validationRules(): array
    {
        return [
            'name' => 'required|string',
            'import_url' => 'required|url'
        ];
    }
}
