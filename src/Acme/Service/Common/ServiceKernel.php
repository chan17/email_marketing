<?php
namespace Acme\Service\Common;

class ServiceKernel
{

    private static $_instance;

    protected $environment;
    protected $debug;
    protected $booted;
    protected $container;

    protected $pool = array();

    public static function create($container, $environment, $debug)
    {
        if (self::$_instance) {
            return ;
        }

        $instance = new self();
        $instance->environment = $environment;
        $instance->debug = (Boolean) $debug;
        $instance->container = $container;

        self::$_instance = $instance;

        return $instance;
    }
/*
* 
    //这是一个关于service调用的方法，写在 app/里面,调用instance前先调用create
    public function boot ()
    {
        if (true === $this->booted) {
            return;
        }
        parent::boot();
        ServiceKernel::create($this->getContainer(), $this->getEnvironment(), $this->isDebug());
    }
*/

    public static function instance()
    {
        if (empty(self::$_instance)) {
            throw new \RuntimeException('ServiceKernel未实例化');
        }
        self::$_instance->boot();
        return self::$_instance;
    }

    public function boot()
    {
        if (true === $this->booted) {
            return;
        }
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function createService($name)
    {
        if (empty($this->pool[$name])) {
            $namespace = substr(__NAMESPACE__, 0, -strlen('Common')-1);
            list($module, $className) = explode('.', $name);
            $class = $namespace . '\\' . $module. '\\Impl\\' . $className . 'Impl';
            $this->pool[$name] = new $class();
        }
        return $this->pool[$name];
    }

    public function createDao($name)
    {
        if (empty($this->pool[$name])) {
            $namespace = substr(__NAMESPACE__, 0, -strlen('Common')-1);
            list($module, $className) = explode('.', $name);
            $class = $namespace . '\\' . $module. '\\Dao\\Impl\\' . $className . 'Impl';
            $dao = new $class();
            $dao->setConnection($this->container->get('database_connection'));
            $this->pool[$name] = $dao;
        }
        return $this->pool[$name];
    }

}