<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $recipe->name }}
            </h2>
            <?php if (Auth::user()->id === $recipe->user_id): ?>
            <nav>
                <ul>
                    <li><a href="{{ route('recipes.edit', $recipe['id']) }}" class="button">Edit</a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </x-slot>

    <?php if($recipe->featured_image) : ?>
    <div class="relative z-0 h-96 overflow-hidden bg-amber-500 " style="background-image: url('<?= asset($recipe->featured_image) ?>'); background-size: cover; background-position: center;">
    </div>
    <?php else: ?>
    <div class="relative z-0 h-96 overflow-hidden bg-sky-50 bg-gradient-to-r from-amber-500 to-orange-500 dark:from-purple-500 dark:to-sky-500" >
    </div>
    <?php endif; ?>

    <div class="relative w-full max-w-4xl mx-auto py-4 -mt-24 z-10">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-lg p-4">
            <div class="px-4 py-5 mb-4 dark:text-gray-300">
                {!! $recipe->description !!}

                <x-recipe-tags :tags="$recipe->tags" />
            </div>

            <div class="px-4 py-5 mb-4 dark:text-gray-300">
                <x-header-with-anchor id="ingredients" title="Ingredients" />
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400 mb-4">
                    The following ingredients are required to make this recipe.
                </p>
                {!! $recipe->ingredients !!}
            </div>

            <div class="px-4 py-5 mb-4 dark:text-gray-300">
                <x-header-with-anchor id="instructions" title="Instructions" />
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Step by step instructions to make this recipe.
                </p>
                {!! $recipe->instructions !!}
            </div>

            <div class="px-4 py-5 mb-4 dark:text-gray-500">Owned by: {{ $recipe->user->email }}</div>

        </div>
    </div>
</x-app-layout>
