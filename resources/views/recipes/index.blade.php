<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Recipes') }}
            </h2>
            <nav>
                <ul>
                    <li><a href="{{ route('recipes.create') }}" class="button">Add</a></li>
                </ul>
            </nav>
        </div>
    </x-slot>

    <div class="container mx-auto p-4">
        @include('shared.search')
        <div class="grid lg:grid-cols-3 sm:grid-cols-2 gap-4">
            @foreach ($recipes as $recipe)
                @include('recipes.partials.recipe')
            @endforeach
        </div>
    </div>
</x-app-layout>
