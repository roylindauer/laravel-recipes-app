<?php

namespace App\Jobs;

use App\Models\Recipe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        logger('Processing recipe import for ' . $this->recipe->name);
        /*
        Now the fun stuff.

        Scrape the URL and save the recipe to the database.
        Featured image should come from open graph tags if available
        Grab the title from the title tag
        Description should come from meta description or og tag if available
        Scrape Ingredients and Instructions
        Tags could/should be generated from the content
        */

    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->recipe->id;
    }
}
