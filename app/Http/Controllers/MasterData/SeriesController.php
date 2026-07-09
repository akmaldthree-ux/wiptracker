<?php
namespace App\Http\Controllers\MasterData;
use App\Http\Controllers\Controller;
use App\Models\{Series, Product, Sku, Color, Size};
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request) {
        $q = Series::with('product','skus');
        if ($request->search) $q->where('name','like',"%{$request->search}%")->orWhere('code','like',"%{$request->search}%");
        if ($request->product_id) $q->where('product_id', $request->product_id);
        return view('master.series.index', [
            'series'    => $q->latest()->paginate(20)->withQueryString(),
            'products'  => Product::where('is_active',true)->orderBy('name')->get(),
            'allSeries' => Series::with('product')->orderBy('code')->get(),
        ]);
    }
    public function create() { return view('master.series.form', ['series'=>new Series(), 'products'=>Product::where('is_active',true)->get()]); }
    public function store(Request $request) {
        $request->validate(['code'=>'required|unique:series,code','name'=>'required','product_id'=>'required|exists:products,id']);
        Series::create($request->all());
        return redirect()->route('master.series.index')->with('success','Series berhasil ditambahkan.');
    }
    public function edit(Series $series) { return view('master.series.form', ['series'=>$series, 'products'=>Product::where('is_active',true)->get()]); }
    public function update(Request $request, Series $series) {
        $request->validate(['name'=>'required','product_id'=>'required']);
        $series->update($request->all());
        return redirect()->route('master.series.index')->with('success','Series berhasil diperbarui.');
    }
    public function destroy(Series $series) { $series->delete(); return back()->with('success','Series berhasil dihapus.'); }

    public function generateSku(Request $request, Series $series)
    {
        $request->validate([
            'color_ids'   => 'required|array|min:1',
            'color_ids.*' => 'exists:colors,id',
            'size_ids'    => 'required|array|min:1',
            'size_ids.*'  => 'exists:sizes,id',
        ]);

        $created = $this->doGenerateSku([$series], $request->color_ids, $request->size_ids);

        return back()->with('success', "$created SKU berhasil di-generate untuk series {$series->name}.");
    }

    public function generateSkuBulk(Request $request)
    {
        $request->validate([
            'series_ids'  => 'required|array|min:1',
            'series_ids.*'=> 'exists:series,id',
            'color_ids'   => 'required|array|min:1',
            'color_ids.*' => 'exists:colors,id',
            'size_ids'    => 'required|array|min:1',
            'size_ids.*'  => 'exists:sizes,id',
        ]);

        $seriesList = Series::whereIn('id', $request->series_ids)->get();
        $created    = $this->doGenerateSku($seriesList, $request->color_ids, $request->size_ids);
        $seriesCount = $seriesList->count();

        return back()->with('success', "$created SKU berhasil di-generate untuk $seriesCount series.");
    }

    private function doGenerateSku($seriesList, array $colorIds, array $sizeIds): int
    {
        $colors  = Color::whereIn('id', $colorIds)->get()->keyBy('id');
        $sizes   = Size::whereIn('id', $sizeIds)->get()->keyBy('id');
        $created = 0;

        foreach ($seriesList as $series) {
            // Load existing combinations to avoid N+1 queries
            $existing = Sku::where('series_id', $series->id)
                ->whereIn('color_id', $colorIds)
                ->whereIn('size_id', $sizeIds)
                ->get(['color_id','size_id'])
                ->map(fn($s) => $s->color_id . '-' . $s->size_id)
                ->flip();

            $inserts = [];
            foreach ($colorIds as $colorId) {
                foreach ($sizeIds as $sizeId) {
                    if (isset($existing[$colorId . '-' . $sizeId])) continue;
                    $inserts[] = [
                        'product_id' => $series->product_id,
                        'series_id'  => $series->id,
                        'color_id'   => $colorId,
                        'size_id'    => $sizeId,
                        'sku_code'   => strtoupper($series->code . '-' . $colors[$colorId]->code . '-' . $sizes[$sizeId]->name),
                        'is_active'  => true,
                    ];
                }
            }

            if ($inserts) {
                Sku::insert($inserts);
                $created += count($inserts);
            }
        }

        return $created;
    }
}
