<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@demo.test'],
            ['name' => 'Admin Toko', 'password' => Hash::make('password123'), 'is_admin' => true]
        );
        User::firstOrCreate(
            ['email' => 'budi@demo.test'],
            ['name' => 'Budi Pembeli', 'password' => Hash::make('password123'), 'is_admin' => false]
        );

        $kategori = collect(['Elektronik', 'Fashion', 'Makanan & Minuman', 'Rumah Tangga'])
            ->mapWithKeys(fn($n) => [$n => Category::firstOrCreate(
                ['slug' => Str::slug($n)], ['name' => $n]
            )]);

        $produk = [
            ['Headphone Bluetooth Pro', 'Elektronik', 299000, 25, 'Headphone nirkabel dengan noise cancelling aktif, baterai 30 jam, dan koneksi multipoint.'],
            ['Smartwatch Fit S2', 'Elektronik', 450000, 15, 'Jam tangan pintar dengan monitor detak jantung, GPS, dan tahan air 5 ATM.'],
            ['Speaker Mini Portabel', 'Elektronik', 185000, 30, 'Speaker bluetooth saku dengan bass mantap dan daya tahan 12 jam.'],
            ['Kemeja Flanel Premium', 'Fashion', 155000, 40, 'Kemeja flanel katun lembut, nyaman dipakai harian, tersedia berbagai ukuran.'],
            ['Sepatu Sneakers Urban', 'Fashion', 320000, 20, 'Sneakers ringan dengan sol empuk untuk aktivitas sehari-hari.'],
            ['Tas Ransel Daypack 20L', 'Fashion', 210000, 18, 'Ransel ringkas dengan kompartemen laptop 14 inci dan bahan anti air.'],
            ['Kopi Arabika Gayo 250g', 'Makanan & Minuman', 75000, 50, 'Biji kopi arabika single origin Gayo dengan notes cokelat dan karamel.'],
            ['Madu Hutan Murni 500ml', 'Makanan & Minuman', 98000, 35, 'Madu hutan asli tanpa campuran, dipanen dari lebah liar.'],
            ['Granola Mix 400g', 'Makanan & Minuman', 65000, 45, 'Campuran oat panggang, kacang, dan buah kering tanpa pemanis buatan.'],
            ['Blender Multifungsi 1.5L', 'Rumah Tangga', 275000, 12, 'Blender kaca 1.5 liter dengan 3 kecepatan dan mata pisau stainless.'],
            ['Set Wadah Penyimpanan 10 pcs', 'Rumah Tangga', 120000, 28, 'Wadah makanan kedap udara bebas BPA, aman untuk microwave.'],
            ['Lampu Meja Minimalis LED', 'Rumah Tangga', 89000, 22, 'Lampu belajar LED 3 mode cahaya dengan lengan fleksibel.'],
        ];

        foreach ($produk as $i => [$nama, $kat, $harga, $stok, $desc]) {
            Product::firstOrCreate(
                ['slug' => Str::slug($nama)],
                [
                    'name' => $nama,
                    'category_id' => $kategori[$kat]->id,
                    'price' => $harga,
                    'stock' => $stok,
                    'description' => $desc,
                    'image' => 'https://picsum.photos/seed/toko' . ($i + 1) . '/640/480',
                ]
            );
        }
    }
}
