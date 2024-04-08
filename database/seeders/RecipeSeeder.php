<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\User;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Recipe::create([
            'name' => 'Spaghetti Carbonara',
            'description' => 'A simple pasta dish with eggs, cheese, bacon, and black pepper.',
            'ingredients' => 'Spaghetti, eggs, Pecorino Romano cheese, guanciale, black pepper',
            'instructions' => 'Cook the guanciale in a pan, cook the spaghetti, mix the eggs and cheese, combine everything',
            'image' => 'spaghetti-carbonara.jpg',
            'tags' => 'pasta, italian, bacon',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'user_id' => User::first()->id,
        ]);

        Recipe::create([
            'name' => 'Chicken Adobo',
            'description' => 'A Filipino dish made with chicken, soy sauce, vinegar, and garlic.',
            'ingredients' => 'Chicken, soy sauce, vinegar, garlic, bay leaves, peppercorns',
            'instructions' => 'Marinate the chicken, cook the chicken in a pan, simmer the chicken in the marinade',
            'image' => 'chicken-adobo.jpg',
            'tags' => 'chicken, filipino, vinegar',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'user_id' => User::first()->id,
        ]);

        Recipe::create([
            'name' => 'Beef Stroganoff',
            'description' => 'A Russian dish made with beef, mushrooms, onions, and sour cream.',
            'ingredients' => 'Beef, mushrooms, onions, sour cream, mustard, egg noodles',
            'instructions' => 'Cook the beef, cook the mushrooms and onions, add the sour cream and mustard, serve over egg noodles',
            'image' => 'beef-stroganoff.jpg',
            'tags' => 'beef, russian, sour cream',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'user_id' => User::first()->id,
        ]);

        Recipe::create([
            'name' => 'Chicken Parmesan',
            'description' => 'An Italian-American dish made with breaded chicken, marinara sauce, and mozzarella cheese.',
            'ingredients' => 'Chicken, breadcrumbs, Parmesan cheese, marinara sauce, mozzarella cheese',
            'instructions' => 'Bread and cook the chicken, top with marinara sauce and mozzarella cheese, bake until cheese is melted',
            'image' => 'chicken-parmesan.jpg',
            'tags' => 'chicken, italian, cheese',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'user_id' => User::first()->id,
        ]);
    }
}
