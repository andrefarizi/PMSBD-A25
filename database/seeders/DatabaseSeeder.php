<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => 'admin',
                'role'     => 'admin',
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name'     => 'Kasir',
                'password' => 'kasir',
                'role'     => 'kasir',
            ]
        );

        $customers = [
            ['nama' => 'Budi Santoso',    'telepon' => '081298765432', 'alamat' => 'Jl. Sudirman No. 45, Jakarta'],
            ['nama' => 'Ahmad Fauzi',     'telepon' => '081234567890', 'alamat' => 'Jl. Merdeka No. 12, Bandung'],
            ['nama' => 'Siti Rahmawati',  'telepon' => '081311223344', 'alamat' => 'Jl. Melati No. 8, Surabaya'],
            ['nama' => 'Rizky Pratama',   'telepon' => '081122334455', 'alamat' => 'Jl. Diponegoro No. 25, Yogyakarta'],
            ['nama' => 'Putri Wulandari', 'telepon' => '081987654321', 'alamat' => 'Jl. Gajah Mada No. 17, Semarang'],
            ['nama' => 'Dimas Saputra',   'telepon' => '082155667788', 'alamat' => 'Jl. Veteran No. 3, Malang'],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = Customer::firstOrCreate(['nama' => $c['nama']], $c);
        }

        $products = [
            ['nama' => 'Laptop ASUS ROG Zephyrus G14',  'kategori' => 'Elektronik', 'harga' => 24500000.00, 'stok' => 15],
            ['nama' => 'Keyboard Mechanical RGB Wireless', 'kategori' => 'Aksesoris',  'harga' => 850000.00,   'stok' => 40],
            ['nama' => 'Mouse Gaming Ergonomis 16000 DPI', 'kategori' => 'Aksesoris',  'harga' => 450000.00,   'stok' => 50],
            ['nama' => 'Monitor Gaming 27 Inch 165Hz IPS', 'kategori' => 'Elektronik', 'harga' => 2800000.00,  'stok' => 20],
            ['nama' => 'TWS Earbuds ANC Noise Cancelling', 'kategori' => 'Audio',       'harga' => 750000.00,   'stok' => 35],
        ];

        $productModels = [];
        foreach ($products as $p) {
            $productModels[] = Product::firstOrCreate(['nama' => $p['nama']], $p);
        }

        if (Transaction::count() === 0) {
            $t1 = Transaction::create([
                'customer_id' => $customerModels[1]->id,
                'user_id'     => $kasir->id,
                'tanggal'     => now(),
                'total_harga' => 24950000.00,
                'status'      => 'selesai',
            ]);
            TransactionDetail::create([
                'transaction_id' => $t1->id,
                'product_id'     => $productModels[0]->id,
                'harga'          => 24500000.00,
                'qty'            => 1,
                'subtotal'       => 24500000.00,
            ]);
            TransactionDetail::create([
                'transaction_id' => $t1->id,
                'product_id'     => $productModels[2]->id,
                'harga'          => 450000.00,
                'qty'            => 1,
                'subtotal'       => 450000.00,
            ]);

            $t2 = Transaction::create([
                'customer_id' => $customerModels[2]->id,
                'user_id'     => $kasir->id,
                'tanggal'     => now(),
                'total_harga' => 1600000.00,
                'status'      => 'selesai',
            ]);
            TransactionDetail::create([
                'transaction_id' => $t2->id,
                'product_id'     => $productModels[1]->id,
                'harga'          => 850000.00,
                'qty'            => 1,
                'subtotal'       => 850000.00,
            ]);
            TransactionDetail::create([
                'transaction_id' => $t2->id,
                'product_id'     => $productModels[4]->id,
                'harga'          => 750000.00,
                'qty'            => 1,
                'subtotal'       => 750000.00,
            ]);

            $t3 = Transaction::create([
                'customer_id' => $customerModels[0]->id,
                'user_id'     => $admin->id,
                'tanggal'     => now(),
                'total_harga' => 850000.00,
                'status'      => 'selesai',
            ]);
            TransactionDetail::create([
                'transaction_id' => $t3->id,
                'product_id'     => $productModels[1]->id,
                'harga'          => 850000.00,
                'qty'            => 1,
                'subtotal'       => 850000.00,
            ]);
        }
    }
}
