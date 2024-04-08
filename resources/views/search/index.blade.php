<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Search Recipes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        @include('shared.search')
        <div class="pb-5">
            Found {{ count($recipes) }} records for: <strong>{{ request('query') }}</strong>
        </div>
        <div class="grid lg:grid-cols-3 sm:grid-cols-2 gap-4">
            @foreach ($recipes as $recipe)
                @include('recipes.partials.recipe')
            @endforeach
        </div>
    </div>
</x-app-layout>
