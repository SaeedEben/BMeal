<?php

namespace Database\Seeders;

use App\Models\Recipe\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Chicken Breast', 'slug' => 'chicken-breast', 'description' => 'Boneless and skinless chicken breast, commonly used in salads, grilled dishes, and main courses.'],
            ['name' => 'Beef', 'slug' => 'beef', 'description' => 'Beef meat used in steaks, stews, burgers, and various international dishes.'],
            ['name' => 'Salmon', 'slug' => 'salmon', 'description' => 'Fresh salmon fillet commonly used in grilled, baked, and pan-seared dishes.'],
            ['name' => 'Egg', 'slug' => 'egg', 'description' => 'Chicken eggs used in breakfasts, baking, sauces, and many other recipes.'],
            ['name' => 'Rice', 'slug' => 'rice', 'description' => 'A staple grain used in dishes ranging from Asian fried rice to Mediterranean and Middle Eastern meals.'],
            ['name' => 'Pasta', 'slug' => 'pasta', 'description' => 'Wheat-based pasta commonly used in Italian and international dishes.'],
            ['name' => 'Tomato', 'slug' => 'tomato', 'description' => 'Fresh tomatoes used in salads, sauces, soups, and many Mediterranean dishes.'],
            ['name' => 'Onion', 'slug' => 'onion', 'description' => 'A versatile vegetable used as a base ingredient in soups, sauces, stews, and many savory dishes.'],
            ['name' => 'Garlic', 'slug' => 'garlic', 'description' => 'Aromatic bulb used to add flavor to sauces, marinades, soups, and savory dishes.'],
            ['name' => 'Potato', 'slug' => 'potato', 'description' => 'A starchy root vegetable used in fries, soups, stews, mashed potatoes, and roasted dishes.'],
            ['name' => 'Carrot', 'slug' => 'carrot', 'description' => 'A root vegetable used in salads, soups, stews, and roasted dishes.'],
            ['name' => 'Bell Pepper', 'slug' => 'bell-pepper', 'description' => 'A sweet pepper commonly used in salads, stir-fries, stews, and grilled dishes.'],
            ['name' => 'Spinach', 'slug' => 'spinach', 'description' => 'Leafy green vegetable used in salads, soups, pasta, omelets, and healthy meals.'],
            ['name' => 'Mozzarella', 'slug' => 'mozzarella-cheese', 'description' => 'Soft Italian cheese commonly used on pizza, pasta, salads, and baked dishes.'],
            ['name' => 'Butter', 'slug' => 'butter', 'description' => 'Dairy product used for cooking, baking, sauces, and spreading.'],
            ['name' => 'All-Purpose Flour', 'slug' => 'all-purpose-flour', 'description' => 'Versatile wheat flour used for baking, bread, sauces, pancakes, and doughs.'],
            ['name' => 'Milk', 'slug' => 'milk', 'description' => 'Dairy milk commonly used in beverages, desserts, sauces, soups, and baking.'],
            ['name' => 'Lemon', 'slug' => 'lemon', 'description' => 'Citrus fruit used for juice, zest, marinades, dressings, sauces, and desserts.'],
            ['name' => 'Basil', 'slug' => 'basil', 'description' => 'Aromatic herb commonly used in Italian, Mediterranean, and other cuisines.'],
            ['name' => 'Spaghetti', 'slug' => 'spaghetti', 'status' => 'active', 'description' => 'Long, thin Italian pasta traditionally used in dishes such as spaghetti carbonara.'],
            ['name' => 'Guanciale', 'slug' => 'guanciale', 'status' => 'active', 'description' => 'Cured pork cheek commonly used in traditional Italian pasta dishes, especially carbonara.'],
            ['name' => 'Egg Yolks', 'slug' => 'egg-yolks', 'status' => 'active', 'description' => 'The yellow portion of eggs, used to create rich and creamy sauces.'],
            ['name' => 'Whole Egg', 'slug' => 'whole-egg', 'status' => 'active', 'description' => 'A complete chicken egg containing both the yolk and egg white, used in cooking and baking.'],
            ['name' => 'Pecorino Romano Cheese', 'slug' => 'pecorino-romano-cheese', 'status' => 'active', 'description' => 'A hard, salty Italian sheep\'s milk cheese used in traditional Roman pasta dishes.'],
            ['name' => 'Black Pepper', 'slug' => 'black-pepper', 'status' => 'active', 'description' => 'A pungent spice made from dried peppercorns, used to season savory dishes.'],
            ['name' => 'Salt', 'slug' => 'salt', 'status' => 'active', 'description' => 'A mineral seasoning used to enhance flavor and season pasta water.'],
            ['name' => 'Pizza Dough', 'slug' => 'pizza-dough', 'status' => 'active', 'description' => 'A yeast-based dough made primarily from flour, water, yeast, and salt, used as a pizza base.'],
            ['name' => 'Crushed Tomatoes', 'slug' => 'crushed-tomatoes', 'status' => 'active', 'description' => 'Crushed ripe tomatoes used to prepare sauces for pizza, pasta, and baked dishes.'],
            ['name' => 'Fresh Mozzarella', 'slug' => 'fresh-mozzarella', 'status' => 'active', 'description' => 'A soft Italian cheese with a delicate flavor and a moist texture, commonly used on pizza.'],
            ['name' => 'Fresh Basil', 'slug' => 'fresh-basil', 'status' => 'active', 'description' => 'An aromatic herb widely used in Italian cuisine, especially in pizza and pesto.'],
            ['name' => 'Extra-Virgin Olive Oil', 'slug' => 'extra-virgin-olive-oil', 'status' => 'active', 'description' => 'High-quality olive oil used for dressings, sauces, sautéing, and finishing Italian dishes.'],
            ['name' => 'Lasagna Sheets', 'slug' => 'lasagna-sheets', 'status' => 'active', 'description' => 'Flat pasta sheets layered with sauces and fillings to prepare baked lasagna.'],
            ['name' => 'Ground Beef', 'slug' => 'ground-beef', 'status' => 'active', 'description' => 'Minced beef used in meat sauces, ragù, and other savory dishes.'],
            ['name' => 'Celery Stalk', 'slug' => 'celery-stalk', 'status' => 'active', 'description' => 'The crunchy stalk of celery, commonly diced with onion and carrot to create an Italian soffritto.'],
            ['name' => 'Parmesan Cheese', 'slug' => 'parmesan-cheese', 'status' => 'active', 'description' => 'A hard Italian cheese with a savory, nutty flavor, commonly grated over pasta, risotto, and lasagna.'],
            ['name' => 'Olive Oil', 'slug' => 'olive-oil', 'status' => 'active', 'description' => 'Oil extracted from olives, used for cooking, sautéing, and preparing sauces.'],
            ['name' => 'Arborio Rice', 'slug' => 'arborio-rice', 'status' => 'active', 'description' => 'A short-grain Italian rice rich in starch, ideal for making creamy risotto.'],
            ['name' => 'Mushrooms', 'slug' => 'mushrooms', 'status' => 'active', 'description' => 'Edible fungi used to add earthy flavor and texture to risotto, pasta, and other dishes.'],
            ['name' => 'Vegetable Stock', 'slug' => 'vegetable-stock', 'status' => 'active', 'description' => 'A savory liquid prepared by simmering vegetables and aromatics, used as a cooking base for risotto and soups.'],
            ['name' => 'Dry White Wine', 'slug' => 'dry-white-wine', 'status' => 'active', 'description' => 'A dry white wine used to deglaze pans and add acidity and aroma to sauces and risotto.'],
            ['name' => 'Pine Nuts', 'slug' => 'pine-nuts', 'status' => 'active', 'description' => 'Small edible seeds with a buttery, nutty flavor, traditionally used in basil pesto.'],
            ["name" => "Rustic Bread", "slug"=> "rustic-bread", "description"=> "Thick slices of rustic Italian bread, toasted or grilled until crisp, forming the base of traditional Bruschetta al Pomodoro."],
            ["name" => "Ripe Tomatoes", "slug"=> "ripe-tomatoes", "description"=> "Fresh, ripe tomatoes diced into small pieces to create the juicy and flavorful topping for Bruschetta al Pomodoro."],
            ["name" => "Breadcrumbs", "slug"=> "breadcrumbs", "description"=> "Dry breadcrumbs used to coat the chicken cutlets, producing a golden, crispy crust when shallow-fried."],
            ["name" => "Tomato Passata", "slug"=> "tomato-passata", "description"=> "Smooth strained tomatoes used as the rich, flavorful sauce spread over the fried chicken before baking."],
            ["name" => "Dried Oregano", "slug"=> "dried-oregano", "description"=> "An aromatic Mediterranean herb that adds a distinctive earthy flavor to the tomato sauce and baked chicken."],
            ["name" => "Ladyfinger Biscuits", "slug" => "ladyfinger-biscuits", "description" => "Light, crisp Italian Savoiardi biscuits that are briefly dipped in cooled espresso and layered with mascarpone cream to form the base of traditional tiramisu."],
            ["name" => "Mascarpone Cheese", "slug" => "mascarpone-cheese", "description" => "Rich, creamy Italian soft cheese with a mild flavor, providing the smooth and velvety texture of the tiramisu filling."],
            ["name" => "Sugar", "slug" => "sugar", "description" => "Granulated sugar used to sweeten the egg yolks and balance the rich flavor of the mascarpone cheese and bitter espresso."],
            ["name" => "Espresso Coffee", "slug" => "espresso-coffee", "description" => "Strong brewed espresso, cooled before use, that gives the ladyfinger biscuits their distinctive coffee flavor and signature Italian tiramisu character."],
            ["name" => "Unsweetened Cocoa Powder", "slug" => "unsweetened-cocoa-powder", "description" => "Fine, unsweetened cocoa powder dusted over the finished tiramisu to add a rich chocolate aroma and a pleasantly bitter contrast to the sweet mascarpone cream."],
            ["name" => "Fettuccine", "slug" => "fettuccine", "description" => "Flat, ribbon-shaped Italian pasta that provides the base of Fettuccine Alfredo and holds the creamy butter and Parmesan sauce."],
            ["name" => "Pasta Cooking Water", "slug" => "pasta-cooking-water", "description" => "Starchy water reserved from boiling the fettuccine, used to help emulsify the butter and Parmesan into a smooth sauce that coats the pasta."],
            ["name" => "Beef Chuck", "slug" => "beef-chuck", "description" => "Beef chuck cut into large cubes, used as the main protein for the slow-braised Burgundy-style stew."],
            ["name" => "Bacon", "slug" => "bacon", "description" => "Diced bacon that adds smoky flavor and rendered fat for browning the beef."],
            ["name" => "Carrots", "slug" => "carrots", "description" => "Sliced carrots that add sweetness and flavor to the slow-cooked beef stew."],
            ["name" => "Red Wine", "slug" => "red-wine", "description" => "Dry Burgundy-style red wine used to braise the beef and create a rich, flavorful sauce."],
            ["name" => "Beef Stock", "slug" => "beef-stock", "description" => "Savory beef stock that adds depth and moisture to the braising liquid."],
            ["name" => "Tomato Paste", "slug" => "tomato-paste", "description" => "Concentrated tomato paste that adds richness, color, and savory depth to the sauce."],
            ["name" => "Pearl Onions", "slug" => "pearl-onions", "description" => "Small peeled onions that add sweetness and traditional flavor to the finished stew."],
            ["name" => "Thyme", "slug" => "thyme", "description" => "Fresh thyme sprigs that infuse the stew with an earthy, herbal aroma."],
            ["name" => "Bay Leaf", "slug" => "bay-leaf", "description" => "An aromatic bay leaf that adds subtle herbal depth during slow braising."],
            ["name" => "Shortcrust Pastry", "slug" => "shortcrust-pastry", "description" => "Shortcrust pastry used to form the crisp tart shell that holds the creamy savory filling."],
            ["name" => "Bacon Lardons", "slug" => "bacon-lardons", "description" => "Small pieces of bacon that add smoky, savory flavor to the quiche filling."],
            ["name" => "Heavy Cream", "slug" => "heavy-cream", "description" => "Rich cream that creates the smooth, velvety custard filling characteristic of Quiche Lorraine."],
            ["name" => "Gruyère Cheese", "slug" => "gruyere-cheese", "description" => "Grated Gruyère cheese that adds nutty, savory flavor and richness to the quiche."],
            ["name" => "Nutmeg", "slug" => "nutmeg", "description" => "A pinch of nutmeg that adds warm, aromatic flavor to the creamy egg filling."],
            ["name" => "Eggplant", "slug" => "eggplant", "description" => "Diced eggplant that provides a tender texture and absorbs the rich flavors of the Provençal vegetable stew."],
            ["name" => "Zucchini", "slug" => "zucchini", "description" => "Diced zucchini that adds a mild, fresh flavor and tender texture to the vegetable stew."],
            ["name" => "Red Bell Pepper", "slug" => "red-bell-pepper", "description" => "Diced red bell pepper that contributes sweetness, color, and a distinctive vegetable flavor."],
            ["name" => "Yellow Bell Pepper", "slug" => "yellow-bell-pepper", "description" => "Diced yellow bell pepper that adds sweetness, color, and freshness to the ratatouille."],
            ["name" => "Dried Thyme", "slug" => "dried-thyme", "description" => "Dried thyme that adds earthy, herbal notes characteristic of Provençal cooking."],
            ["name" => "Cooked Shredded Chicken", "slug" => "cooked-shredded-chicken", "description" => "Cooked chicken shredded into small pieces, used as the main filling for enchiladas."],
            ["name" => "Corn Tortillas", "slug" => "corn-tortillas", "description" => "Traditional corn tortillas used to wrap fillings for enchiladas, tacos, and other Mexican dishes."],
            ["name" => "Enchilada Sauce", "slug" => "enchilada-sauce", "description" => "A red chili-tomato sauce that coats enchiladas and adds savory flavor, color, and mild to moderate heat."],
            ["name" => "Monterey Jack Cheese", "slug" => "monterey-jack-cheese", "description" => "A mild, creamy cheese that melts smoothly over enchiladas and adds a rich, savory finish."],
            ["name" => "Ground Cumin", "slug" => "ground-cumin", "description" => "A warm, earthy spice commonly used to season Mexican dishes, meats, sauces, and fillings."],
            ["name" => "Fresh Cilantro", "slug" => "fresh-cilantro", "description" => "Fresh cilantro leaves that provide a bright, citrusy herbal flavor and a fresh garnish."],
            ["name" => "Chicken Thighs", "slug" => "chicken-thighs", "description" => "Tender, flavorful chicken thigh meat used as the main protein in tacos and other savory dishes."],
            ["name" => "Chili Powder", "slug" => "chili-powder", "description" => "A ground chili-based seasoning that adds warmth, color, and spicy flavor to Mexican-inspired dishes."],
            ["name" => "Paprika", "slug" => "paprika", "description" => "A ground pepper spice that contributes red color and a mild, sweet, or smoky flavor depending on the variety."],
            ["name" => "White Onion", "slug" => "white-onion", "description" => "A crisp onion with a sharp, clean flavor, commonly used fresh or cooked in Mexican dishes."],
            ["name" => "Lime", "slug" => "lime", "description" => "A small citrus fruit with tart juice that adds freshness and acidity to tacos, marinades, and Mexican dishes."],
            ["name" => "Ripe Avocados", "slug" => "ripe-avocados", "description" => "Ripe avocados with creamy flesh that forms the rich, smooth, and slightly chunky base of guacamole."],
            ["name" => "Lime Juice", "slug" => "lime-juice", "description" => "Freshly squeezed lime juice that adds acidity, bright citrus flavor, and helps slow avocado browning."],
            ["name" => "Jalapeño", "slug" => "jalapeno", "description" => "A fresh green chili pepper that adds a grassy flavor and adjustable heat to salsas, dips, and guacamole."],
            ["name" => "Ground Lamb or Beef", "slug" => "ground-lamb-or-beef", "description" => "Ground lamb or beef used to prepare the savory, spiced meat sauce for layered casseroles and Mediterranean dishes."],
            ["name" => "Ground Cinnamon", "slug" => "ground-cinnamon", "description" => "A warm, aromatic spice that adds distinctive sweetness and depth to Greek meat sauces and Mediterranean dishes."],
            ["name" => "Parmesan or Kefalotyri", "slug" => "parmesan-or-kefalotyri", "description" => "A hard, flavorful cheese grated into béchamel or sprinkled over casseroles for a savory finish."],
            ["name" => "Cucumber", "slug" => "cucumber", "description" => "A crisp, refreshing vegetable that adds moisture and a mild flavor to fresh salads."],
            ["name" => "Red Onion", "slug" => "red-onion", "description" => "A red-skinned onion with a mildly sharp flavor that adds crunch, color, and freshness to salads."],
            ["name" => "Green Bell Pepper", "slug" => "green-bell-pepper", "description" => "A crisp green pepper that contributes a fresh, slightly bitter flavor and crunchy texture to salads."],
            ["name" => "Kalamata Olives", "slug" => "kalamata-olives", "description" => "Dark Greek olives with a rich, briny flavor that adds a distinctive Mediterranean taste to salads and appetizers."],
            ["name" => "Feta Cheese", "slug" => "feta-cheese", "description" => "A tangy, salty Greek cheese traditionally made from sheep's milk or a sheep-and-goat milk blend, served crumbled or in a slab."],
            ["name" => "Red Wine Vinegar", "slug" => "red-wine-vinegar", "description" => "A tangy vinegar made from red wine that adds acidity and brightness to dressings and marinades."],
            ["name" => "Pork or Chicken", "slug" => "pork-or-chicken", "description" => "Pork or chicken cut into cubes and marinated with lemon, olive oil, garlic, and herbs before grilling on skewers."],
            ["name" => "Lemon Juice", "slug" => "lemon-juice", "description" => "Fresh lemon juice that adds citrus acidity and freshness to marinades, dressings, and savory dishes."],
            ["name" => "Pita Bread", "slug" => "pita-bread", "description" => "A soft, round flatbread commonly served with Greek grilled meats, wraps, dips, and Mediterranean meals."],
            ["name" => "Tzatziki", "slug" => "tzatziki", "description" => "A creamy Greek dip made primarily from yogurt, cucumber, garlic, and herbs, commonly served with grilled meats and pita."],
            ["name" => "Filo Pastry", "slug" => "filo-pastry", "description" => "Thin sheets of pastry layered with oil or butter to create a delicate, crisp, flaky crust for Greek pies and pastries."],
            ["name" => "Spring Onions", "slug" => "spring-onions", "description" => "Mild onions with fresh green tops that add a delicate onion flavor and freshness to savory fillings."],
            ["name" => "Fresh Dill", "slug" => "fresh-dill", "description" => "A fragrant herb with a fresh, slightly anise-like flavor that complements spinach, feta, and Mediterranean dishes."],

        ];



        foreach ($ingredients as $ingredient) {
            Ingredient::query()->firstOrCreate(
                ['slug' => $ingredient['slug']],
                [
                    'name' => $ingredient['name'],
                    'description' => $ingredient['description'],
                    'status' => $ingredient['status'] ?? 'active',
                ],
            );
        }
    }
}
