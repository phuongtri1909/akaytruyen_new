<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('canAny:xem_danh_sach_danh_muc,them_danh_muc,sua_danh_muc,xoa_danh_muc')->only('index', 'show');
        $this->middleware('can:them_danh_muc')->only('create', 'store');
        $this->middleware('can:sua_danh_muc')->only('edit', 'update');
        $this->middleware('can:xoa_danh_muc')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('stories');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('slug')) {
            $query->where('slug', 'like', '%' . $request->slug . '%');
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('Admin.pages.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.pages.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'desc' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.string' => 'Tên danh mục phải là chuỗi',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'desc.max' => 'Mô tả không được vượt quá 1000 ký tự',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'desc' => $request->desc,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được tạo thành công!',
                'redirect' => route('admin.categories.index')
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['stories.author'])->loadCount('stories');
        return view('Admin.pages.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category->loadCount('stories');
        return view('Admin.pages.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'desc' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.string' => 'Tên danh mục phải là chuỗi',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'desc.max' => 'Mô tả không được vượt quá 1000 ký tự',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'desc' => $request->desc,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được cập nhật thành công!',
                'redirect' => route('admin.categories.index')
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Kiểm tra xem danh mục có truyện nào không
        if ($category->stories()->count() > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa danh mục có chứa truyện!'
                ], 422);
            }
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục có chứa truyện!');
        }

        $category->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được xóa thành công!'
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được xóa thành công!');
    }
}
