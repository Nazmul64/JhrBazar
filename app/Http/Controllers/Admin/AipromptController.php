<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aiprompt;
use Illuminate\Http\Request;

class AipromptController extends Controller
{
    /**
     * Display the AI Prompt settings page.
     */
    public function index()
    {
        $setting = Aiprompt::firstOrCreate(
            ['id' => 1],
            [
                'product_description' => "Product name: {product_name}. Short description: {short_description}. Write a long, SEO-friendly product description that includes relevant keywords, highlights unique features, and encourages buyers to take action.",
                'page_description'    => null,
                'blog_description'    => null,
            ]
        );

        return view('admin.aiprompt.index', compact('setting'));
    }

    /**
     * Update Product Description prompt.
     */
    public function updateProduct(Request $request)
    {
        $request->validate([
            'product_description' => 'required|string',
        ]);

        $setting = Aiprompt::firstOrNew(['id' => 1]);
        $setting->product_description = $request->product_description;
        $setting->save();

        return redirect()->route('admin.aiprompt.index')
            ->with('success_product', 'Product description prompt updated successfully.');
    }

    /**
     * Update Page Description prompt.
     */
    public function updatePage(Request $request)
    {
        $request->validate([
            'page_description' => 'required|string',
        ]);

        $setting = Aiprompt::firstOrNew(['id' => 1]);
        $setting->page_description = $request->page_description;
        $setting->save();

        return redirect()->route('admin.aiprompt.index')
            ->with('success_page', 'Page description prompt updated successfully.');
    }

    /**
     * Update Blog Description prompt.
     */
    public function updateBlog(Request $request)
    {
        $request->validate([
            'blog_description' => 'required|string',
        ]);

        $setting = Aiprompt::firstOrNew(['id' => 1]);
        $setting->blog_description = $request->blog_description;
        $setting->save();

        return redirect()->route('admin.aiprompt.index')
            ->with('success_blog', 'Blog description prompt updated successfully.');
    }

    // Unused resource stubs
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
