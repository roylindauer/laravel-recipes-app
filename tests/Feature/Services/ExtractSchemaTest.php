<?php

namespace Tests\Feature\Services;

use App\Services\ExtractSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtractSchemaTest extends TestCase
{
    public function testExtractSchemaWithGraph()
    {
        $expected = [
            'name' => 'Best Damn Roasted Pork Tenderloin',
            'description' => 'Mouthwatering, buttery tender pork tenderloin seasoned to perfection and roasted in the oven for about 30 minutes.',
            'ingredients' => '<ul>
<li>2 pork tenderloins (3/4lb-1.25lbs each)</li>
<li>3 tbs brown sugar</li>
<li>1.5 tbs paprika</li>
<li>1.5 tsp salt</li>
<li>1.5 tsp ground mustard</li>
<li>1 tsp black pepper</li>
<li>1 tsp onion powder</li>
<li>1/2 tsp garlic powder</li>
<li>1-2 tbs olive oil</li>
</ul>',
            'instructions' => '<ol>
<li>Preheat oven to 425°(F).</li>
<li>Mix all dry ingredients in a small bowl.</li>
<li>Trim pork tenderloin of any excess fat or silverskin. Pat pork dry and coat with olive oil.</li>
<li>Rub seasoning mix all over pork tenderloin.</li>
<li>Place seasoned pork into a shallow baking dish and bake in oven 25-30 minutes (internal temp should be at least 145°)</li>
<li>Remove from oven and place pork tenderloin on a plate and let rest at least 5 minutes before slicing.</li>
</ol>'
        ];

        $jsonData = file_get_contents(base_path('tests/fixtures/recipe_graph.json'));
        $extractMarkup = new ExtractSchema($jsonData);
        $actual = $extractMarkup->extract();

        $this->assertEquals($expected, $actual);
    }

    public function testExtractSchema()
    {
        $expected = [
            'name' => 'Garlic-Braised Chicken',
            'tags' => 'chicken thigh, dry white wine, dutch oven, garlic, white pepper',
            'description' => '“It’s the only place where you can find a giant vat of peeled garlic, because it’s the only place that truly understands how much garlic you’ll need for the kind of food your people eat,” Michelle Zauner writes about the supermarket H Mart in her memoir, “Crying in H Mart.” Thankfully, many other grocery stores now sell containers of peeled garlic cloves. If you don’t already buy those, then this recipe is a great reason to start. Chicken thighs, white pepper, chardonnay and 20 garlic cloves are all you need for this zinger of a one-pot meal, which braises in an hour. In that time, chicken fat, wine and water turn into a luscious sauce packed with garlicky redolence. The white pepper, musky and full of earthiness, is a key taste here, so don’t skip it.',
            'ingredients' => '<ul>
<li>Olive oil</li>
<li>2 pounds bone-in, skin-on chicken thighs (about 4)</li>
<li>Salt</li>
<li>20 peeled garlic cloves</li>
<li>3/4 teaspoon ground white pepper</li>
<li>1 cup dry chardonnay</li>
<li>Steamed white rice, for serving</li>
</ul>',
            'instructions' => '<ol>
<li>Heat oven to 350 degrees.</li>
<li>In a large Dutch oven over medium-high, add enough oil to lightly coat the bottom. Season the chicken with salt on both sides, then add to the pot skin side down. Cook until the skin turns golden and crispy, 8 to 10 minutes. If the skin browns too quickly, lower the heat. Flip, and sear the other side briefly, about 1 minute. Transfer the chicken to a plate and set aside.</li>
<li>Add the garlic to the schmaltzy oil over medium-high, and stir until fragrant and very lightly golden at the edges, 1 to 2 minutes. Stir in the white pepper, then immediately add the wine and 1 cup water. Scrape up any stuck-on bits from the bottom of the pot while bringing the liquid to a simmer. Nestle the chicken in the pot skin side up, cover and cook in the oven until the chicken and garlic are meltingly tender, and the wine has reduced, about 1 hour. Taste for seasoning, adding more salt if needed. Serve with rice.</li>
</ol>'
        ];

        $jsonData = file_get_contents(base_path('tests/fixtures/recipe_flat.json'));
        $extractMarkup = new ExtractSchema($jsonData);
        $actual = $extractMarkup->extract();

        $this->assertEquals($expected, $actual);
    }

    public function testExtractArrayOfSchema()
    {
        $expected = [
            'name' => 'Sriracha Deviled Eggs',
            'description' => 'Sriracha, chile-garlic sauce, gives a kick to deviled eggs, a classic party favorite.',
            'ingredients' => '<ul>
<li>12 eggs</li>
<li>2 tablespoons mayonnaise, or as needed</li>
<li>2 tablespoons Sriracha hot chili sauce, or more to taste</li>
<li>0.125 teaspoon dry mustard</li>
<li>1 pinch cayenne pepper, or to taste</li>
<li>salt to taste</li>
<li>1 pinch smoked paprika, or to taste</li>
</ul>',
            'instructions' => '<ol>
<li>Place eggs in a saucepan; cover with water. Bring to a boil, remove from heat, and let eggs stand in hot water for 15 minutes. Remove eggs from hot water, cool under cold running water, and peel.</li>
<li>Cut eggs in half lengthwise; place egg yolks in a small bowl. Mash yolks with a fork; stir mayonnaise, Sriracha sauce, dry mustard, and cayenne pepper into yolks until smooth; season with salt. Spoon yolk mixture into a resealable plastic bag; snip off one corner of the bag.</li>
<li>Place egg whites cut-side up on a serving platter. Pipe yolk mixture into egg white halves; sprinkle with smoked paprika. Refrigerate deviled eggs, covered, until ready to serve.</li>
</ol>'
        ];

        $jsonData = file_get_contents(base_path('tests/fixtures/recipe_array_schema.json'));
        $extractMarkup = new ExtractSchema($jsonData);
        $actual = $extractMarkup->extract();

        $this->assertEquals($expected, $actual);
    }
}
