<?php

namespace App\Services;
use App\Models\Recipe;

class ImportRecipeService {
    public function __construct()
    {
        //
    }

    public function import(Recipe $recipe): void
    {
        logger('Processing recipe #' . $recipe->id);
    }
}
