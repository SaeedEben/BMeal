<?php

namespace Database\Seeders;

use App\Models\Country\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'England', 'code' => 'GB', 'slug' => 'england', 'description' => 'English cuisine features hearty, comforting dishes made with seasonal ingredients, roasted meats, potatoes, and traditional sauces. From fish and chips to shepherds pie and Sunday roasts, its meals reflect a rich culinary heritage.'],
            ['name' => 'Mexico', 'code' => 'MX', 'slug' => 'mexico' , 'description' => 'Mexican cuisine combines bold spices, fresh chilies, corn, beans, tomatoes, and avocados. Traditional dishes such as tacos and enchiladas offer vibrant flavors and rich cultural heritage.'],
            ['name' => 'Spain', 'code' => 'ES', 'slug' => 'spain' , 'description' => 'Spanish cuisine celebrates fresh seafood, olive oil, tomatoes, rice, and aromatic spices. From paella and tapas to traditional stews, its dishes showcase vibrant Mediterranean flavors and regional diversity.'],
            ['name' => 'Italy', 'code' => 'IT', 'slug' => 'italy' , 'description' => 'Italian cuisine celebrates fresh ingredients, olive oil, herbs, pasta, and regional cheeses. From classic pizzas to creamy risottos, its dishes emphasize simplicity and authentic Mediterranean flavors.'],
            ['name' => 'Germany', 'code' => 'DE', 'slug' => 'germany' , 'description' => 'German cuisine is known for its hearty meals, sausages, roasted meats, potatoes, and flavorful breads. Traditional dishes such as schnitzel, bratwurst, and sauerkraut combine comforting flavors with regional cooking traditions.'],
            ['name' => 'France', 'code' => 'FR', 'slug' => 'france' , 'description' => 'French cuisine blends regional ingredients with traditional cooking techniques and refined flavors. From hearty stews and savory tarts to delicate pastries, its dishes showcase culinary craftsmanship and seasonal produce.'],
            ['name' => 'Greece', 'code' => 'GR', 'slug' => 'greece' , 'description' => 'Greek cuisine features fresh vegetables, olive oil, feta cheese, seafood, and grilled meats. Lemon, oregano, and aromatic herbs create light, flavorful dishes inspired by Mediterranean traditions.'],
        ];

        foreach ($countries as $country) {
            Country::query()->firstOrCreate(
                ['code' => $country['code']],
                [...$country, 'status' => 'active'],
            );
        }
    }
}
