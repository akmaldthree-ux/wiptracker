<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Station;
use App\Models\Product;
use App\Models\Series;
use App\Models\Color;
use App\Models\Size;
use App\Models\Sku;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use App\Models\WipEntry;
use App\Models\Handover;
use App\Models\HandoverItem;
use App\Models\RawMaterial;
use App\Models\MaterialReceipt;
use App\Models\Supplier;
use App\Models\Budget;
use App\Models\Notification;
use App\Models\QcInspection;
use App\Models\QcChecklistItem;
use App\Models\CuttingPlan;
use App\Models\CuttingBundle;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ════════════════════════════════════════
        //  CLEAR EXISTING DATA (order matters for FKs)
        // ════════════════════════════════════════
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $tables = [
            'notifications','qc_checklist_items','qc_inspections',
            'cutting_bundles','cutting_plans',
            'handover_items','handovers',
            'wip_entries',
            'production_order_items','production_orders',
            'budgets',
            'material_receipts',
            'skus','series','products',
            'colors','sizes',
            'raw_materials','suppliers',
            'users','stations',
        ];
        foreach ($tables as $t) { DB::table($t)->truncate(); }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ════════════════════════════════════════
        //  STATIONS
        // ════════════════════════════════════════
        $cut = Station::create(['name'=>'Cutting','code'=>'CUT','order_sequence'=>1,'description'=>'Pemotongan pola kain sesuai spesifikasi desain','bottleneck_threshold'=>300,'is_active'=>true,'is_final'=>false]);
        $sew = Station::create(['name'=>'Sewing','code'=>'SEW','order_sequence'=>2,'description'=>'Penjahitan dan perakitan komponen garmen','bottleneck_threshold'=>200,'is_active'=>true,'is_final'=>false]);
        $fin = Station::create(['name'=>'Finishing','code'=>'FIN','order_sequence'=>3,'description'=>'Setrika, bordiran akhir, pasang label & aksesoris','bottleneck_threshold'=>200,'is_active'=>true,'is_final'=>false]);
        $qc  = Station::create(['name'=>'Quality Control','code'=>'QC','order_sequence'=>4,'description'=>'Pemeriksaan kualitas sebelum ke gudang','bottleneck_threshold'=>150,'is_active'=>true,'is_final'=>false]);
        $wh  = Station::create(['name'=>'Gudang','code'=>'GDG','order_sequence'=>5,'description'=>'Penyimpanan produk jadi dan pengiriman ke buyer','bottleneck_threshold'=>1000,'is_active'=>true,'is_final'=>true]);

        // ════════════════════════════════════════
        //  USERS
        // ════════════════════════════════════════
        $admin  = User::create(['name'=>'Ahmad Fauzi','email'=>'admin@dpis.com','password'=>Hash::make('password123'),'role'=>'admin','phone'=>'081200000001','is_active'=>true]);
        $super  = User::create(['name'=>'Sari Dewi','email'=>'supervisor@dpis.com','password'=>Hash::make('password123'),'role'=>'supervisor','phone'=>'081200000002','is_active'=>true]);
        $mgr    = User::create(['name'=>'H. Ridwan Kurniawan','email'=>'manager@dpis.com','password'=>Hash::make('password123'),'role'=>'manager','phone'=>'081200000003','is_active'=>true]);
        $picCut = User::create(['name'=>'Usman Hakim','email'=>'cutting@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$cut->id,'phone'=>'081200000004','is_active'=>true]);
        $picSew = User::create(['name'=>'Fatimah Zahra','email'=>'sewing@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$sew->id,'phone'=>'081200000005','is_active'=>true]);
        $picFin = User::create(['name'=>'Rina Marlina','email'=>'finishing@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$fin->id,'phone'=>'081200000006','is_active'=>true]);
        $picQC  = User::create(['name'=>'Nurul Hidayah','email'=>'qc@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$qc->id,'phone'=>'081200000007','is_active'=>true]);
        $stfWH  = User::create(['name'=>'Hendra Gunawan','email'=>'gudang@dpis.com','password'=>Hash::make('password123'),'role'=>'staff_gudang','station_id'=>$wh->id,'phone'=>'081200000008','is_active'=>true]);

        // ════════════════════════════════════════
        //  PRODUCTS (Muslim Fashion)
        // ════════════════════════════════════════
        $pJubah = Product::create(['code'=>'JBH','name'=>'Jubah Pria','category'=>'Jubah','description'=>'Jubah panjang pria berbahan premium, cocok untuk sholat dan acara keagamaan']);
        $pAbaya = Product::create(['code'=>'ABY','name'=>'Abaya Wanita','category'=>'Abaya','description'=>'Abaya wanita modern dengan detail bordir eksklusif']);
        $pMukena= Product::create(['code'=>'MKN','name'=>'Mukena Dewasa','category'=>'Mukena','description'=>'Mukena dewasa berbahan katun halus, ringan dan nyaman untuk ibadah']);
        $pKoko  = Product::create(['code'=>'KKO','name'=>'Baju Koko','category'=>'Baju Koko','description'=>'Baju koko pria dengan desain modern dan klasik, kancing depan dan kerah shanghai']);

        // ════════════════════════════════════════
        //  COLORS
        // ════════════════════════════════════════
        $cPutih  = Color::create(['name'=>'Putih','code'=>'WHT','hex_code'=>'#F5F5F0']);
        $cHitam  = Color::create(['name'=>'Hitam','code'=>'BLK','hex_code'=>'#1A1A1A']);
        $cNavy   = Color::create(['name'=>'Navy Blue','code'=>'NVY','hex_code'=>'#1B2A6B']);
        $cMaroon = Color::create(['name'=>'Maroon','code'=>'MRN','hex_code'=>'#6B1A1A']);
        $cOlive  = Color::create(['name'=>'Olive Green','code'=>'OLV','hex_code'=>'#4A5C3A']);
        $cCream  = Color::create(['name'=>'Cream','code'=>'CRM','hex_code'=>'#F5F0DC']);
        $cAbu    = Color::create(['name'=>'Abu-abu','code'=>'GRY','hex_code'=>'#8A8A8A']);
        $cMocca  = Color::create(['name'=>'Mocca','code'=>'MCC','hex_code'=>'#6B4226']);

        // ════════════════════════════════════════
        //  SIZES
        // ════════════════════════════════════════
        $sS    = Size::create(['name'=>'S',   'type'=>'letter','sort_order'=>1]);
        $sM    = Size::create(['name'=>'M',   'type'=>'letter','sort_order'=>2]);
        $sL    = Size::create(['name'=>'L',   'type'=>'letter','sort_order'=>3]);
        $sXL   = Size::create(['name'=>'XL',  'type'=>'letter','sort_order'=>4]);
        $sXXL  = Size::create(['name'=>'XXL', 'type'=>'letter','sort_order'=>5]);
        $sXXXL = Size::create(['name'=>'XXXL','type'=>'letter','sort_order'=>6]);

        // ════════════════════════════════════════
        //  SERIES
        // ════════════════════════════════════════
        $serJubahRmdn  = Series::create(['name'=>'Ramadan Collection 2026','code'=>'JBH-RMD26','product_id'=>$pJubah->id]);
        $serJubahCls   = Series::create(['name'=>'Classic Premium Series','code'=>'JBH-CLS26','product_id'=>$pJubah->id]);
        $serAbayaSyari = Series::create(['name'=>'Syari Elegance 2026','code'=>'ABY-SYR26','product_id'=>$pAbaya->id]);
        $serAbayaMod   = Series::create(['name'=>'Modern Abaya Collection','code'=>'ABY-MOD26','product_id'=>$pAbaya->id]);
        $serMukenaTrvl = Series::create(['name'=>'Travel Mukena Series','code'=>'MKN-TRV26','product_id'=>$pMukena->id]);
        $serMukenaElg  = Series::create(['name'=>'Elegant Mukena Premium','code'=>'MKN-ELG26','product_id'=>$pMukena->id]);
        $serKokoIdl    = Series::create(['name'=>'Idul Fitri Collection 2026','code'=>'KKO-IDL26','product_id'=>$pKoko->id]);
        $serKokoCsl    = Series::create(['name'=>'Casual Koko Everyday','code'=>'KKO-CSL26','product_id'=>$pKoko->id]);

        // ════════════════════════════════════════
        //  SKUs
        // ════════════════════════════════════════
        // Jubah Ramadan (Putih, Navy — S M L XL)
        $skuJ1 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahRmdn->id,'color_id'=>$cPutih->id,'size_id'=>$sM->id, 'sku_code'=>'JBH-RMD26-WHT-M']);
        $skuJ2 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahRmdn->id,'color_id'=>$cPutih->id,'size_id'=>$sL->id, 'sku_code'=>'JBH-RMD26-WHT-L']);
        $skuJ3 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahRmdn->id,'color_id'=>$cPutih->id,'size_id'=>$sXL->id,'sku_code'=>'JBH-RMD26-WHT-XL']);
        $skuJ4 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahRmdn->id,'color_id'=>$cNavy->id, 'size_id'=>$sM->id, 'sku_code'=>'JBH-RMD26-NVY-M']);
        $skuJ5 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahRmdn->id,'color_id'=>$cNavy->id, 'size_id'=>$sL->id, 'sku_code'=>'JBH-RMD26-NVY-L']);

        // Jubah Classic
        $skuJC1 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahCls->id,'color_id'=>$cHitam->id,'size_id'=>$sL->id,  'sku_code'=>'JBH-CLS26-BLK-L']);
        $skuJC2 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahCls->id,'color_id'=>$cHitam->id,'size_id'=>$sXL->id, 'sku_code'=>'JBH-CLS26-BLK-XL']);
        $skuJC3 = Sku::create(['product_id'=>$pJubah->id,'series_id'=>$serJubahCls->id,'color_id'=>$cOlive->id,'size_id'=>$sXL->id, 'sku_code'=>'JBH-CLS26-OLV-XL']);

        // Abaya Syari (Hitam, Maroon, Navy — M L XL)
        $skuA1 = Sku::create(['product_id'=>$pAbaya->id,'series_id'=>$serAbayaSyari->id,'color_id'=>$cHitam->id, 'size_id'=>$sM->id, 'sku_code'=>'ABY-SYR26-BLK-M']);
        $skuA2 = Sku::create(['product_id'=>$pAbaya->id,'series_id'=>$serAbayaSyari->id,'color_id'=>$cHitam->id, 'size_id'=>$sL->id, 'sku_code'=>'ABY-SYR26-BLK-L']);
        $skuA3 = Sku::create(['product_id'=>$pAbaya->id,'series_id'=>$serAbayaSyari->id,'color_id'=>$cHitam->id, 'size_id'=>$sXL->id,'sku_code'=>'ABY-SYR26-BLK-XL']);
        $skuA4 = Sku::create(['product_id'=>$pAbaya->id,'series_id'=>$serAbayaSyari->id,'color_id'=>$cMaroon->id,'size_id'=>$sM->id, 'sku_code'=>'ABY-SYR26-MRN-M']);
        $skuA5 = Sku::create(['product_id'=>$pAbaya->id,'series_id'=>$serAbayaSyari->id,'color_id'=>$cMaroon->id,'size_id'=>$sL->id, 'sku_code'=>'ABY-SYR26-MRN-L']);

        // Abaya Modern
        $skuAM1 = Sku::create(['product_id'=>$pAbaya->id,'series_id'=>$serAbayaMod->id,'color_id'=>$cAbu->id,  'size_id'=>$sM->id, 'sku_code'=>'ABY-MOD26-GRY-M']);
        $skuAM2 = Sku::create(['product_id'=>$pAbaya->id,'series_id'=>$serAbayaMod->id,'color_id'=>$cAbu->id,  'size_id'=>$sL->id, 'sku_code'=>'ABY-MOD26-GRY-L']);

        // Mukena Travel (Putih, Cream — S M L)
        $skuMT1 = Sku::create(['product_id'=>$pMukena->id,'series_id'=>$serMukenaTrvl->id,'color_id'=>$cPutih->id,'size_id'=>$sM->id, 'sku_code'=>'MKN-TRV26-WHT-M']);
        $skuMT2 = Sku::create(['product_id'=>$pMukena->id,'series_id'=>$serMukenaTrvl->id,'color_id'=>$cPutih->id,'size_id'=>$sL->id, 'sku_code'=>'MKN-TRV26-WHT-L']);
        $skuMT3 = Sku::create(['product_id'=>$pMukena->id,'series_id'=>$serMukenaTrvl->id,'color_id'=>$cCream->id,'size_id'=>$sM->id, 'sku_code'=>'MKN-TRV26-CRM-M']);
        $skuMT4 = Sku::create(['product_id'=>$pMukena->id,'series_id'=>$serMukenaTrvl->id,'color_id'=>$cCream->id,'size_id'=>$sL->id, 'sku_code'=>'MKN-TRV26-CRM-L']);

        // Baju Koko Idul Fitri (Putih, Cream, Navy — M L XL XXL)
        $skuKI1 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,'color_id'=>$cPutih->id,'size_id'=>$sM->id,  'sku_code'=>'KKO-IDL26-WHT-M']);
        $skuKI2 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,'color_id'=>$cPutih->id,'size_id'=>$sL->id,  'sku_code'=>'KKO-IDL26-WHT-L']);
        $skuKI3 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,'color_id'=>$cPutih->id,'size_id'=>$sXL->id, 'sku_code'=>'KKO-IDL26-WHT-XL']);
        $skuKI4 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,'color_id'=>$cCream->id,'size_id'=>$sM->id,  'sku_code'=>'KKO-IDL26-CRM-M']);
        $skuKI5 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,'color_id'=>$cCream->id,'size_id'=>$sL->id,  'sku_code'=>'KKO-IDL26-CRM-L']);
        $skuKI6 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,'color_id'=>$cNavy->id, 'size_id'=>$sL->id,  'sku_code'=>'KKO-IDL26-NVY-L']);
        $skuKI7 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,'color_id'=>$cNavy->id, 'size_id'=>$sXL->id, 'sku_code'=>'KKO-IDL26-NVY-XL']);

        // Baju Koko Casual
        $skuKC1 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoCsl->id,'color_id'=>$cMocca->id,'size_id'=>$sL->id,  'sku_code'=>'KKO-CSL26-MCC-L']);
        $skuKC2 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoCsl->id,'color_id'=>$cOlive->id,'size_id'=>$sL->id,  'sku_code'=>'KKO-CSL26-OLV-L']);
        $skuKC3 = Sku::create(['product_id'=>$pKoko->id,'series_id'=>$serKokoCsl->id,'color_id'=>$cOlive->id,'size_id'=>$sXL->id, 'sku_code'=>'KKO-CSL26-OLV-XL']);

        // ════════════════════════════════════════
        //  SUPPLIERS
        // ════════════════════════════════════════
        $sup1 = Supplier::create(['code'=>'SUP-001','name'=>'PT Tekstil Nusantara','phone'=>'02177001234','email'=>'order@tekstilnusantara.co.id','address'=>'Jl. Industri Tekstil No. 45, Bandung','contact_person'=>'Pak Darmawan','notes'=>'Supplier utama kain katun dan rayon','is_active'=>true]);
        $sup2 = Supplier::create(['code'=>'SUP-002','name'=>'CV Kain Berkah','phone'=>'02188002345','email'=>'sales@kainberkah.com','address'=>'Jl. Pasar Baru No. 12, Jakarta Pusat','contact_person'=>'Bu Halimah','notes'=>'Spesialis kain wolfis dan jersey premium','is_active'=>true]);
        $sup3 = Supplier::create(['code'=>'SUP-003','name'=>'UD Aksesoris Garmen Jaya','phone'=>'02199003456','email'=>'info@aksesorisgarmen.id','address'=>'Jl. Garmen Raya No. 8, Surabaya','contact_person'=>'Pak Yusuf','notes'=>'Supplier kancing, zipper YKK, label, dan aksesoris garmen','is_active'=>true]);

        // ════════════════════════════════════════
        //  RAW MATERIALS
        // ════════════════════════════════════════
        $rmKatun  = RawMaterial::create(['code'=>'BB-001','name'=>'Kain Katun Rayon (Putih)','unit'=>'meter','category'=>'kain','color'=>'Putih','min_stock'=>500,'current_stock'=>820,'unit_price'=>28000,'supplier_id'=>$sup1->id]);
        $rmWolfis = RawMaterial::create(['code'=>'BB-002','name'=>'Kain Wolfis Premium (Hitam)','unit'=>'meter','category'=>'kain','color'=>'Hitam','min_stock'=>400,'current_stock'=>310,'unit_price'=>45000,'supplier_id'=>$sup2->id]);
        $rmRayon  = RawMaterial::create(['code'=>'BB-003','name'=>'Kain Rayon Viscose (Navy)','unit'=>'meter','category'=>'kain','color'=>'Navy Blue','min_stock'=>300,'current_stock'=>95,'unit_price'=>32000,'supplier_id'=>$sup1->id]);
        $rmJersey = RawMaterial::create(['code'=>'BB-004','name'=>'Kain Jersey Spandex (Maroon)','unit'=>'meter','category'=>'kain','color'=>'Maroon','min_stock'=>200,'current_stock'=>380,'unit_price'=>38000,'supplier_id'=>$sup2->id]);
        $rmBenang = RawMaterial::create(['code'=>'BB-005','name'=>'Benang Jahit No.40 (Putih)','unit'=>'cone','category'=>'benang','color'=>'Putih','min_stock'=>80,'current_stock'=>52,'unit_price'=>18000,'supplier_id'=>$sup3->id]);
        $rmKancing= RawMaterial::create(['code'=>'BB-006','name'=>'Kancing Baju Koko (Emas)','unit'=>'gross','category'=>'aksesoris','min_stock'=>50,'current_stock'=>78,'unit_price'=>25000,'supplier_id'=>$sup3->id]);
        $rmZipper = RawMaterial::create(['code'=>'BB-007','name'=>'Zipper YKK 20cm','unit'=>'pcs','category'=>'aksesoris','min_stock'=>300,'current_stock'=>120,'unit_price'=>4500,'supplier_id'=>$sup3->id]);
        $rmLabel  = RawMaterial::create(['code'=>'BB-008','name'=>'Label Merk DTHREE Woven','unit'=>'pcs','category'=>'aksesoris','min_stock'=>1000,'current_stock'=>680,'unit_price'=>800,'supplier_id'=>$sup3->id]);
        $rmKemas  = RawMaterial::create(['code'=>'BB-009','name'=>'Polybag Kemasan (30x40cm)','unit'=>'pcs','category'=>'kemasan','min_stock'=>2000,'current_stock'=>3200,'unit_price'=>250,'supplier_id'=>$sup3->id]);

        // ════════════════════════════════════════
        //  MATERIAL RECEIPTS
        // ════════════════════════════════════════
        MaterialReceipt::create([
            'raw_material_id'=>$rmKatun->id,'supplier_id'=>$sup1->id,
            'qty'=>500,'unit_price'=>28000,'total_price'=>14000000,
            'supplier'=>$sup1->name,'po_no'=>'PO-2026-001',
            'receipt_date'=>now()->subDays(15)->toDateString(),
            'confirmed_by'=>$stfWH->id,'notes'=>'Penerimaan rutin bulanan',
        ]);
        MaterialReceipt::create([
            'raw_material_id'=>$rmWolfis->id,'supplier_id'=>$sup2->id,
            'qty'=>400,'unit_price'=>45000,'total_price'=>18000000,
            'supplier'=>$sup2->name,'po_no'=>'PO-2026-002',
            'receipt_date'=>now()->subDays(12)->toDateString(),
            'confirmed_by'=>$stfWH->id,'notes'=>'Untuk produksi Abaya Syari',
        ]);
        MaterialReceipt::create([
            'raw_material_id'=>$rmRayon->id,'supplier_id'=>$sup1->id,
            'qty'=>200,'unit_price'=>32000,'total_price'=>6400000,
            'supplier'=>$sup1->name,'po_no'=>'PO-2026-003',
            'receipt_date'=>now()->subDays(8)->toDateString(),
            'confirmed_by'=>$stfWH->id,'notes'=>'Stok menipis, order darurat',
        ]);
        MaterialReceipt::create([
            'raw_material_id'=>$rmKancing->id,'supplier_id'=>$sup3->id,
            'qty'=>100,'unit_price'=>25000,'total_price'=>2500000,
            'supplier'=>$sup3->name,'po_no'=>'PO-2026-004',
            'receipt_date'=>now()->subDays(5)->toDateString(),
            'confirmed_by'=>$stfWH->id,'notes'=>'Kancing emas untuk Baju Koko Idul Fitri',
        ]);

        // ════════════════════════════════════════
        //  PRODUCTION ORDERS
        // ════════════════════════════════════════
        $today = now()->toDateString();
        $d1    = now()->subDays(1)->toDateString();
        $d2    = now()->subDays(2)->toDateString();
        $d3    = now()->subDays(3)->toDateString();
        $d5    = now()->subDays(5)->toDateString();
        $d7    = now()->subDays(7)->toDateString();
        $d10   = now()->subDays(10)->toDateString();
        $d12   = now()->subDays(12)->toDateString();
        $d14   = now()->subDays(14)->toDateString();

        // ORDER 1: Jubah Ramadan — SELESAI (sudah melewati semua stasiun)
        $ord1 = ProductionOrder::create([
            'order_no'=>'ORD-2026-001','product_id'=>$pJubah->id,'series_id'=>$serJubahRmdn->id,
            'target_date'=>now()->subDays(3)->toDateString(),'status'=>'completed',
            'selling_price'=>285000,'notes'=>'Order pertama Jubah Ramadan 2026. Prioritas tinggi untuk pengiriman.',
            'created_by'=>$super->id,'created_at'=>now()->subDays(14),
        ]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ1->id,'target_qty'=>120]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ2->id,'target_qty'=>150]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ3->id,'target_qty'=>80]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ4->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ5->id,'target_qty'=>80]);

        // ORDER 2: Abaya Syari — AKTIF (sedang di QC, hampir selesai)
        $ord2 = ProductionOrder::create([
            'order_no'=>'ORD-2026-002','product_id'=>$pAbaya->id,'series_id'=>$serAbayaSyari->id,
            'target_date'=>now()->addDays(3)->toDateString(),'status'=>'active',
            'selling_price'=>420000,'notes'=>'Abaya syari dengan bordir eksklusif. Pastikan kualitas jahitan halus.',
            'created_by'=>$super->id,'created_at'=>now()->subDays(10),
        ]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA1->id,'target_qty'=>80]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA2->id,'target_qty'=>120]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA3->id,'target_qty'=>60]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA4->id,'target_qty'=>50]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA5->id,'target_qty'=>90]);

        // ORDER 3: Baju Koko Idul Fitri — AKTIF TERLAMBAT (sedang di Sewing, bottleneck)
        $ord3 = ProductionOrder::create([
            'order_no'=>'ORD-2026-003','product_id'=>$pKoko->id,'series_id'=>$serKokoIdl->id,
            'target_date'=>now()->subDays(2)->toDateString(),'status'=>'active',
            'selling_price'=>195000,'notes'=>'URGENT! Order Baju Koko Idul Fitri sudah terlambat. Segera percepat proses sewing.',
            'created_by'=>$super->id,'created_at'=>now()->subDays(7),
        ]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI1->id,'target_qty'=>200]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI2->id,'target_qty'=>250]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI3->id,'target_qty'=>180]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI4->id,'target_qty'=>120]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI5->id,'target_qty'=>150]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI6->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI7->id,'target_qty'=>80]);

        // ORDER 4: Mukena Travel — AKTIF (baru dimulai, di Cutting)
        $ord4 = ProductionOrder::create([
            'order_no'=>'ORD-2026-004','product_id'=>$pMukena->id,'series_id'=>$serMukenaTrvl->id,
            'target_date'=>now()->addDays(10)->toDateString(),'status'=>'active',
            'selling_price'=>145000,'notes'=>'Mukena travel ringan dan mudah dilipat.',
            'created_by'=>$super->id,'created_at'=>now()->subDays(3),
        ]);
        ProductionOrderItem::create(['production_order_id'=>$ord4->id,'sku_id'=>$skuMT1->id,'target_qty'=>150]);
        ProductionOrderItem::create(['production_order_id'=>$ord4->id,'sku_id'=>$skuMT2->id,'target_qty'=>180]);
        ProductionOrderItem::create(['production_order_id'=>$ord4->id,'sku_id'=>$skuMT3->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord4->id,'sku_id'=>$skuMT4->id,'target_qty'=>120]);

        // ORDER 5: Jubah Classic — DRAFT (belum dimulai)
        $ord5 = ProductionOrder::create([
            'order_no'=>'ORD-2026-005','product_id'=>$pJubah->id,'series_id'=>$serJubahCls->id,
            'target_date'=>now()->addDays(21)->toDateString(),'status'=>'draft',
            'selling_price'=>320000,'notes'=>'Draft order Jubah Classic Premium. Menunggu konfirmasi buyer.',
            'created_by'=>$super->id,'created_at'=>now()->subDays(1),
        ]);
        ProductionOrderItem::create(['production_order_id'=>$ord5->id,'sku_id'=>$skuJC1->id,'target_qty'=>80]);
        ProductionOrderItem::create(['production_order_id'=>$ord5->id,'sku_id'=>$skuJC2->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord5->id,'sku_id'=>$skuJC3->id,'target_qty'=>60]);

        // ════════════════════════════════════════
        //  CUTTING PLANS
        // ════════════════════════════════════════
        $cp1 = CuttingPlan::create(['plan_no'=>'CP-2026-001','production_order_id'=>$ord1->id,'planned_date'=>$d14,'planned_qty'=>530,'actual_qty'=>528,'status'=>'completed','notes'=>'Selesai tepat waktu. 2 pcs reject di awal.','created_by'=>$picCut->id]);
        CuttingBundle::create(['bundle_no'=>'CP-2026-001-B01','cutting_plan_id'=>$cp1->id,'sku_id'=>$skuJ1->id,'qty'=>120,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-001-B02','cutting_plan_id'=>$cp1->id,'sku_id'=>$skuJ2->id,'qty'=>150,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-001-B03','cutting_plan_id'=>$cp1->id,'sku_id'=>$skuJ3->id,'qty'=>80,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-001-B04','cutting_plan_id'=>$cp1->id,'sku_id'=>$skuJ4->id,'qty'=>100,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-001-B05','cutting_plan_id'=>$cp1->id,'sku_id'=>$skuJ5->id,'qty'=>80,'status'=>'completed']);

        $cp2 = CuttingPlan::create(['plan_no'=>'CP-2026-002','production_order_id'=>$ord2->id,'planned_date'=>$d10,'planned_qty'=>400,'actual_qty'=>398,'status'=>'completed','notes'=>'Cutting selesai. 2 pcs scrap kain cacat.','created_by'=>$picCut->id]);
        CuttingBundle::create(['bundle_no'=>'CP-2026-002-B01','cutting_plan_id'=>$cp2->id,'sku_id'=>$skuA1->id,'qty'=>80,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-002-B02','cutting_plan_id'=>$cp2->id,'sku_id'=>$skuA2->id,'qty'=>120,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-002-B03','cutting_plan_id'=>$cp2->id,'sku_id'=>$skuA3->id,'qty'=>60,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-002-B04','cutting_plan_id'=>$cp2->id,'sku_id'=>$skuA4->id,'qty'=>50,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-002-B05','cutting_plan_id'=>$cp2->id,'sku_id'=>$skuA5->id,'qty'=>90,'status'=>'completed']);

        $cp3 = CuttingPlan::create(['plan_no'=>'CP-2026-003','production_order_id'=>$ord3->id,'planned_date'=>$d7,'planned_qty'=>1080,'actual_qty'=>1075,'status'=>'completed','notes'=>'Cutting selesai tapi sewing tertinggal.','created_by'=>$picCut->id]);
        CuttingBundle::create(['bundle_no'=>'CP-2026-003-B01','cutting_plan_id'=>$cp3->id,'sku_id'=>$skuKI1->id,'qty'=>200,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-003-B02','cutting_plan_id'=>$cp3->id,'sku_id'=>$skuKI2->id,'qty'=>250,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-003-B03','cutting_plan_id'=>$cp3->id,'sku_id'=>$skuKI3->id,'qty'=>180,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-003-B04','cutting_plan_id'=>$cp3->id,'sku_id'=>$skuKI4->id,'qty'=>120,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-003-B05','cutting_plan_id'=>$cp3->id,'sku_id'=>$skuKI5->id,'qty'=>150,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-003-B06','cutting_plan_id'=>$cp3->id,'sku_id'=>$skuKI6->id,'qty'=>100,'status'=>'completed']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-003-B07','cutting_plan_id'=>$cp3->id,'sku_id'=>$skuKI7->id,'qty'=>80,'status'=>'completed']);

        $cp4 = CuttingPlan::create(['plan_no'=>'CP-2026-004','production_order_id'=>$ord4->id,'planned_date'=>$today,'planned_qty'=>550,'actual_qty'=>350,'status'=>'cutting','notes'=>'Sedang dalam proses pemotongan.','created_by'=>$picCut->id]);
        CuttingBundle::create(['bundle_no'=>'CP-2026-004-B01','cutting_plan_id'=>$cp4->id,'sku_id'=>$skuMT1->id,'qty'=>150,'status'=>'cut']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-004-B02','cutting_plan_id'=>$cp4->id,'sku_id'=>$skuMT2->id,'qty'=>180,'status'=>'cut']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-004-B03','cutting_plan_id'=>$cp4->id,'sku_id'=>$skuMT3->id,'qty'=>100,'status'=>'bundled']);
        CuttingBundle::create(['bundle_no'=>'CP-2026-004-B04','cutting_plan_id'=>$cp4->id,'sku_id'=>$skuMT4->id,'qty'=>120,'status'=>'bundled']);

        // ════════════════════════════════════════
        //  WIP ENTRIES
        // ════════════════════════════════════════
        $note = fn(string $txt) => $txt;

        // ── ORDER 1: Jubah Ramadan — SELESAI (semua stasiun dilalui, WIP = 0) ──
        // Cutting (in & out)
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ1->id,'station_id'=>$cut->id,'qty_in'=>120,'qty_out'=>120,'qty_reject'=>0,'input_date'=>$d14,'created_by'=>$picCut->id,'notes'=>'Cutting ORD-001 batch 1']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ2->id,'station_id'=>$cut->id,'qty_in'=>150,'qty_out'=>150,'qty_reject'=>0,'input_date'=>$d14,'created_by'=>$picCut->id,'notes'=>'Cutting ORD-001 batch 1']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ3->id,'station_id'=>$cut->id,'qty_in'=>80,'qty_out'=>78,'qty_reject'=>2,'input_date'=>$d14,'created_by'=>$picCut->id,'notes'=>'2 pcs kain cacat']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ4->id,'station_id'=>$cut->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$d14,'created_by'=>$picCut->id,'notes'=>'Cutting ORD-001 batch 1']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ5->id,'station_id'=>$cut->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>0,'input_date'=>$d14,'created_by'=>$picCut->id,'notes'=>'Cutting ORD-001 batch 1']);
        // Sewing
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ1->id,'station_id'=>$sew->id,'qty_in'=>120,'qty_out'=>118,'qty_reject'=>2,'input_date'=>$d12,'created_by'=>$picSew->id,'notes'=>'Jahitan tidak rapi']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ2->id,'station_id'=>$sew->id,'qty_in'=>150,'qty_out'=>149,'qty_reject'=>1,'input_date'=>$d12,'created_by'=>$picSew->id,'notes'=>'']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ3->id,'station_id'=>$sew->id,'qty_in'=>78,'qty_out'=>78,'qty_reject'=>0,'input_date'=>$d12,'created_by'=>$picSew->id,'notes'=>'']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ4->id,'station_id'=>$sew->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$d12,'created_by'=>$picSew->id,'notes'=>'']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ5->id,'station_id'=>$sew->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>0,'input_date'=>$d12,'created_by'=>$picSew->id,'notes'=>'']);
        // Finishing
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ1->id,'station_id'=>$fin->id,'qty_in'=>118,'qty_out'=>118,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picFin->id,'notes'=>'']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ2->id,'station_id'=>$fin->id,'qty_in'=>149,'qty_out'=>149,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picFin->id,'notes'=>'']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ3->id,'station_id'=>$fin->id,'qty_in'=>78,'qty_out'=>77,'qty_reject'=>1,'input_date'=>$d10,'created_by'=>$picFin->id,'notes'=>'Benang sisa terlihat']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ4->id,'station_id'=>$fin->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picFin->id,'notes'=>'']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ5->id,'station_id'=>$fin->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picFin->id,'notes'=>'']);
        // QC
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ1->id,'station_id'=>$qc->id,'qty_in'=>118,'qty_out'=>118,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picQC->id,'notes'=>'Lulus QC']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ2->id,'station_id'=>$qc->id,'qty_in'=>149,'qty_out'=>149,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picQC->id,'notes'=>'Lulus QC']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ3->id,'station_id'=>$qc->id,'qty_in'=>77,'qty_out'=>75,'qty_reject'=>2,'input_date'=>$d7,'created_by'=>$picQC->id,'notes'=>'2 pcs jahitan tidak standar — second quality']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ4->id,'station_id'=>$qc->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picQC->id,'notes'=>'Lulus QC']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ5->id,'station_id'=>$qc->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picQC->id,'notes'=>'Lulus QC']);
        // Gudang (in) + out (selesai)
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ1->id,'station_id'=>$wh->id,'qty_in'=>118,'qty_out'=>118,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$stfWH->id,'notes'=>'Masuk gudang']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ2->id,'station_id'=>$wh->id,'qty_in'=>149,'qty_out'=>149,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$stfWH->id,'notes'=>'Masuk gudang']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ3->id,'station_id'=>$wh->id,'qty_in'=>75,'qty_out'=>75,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$stfWH->id,'notes'=>'Masuk gudang']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ4->id,'station_id'=>$wh->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$stfWH->id,'notes'=>'Masuk gudang']);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skuJ5->id,'station_id'=>$wh->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$stfWH->id,'notes'=>'Masuk gudang']);

        // ── ORDER 2: Abaya Syari — sedang di QC ──
        // Cutting
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA1->id,'station_id'=>$cut->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA2->id,'station_id'=>$cut->id,'qty_in'=>120,'qty_out'=>120,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA3->id,'station_id'=>$cut->id,'qty_in'=>60,'qty_out'=>60,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA4->id,'station_id'=>$cut->id,'qty_in'=>50,'qty_out'=>50,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA5->id,'station_id'=>$cut->id,'qty_in'=>90,'qty_out'=>90,'qty_reject'=>0,'input_date'=>$d10,'created_by'=>$picCut->id]);
        // Sewing
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA1->id,'station_id'=>$sew->id,'qty_in'=>80,'qty_out'=>79,'qty_reject'=>1,'input_date'=>$d7,'created_by'=>$picSew->id,'notes'=>'1 pcs bordir meleset']);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA2->id,'station_id'=>$sew->id,'qty_in'=>120,'qty_out'=>120,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA3->id,'station_id'=>$sew->id,'qty_in'=>60,'qty_out'=>60,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA4->id,'station_id'=>$sew->id,'qty_in'=>50,'qty_out'=>50,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA5->id,'station_id'=>$sew->id,'qty_in'=>90,'qty_out'=>90,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picSew->id]);
        // Finishing
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA1->id,'station_id'=>$fin->id,'qty_in'=>79,'qty_out'=>79,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picFin->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA2->id,'station_id'=>$fin->id,'qty_in'=>120,'qty_out'=>120,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picFin->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA3->id,'station_id'=>$fin->id,'qty_in'=>60,'qty_out'=>60,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picFin->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA4->id,'station_id'=>$fin->id,'qty_in'=>50,'qty_out'=>50,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picFin->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA5->id,'station_id'=>$fin->id,'qty_in'=>90,'qty_out'=>90,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picFin->id]);
        // QC (in, belum out — masih dalam pemeriksaan)
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA1->id,'station_id'=>$qc->id,'qty_in'=>79,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$d2,'created_by'=>$picQC->id,'notes'=>'Sedang diperiksa']);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA2->id,'station_id'=>$qc->id,'qty_in'=>120,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$d2,'created_by'=>$picQC->id,'notes'=>'Sedang diperiksa']);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA3->id,'station_id'=>$qc->id,'qty_in'=>60,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$d2,'created_by'=>$picQC->id,'notes'=>'Sedang diperiksa']);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA4->id,'station_id'=>$qc->id,'qty_in'=>50,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$d2,'created_by'=>$picQC->id,'notes'=>'Sedang diperiksa']);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skuA5->id,'station_id'=>$qc->id,'qty_in'=>90,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$d2,'created_by'=>$picQC->id,'notes'=>'Sedang diperiksa']);

        // ── ORDER 3: Baju Koko Idul Fitri — TERLAMBAT, bottleneck di Sewing ──
        // Cutting (selesai)
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI1->id,'station_id'=>$cut->id,'qty_in'=>200,'qty_out'=>200,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI2->id,'station_id'=>$cut->id,'qty_in'=>250,'qty_out'=>250,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI3->id,'station_id'=>$cut->id,'qty_in'=>180,'qty_out'=>180,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI4->id,'station_id'=>$cut->id,'qty_in'=>120,'qty_out'=>118,'qty_reject'=>2,'input_date'=>$d7,'created_by'=>$picCut->id,'notes'=>'2 pcs kain robek']);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI5->id,'station_id'=>$cut->id,'qty_in'=>150,'qty_out'=>150,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI6->id,'station_id'=>$cut->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI7->id,'station_id'=>$cut->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>0,'input_date'=>$d7,'created_by'=>$picCut->id]);
        // Sewing (in semua, out baru sebagian — bottleneck!)
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI1->id,'station_id'=>$sew->id,'qty_in'=>200,'qty_out'=>80,'qty_reject'=>3,'input_date'=>$d5,'created_by'=>$picSew->id,'notes'=>'Progress lambat, kekurangan mesin']);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI2->id,'station_id'=>$sew->id,'qty_in'=>250,'qty_out'=>90,'qty_reject'=>2,'input_date'=>$d5,'created_by'=>$picSew->id,'notes'=>'Progress lambat']);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI3->id,'station_id'=>$sew->id,'qty_in'=>180,'qty_out'=>60,'qty_reject'=>1,'input_date'=>$d5,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI4->id,'station_id'=>$sew->id,'qty_in'=>118,'qty_out'=>40,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI5->id,'station_id'=>$sew->id,'qty_in'=>150,'qty_out'=>55,'qty_reject'=>1,'input_date'=>$d5,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI6->id,'station_id'=>$sew->id,'qty_in'=>100,'qty_out'=>30,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skuKI7->id,'station_id'=>$sew->id,'qty_in'=>80,'qty_out'=>25,'qty_reject'=>0,'input_date'=>$d5,'created_by'=>$picSew->id]);

        // ── ORDER 4: Mukena Travel — Baru di Cutting ──
        WipEntry::create(['production_order_id'=>$ord4->id,'sku_id'=>$skuMT1->id,'station_id'=>$cut->id,'qty_in'=>150,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$today,'created_by'=>$picCut->id,'notes'=>'Cutting hari ini']);
        WipEntry::create(['production_order_id'=>$ord4->id,'sku_id'=>$skuMT2->id,'station_id'=>$cut->id,'qty_in'=>180,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$today,'created_by'=>$picCut->id,'notes'=>'Cutting hari ini']);

        // ════════════════════════════════════════
        //  HANDOVERS
        // ════════════════════════════════════════
        // ── ORDER 1 (Jubah Ramadan): Chain lengkap semua stasiun ──
        $ho1 = Handover::create([
            'handover_no'=>'HO-2026-001','production_order_id'=>$ord1->id,
            'from_station_id'=>$cut->id,'to_station_id'=>$sew->id,'status'=>'confirmed',
            'initiated_by'=>$picCut->id,'confirmed_by'=>$picSew->id,
            'notes'=>'Cutting selesai, siap dijahit.','condition_notes'=>'Kondisi baik',
            'initiated_at'=>now()->subDays(12),'confirmed_at'=>now()->subDays(12)->addHours(3),
        ]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$skuJ1->id,'qty_sent'=>120,'qty_received'=>120,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$skuJ2->id,'qty_sent'=>150,'qty_received'=>150,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$skuJ3->id,'qty_sent'=>78,'qty_received'=>78,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$skuJ4->id,'qty_sent'=>100,'qty_received'=>100,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$skuJ5->id,'qty_sent'=>80,'qty_received'=>80,'qty_reject'=>0,'discrepancy'=>0]);

        $ho2 = Handover::create([
            'handover_no'=>'HO-2026-002','production_order_id'=>$ord1->id,
            'from_station_id'=>$sew->id,'to_station_id'=>$fin->id,'status'=>'confirmed',
            'initiated_by'=>$picSew->id,'confirmed_by'=>$picFin->id,
            'notes'=>'Jahitan selesai. 3 pcs second quality dipisah.','condition_notes'=>'Baik, ada sedikit benang sisa',
            'initiated_at'=>now()->subDays(10),'confirmed_at'=>now()->subDays(10)->addHours(2),
        ]);
        HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$skuJ1->id,'qty_sent'=>118,'qty_received'=>118,'qty_reject'=>2,'reject_type'=>'second','reject_notes'=>'Jahitan tidak rapi','discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$skuJ2->id,'qty_sent'=>149,'qty_received'=>149,'qty_reject'=>1,'reject_type'=>'second','reject_notes'=>'Lubang kancing tidak presisi','discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$skuJ3->id,'qty_sent'=>78,'qty_received'=>78,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$skuJ4->id,'qty_sent'=>100,'qty_received'=>100,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$skuJ5->id,'qty_sent'=>80,'qty_received'=>80,'qty_reject'=>0,'discrepancy'=>0]);

        $ho3 = Handover::create([
            'handover_no'=>'HO-2026-003','production_order_id'=>$ord1->id,
            'from_station_id'=>$fin->id,'to_station_id'=>$qc->id,'status'=>'confirmed',
            'initiated_by'=>$picFin->id,'confirmed_by'=>$picQC->id,
            'notes'=>'Finishing selesai. Siap QC.','condition_notes'=>'Kondisi baik, sudah disetrika',
            'initiated_at'=>now()->subDays(7),'confirmed_at'=>now()->subDays(7)->addHours(4),
        ]);
        HandoverItem::create(['handover_id'=>$ho3->id,'sku_id'=>$skuJ1->id,'qty_sent'=>118,'qty_received'=>118,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho3->id,'sku_id'=>$skuJ2->id,'qty_sent'=>149,'qty_received'=>149,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho3->id,'sku_id'=>$skuJ3->id,'qty_sent'=>77,'qty_received'=>77,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho3->id,'sku_id'=>$skuJ4->id,'qty_sent'=>100,'qty_received'=>100,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho3->id,'sku_id'=>$skuJ5->id,'qty_sent'=>80,'qty_received'=>80,'qty_reject'=>0,'discrepancy'=>0]);

        $ho4 = Handover::create([
            'handover_no'=>'HO-2026-004','production_order_id'=>$ord1->id,
            'from_station_id'=>$qc->id,'to_station_id'=>$wh->id,'status'=>'approved',
            'initiated_by'=>$picQC->id,'confirmed_by'=>$stfWH->id,'approved_by'=>$super->id,
            'notes'=>'Lulus QC. 2 pcs second quality Jubah Putih XL dipisah.','condition_notes'=>'Produk siap kirim',
            'initiated_at'=>now()->subDays(5),'confirmed_at'=>now()->subDays(5)->addHours(2),
        ]);
        HandoverItem::create(['handover_id'=>$ho4->id,'sku_id'=>$skuJ1->id,'qty_sent'=>118,'qty_received'=>118,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho4->id,'sku_id'=>$skuJ2->id,'qty_sent'=>149,'qty_received'=>149,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho4->id,'sku_id'=>$skuJ3->id,'qty_sent'=>77,'qty_received'=>75,'qty_reject'=>2,'reject_type'=>'second','reject_notes'=>'Ukuran sedikit tidak standar','discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho4->id,'sku_id'=>$skuJ4->id,'qty_sent'=>100,'qty_received'=>100,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho4->id,'sku_id'=>$skuJ5->id,'qty_sent'=>80,'qty_received'=>80,'qty_reject'=>0,'discrepancy'=>0]);

        // ── ORDER 2 (Abaya Syari): Sampai di QC, handover pending ke Gudang ──
        $ho5 = Handover::create([
            'handover_no'=>'HO-2026-005','production_order_id'=>$ord2->id,
            'from_station_id'=>$cut->id,'to_station_id'=>$sew->id,'status'=>'confirmed',
            'initiated_by'=>$picCut->id,'confirmed_by'=>$picSew->id,
            'notes'=>'Abaya cutting selesai.','condition_notes'=>'Baik',
            'initiated_at'=>now()->subDays(9),'confirmed_at'=>now()->subDays(9)->addHours(2),
        ]);
        HandoverItem::create(['handover_id'=>$ho5->id,'sku_id'=>$skuA1->id,'qty_sent'=>80,'qty_received'=>80,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho5->id,'sku_id'=>$skuA2->id,'qty_sent'=>120,'qty_received'=>120,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho5->id,'sku_id'=>$skuA3->id,'qty_sent'=>60,'qty_received'=>60,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho5->id,'sku_id'=>$skuA4->id,'qty_sent'=>50,'qty_received'=>50,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho5->id,'sku_id'=>$skuA5->id,'qty_sent'=>90,'qty_received'=>90,'qty_reject'=>0,'discrepancy'=>0]);

        $ho6 = Handover::create([
            'handover_no'=>'HO-2026-006','production_order_id'=>$ord2->id,
            'from_station_id'=>$sew->id,'to_station_id'=>$fin->id,'status'=>'confirmed',
            'initiated_by'=>$picSew->id,'confirmed_by'=>$picFin->id,
            'notes'=>'Sewing selesai.','condition_notes'=>'Baik, bordir rapi',
            'initiated_at'=>now()->subDays(6),'confirmed_at'=>now()->subDays(6)->addHours(3),
        ]);
        HandoverItem::create(['handover_id'=>$ho6->id,'sku_id'=>$skuA1->id,'qty_sent'=>79,'qty_received'=>79,'qty_reject'=>1,'reject_type'=>'rework','reject_notes'=>'Bordir meleset, dikembalikan untuk diperbaiki','discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho6->id,'sku_id'=>$skuA2->id,'qty_sent'=>120,'qty_received'=>120,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho6->id,'sku_id'=>$skuA3->id,'qty_sent'=>60,'qty_received'=>60,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho6->id,'sku_id'=>$skuA4->id,'qty_sent'=>50,'qty_received'=>50,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho6->id,'sku_id'=>$skuA5->id,'qty_sent'=>90,'qty_received'=>90,'qty_reject'=>0,'discrepancy'=>0]);

        $ho7 = Handover::create([
            'handover_no'=>'HO-2026-007','production_order_id'=>$ord2->id,
            'from_station_id'=>$fin->id,'to_station_id'=>$qc->id,'status'=>'confirmed',
            'initiated_by'=>$picFin->id,'confirmed_by'=>$picQC->id,
            'notes'=>'Finishing selesai. Siap QC.','condition_notes'=>'Sudah disetrika dan dilipat rapi',
            'initiated_at'=>now()->subDays(3),'confirmed_at'=>now()->subDays(2)->addHours(1),
        ]);
        HandoverItem::create(['handover_id'=>$ho7->id,'sku_id'=>$skuA1->id,'qty_sent'=>79,'qty_received'=>79,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho7->id,'sku_id'=>$skuA2->id,'qty_sent'=>120,'qty_received'=>120,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho7->id,'sku_id'=>$skuA3->id,'qty_sent'=>60,'qty_received'=>60,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho7->id,'sku_id'=>$skuA4->id,'qty_sent'=>50,'qty_received'=>50,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho7->id,'sku_id'=>$skuA5->id,'qty_sent'=>90,'qty_received'=>90,'qty_reject'=>0,'discrepancy'=>0]);

        // ── ORDER 3 (Baju Koko): Cutting→Sewing confirmed, Sewing belum kirim ke Finishing ──
        $ho8 = Handover::create([
            'handover_no'=>'HO-2026-008','production_order_id'=>$ord3->id,
            'from_station_id'=>$cut->id,'to_station_id'=>$sew->id,'status'=>'confirmed',
            'initiated_by'=>$picCut->id,'confirmed_by'=>$picSew->id,
            'notes'=>'Cutting Baju Koko selesai. Total 1075 pcs.','condition_notes'=>'Baik, sudah dibundel per SKU',
            'initiated_at'=>now()->subDays(6),'confirmed_at'=>now()->subDays(5)->addHours(1),
        ]);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skuKI1->id,'qty_sent'=>200,'qty_received'=>200,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skuKI2->id,'qty_sent'=>250,'qty_received'=>250,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skuKI3->id,'qty_sent'=>180,'qty_received'=>180,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skuKI4->id,'qty_sent'=>118,'qty_received'=>115,'qty_reject'=>0,'discrepancy'=>-3,'discrepancy_notes'=>'3 pcs hilang saat transport antar stasiun']);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skuKI5->id,'qty_sent'=>150,'qty_received'=>150,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skuKI6->id,'qty_sent'=>100,'qty_received'=>100,'qty_reject'=>0,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skuKI7->id,'qty_sent'=>80,'qty_received'=>80,'qty_reject'=>0,'discrepancy'=>0]);

        // Handover dari Sewing ke Finishing (baru dikirim, pending — menunggu konfirmasi)
        $ho9 = Handover::create([
            'handover_no'=>'HO-2026-009','production_order_id'=>$ord3->id,
            'from_station_id'=>$sew->id,'to_station_id'=>$fin->id,'status'=>'pending',
            'initiated_by'=>$picSew->id,
            'notes'=>'Sebagian unit Baju Koko yang sudah selesai dijahit. Sisanya masih dalam proses.',
            'initiated_at'=>now()->subHours(3),
        ]);
        HandoverItem::create(['handover_id'=>$ho9->id,'sku_id'=>$skuKI1->id,'qty_sent'=>77]);
        HandoverItem::create(['handover_id'=>$ho9->id,'sku_id'=>$skuKI2->id,'qty_sent'=>88]);
        HandoverItem::create(['handover_id'=>$ho9->id,'sku_id'=>$skuKI3->id,'qty_sent'=>59]);
        HandoverItem::create(['handover_id'=>$ho9->id,'sku_id'=>$skuKI5->id,'qty_sent'=>54]);

        // ── ORDER 4 (Mukena): Masih di Cutting, belum ada handover ──

        // ════════════════════════════════════════
        //  QC INSPECTIONS
        // ════════════════════════════════════════
        // QC untuk Order 1 (Jubah Ramadan) — PASS
        $qcInsp1 = QcInspection::create([
            'production_order_id'=>$ord1->id,'inspector_id'=>$picQC->id,
            'inspected_at'=>now()->subDays(5),'status'=>'pass',
            'total_checked'=>522,'total_defect'=>5,'defect_rate'=>0.96,
            'notes'=>'Produk secara keseluruhan bagus. 5 pcs second quality dipisahkan. Disetujui untuk masuk gudang.',
        ]);
        $qcChecks1 = [
            ['Jahitan rapi dan kuat','ok'],['Tidak ada benang sisa terlihat','ok'],
            ['Warna sesuai standar DTHREE','ok'],['Ukuran sesuai spesifikasi desain','ok'],
            ['Label merk terpasang dengan benar','ok'],['Kemasan bersih dan rapi','ok'],
            ['Tidak ada cacat fisik pada kain','ok'],['Kancing/karet berfungsi normal','ok'],
        ];
        foreach ($qcChecks1 as $ck) {
            QcChecklistItem::create(['qc_inspection_id'=>$qcInsp1->id,'checklist_item'=>$ck[0],'result'=>$ck[1],'notes'=>'']);
        }

        // QC untuk Order 2 (Abaya Syari) — CONDITIONAL (masih berlangsung)
        $qcInsp2 = QcInspection::create([
            'production_order_id'=>$ord2->id,'inspector_id'=>$picQC->id,
            'inspected_at'=>now()->subDays(1),'status'=>'conditional',
            'total_checked'=>399,'total_defect'=>8,'defect_rate'=>2.01,
            'notes'=>'Mayoritas produk memenuhi standar. 8 pcs perlu pengecekan bordir ulang. Conditionally approved.',
        ]);
        $qcChecks2 = [
            ['Jahitan rapi dan kuat','ok'],['Tidak ada benang sisa terlihat','ok'],
            ['Warna sesuai standar DTHREE','ok'],['Ukuran sesuai spesifikasi desain','ok'],
            ['Label merk terpasang dengan benar','ok'],['Kemasan bersih dan rapi','ok'],
            ['Tidak ada cacat fisik pada kain','fail'],['Kancing/karet berfungsi normal','ok'],
        ];
        foreach ($qcChecks2 as $ck) {
            QcChecklistItem::create(['qc_inspection_id'=>$qcInsp2->id,'checklist_item'=>$ck[0],'result'=>$ck[1],'notes'=>$ck[1]==='fail'?'8 pcs ditemukan serat kain terlepas pada bagian bordir':'']);
        }

        // ════════════════════════════════════════
        //  BUDGETS
        // ════════════════════════════════════════
        Budget::create([
            'production_order_id'=>$ord1->id,
            'material_cost_plan'=>28500000,'process_cost_plan'=>12000000,'overhead_cost_plan'=>3000000,'total_plan'=>43500000,
            'material_cost_actual'=>27200000,'process_cost_actual'=>11500000,'overhead_cost_actual'=>2800000,'total_actual'=>41500000,
            'created_by'=>$super->id,
        ]);
        Budget::create([
            'production_order_id'=>$ord2->id,
            'material_cost_plan'=>38000000,'process_cost_plan'=>15000000,'overhead_cost_plan'=>4000000,'total_plan'=>57000000,
            'material_cost_actual'=>36500000,'process_cost_actual'=>14200000,'overhead_cost_actual'=>3600000,'total_actual'=>54300000,
            'created_by'=>$super->id,
        ]);
        Budget::create([
            'production_order_id'=>$ord3->id,
            'material_cost_plan'=>55000000,'process_cost_plan'=>22000000,'overhead_cost_plan'=>5000000,'total_plan'=>82000000,
            'material_cost_actual'=>54000000,'process_cost_actual'=>25500000,'overhead_cost_actual'=>6200000,'total_actual'=>85700000,
            'created_by'=>$super->id,
        ]);
        Budget::create([
            'production_order_id'=>$ord4->id,
            'material_cost_plan'=>22000000,'process_cost_plan'=>9500000,'overhead_cost_plan'=>2500000,'total_plan'=>34000000,
            'material_cost_actual'=>8000000,'process_cost_actual'=>1500000,'overhead_cost_actual'=>500000,'total_actual'=>10000000,
            'created_by'=>$super->id,
        ]);

        // ════════════════════════════════════════
        //  NOTIFICATIONS
        // ════════════════════════════════════════
        $notifs = [
            // Admin
            [$admin->id,'Stok Kain Rayon Navy Kritis','Stok Kain Rayon Viscose Navy (95 meter) jauh di bawah minimum (300 meter). Segera order ke PT Tekstil Nusantara.','danger','/bahan-baku'],
            [$admin->id,'Stok Benang Jahit Hampir Habis','Stok Benang Jahit No.40 (52 cone) mendekati minimum (80 cone). Perlu restok segera.','warning','/bahan-baku'],
            [$admin->id,'Stok Zipper YKK Rendah','Stok Zipper YKK 20cm (120 pcs) di bawah minimum (300 pcs).','warning','/bahan-baku'],
            [$admin->id,'Stok Label DTHREE Mendekati Minimum','Stok Label Merk (680 pcs) mendekati batas minimum (1000 pcs).','warning','/bahan-baku'],
            // Supervisor
            [$super->id,'Handover Menunggu Konfirmasi','HO-2026-009: Baju Koko dari Sewing ke Finishing belum dikonfirmasi sejak 3 jam lalu.','warning','/handover/9'],
            [$super->id,'ORD-2026-003 TERLAMBAT 2 Hari','Order Baju Koko Idul Fitri sudah melewati deadline. Sewing masih bottleneck (progress 30%).','danger','/orders/3'],
            [$super->id,'Budget ORD-2026-003 Melebihi Rencana','Realisasi biaya Baju Koko Rp 85,7 juta vs rencana Rp 82 juta (+4.5%). Faktor utama: overtime sewing.','warning','/budget/3'],
            [$super->id,'QC Abaya Syari: Status Conditional','Inspeksi QC-002 Abaya Syari Elegance berstatus Conditional. 8 pcs perlu re-inspeksi bordir.','warning','/qc/2'],
            [$super->id,'Order ORD-2026-001 Berhasil Diselesaikan','Jubah Ramadan Collection (530 pcs) berhasil diselesaikan dan masuk gudang. Siap pengiriman ke buyer.','success','/orders/1'],
            // PIC Finishing
            [$picFin->id,'Handover Masuk — Perlu Konfirmasi Segera','HO-2026-009 dari Sewing sudah dikirim. 278 pcs Baju Koko menunggu konfirmasi penerimaan Anda.','warning','/handover/9'],
            // PIC QC
            [$picQC->id,'Handover Abaya ke QC Dikonfirmasi','HO-2026-007 Abaya Syari (399 pcs) telah masuk QC. Segera lakukan pemeriksaan.','info','/handover/7'],
        ];
        foreach ($notifs as $n) {
            Notification::create(['user_id'=>$n[0],'title'=>$n[1],'message'=>$n[2],'type'=>$n[3],'link'=>$n[4],'is_read'=>false]);
        }
        // Beberapa notif sudah dibaca (simulasi penggunaan nyata)
        Notification::where('user_id', $super->id)->whereIn('title',['Order ORD-2026-001 Berhasil Diselesaikan'])->update(['is_read'=>true]);
    }
}
