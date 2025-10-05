<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laptop;
use App\Models\PartType;
use App\Models\Part;

class LaptopCompatibilitySeeder extends Seeder
{
    public function run()
    {
        // إنشاء أنواع القطع
        $partTypes = [
            ['name' => 'شاشة', 'name_en' => 'Screen'],
            ['name' => 'بطارية', 'name_en' => 'Battery'],
            ['name' => 'لوحة أم', 'name_en' => 'Motherboard'],
            ['name' => 'قطعة WiFi', 'name_en' => 'WiFi Card'],
            ['name' => 'هيكل', 'name_en' => 'Case'],
            ['name' => 'لوحة مفاتيح', 'name_en' => 'Keyboard'],
            ['name' => 'قرص صلب', 'name_en' => 'Hard Drive'],
            ['name' => 'ذاكرة RAM', 'name_en' => 'RAM'],
        ];

        foreach ($partTypes as $type) {
            PartType::create($type);
        }

        // إنشاء الأجهزة
        $hp250g6 = Laptop::create([
            'brand' => 'HP',
            'model' => '250 G6',
            'description' => 'جهاز HP للأعمال والدراسة'
        ]);

        $lenovoIdeapad110 = Laptop::create([
            'brand' => 'Lenovo',
            'model' => 'Ideapad 110',
            'description' => 'جهاز Lenovo متعدد الاستخدامات'
        ]);

        $dellInspirion15 = Laptop::create([
            'brand' => 'Dell',
            'model' => 'Inspiron 15 3000',
            'description' => 'جهاز Dell موثوق'
        ]);

        // إنشاء شاشة متوافقة بين HP و Lenovo
        $screen1 = Part::create([
            'part_type_id' => 1, // شاشة
            'part_number' => 'LCD-15.6-HD-001',
            'specifications' => json_encode([
                'size' => '15.6 inch',
                'resolution' => '1366x768',
                'type' => 'LED'
            ]),
            'price' => 150.00
        ]);

        // ربط الشاشة بـ HP 250 G6 (أصلية)
        $hp250g6->parts()->attach($screen1->id, [
            'is_original' => true,
            'notes' => 'الشاشة الأصلية للجهاز'
        ]);

        // ربط الشاشة بـ Lenovo Ideapad 110 (متوافقة)
        $lenovoIdeapad110->parts()->attach($screen1->id, [
            'is_original' => false,
            'notes' => 'متوافقة تماماً - نفس المقاس والموصل'
        ]);

        // إنشاء بطارية متوافقة
        $battery1 = Part::create([
            'part_type_id' => 2, // بطارية
            'part_number' => 'BAT-HP-41Wh',
            'specifications' => json_encode([
                'capacity' => '41Wh',
                'voltage' => '14.6V',
                'cells' => '4-cell'
            ]),
            'price' => 80.00
        ]);

        $hp250g6->parts()->attach($battery1->id, ['is_original' => true]);
        $lenovoIdeapad110->parts()->attach($battery1->id, [
            'is_original' => false,
            'notes' => 'نفس السعة والفولت'
        ]);

        // إنشاء قطعة WiFi متوافقة مع الثلاثة أجهزة
        $wifi1 = Part::create([
            'part_type_id' => 4, // WiFi
            'part_number' => 'WIFI-RTL8821CE',
            'specifications' => json_encode([
                'chipset' => 'Realtek RTL8821CE',
                'speed' => '433Mbps',
                'bands' => 'Dual Band'
            ]),
            'price' => 25.00
        ]);

        $hp250g6->parts()->attach($wifi1->id, ['is_original' => true]);
        $lenovoIdeapad110->parts()->attach($wifi1->id, ['is_original' => false]);
        $dellInspirion15->parts()->attach($wifi1->id, ['is_original' => false]);

        // لوحة مفاتيح HP
        $keyboard1 = Part::create([
            'part_type_id' => 6, // لوحة مفاتيح
            'part_number' => 'KB-HP-250-AR',
            'specifications' => json_encode([
                'layout' => 'Arabic/English',
                'backlight' => 'No'
            ]),
            'price' => 35.00
        ]);

        $hp250g6->parts()->attach($keyboard1->id, ['is_original' => true]);

        // ذاكرة RAM متوافقة
        $ram1 = Part::create([
            'part_type_id' => 8, // RAM
            'part_number' => 'RAM-DDR4-8GB-2400',
            'specifications' => json_encode([
                'type' => 'DDR4',
                'capacity' => '8GB',
                'speed' => '2400MHz'
            ]),
            'price' => 60.00
        ]);

        $hp250g6->parts()->attach($ram1->id, ['is_original' => true]);
        $lenovoIdeapad110->parts()->attach($ram1->id, ['is_original' => false]);
        $dellInspirion15->parts()->attach($ram1->id, ['is_original' => false]);

        // قرص صلب
        $hdd1 = Part::create([
            'part_type_id' => 7, // قرص صلب
            'part_number' => 'HDD-1TB-5400RPM',
            'specifications' => json_encode([
                'capacity' => '1TB',
                'speed' => '5400RPM',
                'interface' => 'SATA III'
            ]),
            'price' => 90.00
        ]);

        $hp250g6->parts()->attach($hdd1->id, ['is_original' => true]);
        $lenovoIdeapad110->parts()->attach($hdd1->id, ['is_original' => true]);

        // لوحة أم HP
        $motherboard1 = Part::create([
            'part_type_id' => 3, // لوحة أم
            'part_number' => 'MB-HP-250-G6-i5',
            'specifications' => json_encode([
                'cpu_socket' => 'Intel i5-7200U',
                'chipset' => 'Intel HM175'
            ]),
            'price' => 200.00
        ]);

        $hp250g6->parts()->attach($motherboard1->id, ['is_original' => true]);

        // هيكل HP
        $case1 = Part::create([
            'part_type_id' => 5, // هيكل
            'part_number' => 'CASE-HP-250-G6-BOTTOM',
            'specifications' => json_encode([
                'color' => 'Black',
                'material' => 'Plastic'
            ]),
            'price' => 45.00
        ]);

        $hp250g6->parts()->attach($case1->id, ['is_original' => true]);

        echo "✅ تم إنشاء البيانات التجريبية بنجاح!\n";
        echo "📊 الأجهزة: 3\n";
        echo "🔧 القطع: 8\n";
        echo "🔗 التوافقات: متعددة\n";
    }
}