<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class InitialSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Branch Manager']);
        $cashierRole = Role::firstOrCreate(['name' => 'Cashier']);

        $outletJkt = Outlet::create([
            'code' => 'OUT-JKT-01',
            'name' => 'Inventa POS Jakarta Pusat (HQ)',
            'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            'phone' => '021-5550192',
            'is_active' =>  true,
        ]);

        $outletSby = Outlet::create([
            'code' => 'OUT-SBY-01',
            'name' => 'Inventa POS Surabaya Gubeng',
            'address' => 'Jl. Pemuda No. 12, Surabaya',
            'phone' => '031-5550881',
            'is_active' => true,
        ]);

        $superAdmin = User::create([
            'name' => 'Hendro Wijaya (Super Admin)',
            'email' => 'admin@inventapos.id',
            'phone' => '081122334455',
            'password' => Hash::make('password123'),
            'outlet_id' => null, // SuperAdmin Only
            'is_active' => true,
        ]);
        $superAdmin->assignRole($superAdminRole);

        $managerJkt = User::create([
            'name' => 'Budi Santoso (Manager JKT)',
            'email' => 'manager.jkt@inventapos.id',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'outlet_id' => $outletJkt->id,
            'is_active' => true,
        ]);
        $managerJkt->assignRole($managerRole);
        $cashierJkt = User::create([
            'name' => 'Siti Aisyah (Kasir JKT)',
            'email' => 'kasir.jkt@inventapos.id',
            'phone' => '081987654321',
            'password' => Hash::make('password123'),
            'outlet_id' => $outletJkt->id,
            'is_active' => true,
        ]);
        $cashierJkt->assignRole($cashierRole);

        $catBeverage = Category::create([
            'name' => 'Minuman Kemasan',
            'slug' => Str::slug('Minuman Kemasan'),
            'description' => 'Minuman RTD botol dan kaleng',
        ]);
        $catFood = Category::create([
            'name' => 'Makanan Ringan',
            'slug' => Str::slug('Makanan Ringan'),
            'description' => 'Camilan dan biskuit',
        ]);

        $productsData = [
            [
                'category_id' => $catBeverage->id,
                'sku' => 'BEV-001',
                'barcode' => '8992753210101',
                'name' => 'Teh Botol Sosro 450ml',
                'description' => 'Teh melati dalam botol plastik',
                'unit' => 'BTL',
                'cost_price' => 4500.00,
                'selling_price' => 7000.00,
                'min_stock_alert' => 10,
                'stock_jkt' => 100,
                'stock_sby' => 50,
            ],
            [
                'category_id' => $catBeverage->id,
                'sku' => 'BEV-002',
                'barcode' => '8993175538372',
                'name' => 'Kopi Kenangan Mantan RTD 220ml',
                'description' => 'Kopi susu gula aren siap minum',
                'unit' => 'BTL',
                'cost_price' => 6500.00,
                'selling_price' => 9500.00,
                'min_stock_alert' => 15,
                'stock_jkt' => 80,
                'stock_sby' => 40,
            ],
            [
                'category_id' => $catFood->id,
                'sku' => 'FOD-001',
                'barcode' => '8998866200213',
                'name' => 'Indomie Goreng Original 85g',
                'description' => 'Mie instan goreng rasa original',
                'unit' => 'BKS',
                'cost_price' => 2800.00,
                'selling_price' => 3500.00,
                'min_stock_alert' => 20,
                'stock_jkt' => 200,
                'stock_sby' => 120,
            ],
        ];
        foreach ($productsData as $data) {
            $product = Product::create([
                'category_id' => $data['category_id'],
                'sku' => $data['sku'],
                'barcode' => $data['barcode'],
                'name' => $data['name'],
                'description' => $data['description'],
                'unit' => $data['unit'],
                'cost_price' => $data['cost_price'],
                'selling_price' => $data['selling_price'],
                'min_stock_alert' => $data['min_stock_alert'],
                'is_active' => true,
            ]);

            Inventory::create([
                'outlet_id' => $outletJkt->id,
                'product_id' => $product->id,
                'stock' => $data['stock_jkt'],
            ]);

            Inventory::create([
                'outlet_id' => $outletSby->id,
                'product_id' => $product->id,
                'stock' => $data['stock_sby'],
            ]);
        }
    }
}
