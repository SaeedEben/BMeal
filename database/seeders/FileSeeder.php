<?php

namespace Database\Seeders;

use App\Enum\File\StorageEnum;
use App\Enum\File\TypeEnum;
use App\Models\Country\Country;
use App\Models\File\File;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(StorageEnum::PUBLIC->value);

        foreach ($countryFlags as $countryCode => $filename) {
            $country = Country::query()->where('code', $countryCode)->firstOrFail();
            $storagePath = "flags/{$filename}";

            if (! $disk->exists($storagePath)) {
                throw new RuntimeException("Country flag [{$storagePath}] was not found on the public disk.");
            }

            $file = File::query()->updateOrCreate(
                [
                    'storage_path' => $storagePath,
                    'storage_provider' => StorageEnum::PUBLIC->value,
                ],
                [
                    'name' => $country->name,
                    'file_type' => TypeEnum::COUNTRY_FLAG,
                    'mime_type' => $disk->mimeType($storagePath),
                    'size' => $disk->size($storagePath),
                ],
            );

            $country->flag()->associate($file);
            $country->save();
        }
    }
}
