<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Exception;

class SitemapController extends Controller
{
    public function index()
    {
        try {
            if (!View::exists('Frontend.sitemap.index')) {
                Log::error('Sitemap index view does not exist');
                return response()->json(['error' => 'Sitemap template not found'], 500);
            }
            
            return response()->view('Frontend.sitemap.index')->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap index: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    public function categories()
    {
        try {
            if (!View::exists('Frontend.sitemap.categories')) {
                Log::error('Sitemap categories view does not exist');
                return response()->json(['error' => 'Sitemap template not found'], 500);
            }
            
            $categories = Category::all();
            
            return response()->view('Frontend.sitemap.categories', [
                'categories' => $categories,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap categories: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    public function stories()
    {
        try {
            if (!View::exists('Frontend.sitemap.stories')) {
                Log::error('Sitemap stories view does not exist');
                return response()->json(['error' => 'Sitemap template not found'], 500);
            }
            
            $stories = Story::where('status', Story::STATUS_ACTIVE)
                ->select('id', 'slug', 'updated_at')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            return response()->view('Frontend.sitemap.stories', [
                'stories' => $stories,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap stories: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    public function chapters()
    {
        try {
            if (!View::exists('Frontend.sitemap.chapters')) {
                Log::error('Sitemap chapters view does not exist');
                return response()->json(['error' => 'Sitemap template not found'], 500);
            }
            
            $page = (int) request()->get('page', 1);
            $perPage = 500;
            $page = max(1, $page);
            
            $chapters = Chapter::with('story:id,slug')
                ->published()
                ->whereHas('story', fn($q) => $q->where('status', \App\Models\Story::STATUS_ACTIVE))
                ->select('id', 'story_id', 'slug', 'updated_at')
                ->orderBy('id', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            return response()->view('Frontend.sitemap.chapters', [
                'chapters' => $chapters,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in sitemap chapters: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Tạo sitemap index cho chapters
     * Sẽ tạo nhiều sitemap con cho chapters, mỗi sitemap chứa 5000 chapter
     */
    public function chaptersIndex()
    {
        try {
            $totalChapters = Chapter::published()
                ->whereHas('story', fn($q) => $q->where('status', \App\Models\Story::STATUS_ACTIVE))
                ->count();
            $perPage = 500;
            $totalPages = (int) ceil($totalChapters / $perPage);
            
            return response()->view('Frontend.sitemap.chapters_index', [
                'totalPages' => $totalPages,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in chapters sitemap index: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tạo sitemap cho các trang chính
     */
    public function mainPages()
    {
        try {
            return response()->view('Frontend.sitemap.main_pages', [])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in main pages sitemap: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tạo sitemap cho các tác giả
     */
    public function authors()
    {
        try {
            $authors = User::where('is_author', true)->orWhere('role', 'author')->get();
            
            return response()->view('Frontend.sitemap.authors', [
                'authors' => $authors,
            ])->header('Content-Type', 'text/xml');
        } catch (Exception $e) {
            Log::error('Error in authors sitemap: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }
} 