## Query client for Laravel 5

Require this package in your composer.json and update composer.

    composer require czbas23/laravel-query-client

## Installation

### Laravel 5.x:

In Laravel 5.5 or more than the service provider will automatically get registered. In older versions of the framework just add the service provider in config/app.php file:

    Czbas23\LaravelQueryClient\Providers\LaravelQueryClientProvider::class,

You can optionally use the facade for shorter code. Add this to your facades:

    'LaravelQueryClient' => Czbas23\LaravelQueryClient\Facades\LaravelQueryClientFacade::class,
  
## Using

Basic for use

    $User = \LaravelQueryClient::setModel(new \App\User)
    ->setCrud('read')
    ->query([
        ['where', ['id', 1]],
        ['where', [
            ['orWhere', ['name', 'like', '%Foo%']],
            ['orWhere', ['name', 'like', '%Bar%']]
        ]],
        ['get']
    ])
    ->getResult();

    Equals

    $User = \App\User::where('id', 1)
    ->where(function ($query) {
        $query->orWhere('name', 'like', '%Foo%');
        $query->orWhere('name', 'like', '%Bar%');
    })
    ->get();

Query with relationship

    $countUser = \LaravelQueryClient::setModel(new \App\User)
    ->setCrud('read')
    ->pushRelation('posts')
    ->query([
        ['has', ['posts']],
        ['count']
    ])
    ->getResult();

    Equals

    $countUser = \App\User::has('posts')->count();

Insert data

    $result = \LaravelQueryClient::setModel(new \App\User)
    ->setCrud('create')
    ->query([
        ['create', [
            [
                'name' => 'Foo Bar',
                'email' => 'example@mail.com',
                'password' => bcrypt('secret'),
            ]
        ]],
    ])
    ->getResult();

    Equals

    $result = \App\User::create([
        'name' => 'Foo Bar',
        'email' => 'example@mail.com',
        'password' => bcrypt('secret'),
    ]);

### License

This Laravel query client is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)