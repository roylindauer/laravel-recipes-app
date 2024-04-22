<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRecipeImport;
use App\Models\Recipe;
use App\Services\ImportRecipeService;
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
            'name' => 'Importing...',
            'import_url' => $request->import_url,
            'active' => false,
        ]);

        ProcessRecipeImport::dispatch(new ImportRecipeService($recipe));

        return redirect()->route('recipes.index')->with('status', 'Recipe queued for import');
    }

    private function validationRules(): array
    {
        return [
            'import_url' => 'required|url'
        ];
    }
}
