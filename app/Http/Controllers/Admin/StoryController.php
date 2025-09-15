<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('canAny:xem_danh_sach_truyen,them_truyen,sua_truyen,xoa_truyen')->only('index');
        $this->middleware('can:them_truyen')->only('create', 'store');
        $this->middleware('can:sua_truyen')->only('edit', 'update', 'toggleStatus');
        $this->middleware('can:xoa_truyen')->only('destroy');

        $this->middleware('canAny:xem_danh_sach_chuong,them_chuong,sua_chuong,xoa_chuong')->only('show');
    }

    public function index(Request $request)
    {
        $query = Story::with(['author', 'categories'])->withCount('chapters');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $stories = $query->paginate(15)->withQueryString();
        $categories = Category::all();
        $authors = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Admin', 'Content']);
        })->get();

        return view('Admin.pages.stories.index', compact('stories', 'categories', 'authors'));
    }

    public function create()
    {
        $categories = Category::all();
        $authors = User::whereHas('roles.permissions', function($q) {
            $q->whereIn('name', ['them_truyen']);
        })->get();

        return view('Admin.pages.stories.create', compact('categories', 'authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stories,name',
            'desc' => 'required|string|max:5000',
            'author_id' => 'required|exists:users,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'image' => 'required|file|max:10240',
        ], [
            'name.required' => 'Tên truyện là bắt buộc',
            'name.string' => 'Tên truyện phải là chuỗi',
            'name.max' => 'Tên truyện không được vượt quá 255 ký tự',
            'name.unique' => 'Tên truyện đã tồn tại',
            'desc.required' => 'Mô tả là bắt buộc',
            'desc.max' => 'Mô tả không được vượt quá 5000 ký tự',
            'author_id.required' => 'Tác giả là bắt buộc',
            'author_id.exists' => 'Tác giả không tồn tại',
            'categories.required' => 'Danh mục là bắt buộc',
            'categories.array' => 'Danh mục phải là mảng',
            'categories.min' => 'Phải chọn ít nhất 1 danh mục',
            'categories.*.exists' => 'Danh mục không tồn tại',
            'image.max' => 'Hình ảnh không được vượt quá 10MB',
            'image.required' => 'Hình ảnh là bắt buộc',
        ]);

        $cleanDesc = Helper::sanitizeAdvancedContent($request->desc);

        $storyData = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'desc' => $cleanDesc,
            'author_id' => $request->author_id,
            'status' => $request->has('status') ? 1 : 0,
            'is_full' => $request->has('is_full') ? 1 : 0,
            'is_new' => $request->has('is_new') ? 1 : 0,
            'is_hot' => $request->has('is_hot') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            $validation = Helper::validateImageFile($image);
            if (!$validation['valid']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validation['message']
                    ], 422);
                }
                return redirect()->back()->withErrors(['image' => $validation['message']]);
            }
            
            $extension = strtolower($image->getClientOriginalExtension());
            $imageName = 'story_' . time() . '_' . uniqid() . '.' . $extension;
            $imagePath = $image->storeAs('stories', $imageName, 'public');
            $storyData['image'] = $imagePath;
        }

        $story = Story::create($storyData);
        $story->categories()->sync($request->categories);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Truyện đã được tạo thành công!',
                'redirect' => route('admin.stories.index')
            ]);
        }

        return redirect()->route('admin.stories.index')->with('success', 'Truyện đã được tạo thành công!');
    }

    public function show(Request $request, Story $story)
    {
        $story->load(['author', 'categories']);
        
        $query = $story->chapters()->orderBy('chapter', 'desc');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('chapter', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $chapters = $query->paginate(30);
        $story->chapters_count = $story->chapters()->count();
        
        return view('Admin.pages.stories.show', compact('story', 'chapters'));
    }

    public function edit(Story $story)
    {
        $categories = Category::all();
        $authors = User::whereHas('roles.permissions', function($q) {
            $q->whereIn('name', ['sua_truyen']);
        })->get();
        
        $story->load('categories');
        return view('Admin.pages.stories.edit', compact('story', 'categories', 'authors'));
    }

    public function update(Request $request, Story $story)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stories,name,' . $story->id,
            'desc' => 'required|string|max:5000',
            'author_id' => 'required|exists:users,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|file|max:10240',
        ], [
            'name.required' => 'Tên truyện là bắt buộc',
            'name.string' => 'Tên truyện phải là chuỗi',
            'name.max' => 'Tên truyện không được vượt quá 255 ký tự',
            'name.unique' => 'Tên truyện đã tồn tại',
            'desc.required' => 'Mô tả là bắt buộc',
            'desc.max' => 'Mô tả không được vượt quá 5000 ký tự',
            'author_id.required' => 'Tác giả là bắt buộc',
            'author_id.exists' => 'Tác giả không tồn tại',
            'categories.required' => 'Danh mục là bắt buộc',
            'categories.array' => 'Danh mục phải là mảng',
            'categories.min' => 'Phải chọn ít nhất 1 danh mục',
            'categories.*.exists' => 'Danh mục không tồn tại',
            'image.max' => 'Hình ảnh không được vượt quá 10MB',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            $validation = Helper::validateImageFile($image);
            if (!$validation['valid']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validation['message']
                    ], 422);
                }
                return redirect()->back()->withErrors(['image' => $validation['message']]);
            }
        }

        $cleanDesc = Helper::sanitizeAdvancedContent($request->desc);
        
        $storyData = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'desc' => $cleanDesc,
            'author_id' => $request->author_id,
            'status' => $request->has('status') ? 1 : 0,
            'is_full' => $request->has('is_full') ? 1 : 0,
            'is_new' => $request->has('is_new') ? 1 : 0,
            'is_hot' => $request->has('is_hot') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            if ($story->image) {
                Storage::disk('public')->delete($story->image);
            }
            
            $image = $request->file('image');
            
            $validation = Helper::validateImageFile($image);
            if (!$validation['valid']) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validation['message']
                    ], 422);
                }
                return redirect()->back()->withErrors(['image' => $validation['message']]);
            }
            
            $extension = strtolower($image->getClientOriginalExtension());
            $imageName = 'story_' . time() . '_' . uniqid() . '.' . $extension;
            $imagePath = $image->storeAs('stories', $imageName, 'public');
            $storyData['image'] = $imagePath;
        }

        $story->update($storyData);
        $story->categories()->sync($request->categories);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Truyện đã được cập nhật thành công!',
                'redirect' => route('admin.stories.index')
            ]);
        }

        return redirect()->route('admin.stories.index')->with('success', 'Truyện đã được cập nhật thành công!');
    }

    public function destroy(Story $story)
    {
        if ($story->image) {
            Storage::disk('public')->delete($story->image);
        }

        $story->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Truyện đã được xóa thành công!'
            ]);
        }

        return redirect()->route('admin.stories.index')->with('success', 'Truyện đã được xóa thành công!');
    }

    public function toggleStatus(Request $request, Story $story)
    {
        $request->validate([
            'field' => 'required|in:status,is_full,is_new,is_hot',
            'value' => 'required'
        ]);

        $field = $request->field;
        $value = $request->value ? 1 : 0;

        $story->update([$field => $value]);

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật thành công!',
            'field' => $field,
            'value' => $value
        ]);
    }
}
