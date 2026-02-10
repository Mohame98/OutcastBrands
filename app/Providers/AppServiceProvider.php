<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Exceptions\Handler as CustomHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(ExceptionHandler::class, CustomHandler::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Relation::morphMap([
      'brand'   => \App\Models\Brand::class,
      'user'    => \App\Models\User::class,
      'comment' => \App\Models\Comment::class,
    ]);
  }   
}
