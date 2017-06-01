<?php

namespace CrCms\Event;

use CrCms\Event\Contracts\Dispatcher as ContractDispatcher;
use CrCms\Event\Dispatcher;

/**
 * Trait hasEvents
 * @package CrCms\Form
 */
trait HasEvents
{

    /**
     * \Simon\Event\Dispatcher
     */
    protected static $dispatcher = null;

    protected static function fullEventName(string $eventName) : string
    {
        return static::class.":{$eventName}";
    }


    public static function registerEvent(string $event,$callback)
    {
        if (static::$dispatcher instanceof ContractDispatcher && in_array($event,static::events(),true)) {
            static::$dispatcher->listen([static::fullEventName($event)],$callback);
        }
    }

    public static function pushEvent(string $event,$callback)
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event,static::events(),true)
        ) {
            static::$dispatcher->push(static::fullEventName($event),$callback);
        }
    }

    public function fireModelEvent(string $event)
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event,static::events(),true)
        ) {
            static::$dispatcher->dispatch(static::fullEventName($event),$this);
        }

    }

    public static function hasListeners(string $event) : bool
    {
        if (static::$dispatcher instanceof ContractDispatcher && in_array($event,static::events(),true)) {
            static::$dispatcher->hasListeners(static::fullEventName($event));
        }
    }

    abstract public static function events() : array ;
//    public static function events() : array
//    {
//        return [];
//    }

    public static function setDispatcher(Dispatcher $dispatcher)
    {
        if (!static::$dispatcher instanceof ContractDispatcher) {
            static::$dispatcher = $dispatcher;
        }
    }

    public static function getDispatcher()
    {
        return static::$dispatcher;
    }

    public static function unsetDispatcher()
    {
        unset(static::$dispatcher);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $name = snake_case($name);
        if (in_array($name,static::events(),true)) {
            static::registerEvent($name,$arguments[0]);
        }

        $class = static::class;
        throw new \BadMethodCallException("method {$class}:{$name} is not exists");
    }
}