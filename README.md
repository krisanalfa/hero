# Hero

Hero is a web application skeleton in flavor of Laravel.
Hero is a Lightweight version of Laravel application.
Purpose of this skeleton is simplifying Laravel so you can use them for a simple lightweight website.

## Features

Hero works almost like Laravel but in `minified source` as it only contains:

- Elixir
- Mailer
- Caching
- Routing
- Templating (Blade)

## Installation

Follow this simple guide to use Hero as your application skeleton.

```sh
composer create-project krisanalfa/hero my-app --prefer-dist
cd my-app

# below is used when you want to see a landing page of Hero
npm install # have a relax, grab a coffee
bower install # almost there
gulp # preparing assets
php -S 127.0.0.1:8000 -t public/ # open your browser at http://localhost:8000
```

## Error Handling

To handle an exception, you may create your own custom exception handler.
For example if you want to handle `ModelNotFoundException`, you may create an exception handler in `src/Hero/Exceptions/Handlers` with classname `ModelNotFoundExceptionHandler`.
Hero exception handler expect your exception handler has one method name `handle`. Below is the example:

```php
namespace Hero\Exceptions\Handlers;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModelNotFoundExceptionHandler
{
    public function handle(ModelNotFoundException $e)
    {
        // Do something ...
    }
}
```

For real example read `Hero\Exceptions\Handlers\NotFoundHttpExceptionHandler`.

> **Note:** You may change this exception handler behavior in `Hero\Exceptions\Handler` class.

## TODO

- Artisan
- Migration

## License

Copyright (c) 2016 Krisan Alfa Timur

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
