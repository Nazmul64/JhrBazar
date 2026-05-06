<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SellerImportExportController extends Controller
{
    public function productExportIndex()
    {
        $products = SellerProduct::where('seller_id', Auth::id())->get();
        return view('seller.import_export.product_export', compact('products'));
    }

    public function downloadTemplate()
    {
        $csvFileName = 'product_import_template.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
        ];

        $columns = ['name', 'short_description', 'description', 'sku', 'buying_price', 'selling_price', 'discount_price', 'stock_quantity', 'unit', 'size', 'color'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add a sample row
            fputcsv($file, ['Sample Product', 'Short desc', 'Long desc', 'SKU123', '100', '150', '140', '50', 'pc', 'XL', 'Red']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function productExport(Request $request)
    {
        $seller_id = Auth::id();
        $query = SellerProduct::where('seller_id', $seller_id);

        if ($request->export_type && $request->export_type !== 'all') {
            $query->where('id', $request->export_type);
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'You have no products to export.');
        }
        
        $csvFileName = 'my_products_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['name', 'short_description', 'description', 'sku', 'buying_price', 'selling_price', 'discount_price', 'stock_quantity', 'unit', 'size', 'color'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->short_description,
                    $product->description,
                    $product->sku,
                    $product->buying_price,
                    $product->selling_price,
                    $product->discount_price,
                    $product->stock_quantity,
                    $product->unit,
                    $product->size,
                    $product->color,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function productImportIndex()
    {
        return view('seller.import_export.product_import');
    }

    public function productImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Skip header

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            // Mapping based on export columns
            // ['name', 'short_description', 'description', 'sku', 'buying_price', 'selling_price', 'discount_price', 'stock_quantity', 'unit', 'size', 'color']
            
            if (count($row) < 11) continue;

            SellerProduct::create([
                'seller_id'         => Auth::id(),
                'name'              => $row[0],
                'short_description' => $row[1],
                'description'       => $row[2],
                'sku'               => $row[3],
                'buying_price'      => $row[4],
                'selling_price'     => $row[5],
                'discount_price'    => $row[6],
                'stock_quantity'    => $row[7],
                'unit'              => $row[8],
                'size'              => $row[9],
                'color'             => $row[10],
                'is_active'         => true,
            ]);
            $count++;
        }
        fclose($handle);

        return back()->with('success', "$count products imported successfully!");
    }

    public function galleryImportIndex()
    {
        return view('seller.import_export.gallery_import');
    }

    public function galleryImport(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products/gallery'), $imageName);
            }
            return back()->with('success', 'Gallery images uploaded successfully!');
        }

        return back()->with('error', 'No images selected.');
    }
}
