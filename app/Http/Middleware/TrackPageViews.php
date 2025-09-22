<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\ActivityTrackingService;
use Illuminate\Http\Request;

class TrackPageViews
{
        public function handle(Request $request, Closure $next)
        {
            $response = $next($request);

            $isApi = str_starts_with($request->path(), 'api/');
            $ok = $request->isMethod('GET')
                && !str_starts_with($request->path(), 'admin/')
                && $response->getStatusCode() === 200
                && (
                    $isApi // always allow API
                    || (!$request->ajax() && !$request->expectsJson()) // only pages for web
                );

            if ($ok) {
                $pageUrl = $request->fullUrl();

                if ($isApi) {
                    ActivityTrackingService::trackApiHit(
                        endpoint: $pageUrl,
                        input: $request->query(), // GET query params (won't include JSON body for GET)
                        email: optional(auth()->user())->email,
                        status: $response->getStatusCode(),
                        method: $request->getMethod(),
                    );
                    ActivityTrackingService::trackTimeOnSite(null, $pageUrl, optional(auth()->user())->email);
                } else {
                    $pageTitle = $this->extractPageTitle($response->getContent());
                    ActivityTrackingService::trackPageView($pageUrl, $pageTitle);
                    ActivityTrackingService::trackTimeOnSite(null, $pageUrl, optional(auth()->user())->email);
                }
            }

            return $response;
        }


    private function extractPageTitle($content)
    {
        if (preg_match('/<title>(.*?)<\/title>/i', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
