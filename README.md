# Unmaintained

Laravel-Revision for Larvel 5
=============================

This package allows you to log changes made to specific models.

> Note: There are many packages, which cover similar aspects.
> This package was heavily inspired by  [Revisionable](https://github.com/VentureCraft/revisionable/)
> and [Laravel-Auditing](http://www.laravel-auditing.com/)
> you should definitely check them out.

## Why another?

Simple: Both these packages do not - at the time of writing - multi-auth support.
__This one does.__

I ran into a problem while working on a project, namely that I wanted to use
said packages and got stuck as soon as I realized that they do not support
different Auth-Providers. So I went ahead and made this package.
It follows the same basic principle and uses a very similar syntax.

## Installation

Require via composer:
```
composer require lukasjankowski/laravel-revision
```

Include the service provider within your ``` config/app.php ```.
```php
'providers' => [
    // ...
    LukasJankowski\Revision\RevisionServiceProvider::class
];
```

Publish the config file.
```
php artisan vendor:publish --provider="LukasJankowski\Revision\RevisionServiceProvider" --tag="config"
```

Publish the migration.
```
php artisan vendor:publish --provider="LukasJankowski\Revision\RevisionServiceProvider" --tag="migrations"
```

You can change the table name within the config file, which will be located at: ``` config/revision.php ```.

Finally migrate the migration:
```
php artisan migrate
```

## Usage

Add the ``` LukasJankowski\Revision\Traits\HasRevisions ``` trait to the models,
which you want to enable revisions on.

Example:

```php
use Illuminate\Database\Eloquent\Model;
use LukasJankowski\Revision\Traits\HasRevision;
 
class Post extends Model
{
    use HasRevisions;
    
    // ...
}
```

Add the ``` LukasJankowski\Revision\Traits\IsReviser ``` trait to the models,
which you want to be the ones perfoming the revisions.

Example:

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use LukasJankowski\Revision\Traits\IsReviser;
 
class User extends Authenticatable
{
    use IsReviser;
    
    // ...
}
```

You can perform the configuration in the generalized ``` config/revision.php ``` file.
But if you want to perform more fine grained control:

````php
use Illuminate\Database\Eloquent\Model;
use LukasJankowski\Revision\Traits\HasRevision;
 
class Post extends Model
{
    use HasRevisions;
    
    /**
     * The fields, which will not be logged in the revisions.
     *
     * @var array
     */
    protected $revisionExclude = [
        'password', 'remember_token',
    ];
    
    /**
     * The threshold, which will limit the revisions to that number.
     *
     * @var int
     */
    protected $revisionThreshold = 123;
    
    // ...
}
````


Revisions will log the ``` created ```, ``` updated ```, ``` deleted ``` and ``` restored ``` event.

You can then access these revisions:

From the model, which "hasRevisions":
````php
    $post = App\Post::find(1);
    $post->revisions; // Returns all revisions made to this record in a collection.
````
From the model, which "hasRevisions" with eager-loading:
```php
    $post = App\Post::find(1);
    $post->revisions()->with('revisers')->get(); // Same as above, but eager load the ones, who performed the revision.
```
From the model, which "isReviser", which performed the revision:
```php
    $user = App\User::find(1);
    $user->revised; // Returns all revisions made by this record in a collection.
```
From the model, which "isReviser", which performed the revision with eager-loading:
```php
    $user = App\User::find(1);
    $user->revised()->with('revisions')->get(); 
    // Same as above, but eager load the models on which the revisions were performed.
```
Since it's a collection it supports all its methods:
```php
    $post = App\Post::find(1);
    $post->revisions->first();
    $post->revisions->last();
    $post->revisions->find(12);
    // ...
```
But also a way to get the modified data:
```php
    $post = App\Post::find(1);
    $revision = $post->revisions->first();
    $revision->getModified();
    // Which will return an associative array similar to this:
    [
        'title' => [
            'new' => 'Fresh and new',
            'old' => 'Old and stale',
        ],
        'body' => [
            'new' => 'Lorem ipsum...',
            'old' => 'Placeholder.Place...',
        ],
    ];
```

As you can see it is very similar to the above mentioned packages.
However it doesn't matter, who performs the revision as long as they
implement the IsReviser trait. The package will then go ahead and try
to fetch the currently logged in user as the reviser, if that fails it will
default to null.

## TODO
 - Unit tests
