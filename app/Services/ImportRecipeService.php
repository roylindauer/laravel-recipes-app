<?php

namespace App\Services;

use App\Models\Recipe;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Masterminds\HTML5;
use QueryPath\DOMQuery;
use QueryPath\QueryPath;

class ImportRecipeService
{
    public DOMQuery $qp;
    public Recipe $recipe;
    public Client $client;

    public function __construct(
        public $recipe_id,
    )
    {
        //
    }

    /**
     * @param Client $client
     * @return void
     * @throws GuzzleException
     * @throws \Exception
     *
     */
    public function import(\GuzzleHttp\Client $client): void
    {
        try {
            $this->recipe = Recipe::find($this->recipe_id);
            Log::info($this->logMessage('Processing Recipe URL: ' . $this->recipe->import_url));

            Log::info($this->logMessage('Fetching URL: ' . $this->recipe->import_url));
            $this->client = $client;
            $response = $this->client->get($this->recipe->import_url);
            $html = $response->getBody()->getContents();

            Log::info($this->logMessage('Parsing HTML'));
            $html5 = new HTML5();
            $this->qp = QueryPath::with($html5->loadHTML($html));

            Log::info($this->logMessage('Extracting Recipe Data'));
            $recipe_data = [];
            $recipe_data["name"] = $this->qp->find('meta[@property="og:title"]')->attr('content');
            $recipe_data["description"] = $this->qp->find('meta[@property="og:description"]')->attr('content');
            $recipe_data["featured_image"] = $this->qp->find('meta[@property="og:image"]')->attr('content');

            $extractor = $this->extractor();
            $recipe_data = array_merge($recipe_data, $extractor->extract());

            $this->updateRecipe($recipe_data);

            Log::info($this->logMessage("Imported Successfully"));
        } catch (\Exception $e) {
            Log::error($this->logMessage('Error Importing Recipe: ' . $e->getMessage()));
        } catch (GuzzleException $e) {
            Log::error($this->logMessage('Error Processing Recipe URL: ' . $e->getMessage()));
        }

    }

    /**
     * @param $message
     * @return string
     */
    private function logMessage($message): string
    {
        return "[Recipe #" . $this->recipe->id . "] " . $message;
    }

    /**
     * @return ExtractInterface
     */
    private function extractor(): ExtractInterface
    {
        try {

            if (($markup = $this->qp->find('script[type="application/ld+json"]'))) {
                Log::info($this->logMessage('Extracting Recipe Data From Schema'));
                return new ExtractSchema($markup->text());
            }

            Log::info($this->logMessage('Extracting Recipe Data From Markup'));
            return new ExtractMarkup($this->qp->html());
        } catch (\Exception $e) {
            Log::error($this->logMessage('Error Extracting Recipe Data: ' . $e->getMessage()));
            return new ExtractMarkup('');
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws GuzzleException
     */
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

        $this->recipe->featured_image = $this->processFeaturedImage($data);

        $this->recipe->imported_at = now();
        return $this->recipe->save();
    }

    private function processFeaturedImage($data): string|bool
    {
        if (isset($data["featured_image"])) {
            $image_response = $this->client->get($data["featured_image"]);
            $image = $image_response->getBody()->getContents();
            $image_path = 'recipe_images/' . $this->recipe->id . '/featured.jpg';
            Storage::disk('local')->put($image_path, $image);
            return $image_path;
        }

        return false;
    }
}
