<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\ExtractMarkup;

class ExtractMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function testExtract()
    {
        $expected = [
            'name' => 'Recipe Name',
            'description' => 'Recipe Description',
            'ingredients' => ['Ingredient 1', 'Ingredient 2'],
            'instructions' => ['Instruction 1', 'Instruction 2']
        ];

        $markup = "<meta property='og:title' content='Recipe Name'>";
        $markup .= "<meta property='og:description' content='Recipe Description'>";
        $markup .= "<meta property='og:image' content='Recipe Image'>";
        $markup .= "<script type='application/ld+json'>Recipe Schema</script>";

        $extractMarkup = new ExtractMarkup($markup);
        $actual = $extractMarkup->extract();

        $this->assertEquals($expected, $actual);
    }
}
