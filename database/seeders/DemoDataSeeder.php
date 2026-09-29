<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding demo data...\n";

        // 1. Settings
        foreach ([
            'store_name' => 'Al-Madina Super Store',
            'store_address' => 'Shop 12, Main Bazaar, Lahore',
            'store_phone' => '+92 300 1234567',
            'currency_symbol' => 'Rs',
            'currency_code' => 'PKR',
            'tax_rate' => '5',
            'tax_included' => '0',
            'low_stock_threshold' => '10',
            'receipt_footer' => 'Thank you! Visit again.',
        ] as $k => $v) {
            Setting::set($k, $v);
        }

        // 2. Admin user
        User::firstOrCreate(
            ['email' => 'admin@pos.local'],
            ['name' => 'Admin', 'password' => 'admin123']
        );

        // 3. Categories
        $categories = [
            'Beverages' => 'Cold drinks, juices, water',
            'Snacks' => 'Chips, biscuits, chocolates',
            'Dairy' => 'Milk, yogurt, cheese, butter',
            'Bakery' => 'Bread, cakes, rusks',
            'Grocery' => 'Rice, flour, sugar, oil',
            'Household' => 'Soap, detergent, cleaning',
            'Personal Care' => 'Shampoo, toothpaste, cream',
            'Frozen' => 'Frozen food, ice cream',
        ];
        $catModels = [];
        foreach ($categories as $name => $desc) {
            $catModels[$name] = Category::firstOrCreate(['name' => $name], ['description' => $desc, 'is_active' => true]);
        }

        // 4. Suppliers
        $suppliers = [
            ['Unilever Pakistan', '+92 300 1111111', 'sales@unilever.pk'],
            ['Nestle Pakistan', '+92 300 2222222', 'orders@nestle.pk'],
            ['Coca-Cola Pakistan', '+92 300 3333333', 'info@coke.pk'],
            ['Local Dairy Farm', '+92 300 4444444', null],
            ['Metro Wholesale', '+92 300 5555555', 'bulk@metro.pk'],
        ];
        $supModels = [];
        foreach ($suppliers as $s) {
            $supModels[] = Supplier::firstOrCreate(['name' => $s[0]], ['phone' => $s[1], 'email' => $s[2], 'is_active' => true]);
        }

        // 5. Products (~40 items)
        $products = [
            // Beverages
            ['Coca-Cola 1.5L', 'BEV-001', 'Beverages', 120, 160, 60],
            ['Pepsi 1.5L', 'BEV-002', 'Beverages', 120, 160, 45],
            ['Sprite 1.5L', 'BEV-003', 'Beverages', 120, 160, 30],
            ['Mineral Water 1.5L', 'BEV-004', 'Beverages', 40, 60, 80],
            ['Frooti Mango 250ml', 'BEV-005', 'Beverages', 45, 70, 100],
            ['Slice Mango 1L', 'BEV-006', 'Beverages', 150, 200, 25],
            ['Nestle Juice 1L', 'BEV-007', 'Beverages', 200, 260, 20],
            ['Tea Pack 200g', 'BEV-008', 'Beverages', 350, 450, 40],

            // Snacks
            ['Lays Masala 40g', 'SNK-001', 'Snacks', 30, 50, 120],
            ['Kurkure 40g', 'SNK-002', 'Snacks', 30, 50, 100],
            ['Dairy Milk Chocolate', 'SNK-003', 'Snacks', 100, 150, 60],
            ['Oreo Biscuits', 'SNK-004', 'Snacks', 80, 120, 70],
            ['Sooper Biscuits', 'SNK-005', 'Snacks', 50, 80, 80],
            ['Peanuts Roasted 200g', 'SNK-006', 'Snacks', 150, 220, 40],

            // Dairy
            ['Olpers Milk 1L', 'DRY-001', 'Dairy', 180, 220, 50],
            ['Milkpak 1L', 'DRY-002', 'Dairy', 180, 220, 45],
            ['Yogurt 500g', 'DRY-003', 'Dairy', 120, 160, 30],
            ['Butter 200g', 'DRY-004', 'Dairy', 320, 400, 25],
            ['Cheese Slices 200g', 'DRY-005', 'Dairy', 400, 500, 20],

            // Bakery
            ['Fresh Bread', 'BAK-001', 'Bakery', 100, 140, 40],
            ['Bran Bread', 'BAK-002', 'Bakery', 120, 160, 30],
            ['Cake Slice', 'BAK-003', 'Bakery', 60, 100, 25],
            ['Rusk Pack', 'BAK-004', 'Bakery', 150, 200, 40],

            // Grocery
            ['Basmati Rice 5kg', 'GRO-001', 'Grocery', 1800, 2200, 25],
            ['Wheat Flour 10kg', 'GRO-002', 'Grocery', 1100, 1350, 20],
            ['Sugar 1kg', 'GRO-003', 'Grocery', 130, 160, 80],
            ['Cooking Oil 1L', 'GRO-004', 'Grocery', 420, 520, 35],
            ['Red Lentils 1kg', 'GRO-005', 'Grocery', 220, 280, 50],
            ['Chickpeas 1kg', 'GRO-006', 'Grocery', 200, 260, 45],
            ['Salt 800g', 'GRO-007', 'Grocery', 40, 60, 60],

            // Household
            ['Surf Excel 1kg', 'HOU-001', 'Household', 450, 550, 30],
            ['Lifebuoy Soap', 'HOU-002', 'Household', 70, 100, 60],
            ['Harpic 500ml', 'HOU-003', 'Household', 200, 260, 25],
            ['Dettol 500ml', 'HOU-004', 'Household', 350, 420, 20],

            // Personal Care
            ['Head & Shoulders 200ml', 'PER-001', 'Personal Care', 380, 480, 25],
            ['Colgate Toothpaste 100g', 'PER-002', 'Personal Care', 180, 240, 40],
            ['Nivea Cream 100ml', 'PER-003', 'Personal Care', 400, 520, 15],

            // Frozen
            ['Ice Cream 1L', 'FRO-001', 'Frozen', 350, 450, 20],
            ['Frozen Samosa 10pc', 'FRO-002', 'Frozen', 200, 280, 30],
        ];

        $productModels = [];
        foreach ($products as $i => $p) {
            [$name, $sku, $cat, $cost, $price, $stock] = $p;
            $productModels[] = Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'barcode' => 'BC' . str_pad((string) ($i + 1), 10, '0', STR_PAD_LEFT),
                    'barcode_type' => 'generated',
                    'plu' => (string) (100 + $i),
                    'category_id' => $catModels[$cat]->id,
                    'supplier_id' => $supModels[$i % count($supModels)]->id,
                    'cost' => $cost,
                    'price' => $price,
                    'stock' => $stock,
                    'low_stock_threshold' => 10,
                    'unit' => 'pcs',
                    'is_active' => true,
                    'expiry_date' => now()->addDays(rand(20, 365))->format('Y-m-d'),
                ]
            );
        }
        echo "Products seeded: " . count($productModels) . "\n";

        // 6. Customers
        $customers = [
            ['Ali Khan', '+92 300 1111111', 'ali@test.com', 0],
            ['Sara Ahmed', '+92 300 2222222', 'sara@test.com', 0],
            ['Bilal Hussain', '+92 300 3333333', null, 0],
            ['Ayesha Malik', '+92 300 4444444', 'ayesha@test.com', 0],
            ['Usman Tariq', '+92 300 5555555', null, 0],
            ['Fatima Noor', '+92 300 6666666', 'fatima@test.com', 0],
            ['Walk-in Customer', '-', null, 0],
        ];
        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = Customer::firstOrCreate(
                ['name' => $c[0]],
                ['phone' => $c[1], 'email' => $c[2], 'balance' => $c[3], 'is_active' => true]
            );
        }
        echo "Customers seeded: " . count($customerModels) . "\n";

        // 7. Sample sales for last 30 days
        $admin = User::where('email', 'admin@pos.local')->first();
        $salesCount = 0;

        for ($day = 30; $day >= 0; $day--) {
            $salesToday = rand(3, 12);
            for ($s = 0; $s < $salesToday; $s++) {
                $date = now()->subDays($day)->setTime(rand(9, 21), rand(0, 59));
                $customer = $customerModels[array_rand($customerModels)];
                $isCredit = $day < 10 && rand(1, 5) === 1; // ~20% of recent sales on credit

                $itemsCount = rand(1, 5);
                $subtotal = 0;
                $pickedProducts = [];

                for ($i = 0; $i < $itemsCount; $i++) {
                    $product = $productModels[array_rand($productModels)];
                    if (in_array($product->id, array_column($pickedProducts, 'id'))) continue;
                    $qty = rand(1, 3);
                    $lineTotal = $product->price * $qty;
                    $subtotal += $lineTotal;
                    $pickedProducts[] = ['id' => $product->id, 'qty' => $qty, 'price' => $product->price, 'name' => $product->name, 'total' => $lineTotal];
                }

                if (empty($pickedProducts)) continue;

                $discount = rand(1, 10) === 1 ? rand(10, 50) : 0;
                $tax = round($subtotal * 0.05, 2);
                $total = $subtotal - $discount + $tax;
                $paid = $isCredit ? 0 : $total;
                $method = $isCredit ? 'credit' : (rand(1, 4) === 1 ? 'card' : 'cash');

                $invoiceNo = 'INV-' . $date->format('Ymd') . '-' . str_pad((string) ($salesCount + 1), 4, '0', STR_PAD_LEFT);

                $sale = Sale::create([
                    'invoice_no' => $invoiceNo,
                    'customer_id' => $customer->id,
                    'user_id' => $admin->id,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $total,
                    'paid' => $paid,
                    'change_due' => 0,
                    'payment_method' => $method,
                    'status' => 'completed',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                foreach ($pickedProducts as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['qty'],
                        'discount' => 0,
                        'total' => $item['total'],
                    ]);

                    if ($isCredit) {
                        $customer->increment('balance', $total);
                    }
                }

                $salesCount++;
            }
        }
        echo "Sales seeded: $salesCount\n";

        // 8. Customer payments (settle some credit)
        $paymentCount = 0;
        foreach ($customerModels as $customer) {
            if ($customer->balance > 0 && rand(1, 2) === 1) {
                $payAmount = min($customer->balance, rand(200, 2000));
                if ($payAmount > 0) {
                    CustomerPayment::create([
                        'customer_id' => $customer->id,
                        'amount' => $payAmount,
                        'method' => 'cash',
                        'reference' => 'PMT-' . str_pad((string) ($paymentCount + 1), 4, '0', STR_PAD_LEFT),
                        'user_id' => $admin->id,
                        'created_at' => now()->subDays(rand(1, 7)),
                    ]);
                    $customer->decrement('balance', $payAmount);
                    $paymentCount++;
                }
            }
        }
        echo "Payments seeded: $paymentCount\n";

        // 9. Expenses
        $expenses = [
            ['Electricity bill', 8500, 'Utilities'],
            ['Shop rent', 40000, 'Rent'],
            ['Staff salary', 25000, 'Salary'],
            ['Internet bill', 3500, 'Utilities'],
            ['Water bill', 1200, 'Utilities'],
            ['Cleaning supplies', 800, 'Maintenance'],
            ['Gas bill', 2500, 'Utilities'],
        ];
        foreach ($expenses as $e) {
            Expense::firstOrCreate(
                ['title' => $e[0], 'amount' => $e[1]],
                [
                    'category' => $e[2],
                    'expense_date' => now()->subDays(rand(1, 20))->format('Y-m-d'),
                    'user_id' => $admin->id,
                ]
            );
        }
        echo "Expenses seeded\n";

        echo "✅ Demo data ready!\n";
        echo "Login: admin@pos.local / admin123\n";
    }
}