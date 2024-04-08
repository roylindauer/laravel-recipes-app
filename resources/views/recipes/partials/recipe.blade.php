<!-- Recipe Card -->
<article id="recipe-1" class="bg-neutral-50 dark:bg-gray-700 mb-5 rounded overflow-hidden shadow-lg">
    <?php
       $image = $recipe->featured_image ?? 'img/recipe.jpg';
    ?>
    <div class="bg-cover bg-center h-64" style="background-image: url('<?= asset($image) ?>');">
        <img src="<?= asset($image) ?>" alt="<?= $recipe["name"] ?>" class="hidden">
    </div>
    <header class="flex justify-between items-center px-5 pt-5 pb-1 ">
        <h2 class="text-xl font-black dark:text-white">
            <a href="{{ route('recipes.show', $recipe['id']) }}"><?= $recipe["name"] ?></a>
        </h2>
    </header>
    <div class="px-5 pt-2 pb-2 dark:text-gray-300">
        <div class="pb-4"><?= $recipe["description"] ?></div>
        <x-recipe-tags :tags="$recipe->tags" />
    </div>
    <footer class="px-5 pt-2 pb-5 text-sm">
        <nav>
            <a href="{{ route('recipes.edit', $recipe['id']) }}"
               class="text-neutral-500 hover:text-neutral-600 dark:text-gray-200 dark:hover:text-gray-400">Edit Recipe</a>
        </nav>
        <div class="dark:text-gray-300">
            Created on <?= $recipe["created_at"] ?><br>
            Updated on <?= $recipe["updated_at"] ?>
        </div>


    </footer>
</article>
