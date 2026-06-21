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
use App\Models\SewingLocation;
use App\Models\BomItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $isMySQL = DB::getDriverName() === 'mysql';
        if ($isMySQL) DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate all tables
        DB::table('notifications')->truncate();
        DB::table('qc_checklist_items')->truncate();
        DB::table('qc_inspections')->truncate();
        DB::table('cutting_bundles')->truncate();
        DB::table('cutting_plans')->truncate();
        DB::table('wip_entries')->truncate();
        DB::table('handover_items')->truncate();
        DB::table('handovers')->truncate();
        DB::table('production_order_items')->truncate();
        DB::table('production_orders')->truncate();
        DB::table('budgets')->truncate();
        DB::table('material_receipts')->truncate();
        DB::table('purchase_order_items')->truncate();
        DB::table('purchase_orders')->truncate();
        DB::table('bom_items')->truncate();
        DB::table('skus')->truncate();
        DB::table('series')->truncate();
        DB::table('raw_materials')->truncate();
        DB::table('suppliers')->truncate();
        DB::table('sewing_locations')->truncate();
        DB::table('users')->truncate();
        DB::table('stations')->truncate();
        DB::table('products')->truncate();
        DB::table('colors')->truncate();
        DB::table('sizes')->truncate();

        if ($isMySQL) DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ─────────────────────────────────────────────
        // STASIUN
        // ─────────────────────────────────────────────
        $stCut = Station::create(['name'=>'Cutting','code'=>'CUT','order_sequence'=>1,'bottleneck_threshold'=>500,'is_active'=>true,'is_final'=>false]);
        $stSew = Station::create(['name'=>'Sewing','code'=>'SEW','order_sequence'=>2,'bottleneck_threshold'=>400,'is_active'=>true,'is_final'=>false]);
        $stFin = Station::create(['name'=>'Finishing','code'=>'FIN','order_sequence'=>3,'bottleneck_threshold'=>300,'is_active'=>true,'is_final'=>false]);
        $stQc  = Station::create(['name'=>'Quality Control','code'=>'QC','order_sequence'=>4,'bottleneck_threshold'=>200,'is_active'=>true,'is_final'=>false]);
        $stGdg = Station::create(['name'=>'Gudang','code'=>'GDG','order_sequence'=>5,'bottleneck_threshold'=>1000,'is_active'=>true,'is_final'=>true]);

        // ─────────────────────────────────────────────
        // TEMPAT SEWING
        // ─────────────────────────────────────────────
        $swLoc1 = SewingLocation::create(['name'=>'Sewing Internal Lt. 1','code'=>'INT-1','address'=>'Lantai 1 Gedung Produksi, Jl. Industri No.12, Bandung','capacity'=>80,'is_active'=>true]);
        $swLoc2 = SewingLocation::create(['name'=>'Sewing Internal Lt. 2','code'=>'INT-2','address'=>'Lantai 2 Gedung Produksi, Jl. Industri No.12, Bandung','capacity'=>60,'is_active'=>true]);
        $swLoc3 = SewingLocation::create(['name'=>'Makloon CV Berkah Jaya','code'=>'MKL-1','address'=>'Jl. Rancaekek No.45, Kabupaten Bandung','capacity'=>150,'is_active'=>true]);
        $swLoc4 = SewingLocation::create(['name'=>'Makloon UD Karya Mandiri','code'=>'MKL-2','address'=>'Jl. Cimahi Tengah No.88, Cimahi','capacity'=>100,'is_active'=>true]);
        $swLoc5 = SewingLocation::create(['name'=>'Sewing Khusus Bordir','code'=>'BRD-1','address'=>'Jl. Cigondewah No.23, Bandung','capacity'=>40,'is_active'=>true]);

        // ─────────────────────────────────────────────
        // SUPPLIER
        // ─────────────────────────────────────────────
        $sup1 = Supplier::create(['name'=>'PT Tekstil Nusantara','code'=>'SUP-001','contact_person'=>'Bapak Hendra Wijaya','phone'=>'08111234567','email'=>'hendra@tekstilnusantara.com','address'=>'Jl. Tekstil No.1, Bandung','is_active'=>true]);
        $sup2 = Supplier::create(['name'=>'CV Kain Berkah','code'=>'SUP-002','contact_person'=>'Ibu Siti Aminah','phone'=>'08222345678','email'=>'siti@kainberkah.com','address'=>'Jl. Pasar Baru No.55, Jakarta','is_active'=>true]);
        $sup3 = Supplier::create(['name'=>'UD Aksesoris Garmen Jaya','code'=>'SUP-003','contact_person'=>'Bapak Dodi Santoso','phone'=>'08333456789','email'=>'dodi@aksesorisjaya.com','address'=>'Jl. Garmen No.77, Surabaya','is_active'=>true]);

        // ─────────────────────────────────────────────
        // BAHAN BAKU
        // ─────────────────────────────────────────────
        $rmKainWolly  = RawMaterial::create(['code'=>'BHN-001','name'=>'Kain Wolly Crepe','unit'=>'meter','category'=>'kain','color'=>'Putih','min_stock'=>100,'current_stock'=>280,'unit_price'=>45000,'is_active'=>true,'supplier_id'=>$sup1->id]);
        $rmKainVoal   = RawMaterial::create(['code'=>'BHN-002','name'=>'Kain Voal Premium','unit'=>'meter','category'=>'kain','color'=>'Putih','min_stock'=>80,'current_stock'=>195,'unit_price'=>38000,'is_active'=>true,'supplier_id'=>$sup1->id]);
        $rmKainKatun  = RawMaterial::create(['code'=>'BHN-003','name'=>'Kain Katun Combed','unit'=>'meter','category'=>'kain','color'=>'Putih','min_stock'=>120,'current_stock'=>310,'unit_price'=>32000,'is_active'=>true,'supplier_id'=>$sup2->id]);
        $rmKainRayon  = RawMaterial::create(['code'=>'BHN-004','name'=>'Kain Rayon Viscose','unit'=>'meter','category'=>'kain','color'=>'Putih','min_stock'=>60,'current_stock'=>45,'unit_price'=>28000,'is_active'=>true,'supplier_id'=>$sup2->id]);
        $rmBenang     = RawMaterial::create(['code'=>'BHN-005','name'=>'Benang Jahit Polyester','unit'=>'lusin','category'=>'benang','color'=>'Aneka','min_stock'=>20,'current_stock'=>48,'unit_price'=>25000,'is_active'=>true,'supplier_id'=>$sup3->id]);
        $rmKancing    = RawMaterial::create(['code'=>'BHN-006','name'=>'Kancing Baju','unit'=>'gross','category'=>'aksesoris','color'=>'Putih','min_stock'=>10,'current_stock'=>24,'unit_price'=>15000,'is_active'=>true,'supplier_id'=>$sup3->id]);
        $rmResleting  = RawMaterial::create(['code'=>'BHN-007','name'=>'Resleting YKK 60cm','unit'=>'pcs','category'=>'aksesoris','color'=>'Hitam','min_stock'=>200,'current_stock'=>580,'unit_price'=>3500,'is_active'=>true,'supplier_id'=>$sup3->id]);
        $rmLabel      = RawMaterial::create(['code'=>'BHN-008','name'=>'Label Merek Woven','unit'=>'pcs','category'=>'aksesoris','color'=>'-','min_stock'=>500,'current_stock'=>1200,'unit_price'=>500,'is_active'=>true,'supplier_id'=>$sup3->id]);
        $rmPlastik    = RawMaterial::create(['code'=>'BHN-009','name'=>'Plastik Kemasan OPP','unit'=>'pcs','category'=>'aksesoris','color'=>'-','min_stock'=>300,'current_stock'=>720,'unit_price'=>800,'is_active'=>true,'supplier_id'=>$sup3->id]);
        $rmInterlining= RawMaterial::create(['code'=>'BHN-010','name'=>'Kain Interlining/Keras','unit'=>'meter','category'=>'kain','color'=>'Putih','min_stock'=>30,'current_stock'=>18,'unit_price'=>12000,'is_active'=>true,'supplier_id'=>$sup1->id]);

        // ─────────────────────────────────────────────
        // USERS
        // ─────────────────────────────────────────────
        $uAdmin = User::create(['name'=>'Ahmad Fauzi','email'=>'admin@dthree.id','password'=>Hash::make('password'),'role'=>'admin','phone'=>'08100000001','station_id'=>null]);
        $uSupv  = User::create(['name'=>'Budi Santoso','email'=>'supervisor@dthree.id','password'=>Hash::make('password'),'role'=>'supervisor','phone'=>'08100000002','station_id'=>null]);
        $uMgr   = User::create(['name'=>'Citra Dewi','email'=>'manager@dthree.id','password'=>Hash::make('password'),'role'=>'manager','phone'=>'08100000003','station_id'=>null]);
        $uCut   = User::create(['name'=>'Deni Kurniawan','email'=>'cutting@dthree.id','password'=>Hash::make('password'),'role'=>'staff_produksi','phone'=>'08100000004','station_id'=>$stCut->id]);
        $uSew   = User::create(['name'=>'Eka Rahayu','email'=>'sewing@dthree.id','password'=>Hash::make('password'),'role'=>'staff_produksi','phone'=>'08100000005','station_id'=>$stSew->id]);
        $uFin   = User::create(['name'=>'Fira Anggraini','email'=>'finishing@dthree.id','password'=>Hash::make('password'),'role'=>'staff_produksi','phone'=>'08100000006','station_id'=>$stFin->id]);
        $uQc    = User::create(['name'=>'Galih Prasetyo','email'=>'qc@dthree.id','password'=>Hash::make('password'),'role'=>'staff_produksi','phone'=>'08100000007','station_id'=>$stQc->id]);
        $uGdg   = User::create(['name'=>'Hani Widiastuti','email'=>'gudang@dthree.id','password'=>Hash::make('password'),'role'=>'staff_gudang','phone'=>'08100000008','station_id'=>$stGdg->id]);
        $uPic2  = User::create(['name'=>'Irfan Hakim','email'=>'sewing2@dthree.id','password'=>Hash::make('password'),'role'=>'staff_produksi','phone'=>'08100000009','station_id'=>$stSew->id]);

        // ─────────────────────────────────────────────
        // PRODUK, WARNA, UKURAN, SERIES, SKU
        // ─────────────────────────────────────────────
        $pJubah  = Product::create(['code'=>'PRD-001','name'=>'Jubah Pria','category'=>'jubah','is_active'=>true]);
        $pAbaya  = Product::create(['code'=>'PRD-002','name'=>'Abaya Wanita','category'=>'abaya','is_active'=>true]);
        $pMukena = Product::create(['code'=>'PRD-003','name'=>'Mukena Dewasa','category'=>'mukena','is_active'=>true]);
        $pKoko   = Product::create(['code'=>'PRD-004','name'=>'Baju Koko','category'=>'koko','is_active'=>true]);

        $cPutih  = Color::create(['name'=>'Putih','code'=>'WHT','hex_code'=>'#FFFFFF','is_active'=>true]);
        $cHitam  = Color::create(['name'=>'Hitam','code'=>'BLK','hex_code'=>'#000000','is_active'=>true]);
        $cNavy   = Color::create(['name'=>'Navy Blue','code'=>'NVY','hex_code'=>'#1B2A4A','is_active'=>true]);
        $cMaroon = Color::create(['name'=>'Maroon','code'=>'MRN','hex_code'=>'#800000','is_active'=>true]);
        $cOlive  = Color::create(['name'=>'Olive Green','code'=>'OLV','hex_code'=>'#556B2F','is_active'=>true]);
        $cCream  = Color::create(['name'=>'Cream','code'=>'CRM','hex_code'=>'#FFFDD0','is_active'=>true]);
        $cAbu    = Color::create(['name'=>'Abu-abu','code'=>'GRY','hex_code'=>'#808080','is_active'=>true]);
        $cMocca  = Color::create(['name'=>'Mocca','code'=>'MCC','hex_code'=>'#6B4226','is_active'=>true]);

        $sS   = Size::create(['name'=>'S','sort_order'=>1,'is_active'=>true]);
        $sM   = Size::create(['name'=>'M','sort_order'=>2,'is_active'=>true]);
        $sL   = Size::create(['name'=>'L','sort_order'=>3,'is_active'=>true]);
        $sXL  = Size::create(['name'=>'XL','sort_order'=>4,'is_active'=>true]);
        $sXXL = Size::create(['name'=>'XXL','sort_order'=>5,'is_active'=>true]);
        $s3XL = Size::create(['name'=>'XXXL','sort_order'=>6,'is_active'=>true]);

        // Series
        $serJubahRamadan = Series::create(['product_id'=>$pJubah->id,'name'=>'Jubah Ramadan Premium 2026','code'=>'JBH-RAM-26','is_active'=>true]);
        $serJubahClassic = Series::create(['product_id'=>$pJubah->id,'name'=>'Jubah Classic Everyday','code'=>'JBH-CLS-26','is_active'=>true]);
        $serAbayaSyari   = Series::create(['product_id'=>$pAbaya->id,'name'=>'Abaya Syari Collection','code'=>'ABY-SYR-26','is_active'=>true]);
        $serAbayaModern  = Series::create(['product_id'=>$pAbaya->id,'name'=>'Abaya Modern Bordir','code'=>'ABY-BRD-26','is_active'=>true]);
        $serMukenaPremium= Series::create(['product_id'=>$pMukena->id,'name'=>'Mukena Voal Premium','code'=>'MKN-VOL-26','is_active'=>true]);
        $serMukenaTravel = Series::create(['product_id'=>$pMukena->id,'name'=>'Mukena Travel Series','code'=>'MKN-TRV-26','is_active'=>true]);
        $serKokoSlim     = Series::create(['product_id'=>$pKoko->id,'name'=>'Koko Slim Fit Modern','code'=>'KKO-SLM-26','is_active'=>true]);
        $serKokoClassic  = Series::create(['product_id'=>$pKoko->id,'name'=>'Koko Classic Lengan Panjang','code'=>'KKO-CLS-26','is_active'=>true]);

        // SKU builder helper
        $skuData = [
            // Jubah Ramadan: Putih, Hitam, Navy — S,M,L,XL,XXL
            [$pJubah->id,$serJubahRamadan->id,$cPutih->id,$sM->id,'SKU-JBH-RAM-WHT-M',235000],
            [$pJubah->id,$serJubahRamadan->id,$cPutih->id,$sL->id,'SKU-JBH-RAM-WHT-L',235000],
            [$pJubah->id,$serJubahRamadan->id,$cPutih->id,$sXL->id,'SKU-JBH-RAM-WHT-XL',240000],
            [$pJubah->id,$serJubahRamadan->id,$cHitam->id,$sM->id,'SKU-JBH-RAM-BLK-M',235000],
            [$pJubah->id,$serJubahRamadan->id,$cHitam->id,$sL->id,'SKU-JBH-RAM-BLK-L',235000],
            [$pJubah->id,$serJubahRamadan->id,$cNavy->id,$sL->id,'SKU-JBH-RAM-NVY-L',240000],
            // Jubah Classic: Putih, Cream, Maroon
            [$pJubah->id,$serJubahClassic->id,$cPutih->id,$sL->id,'SKU-JBH-CLS-WHT-L',195000],
            [$pJubah->id,$serJubahClassic->id,$cCream->id,$sL->id,'SKU-JBH-CLS-CRM-L',195000],
            [$pJubah->id,$serJubahClassic->id,$cMaroon->id,$sXL->id,'SKU-JBH-CLS-MRN-XL',200000],
            // Abaya Syari: Hitam, Navy, Maroon
            [$pAbaya->id,$serAbayaSyari->id,$cHitam->id,$sM->id,'SKU-ABY-SYR-BLK-M',285000],
            [$pAbaya->id,$serAbayaSyari->id,$cHitam->id,$sL->id,'SKU-ABY-SYR-BLK-L',285000],
            [$pAbaya->id,$serAbayaSyari->id,$cNavy->id,$sL->id,'SKU-ABY-SYR-NVY-L',290000],
            [$pAbaya->id,$serAbayaSyari->id,$cMaroon->id,$sXL->id,'SKU-ABY-SYR-MRN-XL',295000],
            // Abaya Modern Bordir: Hitam, Olive
            [$pAbaya->id,$serAbayaModern->id,$cHitam->id,$sM->id,'SKU-ABY-BRD-BLK-M',350000],
            [$pAbaya->id,$serAbayaModern->id,$cOlive->id,$sL->id,'SKU-ABY-BRD-OLV-L',355000],
            // Mukena Voal: Putih, Cream, Abu
            [$pMukena->id,$serMukenaPremium->id,$cPutih->id,$sM->id,'SKU-MKN-VOL-WHT-M',185000],
            [$pMukena->id,$serMukenaPremium->id,$cPutih->id,$sL->id,'SKU-MKN-VOL-WHT-L',185000],
            [$pMukena->id,$serMukenaPremium->id,$cCream->id,$sL->id,'SKU-MKN-VOL-CRM-L',190000],
            [$pMukena->id,$serMukenaPremium->id,$cAbu->id,$sL->id,'SKU-MKN-VOL-GRY-L',190000],
            // Mukena Travel: Putih, Cream
            [$pMukena->id,$serMukenaTravel->id,$cPutih->id,$sM->id,'SKU-MKN-TRV-WHT-M',220000],
            [$pMukena->id,$serMukenaTravel->id,$cCream->id,$sL->id,'SKU-MKN-TRV-CRM-L',225000],
            // Koko Slim: Putih, Navy, Abu, Mocca
            [$pKoko->id,$serKokoSlim->id,$cPutih->id,$sM->id,'SKU-KKO-SLM-WHT-M',165000],
            [$pKoko->id,$serKokoSlim->id,$cPutih->id,$sL->id,'SKU-KKO-SLM-WHT-L',165000],
            [$pKoko->id,$serKokoSlim->id,$cNavy->id,$sL->id,'SKU-KKO-SLM-NVY-L',170000],
            [$pKoko->id,$serKokoSlim->id,$cAbu->id,$sXL->id,'SKU-KKO-SLM-GRY-XL',175000],
            [$pKoko->id,$serKokoSlim->id,$cMocca->id,$sL->id,'SKU-KKO-SLM-MCC-L',170000],
            // Koko Classic: Putih, Hitam, Navy
            [$pKoko->id,$serKokoClassic->id,$cPutih->id,$sL->id,'SKU-KKO-CLS-WHT-L',175000],
            [$pKoko->id,$serKokoClassic->id,$cHitam->id,$sL->id,'SKU-KKO-CLS-BLK-L',175000],
            [$pKoko->id,$serKokoClassic->id,$cNavy->id,$sXL->id,'SKU-KKO-CLS-NVY-XL',180000],
        ];

        $skus = [];
        foreach ($skuData as $d) {
            $skus[$d[4]] = Sku::create(['product_id'=>$d[0],'series_id'=>$d[1],'color_id'=>$d[2],'size_id'=>$d[3],'sku_code'=>$d[4],'is_active'=>true]);
        }

        // ─────────────────────────────────────────────
        // BOM (Bill of Materials)
        // ─────────────────────────────────────────────
        // Jubah Pria
        BomItem::create(['product_id'=>$pJubah->id,'raw_material_id'=>$rmKainWolly->id,'qty_per_unit'=>2.5,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pJubah->id,'raw_material_id'=>$rmBenang->id,'qty_per_unit'=>0.1,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pJubah->id,'raw_material_id'=>$rmKancing->id,'qty_per_unit'=>0.05,'waste_percentage'=>0]);
        BomItem::create(['product_id'=>$pJubah->id,'raw_material_id'=>$rmLabel->id,'qty_per_unit'=>1,'waste_percentage'=>0]);
        BomItem::create(['product_id'=>$pJubah->id,'raw_material_id'=>$rmPlastik->id,'qty_per_unit'=>1,'waste_percentage'=>0]);

        // Abaya Wanita
        BomItem::create(['product_id'=>$pAbaya->id,'raw_material_id'=>$rmKainWolly->id,'qty_per_unit'=>3.0,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pAbaya->id,'raw_material_id'=>$rmKainVoal->id,'qty_per_unit'=>1.0,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pAbaya->id,'raw_material_id'=>$rmResleting->id,'qty_per_unit'=>1,'waste_percentage'=>0]);
        BomItem::create(['product_id'=>$pAbaya->id,'raw_material_id'=>$rmBenang->id,'qty_per_unit'=>0.12,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pAbaya->id,'raw_material_id'=>$rmLabel->id,'qty_per_unit'=>1,'waste_percentage'=>0]);
        BomItem::create(['product_id'=>$pAbaya->id,'raw_material_id'=>$rmPlastik->id,'qty_per_unit'=>1,'waste_percentage'=>0]);

        // Mukena Dewasa
        BomItem::create(['product_id'=>$pMukena->id,'raw_material_id'=>$rmKainVoal->id,'qty_per_unit'=>3.5,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pMukena->id,'raw_material_id'=>$rmBenang->id,'qty_per_unit'=>0.08,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pMukena->id,'raw_material_id'=>$rmLabel->id,'qty_per_unit'=>1,'waste_percentage'=>0]);
        BomItem::create(['product_id'=>$pMukena->id,'raw_material_id'=>$rmPlastik->id,'qty_per_unit'=>1,'waste_percentage'=>0]);

        // Baju Koko
        BomItem::create(['product_id'=>$pKoko->id,'raw_material_id'=>$rmKainKatun->id,'qty_per_unit'=>1.8,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pKoko->id,'raw_material_id'=>$rmInterlining->id,'qty_per_unit'=>0.3,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pKoko->id,'raw_material_id'=>$rmBenang->id,'qty_per_unit'=>0.08,'waste_percentage'=>5]);
        BomItem::create(['product_id'=>$pKoko->id,'raw_material_id'=>$rmKancing->id,'qty_per_unit'=>0.04,'waste_percentage'=>0]);
        BomItem::create(['product_id'=>$pKoko->id,'raw_material_id'=>$rmLabel->id,'qty_per_unit'=>1,'waste_percentage'=>0]);
        BomItem::create(['product_id'=>$pKoko->id,'raw_material_id'=>$rmPlastik->id,'qty_per_unit'=>1,'waste_percentage'=>0]);

        // ─────────────────────────────────────────────
        // PURCHASE ORDERS
        // ─────────────────────────────────────────────
        $po1 = PurchaseOrder::create(['po_no'=>'PO-2026-001','supplier_id'=>$sup1->id,'status'=>'received','order_date'=>'2026-06-01','expected_date'=>'2026-06-10','total_amount'=>16750000,'notes'=>'Pengadaan kain awal produksi Ramadan','created_by'=>$uAdmin->id,'sent_at'=>'2026-06-01','received_at'=>'2026-06-09']);
        PurchaseOrderItem::create(['purchase_order_id'=>$po1->id,'raw_material_id'=>$rmKainWolly->id,'qty_ordered'=>200,'qty_received'=>200,'unit_price'=>45000,'total_price'=>9000000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po1->id,'raw_material_id'=>$rmKainVoal->id,'qty_ordered'=>120,'qty_received'=>120,'unit_price'=>38000,'total_price'=>4560000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po1->id,'raw_material_id'=>$rmKainKatun->id,'qty_ordered'=>100,'qty_received'=>100,'unit_price'=>32000,'total_price'=>3200000]);

        $po2 = PurchaseOrder::create(['po_no'=>'PO-2026-002','supplier_id'=>$sup3->id,'status'=>'partial','order_date'=>'2026-06-10','expected_date'=>'2026-06-18','total_amount'=>5850000,'notes'=>'Pengadaan aksesoris dan label','created_by'=>$uAdmin->id,'sent_at'=>'2026-06-10','received_at'=>null]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po2->id,'raw_material_id'=>$rmKancing->id,'qty_ordered'=>20,'qty_received'=>20,'unit_price'=>15000,'total_price'=>300000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po2->id,'raw_material_id'=>$rmResleting->id,'qty_ordered'=>500,'qty_received'=>500,'unit_price'=>3500,'total_price'=>1750000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po2->id,'raw_material_id'=>$rmLabel->id,'qty_ordered'=>2000,'qty_received'=>1200,'unit_price'=>500,'total_price'=>1000000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po2->id,'raw_material_id'=>$rmPlastik->id,'qty_ordered'=>2000,'qty_received'=>1000,'unit_price'=>800,'total_price'=>1600000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po2->id,'raw_material_id'=>$rmBenang->id,'qty_ordered'=>30,'qty_received'=>28,'unit_price'=>25000,'total_price'=>750000]);

        $po3 = PurchaseOrder::create(['po_no'=>'PO-2026-003','supplier_id'=>$sup2->id,'status'=>'draft','order_date'=>'2026-06-20','expected_date'=>'2026-06-30','total_amount'=>4480000,'notes'=>'Restock kain rayon untuk produksi berikutnya','created_by'=>$uSupv->id,'sent_at'=>null,'received_at'=>null]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po3->id,'raw_material_id'=>$rmKainRayon->id,'qty_ordered'=>100,'qty_received'=>0,'unit_price'=>28000,'total_price'=>2800000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po3->id,'raw_material_id'=>$rmInterlining->id,'qty_ordered'=>80,'qty_received'=>0,'unit_price'=>12000,'total_price'=>960000]);
        PurchaseOrderItem::create(['purchase_order_id'=>$po3->id,'raw_material_id'=>$rmKainKatun->id,'qty_ordered'=>22,'qty_received'=>0,'unit_price'=>32000,'total_price'=>720000]);

        // ─────────────────────────────────────────────
        // MATERIAL RECEIPTS (dari PO received)
        // ─────────────────────────────────────────────
        MaterialReceipt::create(['raw_material_id'=>$rmKainWolly->id,'supplier_id'=>$sup1->id,'qty'=>200,'unit_price'=>45000,'total_price'=>9000000,'supplier'=>'PT Tekstil Nusantara','po_no'=>'PO-2026-001','receipt_date'=>'2026-06-09','confirmed_by'=>$uGdg->id]);
        MaterialReceipt::create(['raw_material_id'=>$rmKainVoal->id,'supplier_id'=>$sup1->id,'qty'=>120,'unit_price'=>38000,'total_price'=>4560000,'supplier'=>'PT Tekstil Nusantara','po_no'=>'PO-2026-001','receipt_date'=>'2026-06-09','confirmed_by'=>$uGdg->id]);
        MaterialReceipt::create(['raw_material_id'=>$rmKainKatun->id,'supplier_id'=>$sup1->id,'qty'=>100,'unit_price'=>32000,'total_price'=>3200000,'supplier'=>'PT Tekstil Nusantara','po_no'=>'PO-2026-001','receipt_date'=>'2026-06-09','confirmed_by'=>$uGdg->id]);
        MaterialReceipt::create(['raw_material_id'=>$rmKancing->id,'supplier_id'=>$sup3->id,'qty'=>20,'unit_price'=>15000,'total_price'=>300000,'supplier'=>'UD Aksesoris Garmen Jaya','po_no'=>'PO-2026-002','receipt_date'=>'2026-06-15','confirmed_by'=>$uGdg->id]);
        MaterialReceipt::create(['raw_material_id'=>$rmResleting->id,'supplier_id'=>$sup3->id,'qty'=>500,'unit_price'=>3500,'total_price'=>1750000,'supplier'=>'UD Aksesoris Garmen Jaya','po_no'=>'PO-2026-002','receipt_date'=>'2026-06-15','confirmed_by'=>$uGdg->id]);

        // ─────────────────────────────────────────────
        // PRODUCTION ORDERS
        // ─────────────────────────────────────────────

        // ORD-001 Jubah Ramadan — COMPLETED (sudah melewati semua stasiun)
        $ord1 = ProductionOrder::create(['order_no'=>'ORD-2026-001','product_id'=>$pJubah->id,'series_id'=>$serJubahRamadan->id,'target_date'=>'2026-06-15','status'=>'completed','selling_price'=>235000,'notes'=>'Order pertama koleksi Ramadan 2026','created_by'=>$uAdmin->id,'created_at'=>'2026-06-01']);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus['SKU-JBH-RAM-WHT-M']->id,'target_qty'=>120]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus['SKU-JBH-RAM-WHT-L']->id,'target_qty'=>150]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus['SKU-JBH-RAM-BLK-M']->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus['SKU-JBH-RAM-BLK-L']->id,'target_qty'=>130]);

        // ORD-002 Abaya Syari — ACTIVE (sedang di Finishing)
        $ord2 = ProductionOrder::create(['order_no'=>'ORD-2026-002','product_id'=>$pAbaya->id,'series_id'=>$serAbayaSyari->id,'target_date'=>'2026-06-28','status'=>'active','selling_price'=>285000,'notes'=>'Order abaya syari koleksi terbaru','created_by'=>$uAdmin->id,'created_at'=>'2026-06-05']);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skus['SKU-ABY-SYR-BLK-M']->id,'target_qty'=>80]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skus['SKU-ABY-SYR-BLK-L']->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skus['SKU-ABY-SYR-NVY-L']->id,'target_qty'=>70]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$skus['SKU-ABY-SYR-MRN-XL']->id,'target_qty'=>50]);

        // ORD-003 Baju Koko Slim — ACTIVE OVERDUE (terlambat, sedang di Sewing)
        $ord3 = ProductionOrder::create(['order_no'=>'ORD-2026-003','product_id'=>$pKoko->id,'series_id'=>$serKokoSlim->id,'target_date'=>'2026-06-10','status'=>'active','selling_price'=>165000,'notes'=>'URGENT: order koko untuk distributor Surabaya, harus selesai tepat waktu','created_by'=>$uSupv->id,'created_at'=>'2026-06-03']);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus['SKU-KKO-SLM-WHT-M']->id,'target_qty'=>60]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus['SKU-KKO-SLM-WHT-L']->id,'target_qty'=>80]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus['SKU-KKO-SLM-NVY-L']->id,'target_qty'=>70]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus['SKU-KKO-SLM-GRY-XL']->id,'target_qty'=>50]);

        // ORD-004 Mukena Travel — ACTIVE (sedang di QC)
        $ord4 = ProductionOrder::create(['order_no'=>'ORD-2026-004','product_id'=>$pMukena->id,'series_id'=>$serMukenaTravel->id,'target_date'=>'2026-06-30','status'=>'active','selling_price'=>220000,'notes'=>'Order mukena travel untuk reseller online','created_by'=>$uAdmin->id,'created_at'=>'2026-06-08']);
        ProductionOrderItem::create(['production_order_id'=>$ord4->id,'sku_id'=>$skus['SKU-MKN-TRV-WHT-M']->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord4->id,'sku_id'=>$skus['SKU-MKN-TRV-CRM-L']->id,'target_qty'=>80]);

        // ORD-005 Jubah Classic — ON HOLD (bahan baku kurang)
        $ord5 = ProductionOrder::create(['order_no'=>'ORD-2026-005','product_id'=>$pJubah->id,'series_id'=>$serJubahClassic->id,'target_date'=>'2026-07-10','status'=>'on_hold','selling_price'=>195000,'notes'=>'Ditahan sementara, menunggu restock kain rayon','created_by'=>$uAdmin->id,'created_at'=>'2026-06-10']);
        ProductionOrderItem::create(['production_order_id'=>$ord5->id,'sku_id'=>$skus['SKU-JBH-CLS-WHT-L']->id,'target_qty'=>90]);
        ProductionOrderItem::create(['production_order_id'=>$ord5->id,'sku_id'=>$skus['SKU-JBH-CLS-CRM-L']->id,'target_qty'=>70]);
        ProductionOrderItem::create(['production_order_id'=>$ord5->id,'sku_id'=>$skus['SKU-JBH-CLS-MRN-XL']->id,'target_qty'=>50]);

        // ORD-006 Abaya Modern Bordir — DRAFT
        $ord6 = ProductionOrder::create(['order_no'=>'ORD-2026-006','product_id'=>$pAbaya->id,'series_id'=>$serAbayaModern->id,'target_date'=>'2026-07-20','status'=>'draft','selling_price'=>350000,'notes'=>'Draft order abaya bordir premium','created_by'=>$uSupv->id,'created_at'=>'2026-06-18']);
        ProductionOrderItem::create(['production_order_id'=>$ord6->id,'sku_id'=>$skus['SKU-ABY-BRD-BLK-M']->id,'target_qty'=>50]);
        ProductionOrderItem::create(['production_order_id'=>$ord6->id,'sku_id'=>$skus['SKU-ABY-BRD-OLV-L']->id,'target_qty'=>40]);

        // ─────────────────────────────────────────────
        // BUDGET
        // ─────────────────────────────────────────────
        Budget::create(['production_order_id'=>$ord1->id,'material_cost_plan'=>28000000,'process_cost_plan'=>8500000,'overhead_cost_plan'=>4500000,'total_plan'=>41000000,'material_cost_actual'=>27500000,'process_cost_actual'=>8800000,'overhead_cost_actual'=>4200000,'total_actual'=>40500000,'created_by'=>$uAdmin->id]);
        Budget::create(['production_order_id'=>$ord2->id,'material_cost_plan'=>22000000,'process_cost_plan'=>7000000,'overhead_cost_plan'=>3500000,'total_plan'=>32500000,'material_cost_actual'=>21000000,'process_cost_actual'=>6500000,'overhead_cost_actual'=>3200000,'total_actual'=>30700000,'created_by'=>$uAdmin->id]);
        Budget::create(['production_order_id'=>$ord3->id,'material_cost_plan'=>15000000,'process_cost_plan'=>5000000,'overhead_cost_plan'=>2500000,'total_plan'=>22500000,'material_cost_actual'=>15800000,'process_cost_actual'=>5500000,'overhead_cost_actual'=>2800000,'total_actual'=>24100000,'created_by'=>$uAdmin->id]);
        Budget::create(['production_order_id'=>$ord4->id,'material_cost_plan'=>18000000,'process_cost_plan'=>5500000,'overhead_cost_plan'=>3000000,'total_plan'=>26500000,'material_cost_actual'=>17000000,'process_cost_actual'=>5000000,'overhead_cost_actual'=>2800000,'total_actual'=>24800000,'created_by'=>$uAdmin->id]);

        // ─────────────────────────────────────────────
        // CUTTING PLANS
        // ─────────────────────────────────────────────
        $cp1 = CuttingPlan::create(['plan_no'=>'CP-2026-001','production_order_id'=>$ord1->id,'planned_date'=>'2026-06-02','marker_length'=>8.5,'fabric_width'=>150,'total_layers'=>30,'planned_qty'=>500,'actual_qty'=>500,'efficiency'=>88.5,'shift'=>'pagi','notes'=>'Cutting selesai tepat waktu','status'=>'completed','created_by'=>$uCut->id]);
        CuttingBundle::create(['cutting_plan_id'=>$cp1->id,'bundle_no'=>'CP-2026-001-A','sku_id'=>$skus['SKU-JBH-RAM-WHT-M']->id,'qty'=>120,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp1->id,'bundle_no'=>'CP-2026-001-B','sku_id'=>$skus['SKU-JBH-RAM-WHT-L']->id,'qty'=>150,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp1->id,'bundle_no'=>'CP-2026-001-C','sku_id'=>$skus['SKU-JBH-RAM-BLK-M']->id,'qty'=>100,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp1->id,'bundle_no'=>'CP-2026-001-D','sku_id'=>$skus['SKU-JBH-RAM-BLK-L']->id,'qty'=>130,'status'=>'completed']);

        $cp2 = CuttingPlan::create(['plan_no'=>'CP-2026-002','production_order_id'=>$ord2->id,'planned_date'=>'2026-06-06','marker_length'=>9.0,'fabric_width'=>150,'total_layers'=>25,'planned_qty'=>300,'actual_qty'=>300,'efficiency'=>86.0,'shift'=>'pagi','notes'=>'Cutting abaya selesai','status'=>'completed','created_by'=>$uCut->id]);
        CuttingBundle::create(['cutting_plan_id'=>$cp2->id,'bundle_no'=>'CP-2026-002-A','sku_id'=>$skus['SKU-ABY-SYR-BLK-M']->id,'qty'=>80,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp2->id,'bundle_no'=>'CP-2026-002-B','sku_id'=>$skus['SKU-ABY-SYR-BLK-L']->id,'qty'=>100,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp2->id,'bundle_no'=>'CP-2026-002-C','sku_id'=>$skus['SKU-ABY-SYR-NVY-L']->id,'qty'=>70,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp2->id,'bundle_no'=>'CP-2026-002-D','sku_id'=>$skus['SKU-ABY-SYR-MRN-XL']->id,'qty'=>50,'status'=>'completed']);

        $cp3 = CuttingPlan::create(['plan_no'=>'CP-2026-003','production_order_id'=>$ord3->id,'planned_date'=>'2026-06-04','marker_length'=>6.5,'fabric_width'=>150,'total_layers'=>40,'planned_qty'=>260,'actual_qty'=>260,'efficiency'=>90.0,'shift'=>'pagi','notes'=>'Cutting koko selesai, langsung ke sewing','status'=>'completed','created_by'=>$uCut->id]);
        CuttingBundle::create(['cutting_plan_id'=>$cp3->id,'bundle_no'=>'CP-2026-003-A','sku_id'=>$skus['SKU-KKO-SLM-WHT-M']->id,'qty'=>60,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp3->id,'bundle_no'=>'CP-2026-003-B','sku_id'=>$skus['SKU-KKO-SLM-WHT-L']->id,'qty'=>80,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp3->id,'bundle_no'=>'CP-2026-003-C','sku_id'=>$skus['SKU-KKO-SLM-NVY-L']->id,'qty'=>70,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp3->id,'bundle_no'=>'CP-2026-003-D','sku_id'=>$skus['SKU-KKO-SLM-GRY-XL']->id,'qty'=>50,'status'=>'completed']);

        $cp4 = CuttingPlan::create(['plan_no'=>'CP-2026-004','production_order_id'=>$ord4->id,'planned_date'=>'2026-06-09','marker_length'=>7.5,'fabric_width'=>150,'total_layers'=>20,'planned_qty'=>180,'actual_qty'=>180,'efficiency'=>87.5,'shift'=>'pagi','notes'=>'Cutting mukena travel selesai','status'=>'completed','created_by'=>$uCut->id]);
        CuttingBundle::create(['cutting_plan_id'=>$cp4->id,'bundle_no'=>'CP-2026-004-A','sku_id'=>$skus['SKU-MKN-TRV-WHT-M']->id,'qty'=>100,'status'=>'completed']);
        CuttingBundle::create(['cutting_plan_id'=>$cp4->id,'bundle_no'=>'CP-2026-004-B','sku_id'=>$skus['SKU-MKN-TRV-CRM-L']->id,'qty'=>80,'status'=>'completed']);

        // ─────────────────────────────────────────────
        // WIP ENTRIES & HANDOVER — ORD-001 (COMPLETED, all stations)
        // ─────────────────────────────────────────────
        $d = '2026-06-02';
        // Cutting in
        foreach ([['SKU-JBH-RAM-WHT-M',120],['SKU-JBH-RAM-WHT-L',150],['SKU-JBH-RAM-BLK-M',100],['SKU-JBH-RAM-BLK-L',130]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stCut->id,'qty_in'=>$q,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$d,'created_by'=>$uCut->id]);
        }
        // Cutting out → Sewing
        foreach ([['SKU-JBH-RAM-WHT-M',120],['SKU-JBH-RAM-WHT-L',150],['SKU-JBH-RAM-BLK-M',100],['SKU-JBH-RAM-BLK-L',130]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stCut->id,'qty_in'=>0,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-03','created_by'=>$uCut->id]);
        }

        $ho1 = Handover::create(['handover_no'=>'HO-2026-001','production_order_id'=>$ord1->id,'from_station_id'=>$stCut->id,'to_station_id'=>$stSew->id,'sewing_location_id'=>$swLoc1->id,'status'=>'confirmed','initiated_by'=>$uCut->id,'confirmed_by'=>$uSew->id,'notes'=>'Transfer cutting ke sewing, kualitas bagus','initiated_at'=>'2026-06-03 08:00:00','confirmed_at'=>'2026-06-03 09:00:00']);
        foreach ([['SKU-JBH-RAM-WHT-M',120,120],['SKU-JBH-RAM-WHT-L',150,150],['SKU-JBH-RAM-BLK-M',100,100],['SKU-JBH-RAM-BLK-L',130,130]] as [$sc,$s,$r]) {
            HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$skus[$sc]->id,'qty_sent'=>$s,'qty_received'=>$r]);
        }

        // Sewing in/out
        foreach ([['SKU-JBH-RAM-WHT-M',120],['SKU-JBH-RAM-WHT-L',150],['SKU-JBH-RAM-BLK-M',100],['SKU-JBH-RAM-BLK-L',130]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stSew->id,'qty_in'=>$q,'qty_out'=>0,'qty_reject'=>0,'input_date'=>'2026-06-03','created_by'=>$uSew->id]);
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stSew->id,'qty_in'=>0,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-07','created_by'=>$uSew->id]);
        }

        $ho2 = Handover::create(['handover_no'=>'HO-2026-002','production_order_id'=>$ord1->id,'from_station_id'=>$stSew->id,'to_station_id'=>$stFin->id,'sewing_location_id'=>$swLoc1->id,'status'=>'confirmed','initiated_by'=>$uSew->id,'confirmed_by'=>$uFin->id,'notes'=>'Sewing selesai, transfer ke finishing','initiated_at'=>'2026-06-07 13:00:00','confirmed_at'=>'2026-06-07 14:30:00']);
        foreach ([['SKU-JBH-RAM-WHT-M',120,120],['SKU-JBH-RAM-WHT-L',150,150],['SKU-JBH-RAM-BLK-M',100,100],['SKU-JBH-RAM-BLK-L',130,130]] as [$sc,$s,$r]) {
            HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$skus[$sc]->id,'qty_sent'=>$s,'qty_received'=>$r]);
        }

        // Finishing in/out
        foreach ([['SKU-JBH-RAM-WHT-M',120],['SKU-JBH-RAM-WHT-L',150],['SKU-JBH-RAM-BLK-M',100],['SKU-JBH-RAM-BLK-L',130]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stFin->id,'qty_in'=>$q,'qty_out'=>0,'qty_reject'=>0,'input_date'=>'2026-06-07','created_by'=>$uFin->id]);
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stFin->id,'qty_in'=>0,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-10','created_by'=>$uFin->id]);
        }

        $ho3 = Handover::create(['handover_no'=>'HO-2026-003','production_order_id'=>$ord1->id,'from_station_id'=>$stFin->id,'to_station_id'=>$stQc->id,'status'=>'confirmed','initiated_by'=>$uFin->id,'confirmed_by'=>$uQc->id,'notes'=>'Transfer ke QC untuk inspeksi akhir','initiated_at'=>'2026-06-10 08:00:00','confirmed_at'=>'2026-06-10 09:30:00']);
        foreach ([['SKU-JBH-RAM-WHT-M',120,120],['SKU-JBH-RAM-WHT-L',150,150],['SKU-JBH-RAM-BLK-M',100,100],['SKU-JBH-RAM-BLK-L',130,128]] as [$sc,$s,$r]) {
            HandoverItem::create(['handover_id'=>$ho3->id,'sku_id'=>$skus[$sc]->id,'qty_sent'=>$s,'qty_received'=>$r]);
        }

        // QC in/out (2 reject)
        foreach ([['SKU-JBH-RAM-WHT-M',120,0,0],['SKU-JBH-RAM-WHT-L',150,0,0],['SKU-JBH-RAM-BLK-M',100,0,0],['SKU-JBH-RAM-BLK-L',128,0,0]] as [$sc,$i,$o,$r]) {
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stQc->id,'qty_in'=>$i,'qty_out'=>0,'qty_reject'=>0,'input_date'=>'2026-06-10','created_by'=>$uQc->id]);
        }
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus['SKU-JBH-RAM-BLK-L']->id,'station_id'=>$stQc->id,'qty_in'=>0,'qty_out'=>0,'qty_reject'=>2,'input_date'=>'2026-06-11','created_by'=>$uQc->id]);
        foreach ([['SKU-JBH-RAM-WHT-M',120],['SKU-JBH-RAM-WHT-L',150],['SKU-JBH-RAM-BLK-M',100],['SKU-JBH-RAM-BLK-L',126]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stQc->id,'qty_in'=>0,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-11','created_by'=>$uQc->id]);
        }

        $ho4 = Handover::create(['handover_no'=>'HO-2026-004','production_order_id'=>$ord1->id,'from_station_id'=>$stQc->id,'to_station_id'=>$stGdg->id,'status'=>'confirmed','initiated_by'=>$uQc->id,'confirmed_by'=>$uGdg->id,'notes'=>'QC lulus, transfer ke gudang. 2 pcs reject tidak dikirim','initiated_at'=>'2026-06-11 10:00:00','confirmed_at'=>'2026-06-11 11:00:00']);
        foreach ([['SKU-JBH-RAM-WHT-M',120,120],['SKU-JBH-RAM-WHT-L',150,150],['SKU-JBH-RAM-BLK-M',100,100],['SKU-JBH-RAM-BLK-L',126,126]] as [$sc,$s,$r]) {
            HandoverItem::create(['handover_id'=>$ho4->id,'sku_id'=>$skus[$sc]->id,'qty_sent'=>$s,'qty_received'=>$r]);
        }

        // Gudang in (final)
        foreach ([['SKU-JBH-RAM-WHT-M',120],['SKU-JBH-RAM-WHT-L',150],['SKU-JBH-RAM-BLK-M',100],['SKU-JBH-RAM-BLK-L',126]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stGdg->id,'qty_in'=>$q,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-11','created_by'=>$uGdg->id]);
        }

        // QC Inspeksi ORD-001
        $qc1 = QcInspection::create(['production_order_id'=>$ord1->id,'inspector_id'=>$uQc->id,'inspected_at'=>'2026-06-11 09:00:00','status'=>'pass','total_checked'=>500,'total_defect'=>2,'defect_rate'=>0.4]);
        QcChecklistItem::create(['qc_inspection_id'=>$qc1->id,'checklist_item'=>'Kerapian Jahitan','result'=>'ok']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc1->id,'checklist_item'=>'Kelengkapan Kancing','result'=>'ok']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc1->id,'checklist_item'=>'Kebersihan Produk','result'=>'ok']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc1->id,'checklist_item'=>'Kesesuaian Ukuran','result'=>'ok']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc1->id,'checklist_item'=>'Label & Kemasan','result'=>'ok']);

        // ─────────────────────────────────────────────
        // WIP & HANDOVER — ORD-002 (ACTIVE, sedang di Finishing)
        // ─────────────────────────────────────────────
        foreach ([['SKU-ABY-SYR-BLK-M',80],['SKU-ABY-SYR-BLK-L',100],['SKU-ABY-SYR-NVY-L',70],['SKU-ABY-SYR-MRN-XL',50]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stCut->id,'qty_in'=>$q,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-06','created_by'=>$uCut->id]);
        }

        $ho5 = Handover::create(['handover_no'=>'HO-2026-005','production_order_id'=>$ord2->id,'from_station_id'=>$stCut->id,'to_station_id'=>$stSew->id,'sewing_location_id'=>$swLoc2->id,'status'=>'confirmed','initiated_by'=>$uCut->id,'confirmed_by'=>$uSew->id,'notes'=>'Transfer abaya cutting ke sewing','initiated_at'=>'2026-06-07 08:00:00','confirmed_at'=>'2026-06-07 09:00:00']);
        foreach ([['SKU-ABY-SYR-BLK-M',80,80],['SKU-ABY-SYR-BLK-L',100,100],['SKU-ABY-SYR-NVY-L',70,70],['SKU-ABY-SYR-MRN-XL',50,50]] as [$sc,$s,$r]) {
            HandoverItem::create(['handover_id'=>$ho5->id,'sku_id'=>$skus[$sc]->id,'qty_sent'=>$s,'qty_received'=>$r]);
        }

        foreach ([['SKU-ABY-SYR-BLK-M',80],['SKU-ABY-SYR-BLK-L',100],['SKU-ABY-SYR-NVY-L',70],['SKU-ABY-SYR-MRN-XL',50]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stSew->id,'qty_in'=>$q,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-07','created_by'=>$uSew->id]);
        }

        $ho6 = Handover::create(['handover_no'=>'HO-2026-006','production_order_id'=>$ord2->id,'from_station_id'=>$stSew->id,'to_station_id'=>$stFin->id,'status'=>'confirmed','initiated_by'=>$uSew->id,'confirmed_by'=>$uFin->id,'notes'=>'Transfer abaya ke finishing','initiated_at'=>'2026-06-14 08:00:00','confirmed_at'=>'2026-06-14 09:30:00']);
        foreach ([['SKU-ABY-SYR-BLK-M',80,80],['SKU-ABY-SYR-BLK-L',100,98],['SKU-ABY-SYR-NVY-L',70,70],['SKU-ABY-SYR-MRN-XL',50,50]] as [$sc,$s,$r]) {
            HandoverItem::create(['handover_id'=>$ho6->id,'sku_id'=>$skus[$sc]->id,'qty_sent'=>$s,'qty_received'=>$r]);
        }

        // Finishing in (belum out, masih proses)
        foreach ([['SKU-ABY-SYR-BLK-M',80],['SKU-ABY-SYR-BLK-L',98],['SKU-ABY-SYR-NVY-L',70],['SKU-ABY-SYR-MRN-XL',50]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stFin->id,'qty_in'=>$q,'qty_out'=>0,'qty_reject'=>0,'input_date'=>'2026-06-14','created_by'=>$uFin->id]);
        }

        // ─────────────────────────────────────────────
        // WIP & HANDOVER — ORD-003 (ACTIVE OVERDUE, sedang di Sewing)
        // ─────────────────────────────────────────────
        foreach ([['SKU-KKO-SLM-WHT-M',60],['SKU-KKO-SLM-WHT-L',80],['SKU-KKO-SLM-NVY-L',70],['SKU-KKO-SLM-GRY-XL',50]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stCut->id,'qty_in'=>$q,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-04','created_by'=>$uCut->id]);
        }

        $ho7 = Handover::create(['handover_no'=>'HO-2026-007','production_order_id'=>$ord3->id,'from_station_id'=>$stCut->id,'to_station_id'=>$stSew->id,'sewing_location_id'=>$swLoc3->id,'status'=>'confirmed','initiated_by'=>$uCut->id,'confirmed_by'=>$uSew->id,'notes'=>'Transfer ke makloon CV Berkah Jaya','initiated_at'=>'2026-06-05 07:30:00','confirmed_at'=>'2026-06-05 08:30:00']);
        foreach ([['SKU-KKO-SLM-WHT-M',60,60],['SKU-KKO-SLM-WHT-L',80,80],['SKU-KKO-SLM-NVY-L',70,70],['SKU-KKO-SLM-GRY-XL',50,50]] as [$sc,$s,$r]) {
            HandoverItem::create(['handover_id'=>$ho7->id,'sku_id'=>$skus[$sc]->id,'qty_sent'=>$s,'qty_received'=>$r]);
        }

        // Sewing in (belum selesai semua - bottleneck)
        foreach ([['SKU-KKO-SLM-WHT-M',60],['SKU-KKO-SLM-WHT-L',80],['SKU-KKO-SLM-NVY-L',70],['SKU-KKO-SLM-GRY-XL',50]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stSew->id,'qty_in'=>$q,'qty_out'=>0,'qty_reject'=>0,'input_date'=>'2026-06-05','created_by'=>$uSew->id]);
        }
        // Sebagian sudah keluar
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus['SKU-KKO-SLM-WHT-M']->id,'station_id'=>$stSew->id,'qty_in'=>0,'qty_out'=>40,'qty_reject'=>0,'input_date'=>'2026-06-12','created_by'=>$uSew->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$skus['SKU-KKO-SLM-WHT-L']->id,'station_id'=>$stSew->id,'qty_in'=>0,'qty_out'=>50,'qty_reject'=>0,'input_date'=>'2026-06-12','created_by'=>$uSew->id]);

        // ─────────────────────────────────────────────
        // WIP & HANDOVER — ORD-004 (ACTIVE, sedang di QC)
        // ─────────────────────────────────────────────
        foreach ([['SKU-MKN-TRV-WHT-M',100],['SKU-MKN-TRV-CRM-L',80]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord4->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stCut->id,'qty_in'=>$q,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-09','created_by'=>$uCut->id]);
        }

        $ho8 = Handover::create(['handover_no'=>'HO-2026-008','production_order_id'=>$ord4->id,'from_station_id'=>$stCut->id,'to_station_id'=>$stSew->id,'sewing_location_id'=>$swLoc4->id,'status'=>'confirmed','initiated_by'=>$uCut->id,'confirmed_by'=>$uSew->id,'notes'=>'Transfer mukena ke makloon UD Karya Mandiri','initiated_at'=>'2026-06-10 07:00:00','confirmed_at'=>'2026-06-10 08:00:00']);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skus['SKU-MKN-TRV-WHT-M']->id,'qty_sent'=>100,'qty_received'=>100]);
        HandoverItem::create(['handover_id'=>$ho8->id,'sku_id'=>$skus['SKU-MKN-TRV-CRM-L']->id,'qty_sent'=>80,'qty_received'=>80]);

        foreach ([['SKU-MKN-TRV-WHT-M',100],['SKU-MKN-TRV-CRM-L',80]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord4->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stSew->id,'qty_in'=>$q,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-10','created_by'=>$uSew->id]);
            WipEntry::create(['production_order_id'=>$ord4->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stFin->id,'qty_in'=>$q,'qty_out'=>$q,'qty_reject'=>0,'input_date'=>'2026-06-14','created_by'=>$uFin->id]);
        }

        $ho9 = Handover::create(['handover_no'=>'HO-2026-009','production_order_id'=>$ord4->id,'from_station_id'=>$stSew->id,'to_station_id'=>$stFin->id,'status'=>'confirmed','initiated_by'=>$uSew->id,'confirmed_by'=>$uFin->id,'initiated_at'=>'2026-06-14 07:00:00','confirmed_at'=>'2026-06-14 08:00:00']);
        HandoverItem::create(['handover_id'=>$ho9->id,'sku_id'=>$skus['SKU-MKN-TRV-WHT-M']->id,'qty_sent'=>100,'qty_received'=>100]);
        HandoverItem::create(['handover_id'=>$ho9->id,'sku_id'=>$skus['SKU-MKN-TRV-CRM-L']->id,'qty_sent'=>80,'qty_received'=>80]);

        $ho10 = Handover::create(['handover_no'=>'HO-2026-010','production_order_id'=>$ord4->id,'from_station_id'=>$stFin->id,'to_station_id'=>$stQc->id,'status'=>'pending','initiated_by'=>$uFin->id,'confirmed_by'=>null,'notes'=>'Menunggu konfirmasi QC','initiated_at'=>'2026-06-18 13:00:00']);
        HandoverItem::create(['handover_id'=>$ho10->id,'sku_id'=>$skus['SKU-MKN-TRV-WHT-M']->id,'qty_sent'=>100,'qty_received'=>null]);
        HandoverItem::create(['handover_id'=>$ho10->id,'sku_id'=>$skus['SKU-MKN-TRV-CRM-L']->id,'qty_sent'=>80,'qty_received'=>null]);

        // QC in (menunggu inspeksi)
        foreach ([['SKU-MKN-TRV-WHT-M',100],['SKU-MKN-TRV-CRM-L',80]] as [$sc,$q]) {
            WipEntry::create(['production_order_id'=>$ord4->id,'sku_id'=>$skus[$sc]->id,'station_id'=>$stQc->id,'qty_in'=>$q,'qty_out'=>0,'qty_reject'=>0,'input_date'=>'2026-06-18','created_by'=>$uFin->id]);
        }

        // QC Inspeksi ORD-004 (ongoing)
        $qc2 = QcInspection::create(['production_order_id'=>$ord4->id,'inspector_id'=>$uQc->id,'inspected_at'=>'2026-06-19 09:00:00','status'=>'conditional','total_checked'=>90,'total_defect'=>4,'defect_rate'=>4.4]);
        QcChecklistItem::create(['qc_inspection_id'=>$qc2->id,'checklist_item'=>'Kerapian Jahitan','result'=>'ok']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc2->id,'checklist_item'=>'Kelengkapan Mukena','result'=>'ok']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc2->id,'checklist_item'=>'Kebersihan Kain','result'=>'fail']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc2->id,'checklist_item'=>'Kesesuaian Ukuran','result'=>'ok']);

        // QC Inspeksi ORD-003 (fail — jadi catatan bottleneck)
        $qc3 = QcInspection::create(['production_order_id'=>$ord3->id,'inspector_id'=>$uQc->id,'inspected_at'=>'2026-06-13 10:00:00','status'=>'fail','total_checked'=>50,'total_defect'=>8,'defect_rate'=>16.0]);
        QcChecklistItem::create(['qc_inspection_id'=>$qc3->id,'checklist_item'=>'Kerapian Jahitan','result'=>'fail']);
        QcChecklistItem::create(['qc_inspection_id'=>$qc3->id,'checklist_item'=>'Kancing','result'=>'ok']);

        // ─────────────────────────────────────────────
        // NOTIFIKASI
        // ─────────────────────────────────────────────
        Notification::create(['user_id'=>$uAdmin->id,'title'=>'✅ Order ORD-2026-001 Selesai','message'=>'Order jubah Ramadan Premium telah selesai dan masuk gudang. Total 496 pcs diterima.','type'=>'success','link'=>'/orders/'.$ord1->id,'is_read'=>true]);
        Notification::create(['user_id'=>$uSupv->id,'title'=>'✅ Order ORD-2026-001 Selesai','message'=>'Order jubah Ramadan Premium telah selesai. 2 pcs reject (0.4% defect rate).','type'=>'success','link'=>'/orders/'.$ord1->id,'is_read'=>true]);
        Notification::create(['user_id'=>$uAdmin->id,'title'=>'⚠ Order ORD-2026-003 TERLAMBAT','message'=>'Order Koko Slim Fit melewati target date 10 Juni 2026. Masih dalam proses sewing di makloon CV Berkah Jaya.','type'=>'danger','link'=>'/orders/'.$ord3->id,'is_read'=>false]);
        Notification::create(['user_id'=>$uSupv->id,'title'=>'⚠ Order ORD-2026-003 TERLAMBAT','message'=>'Order Koko Slim Fit overdue. Segera koordinasi dengan makloon untuk percepatan.','type'=>'danger','link'=>'/orders/'.$ord3->id,'is_read'=>false]);
        Notification::create(['user_id'=>$uQc->id,'title'=>'Handover Masuk: HO-2026-010','message'=>'Mukena Travel dari stasiun Finishing menunggu konfirmasi QC Anda.','type'=>'warning','link'=>'/handover/'.$ho10->id,'is_read'=>false]);
        Notification::create(['user_id'=>$uAdmin->id,'title'=>'QC Conditional: ORD-2026-004','message'=>'Inspeksi mukena travel menunjukkan 4 pcs conditional. Perlu tindak lanjut.','type'=>'warning','link'=>'/qc/'.$qc2->id,'is_read'=>false]);
        Notification::create(['user_id'=>$uSupv->id,'title'=>'QC Fail: ORD-2026-003','message'=>'Inspeksi sampling koko slim dari makloon menunjukkan 16% defect rate. Rework diperlukan.','type'=>'danger','link'=>'/qc/'.$qc3->id,'is_read'=>false]);
        Notification::create(['user_id'=>$uAdmin->id,'title'=>'📦 Stok Kain Rayon Rendah','message'=>'Kain Rayon Viscose (BHN-004) hanya tersisa 45 meter, di bawah minimum stok 60 meter. Segera lakukan pembelian.','type'=>'danger','link'=>'/bahan-baku','is_read'=>false]);
        Notification::create(['user_id'=>$uAdmin->id,'title'=>'📦 Stok Kain Interlining Rendah','message'=>'Kain Interlining (BHN-010) hanya tersisa 18 meter, di bawah minimum stok 30 meter.','type'=>'danger','link'=>'/bahan-baku','is_read'=>false]);
        Notification::create(['user_id'=>$uAdmin->id,'title'=>'PO-2026-002 Diterima Sebagian','message'=>'Purchase Order aksesoris dari UD Aksesoris Garmen Jaya diterima sebagian. Label dan plastik kemasan belum lengkap.','type'=>'warning','link'=>'/purchase-order/'.$po2->id,'is_read'=>true]);
        Notification::create(['user_id'=>$uGdg->id,'title'=>'PO-2026-001 Diterima Lengkap','message'=>'Semua kain dari PT Tekstil Nusantara telah diterima. Stok diperbarui otomatis.','type'=>'success','link'=>'/purchase-order/'.$po1->id,'is_read'=>true]);
        Notification::create(['user_id'=>$uFin->id,'title'=>'Handover Dikonfirmasi: HO-2026-006','message'=>'Abaya dari sewing telah dikonfirmasi masuk ke stasiun Finishing.','type'=>'success','link'=>'/handover/'.$ho6->id,'is_read'=>true]);
        Notification::create(['user_id'=>$uSew->id,'title'=>'⚠ Bottleneck Sewing: ORD-2026-003','message'=>'Order Koko Slim masih tertahan di sewing makloon. 90 pcs dari 260 belum selesai jahit.','type'=>'warning','link'=>'/orders/'.$ord3->id,'is_read'=>false]);
    }
}
