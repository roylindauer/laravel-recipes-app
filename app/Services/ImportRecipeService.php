<?php

namespace App\Services;

use App\Models\Recipe;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;
use Masterminds\HTML5;
use QueryPath\QueryPath;

class ImportRecipeService
{
    public \GuzzleHttp\Client $client;
    public \QueryPath\DOMQuery $qp;

    public function __construct(
        public Recipe $recipe
    )
    {
        //
    }

    public function import(): void
    {
        logger('Processing recipe #' . $this->recipe->id . ' - ' . $this->recipe->import_url);

        try {
            $this->client = new Client();
            $response = $this->client->get($this->recipe->import_url);
            $html = $response->getBody()->getContents();

            $html5 = new HTML5();
            $this->qp = QueryPath::with($html5->loadHTML($html));

            if (!$this->qp->find('script[type="application/ld+json"]')) {
                throw new \Exception('Recipe does not have a Recipe schema');
            }

            $parsed_schema = $this->jsonDecode($this->qp->find('script[type="application/ld+json"]')->text());
            $recipe_schema = $this->findRecipeSchema($parsed_schema);
            $this->createRecipe($recipe_schema);

            logger('Recipe #' . $this->recipe->id . " imported successfully");
        } catch (\Exception $e) {
            logger('Error processing recipe #' . $this->recipe->id . ': ' . $e->getMessage());
        } catch (GuzzleException $e) {
            logger('Error processing recipe URL #' . $this->recipe->id . ': ' . $e->getMessage());
        }

    }

    private function jsonDecode($json)
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            logger('Error parsing JSON: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            logger('Error parsing JSON: ' . $e->getMessage());
            return false;
        }
    }

    private function isRecipe($src): bool
    {
        if (is_array($src)) {
            return in_array('Recipe', $src);
        }

        return $src === 'Recipe';
    }

    private function createRecipe($recipe_schema): bool
    {
        // $name = $qp->find('meta[@property="og:title"]')->attr('content');
        // $description = $qp->find('meta[@property="og:description"]')->attr('content');

        $this->recipe->name = $recipe_schema["name"];
        $this->recipe->active = true;
        $this->recipe->description = $recipe_schema["description"];
        $this->recipe->ingredients = $this->parseIngredients($recipe_schema["recipeIngredient"]);
        $this->recipe->instructions = $this->parseInstructions($recipe_schema["recipeInstructions"]);

        if (isset($recipe_schema["keywords"])) {
            $this->recipe->tags = $recipe_schema["keywords"];
        }

        $featured_image = $this->qp->find('meta[@property="og:image"]')->attr('content');
        if ($featured_image) {
            $image_response = $this->client->get($featured_image);
            $image = $image_response->getBody()->getContents();
            $image_path = 'recipe_images/' . $this->recipe->id . '/featured.jpg';
            Storage::disk('local')->put($image_path, $image);
            $this->recipe->featured_image = $image_path;
        }

        $this->recipe->imported_at = now();
        return $this->recipe->save();
    }

    private function findRecipeSchema($parsed_schema)
    {
        $recipe_schema = false;

        // We may have an array of schema objects.
        if (!isset($parsed_schema["@context"]) && is_array($parsed_schema)) {
            foreach ($parsed_schema as $schema) {
                if ($this->isRecipe($schema["@type"])) {
                    $recipe_schema = $parsed_schema[0];
                    break;
                }
            }
        }

        // We may get an array of graph schemas, so we need to find the Recipe schema
        if (isset($parsed_schema["@graph"])) {
            foreach ($parsed_schema["@graph"] as $schema) {
                if ($this->isRecipe($schema["@type"], 'Recipe')) {
                    $recipe_schema = $schema;
                    break;
                }
            }
        }

        if (isset($parsed_schema["@type"]) && $this->isRecipe($parsed_schema["@type"])) {
            $recipe_schema = $parsed_schema;
        }

        if (isset($parsed_schema["@type"]) && !$this->isRecipe($parsed_schema["@type"])) {
            logger('Recipe #' . $this->recipe->id . ' is not a Recipe type of document');
            throw new \Exception('Recipe is not a Recipe type of document');
        }

        if (!$recipe_schema) {
            logger('Recipe #' . $this->recipe->id . ' does not have a Recipe schema');
            throw new \Exception('Recipe does not have a Recipe schema');
        }

        return $recipe_schema;
    }


    private
    function parseIngredients($ingredients): string
    {
        $parsed = ["<ul>"];
        foreach ($ingredients as $ingredient) {
            $parsed[] = "<li>" . $ingredient . "</li>";
        }
        $parsed[] = "</ul>";
        return implode("\n", $parsed);
    }

    private
    function parseInstructions($instructions): string
    {
        $parsed = ["<ol>"];
        foreach ($instructions as $instruction) {
            $parsed[] = "<li>" . $instruction["text"] . "</li>";
        }
        $parsed[] = "</ol>";
        return implode("\n", $parsed);
    }

    /**
     * Get the unique ID for the job.
     */
    public
    function uniqueId(): string
    {
        return $this->recipe->id;
    }
}
