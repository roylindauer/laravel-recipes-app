<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtractSchema implements ExtractInterface
{
    public function __construct(public string $json)
    {
        //
    }

    public function extract(): array
    {
        return $this->createRecipe($this->findRecipeSchema($this->jsonDecode($this->json)));
    }

    private function jsonDecode($json)
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Log::error('Error parsing JSON for Recipe: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('Error executing jsonDecode for Recipe: ' . $e->getMessage());
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

    private function createRecipe($recipe_schema): array
    {
        $recipe = [];

        $recipe["name"] = $recipe_schema["name"];
        $recipe["description"] = $recipe_schema["description"];
        $recipe["ingredients"] = $this->parseIngredients($recipe_schema["recipeIngredient"]);
        $recipe["instructions"] = $this->parseInstructions($recipe_schema["recipeInstructions"]);

        if (isset($recipe_schema["keywords"])) {
            $recipe["tags"] = $recipe_schema["keywords"];
        }

        return $recipe;
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
            throw new \Exception('Recipe is not a Recipe type of document');
        }

        if (!$recipe_schema) {
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
}
