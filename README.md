## Lara Form - Laravel Form Package with Form Tampering protection

## Contents

1. <a href="#introduction">Introduction</a>
2. <a href="#installation">Installation</a>
3. <a href="#quick-start">Quick start</a>
3. <a href="#security">Security</a>
4. <a href="#helpers">Helpers</a>
    * <a href="#form-create">Create Form</a>
    * <a href="#form-text-input">Text Input</a>
    * <a href="#form-textarea">Textarea</a>
    * <a href="#form-select">Select</a>
    * <a href="#form-checkbox">Checkbox</a>
    * <a href="#form-radio">Radio Buttons</a>
    * <a href="#form-hidden">Hidden</a>
    * <a href="#form-password">Password</a>
    * <a href="#form-close">close</a>
    * <a href="#form-buttons">buttons</a>
5. <a href="#license">License</a>

## <a id="introduction"></a>Introduction

LaraForm is a Laravel Form wrapper with convenient methods, that includes **Form Tampering protection** and prevents double form submission.

## <a id="installation"></a>Installation

At `composer.json` of your Laravel installation, add the following require line:

``` json
{
    "require": {
        "omnicode/lara-form": "~0.0"
    }
}
```

Run `composer update` to add the package to your Laravel app.

### Laravel 5.0

At `config/app.php`, add the Service Provider and the Facade:

```php
    'providers' => [
      // ...
		  'LaraForm\ServiceProvider\LaraFormServiceProvider'
    ]

	//...

    'aliases' => [
        'Form' => 'LaraForm\Facades\LaraForm'
    ]
```

### Laravel 5.1+

At `config/app.php`, add the Service Provider and the Facade:

```php
    'providers' => [
        LaraForm\ServiceProvider\LaraFormServiceProvider::class,
    ]

    //...

    'aliases' => [
        'Form' => LaraForm\Facades\LaraForm::class,
    ]
```

## <a id="quick-start"></a>Quick start

To create a simple form

```php
{!! LaraForm::create($model, ['action' => route('posts.create') ]) !!}
					
{!! LaraForm::input('email') !!}

{!! LaraForm::submit('Submit') !!}

{!! LaraForm::end() !!}
```

## <a id="helpers"></a>Helpers

### <a id="form-open"></a>open
