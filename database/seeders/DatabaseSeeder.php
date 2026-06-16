<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
use App\Models\Budget;
use App\Models\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Stations
        $cut = Station::create(['name'=>'Cutting','code'=>'CUT','order_sequence'=>1,'description'=>'Pemotongan pola kain','bottleneck_threshold'=>200]);
        $sew = Station::create(['name'=>'Sewing','code'=>'SEW','order_sequence'=>2,'description'=>'Penjahitan dan perakitan','bottleneck_threshold'=>150]);
        $fin = Station::create(['name'=>'Finishing','code'=>'FIN','order_sequence'=>3,'description'=>'Setrika dan aksesoris','bottleneck_threshold'=>150]);
        $qc  = Station::create(['name'=>'QC','code'=>'QC','order_sequence'=>4,'description'=>'Quality Control','bottleneck_threshold'=>100]);
        $wh  = Station::create(['name'=>'Warehouse','code'=>'WH','order_sequence'=>5,'description'=>'Penyimpanan dan pengiriman','bottleneck_threshold'=>500]);

        // Users
        $admin  = User::create(['name'=>'Admin DPIS','email'=>'admin@dpis.com','password'=>Hash::make('password123'),'role'=>'admin','phone'=>'081200000001','is_active'=>true]);
        $super  = User::create(['name'=>'Budi Supervisor','email'=>'supervisor@dpis.com','password'=>Hash::make('password123'),'role'=>'supervisor','phone'=>'081200000002','is_active'=>true]);
        $mgr    = User::create(['name'=>'Direktur Utama','email'=>'manager@dpis.com','password'=>Hash::make('password123'),'role'=>'manager','phone'=>'081200000003','is_active'=>true]);
        $picCut = User::create(['name'=>'Ahmad Cutting','email'=>'cutting@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$cut->id,'phone'=>'081200000004','is_active'=>true]);
        $picSew = User::create(['name'=>'Siti Sewing','email'=>'sewing@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$sew->id,'phone'=>'081200000005','is_active'=>true]);
        $picFin = User::create(['name'=>'Rudi Finishing','email'=>'finishing@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$fin->id,'phone'=>'081200000006','is_active'=>true]);
        $picQC  = User::create(['name'=>'Dewi QC','email'=>'qc@dpis.com','password'=>Hash::make('password123'),'role'=>'pic_stasiun','station_id'=>$qc->id,'phone'=>'081200000007','is_active'=>true]);
        $stfWH  = User::create(['name'=>'Hendra Warehouse','email'=>'warehouse@dpis.com','password'=>Hash::make('password123'),'role'=>'staff_gudang','station_id'=>$wh->id,'phone'=>'081200000008','is_active'=>true]);

        // Products
        $prod1 = Product::create(['code'=>'PRD-001','name'=>'Kemeja Formal Pria','category'=>'Kemeja','description'=>'Kemeja formal lengan panjang']);
        $prod2 = Product::create(['code'=>'PRD-002','name'=>'Celana Chino','category'=>'Celana','description'=>'Celana chino casual']);
        $prod3 = Product::create(['code'=>'PRD-003','name'=>'Kaos Polo','category'=>'Kaos','description'=>'Kaos polo berkerah']);

        // Colors & Sizes
        $white  = Color::create(['name'=>'Putih','code'=>'WHT','hex_code'=>'#FFFFFF']);
        $black  = Color::create(['name'=>'Hitam','code'=>'BLK','hex_code'=>'#000000']);
        $navy   = Color::create(['name'=>'Navy Blue','code'=>'NVY','hex_code'=>'#001F5B']);
        $grey   = Color::create(['name'=>'Abu-abu','code'=>'GRY','hex_code'=>'#808080']);
        $maroon = Color::create(['name'=>'Maroon','code'=>'MRN','hex_code'=>'#800000']);

        $sS  = Size::create(['name'=>'S','type'=>'letter','sort_order'=>1]);
        $sM  = Size::create(['name'=>'M','type'=>'letter','sort_order'=>2]);
        $sL  = Size::create(['name'=>'L','type'=>'letter','sort_order'=>3]);
        $sXL = Size::create(['name'=>'XL','type'=>'letter','sort_order'=>4]);
        $sXXL= Size::create(['name'=>'XXL','type'=>'letter','sort_order'=>5]);
        $s28 = Size::create(['name'=>'28','type'=>'number','sort_order'=>6]);
        $s30 = Size::create(['name'=>'30','type'=>'number','sort_order'=>7]);
        $s32 = Size::create(['name'=>'32','type'=>'number','sort_order'=>8]);
        $s34 = Size::create(['name'=>'34','type'=>'number','sort_order'=>9]);

        // Series
        $ser1 = Series::create(['name'=>'Summer 2026','code'=>'SUM26','product_id'=>$prod1->id]);
        $ser2 = Series::create(['name'=>'Executive Line','code'=>'EXC26','product_id'=>$prod1->id]);
        $ser3 = Series::create(['name'=>'Casual Chino 2026','code'=>'CAS26','product_id'=>$prod2->id]);
        $ser4 = Series::create(['name'=>'Sport Edition','code'=>'SPT26','product_id'=>$prod3->id]);

        // SKUs
        $sku1 = Sku::create(['product_id'=>$prod1->id,'series_id'=>$ser1->id,'color_id'=>$white->id,'size_id'=>$sM->id,'sku_code'=>'PRD001-SUM26-WHT-M']);
        $sku2 = Sku::create(['product_id'=>$prod1->id,'series_id'=>$ser1->id,'color_id'=>$white->id,'size_id'=>$sL->id,'sku_code'=>'PRD001-SUM26-WHT-L']);
        $sku3 = Sku::create(['product_id'=>$prod1->id,'series_id'=>$ser1->id,'color_id'=>$navy->id,'size_id'=>$sM->id,'sku_code'=>'PRD001-SUM26-NVY-M']);
        $sku4 = Sku::create(['product_id'=>$prod1->id,'series_id'=>$ser1->id,'color_id'=>$navy->id,'size_id'=>$sL->id,'sku_code'=>'PRD001-SUM26-NVY-L']);
        $sku5 = Sku::create(['product_id'=>$prod2->id,'series_id'=>$ser3->id,'color_id'=>$black->id,'size_id'=>$s30->id,'sku_code'=>'PRD002-CAS26-BLK-30']);
        $sku6 = Sku::create(['product_id'=>$prod2->id,'series_id'=>$ser3->id,'color_id'=>$black->id,'size_id'=>$s32->id,'sku_code'=>'PRD002-CAS26-BLK-32']);
        $sku7 = Sku::create(['product_id'=>$prod2->id,'series_id'=>$ser3->id,'color_id'=>$navy->id,'size_id'=>$s30->id,'sku_code'=>'PRD002-CAS26-NVY-30']);
        $sku8 = Sku::create(['product_id'=>$prod3->id,'series_id'=>$ser4->id,'color_id'=>$white->id,'size_id'=>$sM->id,'sku_code'=>'PRD003-SPT26-WHT-M']);
        $sku9 = Sku::create(['product_id'=>$prod3->id,'series_id'=>$ser4->id,'color_id'=>$navy->id,'size_id'=>$sL->id,'sku_code'=>'PRD003-SPT26-NVY-L']);
        $sku10= Sku::create(['product_id'=>$prod3->id,'series_id'=>$ser4->id,'color_id'=>$maroon->id,'size_id'=>$sXL->id,'sku_code'=>'PRD003-SPT26-MRN-XL']);

        // Production Orders
        $ord1 = ProductionOrder::create(['order_no'=>'ORD-2026-001','product_id'=>$prod1->id,'series_id'=>$ser1->id,'target_date'=>now()->addDays(7)->toDateString(),'status'=>'active','selling_price'=>185000,'notes'=>'Order kemeja summer','created_by'=>$super->id]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku1->id,'target_qty'=>100]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku2->id,'target_qty'=>120]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku3->id,'target_qty'=>80]);
        ProductionOrderItem::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku4->id,'target_qty'=>100]);

        $ord2 = ProductionOrder::create(['order_no'=>'ORD-2026-002','product_id'=>$prod2->id,'series_id'=>$ser3->id,'target_date'=>now()->addDays(14)->toDateString(),'status'=>'active','selling_price'=>220000,'notes'=>'Order celana chino batch 1','created_by'=>$super->id]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$sku5->id,'target_qty'=>150]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$sku6->id,'target_qty'=>150]);
        ProductionOrderItem::create(['production_order_id'=>$ord2->id,'sku_id'=>$sku7->id,'target_qty'=>100]);

        $ord3 = ProductionOrder::create(['order_no'=>'ORD-2026-003','product_id'=>$prod3->id,'series_id'=>$ser4->id,'target_date'=>now()->subDays(2)->toDateString(),'status'=>'active','selling_price'=>95000,'notes'=>'URGENT - sudah terlambat!','created_by'=>$super->id]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$sku8->id,'target_qty'=>200]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$sku9->id,'target_qty'=>200]);
        ProductionOrderItem::create(['production_order_id'=>$ord3->id,'sku_id'=>$sku10->id,'target_qty'=>150]);

        $ord4 = ProductionOrder::create(['order_no'=>'ORD-2026-004','product_id'=>$prod1->id,'series_id'=>$ser2->id,'target_date'=>now()->addDays(30)->toDateString(),'status'=>'draft','notes'=>'Draft order executive line','created_by'=>$super->id]);

        // WIP Entries
        $today = now()->toDateString();
        $y1 = now()->subDay()->toDateString();
        $y2 = now()->subDays(2)->toDateString();

        // Order 1 - progressing through pipeline
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku1->id,'station_id'=>$cut->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$y2,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku2->id,'station_id'=>$cut->id,'qty_in'=>120,'qty_out'=>120,'qty_reject'=>0,'input_date'=>$y2,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku3->id,'station_id'=>$cut->id,'qty_in'=>80,'qty_out'=>80,'qty_reject'=>2,'input_date'=>$y2,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku4->id,'station_id'=>$cut->id,'qty_in'=>100,'qty_out'=>100,'qty_reject'=>0,'input_date'=>$y2,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku1->id,'station_id'=>$sew->id,'qty_in'=>100,'qty_out'=>85,'qty_reject'=>3,'input_date'=>$y1,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku2->id,'station_id'=>$sew->id,'qty_in'=>120,'qty_out'=>110,'qty_reject'=>2,'input_date'=>$y1,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku3->id,'station_id'=>$sew->id,'qty_in'=>78,'qty_out'=>70,'qty_reject'=>1,'input_date'=>$y1,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku4->id,'station_id'=>$sew->id,'qty_in'=>100,'qty_out'=>90,'qty_reject'=>2,'input_date'=>$y1,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku1->id,'station_id'=>$fin->id,'qty_in'=>85,'qty_out'=>50,'qty_reject'=>1,'input_date'=>$today,'created_by'=>$picFin->id]);
        WipEntry::create(['production_order_id'=>$ord1->id,'sku_id'=>$sku2->id,'station_id'=>$fin->id,'qty_in'=>110,'qty_out'=>60,'qty_reject'=>0,'input_date'=>$today,'created_by'=>$picFin->id]);

        // Order 2 - just started cutting
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$sku5->id,'station_id'=>$cut->id,'qty_in'=>150,'qty_out'=>130,'qty_reject'=>5,'input_date'=>$y1,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$sku6->id,'station_id'=>$cut->id,'qty_in'=>150,'qty_out'=>140,'qty_reject'=>3,'input_date'=>$y1,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord2->id,'sku_id'=>$sku5->id,'station_id'=>$sew->id,'qty_in'=>130,'qty_out'=>0,'qty_reject'=>0,'input_date'=>$today,'created_by'=>$picSew->id]);

        // Order 3 - overdue, bottleneck at sewing
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$sku8->id,'station_id'=>$cut->id,'qty_in'=>200,'qty_out'=>200,'qty_reject'=>0,'input_date'=>$y2,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$sku9->id,'station_id'=>$cut->id,'qty_in'=>200,'qty_out'=>200,'qty_reject'=>4,'input_date'=>$y2,'created_by'=>$picCut->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$sku8->id,'station_id'=>$sew->id,'qty_in'=>200,'qty_out'=>50,'qty_reject'=>2,'input_date'=>$y1,'created_by'=>$picSew->id]);
        WipEntry::create(['production_order_id'=>$ord3->id,'sku_id'=>$sku9->id,'station_id'=>$sew->id,'qty_in'=>196,'qty_out'=>40,'qty_reject'=>3,'input_date'=>$y1,'created_by'=>$picSew->id]);

        // Handovers
        $ho1 = Handover::create(['handover_no'=>'HO-2026-001','production_order_id'=>$ord1->id,'from_station_id'=>$cut->id,'to_station_id'=>$sew->id,'status'=>'confirmed','initiated_by'=>$picCut->id,'confirmed_by'=>$picSew->id,'notes'=>'Batch pertama','initiated_at'=>now()->subDays(2),'confirmed_at'=>now()->subDay()]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$sku1->id,'qty_sent'=>100,'qty_received'=>100,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$sku2->id,'qty_sent'=>120,'qty_received'=>120,'discrepancy'=>0]);
        HandoverItem::create(['handover_id'=>$ho1->id,'sku_id'=>$sku3->id,'qty_sent'=>78,'qty_received'=>76,'discrepancy'=>-2,'discrepancy_notes'=>'2 pcs jatuh saat transport']);

        $ho2 = Handover::create(['handover_no'=>'HO-2026-002','production_order_id'=>$ord1->id,'from_station_id'=>$sew->id,'to_station_id'=>$fin->id,'status'=>'pending','initiated_by'=>$picSew->id,'notes'=>'Siap ke finishing','initiated_at'=>now()->subHours(2)]);
        HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$sku1->id,'qty_sent'=>85]);
        HandoverItem::create(['handover_id'=>$ho2->id,'sku_id'=>$sku2->id,'qty_sent'=>110]);

        $ho3 = Handover::create(['handover_no'=>'HO-2026-003','production_order_id'=>$ord3->id,'from_station_id'=>$cut->id,'to_station_id'=>$sew->id,'status'=>'discrepancy','initiated_by'=>$picCut->id,'confirmed_by'=>$picSew->id,'notes'=>'Ada selisih','initiated_at'=>now()->subDay(),'confirmed_at'=>now()->subHours(5)]);
        HandoverItem::create(['handover_id'=>$ho3->id,'sku_id'=>$sku8->id,'qty_sent'=>200,'qty_received'=>195,'discrepancy'=>-5,'discrepancy_notes'=>'5 pcs rusak ditemukan']);

        // Raw Materials
        RawMaterial::create(['code'=>'BB-001','name'=>'Kain Katun Premium (Putih)','unit'=>'meter','category'=>'kain','color'=>'Putih','min_stock'=>500,'current_stock'=>750,'unit_price'=>25000]);
        RawMaterial::create(['code'=>'BB-002','name'=>'Kain Katun Premium (Navy)','unit'=>'meter','category'=>'kain','color'=>'Navy Blue','min_stock'=>300,'current_stock'=>120,'unit_price'=>25000]);
        RawMaterial::create(['code'=>'BB-003','name'=>'Benang Jahit No.40','unit'=>'cone','category'=>'benang','color'=>'Putih','min_stock'=>50,'current_stock'=>45,'unit_price'=>15000]);
        RawMaterial::create(['code'=>'BB-004','name'=>'Kancing Kemeja','unit'=>'lusin','category'=>'aksesoris','min_stock'=>100,'current_stock'=>230,'unit_price'=>5000]);
        RawMaterial::create(['code'=>'BB-005','name'=>'Label Merk DTHREE','unit'=>'pcs','category'=>'aksesoris','min_stock'=>500,'current_stock'=>300,'unit_price'=>500]);
        RawMaterial::create(['code'=>'BB-006','name'=>'Kain Drill (Hitam)','unit'=>'meter','category'=>'kain','color'=>'Hitam','min_stock'=>400,'current_stock'=>580,'unit_price'=>35000]);
        RawMaterial::create(['code'=>'BB-007','name'=>'Zipper YKK 20cm','unit'=>'pcs','category'=>'aksesoris','min_stock'=>200,'current_stock'=>80,'unit_price'=>3500]);
        RawMaterial::create(['code'=>'BB-008','name'=>'Plastik Kemasan','unit'=>'pcs','category'=>'lainnya','min_stock'=>1000,'current_stock'=>850,'unit_price'=>200]);

        // Material Receipt
        $matNavy = \App\Models\RawMaterial::where('code','BB-002')->first();
        MaterialReceipt::create(['raw_material_id'=>$matNavy->id,'qty'=>500,'unit_price'=>25000,'total_price'=>12500000,'supplier'=>'PT Tekstil Makmur','po_no'=>'PO-2026-001','receipt_date'=>now()->subDays(10)->toDateString(),'confirmed_by'=>$stfWH->id,'notes'=>'Penerimaan normal']);

        // Budgets
        Budget::create(['production_order_id'=>$ord1->id,'material_cost_plan'=>18000000,'process_cost_plan'=>8000000,'overhead_cost_plan'=>2000000,'total_plan'=>28000000,'material_cost_actual'=>16500000,'process_cost_actual'=>7200000,'overhead_cost_actual'=>1800000,'total_actual'=>25500000,'created_by'=>$super->id]);
        Budget::create(['production_order_id'=>$ord2->id,'material_cost_plan'=>35000000,'process_cost_plan'=>12000000,'overhead_cost_plan'=>3000000,'total_plan'=>50000000,'material_cost_actual'=>28000000,'process_cost_actual'=>5000000,'overhead_cost_actual'=>1200000,'total_actual'=>34200000,'created_by'=>$super->id]);
        Budget::create(['production_order_id'=>$ord3->id,'material_cost_plan'=>25000000,'process_cost_plan'=>9000000,'overhead_cost_plan'=>2500000,'total_plan'=>36500000,'material_cost_actual'=>27500000,'process_cost_actual'=>11000000,'overhead_cost_actual'=>3200000,'total_actual'=>41700000,'created_by'=>$super->id]);

        // Notifications
        $notifs = [
            [$admin->id,'Stok Kain Navy Blue Rendah','Stok Kain Katun Navy Blue (120 meter) di bawah minimum (300 meter).','danger','/bahan-baku'],
            [$admin->id,'Stok Benang Jahit Kritis','Stok Benang Jahit No.40 (45 cone) di bawah minimum (50 cone).','warning','/bahan-baku'],
            [$admin->id,'Stok Label Merk Rendah','Stok Label Merk (300 pcs) di bawah minimum (500 pcs).','warning','/bahan-baku'],
            [$admin->id,'Stok Zipper YKK Kritis','Stok Zipper YKK 20cm (80 pcs) di bawah minimum (200 pcs).','danger','/bahan-baku'],
            [$super->id,'Order ORD-2026-003 Terlambat!','Order ORD-2026-003 (Kaos Polo) sudah melewati target tanggal selesai.','danger','/orders/3'],
            [$super->id,'Handover Menunggu Konfirmasi','Handover HO-2026-002 dari Sewing ke Finishing menunggu konfirmasi.','warning','/handover/2'],
            [$super->id,'Discrepancy Perlu Persetujuan','Handover HO-2026-003 memiliki selisih 5 pcs yang memerlukan persetujuan.','danger','/handover/3'],
            [$super->id,'Budget Order 3 Melebihi Rencana','Realisasi biaya ORD-2026-003 (Rp 41.7 juta) melebihi budget (Rp 36.5 juta) 14.2%.','danger','/budget/3'],
            [$picFin->id,'Handover Masuk - Perlu Konfirmasi','Handover HO-2026-002 dari Sewing sudah tiba. Silakan konfirmasi penerimaan.','warning','/handover/2'],
        ];
        foreach ($notifs as $n) {
            Notification::create(['user_id'=>$n[0],'title'=>$n[1],'message'=>$n[2],'type'=>$n[3],'link'=>$n[4],'is_read'=>false]);
        }
    }
}
