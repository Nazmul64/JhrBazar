<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('category')->latest()->get();
        return view('admin.blog.post.index', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::where('status', 1)->get();
        return view('admin.blog.post.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'status'           => 'nullable',
        ]);

        $thumbnail = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $this->uploadImage($request->file('thumbnail'));
        }

        Blog::create([
            'title'            => $request->title,
            'slug'             => Str::slug($request->title),
            'blog_category_id' => $request->blog_category_id,
            'thumbnail'        => $thumbnail,
            'content'          => $request->content,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'status'           => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created successfully.');
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::where('status', 1)->get();
        return view('admin.blog.post.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title'            => 'required|string|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'status'           => 'nullable',
        ]);

        $thumbnail = $blog->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $this->deleteImage($blog->thumbnail);
            $thumbnail = $this->uploadImage($request->file('thumbnail'));
        }

        $blog->update([
            'title'            => $request->title,
            'slug'             => Str::slug($request->title),
            'blog_category_id' => $request->blog_category_id,
            'thumbnail'        => $thumbnail,
            'content'          => $request->content,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'status'           => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $this->deleteImage($blog->thumbnail);
        $blog->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted successfully.');
    }

    private function uploadImage($file): string
    {
        $path = public_path('uploads/blog');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->extension();
        $file->move($path, $fileName);
        return 'uploads/blog/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
