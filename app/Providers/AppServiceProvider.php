<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        // Mail::alwaysTo('subhabrata06.dapl@gmail.com');
        if(config('app.env') === 'production')
        \URL::forceScheme('https');
    
        date_default_timezone_set(config('app.timezone'));
        Paginator::useBootstrap();
        \App\Models\TicketType::observe(\App\Observers\TicketTypeObserver::class);

        view()->composer('*', function ($view) {
            $dynamicNavs = \App\Models\NavbarDynamic::where('status', 'active')
                ->orderBy('order_by', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    return $item->category ?? 'General';
                });
            $view->with('dynamicNavs', $dynamicNavs);
        });

        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }
}
