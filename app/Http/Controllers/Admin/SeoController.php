<?php

namespace App\Http\Controllers\Admin;

use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeoController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:cau_hinh_seo')->only(['index', 'edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seoSettings = SeoSetting::orderBy('page_key')->get();
        $pageKeys = SeoSetting::getPageKeys();
        
        return view('Admin.pages.seo.index', compact('seoSettings', 'pageKeys'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeoSetting $seo)
    {
        $pageKeys = SeoSetting::getPageKeys();
        
        return view('Admin.pages.seo.edit', compact('seo', 'pageKeys'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeoSetting $seo)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'keywords' => 'required|string|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->only(['title', 'description', 'keywords']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // Upload new thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($seo->thumbnail) {
                Storage::disk('public')->delete($seo->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('seo-thumbnails', 'public');
        }

        $seo->update($data);

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO settings đã được cập nhật thành công.');
    }

}
