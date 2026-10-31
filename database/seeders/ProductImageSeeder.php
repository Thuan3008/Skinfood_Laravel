<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = json_decode(
            file_get_contents(database_path('seeders/data/product-images.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($images): void {
            foreach ($images as $image) {
                if (!is_file(public_path('legacy/Content/images/' . $image['image']))) {
                    throw new RuntimeException('Missing product image: ' . $image['image']);
                }

                // Do not overwrite unrelated products if a demo ID was reused.
                DB::table('SanPham')
                    ->where('MaSP', $image['id'])
                    ->where('TenSP', $image['name'])
                    ->update(['HinhAnh' => $image['image']]);
            }
        });
    }
}
