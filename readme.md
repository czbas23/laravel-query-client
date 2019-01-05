## Query client for Laravel 5

Require this package in your composer.json and update composer.

    composer require czbas23/laravel-query-client

## Installation

### Laravel 5.x:

After updating composer, add the ServiceProvider to the providers array in config/app.php

    Czbas23\LaravelQueryClient\Providers\LaravelQueryClientProvider::class,

You can optionally use the facade for shorter code. Add this to your facades:

    'LaravelQueryClient' => Czbas23\LaravelQueryClient\Facades\LaravelQueryClientFacade::class,
  
## Using

Basic for use

    $User = \LaravelQueryClient::setModel(new \App\User)
    ->setCrud('read')
    ->query([
        'whereIn' => ['id', [1, 2, 3]],
    ])
    ->getModel();
    dd($User->first()->toArray());

Query with relationship

    $countUser = \LaravelQueryClient::setModel(new \App\User)
    ->setCrud('read')
    ->pushRelation('user_type')
    ->query([
        'with' => ['user_type'],
        'count' => null,
    ])
    ->getRetrievingResult();
    dd($countUser);

### License

This Laravel query client is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)