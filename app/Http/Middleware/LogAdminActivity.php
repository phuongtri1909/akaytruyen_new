<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $oldValues = $this->captureOldValuesBeforeRequest($request);
        
        $response = $next($request);

        if (!Auth::check()) {
            return $response;
        }

        if (!$request->is('admin/*')) {
            return $response;
        }

        if ($this->shouldSkipLogging($request)) {
            return $response;
        }

        $this->logActivity($request, $response, $oldValues);

        return $response;
    }

    private function shouldSkipLogging(Request $request): bool
    {
        $skipRoutes = [
            'admin/activity-logs',
            'admin/dashboard',
        ];

        $skipMethods = ['GET'];

        if (in_array($request->method(), $skipMethods)) {
            return true;
        }

        foreach ($skipRoutes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }

    private function captureOldValuesBeforeRequest(Request $request): ?array
    {
        return null;
    }

    private function logActivity(Request $request, Response $response, ?array $oldValues = null): void
    {
        try {
            $action = $this->getActionFromRequest($request);
            $modelInfo = $this->getModelInfoFromRequest($request);
            $description = $this->getDescriptionFromRequest($request, $modelInfo);

            // Get old values from session
            $sessionOldValues = null;
            if ($modelInfo['type'] && $modelInfo['id']) {
                $key = $modelInfo['type'] . ':' . $modelInfo['id'];
                $sessionOldValues = session()->get("old_values.{$key}");
                if ($sessionOldValues) {
                    session()->forget("old_values.{$key}");
                }
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => $modelInfo['type'] ?? null,
                'model_id' => $modelInfo['id'] ?? null,
                'model_name' => $modelInfo['name'] ?? null,
                'old_values' => $sessionOldValues,
                'new_values' => $this->getNewValuesFromRequest($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => $description,
                'route_name' => $request->route()?->getName(),
                'route_url' => $request->url()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error logging admin activity', [
                'error' => $e->getMessage(),
                'request' => $request->url(),
                'user_id' => Auth::id()
            ]);
        }
    }

    private function getActionFromRequest(Request $request): string
    {
        $method = $request->method();
        $routeName = $request->route()?->getName() ?? '';

        $actionMap = [
            'POST' => 'create',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete',
        ];

        if (str_contains($routeName, 'bulk-delete')) {
            return 'bulk_delete';
        }
        if (str_contains($routeName, 'toggle-pin')) {
            return 'toggle_pin';
        }
        if (str_contains($routeName, 'ban')) {
            return 'ban';
        }
        if (str_contains($routeName, 'unban')) {
            return 'unban';
        }

        return $actionMap[$method] ?? 'unknown';
    }

    private function getModelInfoFromRequest(Request $request): array
    {
        $routeName = $request->route()?->getName() ?? '';
        $routeParameters = $request->route()?->parameters() ?? [];

        $modelMap = [
            'admin.stories' => 'App\Models\Story',
            'admin.categories' => 'App\Models\Category',
            'admin.chapters' => 'App\Models\Chapter',
            'admin.users' => 'App\Models\User',
            'admin.comments' => 'App\Models\Comment',
            'admin.ratings' => 'App\Models\Rating',
            'admin.seo' => 'App\Models\SeoSetting',
            'admin.donations' => 'App\Models\Donation',
            'admin.donates' => 'App\Models\Donate',
        ];

        $modelType = null;
        foreach ($modelMap as $routePrefix => $modelClass) {
            if (str_starts_with($routeName, $routePrefix)) {
                $modelType = $modelClass;
                break;
            }
        }

        $modelId = null;
        $modelName = null;

        if ($modelType && !empty($routeParameters)) {
            $idParams = ['story', 'category', 'chapter', 'user', 'comment', 'rating', 'seo', 'donation', 'donate'];
            
            foreach ($idParams as $param) {
                if (isset($routeParameters[$param])) {
                    $modelId = $routeParameters[$param]->id ?? $routeParameters[$param];
                    
                    if (is_object($routeParameters[$param])) {
                        $model = $routeParameters[$param];
                        $modelName = $this->getModelDisplayName($model);
                    }
                    break;
                }
            }
        }

        return [
            'type' => $modelType,
            'id' => $modelId,
            'name' => $modelName
        ];
    }

    private function getDescriptionFromRequest(Request $request, array $modelInfo): string
    {
        $action = $this->getActionFromRequest($request);
        $routeName = $request->route()?->getName() ?? '';
        
        $actionText = [
            'create' => 'Tạo mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'bulk_delete' => 'Xóa hàng loạt',
            'toggle_pin' => 'Ghim/Bỏ ghim',
            'ban' => 'Cấm',
            'unban' => 'Bỏ cấm',
        ][$action] ?? ucfirst($action);

        $modelText = $modelInfo['name'] ?? $this->getModelTypeText($modelInfo['type']);

        if ($modelText) {
            return "{$actionText} {$modelText}";
        }

        return "{$actionText} thông qua {$routeName}";
    }

    private function getModelTypeText(?string $modelType): string
    {
        if (!$modelType) {
            return '';
        }

        $modelTexts = [
            'App\Models\Story' => 'truyện',
            'App\Models\Category' => 'danh mục',
            'App\Models\Chapter' => 'chương',
            'App\Models\User' => 'người dùng',
            'App\Models\Comment' => 'bình luận',
            'App\Models\Rating' => 'đánh giá',
            'App\Models\SeoSetting' => 'SEO settings',
            'App\Models\Donation' => 'donation',
            'App\Models\Donate' => 'donate',
        ];

        return $modelTexts[$modelType] ?? class_basename($modelType);
    }

    private function getNewValuesFromRequest(Request $request): ?array
    {
        $safeFields = ['name', 'title', 'description', 'content', 'status', 'is_active', 'is_pinned'];
        
        $data = $request->except(['password', 'password_confirmation', '_token', '_method']);
        
        return array_intersect_key($data, array_flip($safeFields));
    }

    private function getModelDisplayName($model): string
    {
        if (method_exists($model, 'getDisplayName')) {
            return $model->getDisplayName();
        }

        $nameFields = ['name', 'title', 'username', 'email'];
        foreach ($nameFields as $field) {
            if (isset($model->$field)) {
                return $model->$field;
            }
        }

        return class_basename($model) . ' #' . $model->id;
    }

}
