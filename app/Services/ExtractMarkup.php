<?php

namespace App\Services;

class ExtractMarkup implements ExtractInterface
{
    public function __construct(public string $markup)
    {
        //
    }

    public function extract(): array
    {
        return $this->parseMarkup($this->markup);
    }

    public function parseMarkup($markup)
    {
        $recipe = [];

        $recipe["name"] = $this->parseName($markup);
        $recipe["description"] = $this->parseDescription($markup);
        $recipe["ingredients"] = $this->parseIngredients($markup);
        $recipe["instructions"] = $this->parseInstructions($markup);

        return $recipe;
    }

    private function parseName($markup)
    {
        return 'Recipe Name';
    }

    private function parseDescription($markup)
    {
        return 'Recipe Description';
    }

    private function parseIngredients($markup)
    {
        return ['Ingredient 1', 'Ingredient 2'];
    }

    private function parseInstructions($markup)
    {
        return ['Instruction 1', 'Instruction 2'];
    }
}
