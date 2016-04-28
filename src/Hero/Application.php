<?php

namespace Hero;

use Exception;
use Hero\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\Debug\ErrorHandler;
use Zend\Diactoros\Response as PsrResponse;
use Symfony\Component\Debug\ExceptionHandler;
use Illuminate\Config\Repository as ConfigRepository;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class Application extends Container
{
    /**
     * Indicates if the class aliases have been registered.
     *
     * @var bool
     */
    protected static $aliasesRegistered = false;

    /**
     * The base path of the application installation.
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * All of the loaded configuration files.
     *
     * @var array
     */
    protected $loadedConfigurations = [];

    /**
     * The loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * The service binding methods that have been executed.
     *
     * @var array
     */
    protected $ranServiceBinders = [];

    /**
     * The available container bindings and their respective load methods.
     *
     * @var array
     */
    protected $availableBindings = [
        // Caching
        'cache' => 'registerCacheBindings',
        'cache.store' => 'registerCacheBindings',
        'Illuminate\Contracts\Cache\Factory' => 'registerCacheBindings',
        'Illuminate\Contracts\Cache\Repository' => 'registerCacheBindings',

        // Configuration
        'config' => 'registerConfigBindings',

        // Databases
        'db' => 'registerDatabaseBindings',
        'Illuminate\Database\Eloquent\Factory' => 'registerDatabaseBindings',

        // Event
        'events' => 'registerEventBindings',
        'Illuminate\Contracts\Events\Dispatcher' => 'registerEventBindings',

        // Filesystem
        'files' => 'registerFilesBindings',

        // HTTP Request
        'request' => 'registerRequestBindings',
        'Illuminate\Http\Request' => 'registerRequestBindings',

        // HTTP Response
        'Illuminate\Contracts\Routing\ResponseFactory' => 'registerResponseBindings',

        // PSR compliment HTTP Request
        'Psr\Http\Message\ServerRequestInterface' => 'registerPsrRequestBindings',

        // PSR compliment HTTP Response
        'Psr\Http\Message\ResponseInterface' => 'registerPsrResponseBindings',

        // Laravel router
        'router' => 'registerRouterBindings',
        'Illuminate\Routing\Router' => 'registerRouterBindings',

        // View
        'view' => 'registerViewBindings',
        'Illuminate\Contracts\View\Factory' => 'registerViewBindings',

        // Mailer
        'mailer' => 'registerMailBindings',
    ];

    /**
     * Create a new Lumen application instance.
     *
     * @param string|null $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;

        date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

        $this->bootstrapContainer();
        $this->registerContainerAliases();
        $this->registerWebDebugger();
        $this->withFacades();
    }

    /**
     * Run application.
     */
    public function run()
    {
        try {
            return $this
                ->make('router')
                ->dispatch($this->make('request'))
                ->send();
        } catch (Exception $e) {
            $handler = new Handler();

            return $handler->handle($e);
        }
    }

    /**
     * Register a service provider with the application.
     *
     * @param \Illuminate\Support\ServiceProvider|string $provider
     * @param array                                      $options
     * @param bool                                       $force
     */
    public function register($provider, $options = [], $force = false)
    {
        if (array_key_exists($provider, $this->loadedProviders)) {
            return;
        }

        $this->loadedProviders[$provider] = true;

        $provider = new $provider($this);

        $provider->register();
        $provider->boot();
    }

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     * @param array  $parameters
     *
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        $abstract = $this->getAlias($this->normalize($abstract));

        if (array_key_exists($abstract, $this->availableBindings) &&
            !array_key_exists($this->availableBindings[$abstract], $this->ranServiceBinders)) {
            $this->{$method = $this->availableBindings[$abstract]}();

            $this->ranServiceBinders[$method] = true;
        }

        return parent::make($abstract, $parameters);
    }

    /**
     * Configure and load the given component and provider.
     *
     * @param string       $config
     * @param array|string $providers
     * @param string|null  $return
     *
     * @return mixed
     */
    public function loadComponent($config, $providers, $return = null)
    {
        $this->configure($config);

        foreach ((array) $providers as $provider) {
            $this->register($provider);
        }

        return $this->make($return ?: $config);
    }

    /**
     * Load a configuration file into the application.
     *
     * @param string $name
     */
    public function configure($name)
    {
        if (isset($this->loadedConfigurations[$name])) {
            return;
        }

        $this->loadedConfigurations[$name] = true;

        if (is_readable($path = $this->configPath($name))) {
            $this->make('config')->set($name, require $path);
        }
    }

    /**
     * Get the base path for the application.
     *
     * @param string|null $path
     *
     * @return string
     */
    public function basePath($path = null)
    {
        return $this->basePath.($path ? '/'.$path : $path);
    }

    /**
     * Get source path of application
     * .
     *
     * @param string $path
     *
     * @return string
     */
    public function appPath($path = null)
    {
        return $this->basePath('/src/Hero'.($path ? '/'.$path : $path));
    }

    /**
     * Get configuration path.
     *
     * @param string $name
     *
     * @return string
     */
    public function configPath($name = null)
    {
        return ($name === null)
            ? $this->basePath('config').'/'
            : $this->basePath('config').'/'.$name.'.php';
    }

    /**
     * Get database path.
     *
     * @param string $path
     *
     * @return string
     */
    public function databasePath($path = null)
    {
        return $this->basePath().'/database'.($path ? '/'.$path : $path);
    }

    /**
     * Get storage path.
     *
     * @param string $path
     *
     * @return string
     */
    public function storagePath($path = null)
    {
        return $this->basePath().'/storage'.($path ? '/'.$path : $path);
    }

    /**
     * Bootstrap the application container.
     */
    protected function bootstrapContainer()
    {
        static::setInstance($this);

        $this->instance('app', $this);
    }

    /**
     * Register the core container aliases.
     */
    protected function registerContainerAliases()
    {
        $this->aliases = [
            'Illuminate\Contracts\Foundation\Application' => 'app',
            'Illuminate\Contracts\Cache\Factory' => 'cache',
            'Illuminate\Contracts\Cache\Repository' => 'cache.store',
            'Illuminate\Contracts\Config\Repository' => 'config',
            'Illuminate\Container\Container' => 'app',
            'Illuminate\Contracts\Container\Container' => 'app',
            'Illuminate\Database\ConnectionResolverInterface' => 'db',
            'Illuminate\Database\DatabaseManager' => 'db',
            'Illuminate\Contracts\Events\Dispatcher' => 'events',
            'request' => 'Illuminate\Http\Request',
            'Illuminate\Contracts\View\Factory' => 'view',
        ];
    }

    /**
     * Register web debugger and exception handler.
     */
    protected function registerWebDebugger()
    {
        ErrorHandler::register();
        ExceptionHandler::register(env('APP_DEBUG', false));
    }

    /**
     * Register the facades for the application.
     */
    protected function withFacades()
    {
        Facade::setFacadeApplication($this);

        if (!static::$aliasesRegistered) {
            static::$aliasesRegistered = true;

            class_alias('Illuminate\Support\Facades\App', 'App');
            class_alias('Illuminate\Support\Facades\Auth', 'Auth');
            class_alias('Illuminate\Support\Facades\Cache', 'Cache');
            class_alias('Illuminate\Support\Facades\Config', 'Config');
            class_alias('Illuminate\Support\Facades\File', 'File');
            class_alias('Illuminate\Support\Facades\Mail', 'Mail');
            class_alias('Illuminate\Support\Facades\DB', 'DB');
            class_alias('Illuminate\Support\Facades\Schema', 'Schema');
            class_alias('Illuminate\Support\Facades\Route', 'Route');
            class_alias('Illuminate\Support\Facades\URL', 'URL');
            class_alias('Illuminate\Support\Facades\View', 'View');
            class_alias('Illuminate\Support\Facades\Request', 'Request');
            class_alias('Illuminate\Support\Facades\Response', 'Response');
        }
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerCacheBindings()
    {
        $this->singleton('cache', function () {
            return $this->loadComponent('cache', 'Illuminate\Cache\CacheServiceProvider');
        });

        $this->singleton('cache.store', function () {
            return $this->loadComponent('cache', 'Illuminate\Cache\CacheServiceProvider', 'cache.store');
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerConfigBindings()
    {
        $this->singleton('config', function () {
            return new ConfigRepository();
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerDatabaseBindings()
    {
        $this->singleton('db', function () {
            return $this->loadComponent(
                'database',
                [
                    'Illuminate\Database\DatabaseServiceProvider',
                ],
                'db'
            );
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerEventBindings()
    {
        $this->singleton('events', function () {
            $this->register('Illuminate\Events\EventServiceProvider');

            return $this->make('events');
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerFilesBindings()
    {
        $this->singleton('files', function () {
            return new Filesystem();
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerRequestBindings()
    {
        $this->singleton('Illuminate\Http\Request', function () {
            return Request::capture();
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerResponseBindings()
    {
        $this->singleton('Illuminate\Contracts\Routing\ResponseFactory', function () {
            return $this->make('Illuminate\Routing\ResponseFactory');
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerPsrRequestBindings()
    {
        $this->singleton('Psr\Http\Message\ServerRequestInterface', function () {
            return (new DiactorosFactory())->createRequest($this->make('request'));
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerPsrResponseBindings()
    {
        $this->singleton('Psr\Http\Message\ResponseInterface', function () {
            return new PsrResponse();
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerRouterBindings()
    {
        $this->singleton('router', function () {
            return $this->loadComponent('router', 'Illuminate\Routing\RoutingServiceProvider');
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerViewBindings()
    {
        $this->singleton('view', function () {
            return $this->loadComponent('view', 'Illuminate\View\ViewServiceProvider');
        });
    }

    /**
     * Register container bindings for the application.
     */
    protected function registerMailBindings()
    {
        $this->singleton('mailer', function () {
            $this->configure('mail');

            return $this->loadComponent('mailer', 'Illuminate\Mail\MailServiceProvider');
        });
    }
}
