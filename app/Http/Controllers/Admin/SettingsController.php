<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:xem_display_data')->only('index');
        $this->middleware('can:sua_display_data')->only('update');
    }

    public function index(Request $request)
    {
        $setting = Setting::query()->first();
        return view('Admin.pages.settings.index', [
            'setting' => $setting
        ]);
    }

    public function update(Request $request)
    {
        // Validate input
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500', // Giới hạn độ dài
            'index' => 'nullable|boolean',
            'header_script' => 'nullable|string|max:10000',
            'body_script' => 'nullable|string|max:10000',
            'footer_script' => 'nullable|string|max:10000',
        ]);

        // Tìm hoặc tạo mới Setting
        $setting = Setting::query()->first();
        if (!$setting) {
            $setting = new Setting();
        }

        // Sanitize và cập nhật các trường
        $setting->title = strip_tags($request->input('title', $setting->title));
        $setting->description = strip_tags($request->input('description', $setting->description));
        $setting->index = $request->input('index', $setting->index);
        
        // Sanitize script fields - chỉ cho phép script tags an toàn
        $setting->header_script = $this->sanitizeScript($request->input('header_script', $setting->header_script));
        $setting->body_script = $this->sanitizeScript($request->input('body_script', $setting->body_script));
        $setting->footer_script = $this->sanitizeScript($request->input('footer_script', $setting->footer_script));

        $setting->save();

        return back()->with('success', 'Cập nhật thành công');
    }

    /**
     * Sanitize script content to prevent XSS
     */
    private function sanitizeScript($script)
    {
        if (empty($script)) return '';
        
        // Remove dangerous content
        $dangerousPatterns = [
            '/<script[^>]*>.*?<\/script>/is', // Remove script tags
            '/javascript\s*:/i', // Remove javascript: URLs
            '/vbscript\s*:/i', // Remove vbscript: URLs
            '/data\s*:/i', // Remove data: URLs
            '/on\w+\s*=/i', // Remove event handlers
            '/expression\s*\(/i', // Remove CSS expressions
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            $script = preg_replace($pattern, '', $script);
        }
        
        // Remove null bytes and control characters
        $script = str_replace("\0", '', $script);
        $script = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $script);
        
        return $script;
    }
}
