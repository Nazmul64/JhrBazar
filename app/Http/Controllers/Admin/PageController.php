<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index()
    {
        $pages = Page::latest()->get();
        return view('admin.pagescreate.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        $categories = PageCategory::where('status', 1)->get();
        return view('admin.pagescreate.create', compact('categories'));
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'page_category_id' => 'nullable|exists:page_categories,id',
            'name'             => 'required|string|max:255|unique:pages,name',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'status'           => 'nullable',
        ]);

        $slug = Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'page-' . time();
        }

        Page::create([
            'page_category_id' => $request->page_category_id,
            'name'             => $request->name,
            'title'            => $request->title,
            'slug'             => $slug,
            'description'      => $request->description,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'status'           => $request->has('status') ? 1 : 0,
        ]);

        Cache::forget('footer_data_v2');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page)
    {
        $categories = PageCategory::where('status', 1)->get();
        return view('admin.pagescreate.edit', compact('page', 'categories'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'page_category_id' => 'nullable|exists:page_categories,id',
            'name'             => 'required|string|max:255|unique:pages,name,' . $page->id,
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'status'           => 'nullable',
        ]);

        $slug = Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'page-' . $page->id;
        }

        $page->update([
            'page_category_id' => $request->page_category_id,
            'name'             => $request->name,
            'title'            => $request->title,
            'slug'             => $slug,
            'description'      => $request->description,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'status'           => $request->has('status') ? 1 : 0,
        ]);

        Cache::forget('footer_data_v2');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();

        Cache::forget('footer_data_v2');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Quick access/edit page by slug.
     */
    public function editBySlug($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if (!$page) {
            $page = Page::create([
                'name'   => ucfirst(str_replace('-', ' ', $slug)),
                'title'  => ucfirst(str_replace('-', ' ', $slug)),
                'slug'   => $slug,
                'status' => 1,
            ]);
            Cache::forget('footer_data_v2');
        }
        $categories = PageCategory::where('status', 1)->get();
        return view('admin.pagescreate.edit', compact('page', 'categories'));
    }
}
