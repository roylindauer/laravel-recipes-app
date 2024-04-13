<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('recipes.index', [
            'recipes' => Recipe::where('active', true)->withRichText()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('recipes.create', [
            'recipe' => new Recipe()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->validationRules());

        $recipe = Auth::user()->recipes()->create([
            'name' => $request->name,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'instructions' => $request->instructions,
            'tags' => $request->tags
        ]);

        if ($request->hasFile('featured_image')) {
            $recipe->featured_image = $request->file('featured_image')->store('recipe_images');
            $recipe->save();
        }

        return redirect()->route('recipes.index')->with('status', 'Recipe created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recipe = Recipe::withRichText()->find($id);
        return view('recipes.show', [
            'recipe' => $recipe
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $recipe = Recipe::currentUser()->find($id);;
        Gate::authorize('update', $recipe);
        return view('recipes.edit', [
            'recipe' => $recipe
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $recipe = Recipe::currentUser()->find($id);
        Gate::authorize('update', $recipe);

        $request->validate($this->validationRules());

        $recipe->name = $request->name;
        if ($request->hasFile('featured_image')) {
            $recipe->featured_image = $request->file('featured_image')->store('recipe_images');
        }
        $recipe->description = $request->description;
        $recipe->ingredients = $request->ingredients;
        $recipe->instructions = $request->instructions;
        $recipe->tags = $request->tags;
        $recipe->save();

        return redirect()->route('recipes.index')->with('status', 'Recipe updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $recipe = Recipe::currentUser()->find($id);
        Gate::authorize('destroy', $recipe);
    }

    private function validationRules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'nullable|string'
        ];
    }
}
