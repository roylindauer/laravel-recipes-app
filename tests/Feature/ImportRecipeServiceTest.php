<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Recipe;
use App\Models\User;
use App\Services\ImportRecipeService;
use Tests\TestCase;

class ImportRecipeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testImport()
    {
        // https://laravel.com/docs/11.x/eloquent-factories#belongs-to-relationships
        $recipe = Recipe::factory()->for(User::factory()->state([
            'email' => 'recipeuser@example.org'
        ]))->create();

        $importRecipeService = new ImportRecipeService($recipe);
        $importRecipeService->import();

        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
