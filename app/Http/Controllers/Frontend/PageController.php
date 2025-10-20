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
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class PageController extends Controller
{
    /**
     * Setup SEO for page
     */
    private function setupSEO($pageKey)
    {
        $seoSetting = \App\Models\SeoSetting::getByPageKey($pageKey);
        
        if ($seoSetting) {
            SEOTools::setTitle($seoSetting->title);
            SEOTools::setDescription($seoSetting->description);
            SEOMeta::setKeywords($seoSetting->keywords);
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($seoSetting->title);
            OpenGraph::setDescription($seoSetting->description);
            OpenGraph::setUrl(url()->current());
            OpenGraph::addProperty('type', 'website');
            if ($seoSetting->thumbnail) {
                OpenGraph::addImage($seoSetting->thumbnail_url);
            }

            TwitterCard::setTitle($seoSetting->title);
            TwitterCard::setDescription($seoSetting->description);
            TwitterCard::setSite('@AkayTruyen');
            if ($seoSetting->thumbnail) {
                TwitterCard::addImage($seoSetting->thumbnail_url);
            }
        } else {
            // Fallback SEO
            $fallbackTitle = ucfirst(str_replace('-', ' ', $pageKey)) . ' - Akay Truyện';
            SEOTools::setTitle($fallbackTitle);
            SEOTools::setDescription('Trang ' . ucfirst(str_replace('-', ' ', $pageKey)) . ' của Akay Truyện.');
            SEOTools::setCanonical(url()->current());

            OpenGraph::setTitle($fallbackTitle);
            OpenGraph::setDescription('Trang ' . ucfirst(str_replace('-', ' ', $pageKey)) . ' của Akay Truyện.');
            OpenGraph::setUrl(url()->current());
            OpenGraph::addProperty('type', 'website');
            OpenGraph::addImage(asset('images/logo/Logoakay.png'));

            TwitterCard::setTitle($fallbackTitle);
            TwitterCard::setDescription('Trang ' . ucfirst(str_replace('-', ' ', $pageKey)) . ' của Akay Truyện.');
            TwitterCard::setSite('@AkayTruyen');
            TwitterCard::addImage(asset('images/logo/Logoakay.png'));
        }
    }

    public function contact()
    {
        $this->setupSEO('contact');
        return view('Frontend.pages.contact');
    }

    public function privacyPolicy()
    {
        $this->setupSEO('privacy-policy');
        return view('Frontend.pages.privacy-policy');
    }

    public function terms()
    {
        $this->setupSEO('terms');
        return view('Frontend.pages.terms');
    }

    public function contentRules()
    {
        $this->setupSEO('content-rules');
        return view('Frontend.pages.content-rules');
    }

    public function confidental()
    {
        $this->setupSEO('confidental');
        return view('Frontend.pages.confidental');
    }
} 