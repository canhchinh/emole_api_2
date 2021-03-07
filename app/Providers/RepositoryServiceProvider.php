<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CareerRepository::class, \App\Repositories\CareerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CategoryRepository::class, \App\Repositories\CategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EducationRepository::class, \App\Repositories\EducationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\GenreRepository::class, \App\Repositories\GenreRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\JobRepository::class, \App\Repositories\JobRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PortfolioRepository::class, \App\Repositories\PortfolioRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SnsRepository::class, \App\Repositories\SnsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserCareerRepository::class, \App\Repositories\UserCareerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserCategoryRepository::class, \App\Repositories\UserCategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserGenreRepository::class, \App\Repositories\UserGenreRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserImageRepository::class, \App\Repositories\UserImageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserJobRepository::class, \App\Repositories\UserJobRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserSnsRepository::class, \App\Repositories\UserSnsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ActivityBaseRepository::class, \App\Repositories\ActivityBaseRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FollowRepository::class, \App\Repositories\FollowRepositoryEloquent::class);
        //:end-bindings:
    }
}
