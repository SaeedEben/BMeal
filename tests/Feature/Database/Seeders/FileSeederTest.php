<?php

namespace Tests\Feature\Database\Seeders;

use App\Enum\File\TypeEnum;
use App\Models\Country\Country;
use Database\Seeders\CountrySeeder;
use Database\Seeders\FileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_country_flag_files_and_links_them_idempotently(): void
    {
        $countryFlags = [
            'GB' => 'England.png',
            'US' => 'USA.png',
            'MX' => 'Mexico.jpg',
            'ES' => 'Spain.png',
            'IT' => 'Italy.png',
            'DE' => 'Germany.png',
            'FR' => 'France.png',
            'GR' => 'Greece.png',
        ];

        Storage::fake('public');

        foreach ($countryFlags as $filename) {
            Storage::disk('public')->put("flags/{$filename}", 'flag-image');
        }

        $this->seed(CountrySeeder::class);
        $this->seed(FileSeeder::class);

        foreach ($countryFlags as $countryCode => $filename) {
            $country = Country::query()->where('code', $countryCode)->firstOrFail();

            $this->assertNotNull($country->flag_id);
            $this->assertSame($country->name, $country->flag->name);
            $this->assertSame("flags/{$filename}", $country->flag->storage_path);
            $this->assertSame(TypeEnum::COUNTRY_FLAG, $country->flag->file_type);
        }

        $this->seed(FileSeeder::class);

        $this->assertDatabaseCount('files', count($countryFlags));
    }
}
