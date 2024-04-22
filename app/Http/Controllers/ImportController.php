<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRecipeImport;
use App\Models\Recipe;
use App\Services\ImportRecipeService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        try {
            $request->validate($this->validationRules());

            $recipe = Auth::user()->recipes()->create([
                'name' => 'Importing...',
                'import_url' => $request->import_url,
                'active' => false,
            ]);
            Log::info('[User ' . Auth::user()->email . '] Created Recipe [Recipe #'.$recipe->id.'] to Import');

            ProcessRecipeImport::dispatch(new ImportRecipeService($recipe->id));
            Log::info('[Recipe #' . $recipe->id . '] Queued for Import');

            return redirect()->route('recipes.index')->with('status', 'Recipe #' . $recipe->id . ' Queued for Import');
        } catch (\Exception $e) {
            Log::error('[Recipe #' . $recipe->id . '] Error Queuing Recipe for Import: ' . $e->getMessage());
            return redirect()->route('recipes.index')->with('status', 'Error Queuing Recipe for Import');
        }
    }

    private function validationRules(): array
    {
        return [
            'import_url' => 'required|url'
        ];
    }
}
