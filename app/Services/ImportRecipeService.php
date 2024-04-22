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

            $recipe_data = [];
            $recipe_data["name"] = $this->qp->find('meta[@property="og:title"]')->attr('content');
            $recipe_data["description"] = $this->qp->find('meta[@property="og:description"]')->attr('content');
            $recipe_data["featured_image"] = $this->qp->find('meta[@property="og:image"]')->attr('content');

            $extractor = $this->extractor();
            $recipe_data = array_merge($recipe_data, $extractor->extract());

            $this->updateRecipe($recipe_data);

            logger('Recipe #' . $this->recipe->id . " imported successfully");
        } catch (\Exception $e) {
            logger('Error processing recipe #' . $this->recipe->id . ': ' . $e->getMessage());
        } catch (GuzzleException $e) {
            logger('Error processing recipe URL #' . $this->recipe->id . ': ' . $e->getMessage());
        }

    }

    private function extractor(): ExtractInterface
    {
        try {

            if (($markup = $this->qp->find('script[type="application/ld+json"]'))) {
                return new ExtractSchema($markup->text());
            }

            return new ExtractMarkup($this->qp->html());
        } catch (\Exception $e) {
            logger('Error extracting recipe data: ' . $e->getMessage());
            return new ExtractMarkup('');
        }
    }

    private function updateRecipe($data): bool
    {
        $this->recipe->name = $data["name"];
        $this->recipe->active = true;
        $this->recipe->description = $data["description"];
        $this->recipe->ingredients = $data["ingredients"];
        $this->recipe->instructions = $data["instructions"];

        if (isset($data["keywords"])) {
            $this->recipe->tags = $data["keywords"];
        }

        if (isset($data["featured_image"])) {
            $image_response = $this->client->get($data["featured_image"]);
            $image = $image_response->getBody()->getContents();
            $image_path = 'recipe_images/' . $this->recipe->id . '/featured.jpg';
            Storage::disk('local')->put($image_path, $image);
            $this->recipe->featured_image = $image_path;
        }

        $this->recipe->imported_at = now();
        return $this->recipe->save();
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
