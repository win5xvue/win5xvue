<?php namespace Bllim\Datatables;

use Illuminate\Support\ServiceProvider;

class DatatablesServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('bllim/datatables', 'datatables', __DIR__);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('bllim.datatables', function($app) {
            return new Datatables;
        });
    }

    /**
     * Register the package's component namespaces.
     *
     * @param  string  $package
     * @param  string  $namespace
     * @param  string  $path
     * @return void
     */
    public function package($package, $namespace = null, $path = null)
    {

        // Is it possible to register the config?
        if (method_exists($this->app['config'], 'package')) {
            $this->app['config']->package($package, $path.'/../../config', $namespace);
        } else {
            // Load the config for now..
            $config = $this->app['files']->getRequire($path .'/../../config/config.php');
            foreach($config as $key => $value){
                $this->app['config']->set($namespace.'::'.$key, $value);
            }
        }

        // Register view files
        $appView = $this->app['path']."/views/packages/{$package}";
        if ($this->app['files']->isDirectory($appView))
        {
            $this->app['view']->addNamespace($namespace, $appView);
        }

        $this->app['view']->addNamespace($namespace, $path.'/views');
    }
}
