<?php

namespace Tests\Feature\Services;

use App\Models\Recipe;
use App\Models\User;
use App\Services\ImportRecipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class ImportRecipeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testImport()
    {
        $recipe = Recipe::factory()->for(User::factory()->state([
            'email' => 'recipeuser@example.org'
        ]))->create();

        $jsonData = file_get_contents(base_path('tests/fixtures/recipe_flat.json'));
        $markup = "<html lang=\"\"><head><title>Testo</title><script type=\"application/ld+json\">$jsonData</script></head><body></body></html>";

        $mock = new MockHandler([
            new Response(200, [], $markup),
            new Response(202, ['Content-Length' => 0]),
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $importRecipeService = new ImportRecipeService($client, $recipe);
        $importRecipeService->import();

        $updated_recipe = Recipe::find($recipe->id);
        $this->assertEquals('Garlic-Braised Chicken', $updated_recipe->name);
    }
}
