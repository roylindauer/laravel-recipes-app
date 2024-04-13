<?php

namespace App\Jobs;

use App\Models\Recipe;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use QueryPath\QueryPath;

class ProcessRecipeImport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Recipe $recipe
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('Processing recipe #' . $this->recipe->id);

        $client = new Client();
        $response = $client->get($this->recipe->import_url);
        $html = $response->getBody()->getContents();

        $html5 = new \Masterminds\HTML5();
        $dom = $html5->loadHTML($html);
        $qp = QueryPath::with($dom);

        // $name = $qp->find('meta[@property="og:title"]')->attr('content');
        // $description = $qp->find('meta[@property="og:description"]')->attr('content');

        if ($qp->find('script[type="application/ld+json"]')) {
            $recipe_schema = json_decode($qp->find('script[type="application/ld+json"]')->text());
            if (is_array($recipe_schema)) {
                $recipe_schema = $recipe_schema[0];
            }

            $this->recipe->name = $recipe_schema->name;
            $this->recipe->active = true;
            $this->recipe->description = $recipe_schema->description;
            $this->recipe->ingredients = $this->parseIngredients($recipe_schema->recipeIngredient);
            $this->recipe->instructions = $this->parseInstructions($recipe_schema->recipeInstructions);

            if (isset($recipe_schema->keywords)) {
                $this->recipe->tags = $recipe_schema->keywords;
            }

            $featured_image = $qp->find('meta[@property="og:image"]')->attr('content');
            if ($featured_image) {
                $image_response = $client->get($featured_image);
                $image = $image_response->getBody()->getContents();
                $image_path = 'recipe_images/' . $this->recipe->id . '/featured.jpg';
                Storage::disk('local')->put($image_path, $image);
                $this->recipe->featured_image = $image_path;
            }

            $this->recipe->imported_at = now();
            $this->recipe->save();
        }

        logger('Processed recipe #' . $this->recipe->id);
    }

    private function parseIngredients($ingredients): string
    {
        $parsed = ["<ul>"];
        foreach ($ingredients as $ingredient) {
            $parsed[] = "<li>" . $ingredient . "</li>";
        }
        $parsed[] = "</ul>";
        return implode("\n", $parsed);
    }

    private function parseInstructions($instructions): string
    {
        $parsed = ["<ol>"];
        foreach ($instructions as $instruction) {
            $parsed[] = "<li>" . $instruction->text . "</li>";
        }
        $parsed[] = "</ol>";
        return implode("\n", $parsed);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->recipe->id;
    }
}
