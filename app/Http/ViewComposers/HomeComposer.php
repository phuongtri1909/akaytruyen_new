<?php

namespace App\Http\ViewComposers;

use App\Helpers\Helper;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;

class HomeComposer
{

    /**
     * Create a new view composer instance.
     *
     */
    public function __construct(

    )
    {
    }

    /**
     * Bind data to the view.
     *
     * @param \Illuminate\View\View $view
     * @return void
     */
    public function compose($view)
    {
        $categories = Helper::getCachedCategories();

        $view->with([
            'categories'    => $categories
        ]);
    }
}
