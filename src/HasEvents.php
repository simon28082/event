<?php

namespace CrCms\Event;

use CrCms\Event\Contracts\Dispatcher as ContractDispatcher;

/**
 * Class HasEvents
 * @package CrCms\Event
 */
trait HasEvents
{
    /**
     * @var null
     */
    protected static $dispatcher = null;

    /**
     * @param string $eventName
     * @return string
     */
    protected static function fullEventName(string $eventName): string
    {
        return static::class . ":{$eventName}";
    }

    /**
     * @param string $event
     * @param $callback
     * @return void
     */
    public static function registerEvent(string $event, $callback)
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event, static::events(), true)
        ) {
            static::$dispatcher->listen([static::fullEventName($event)], $callback);
        }
    }

    /**
     * @param string $event
     * @param $listener
     * @return void
     */
    public static function pushEvent(string $event, $listener)
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event, static::events(), true)
        ) {
            static::$dispatcher->push(static::fullEventName($event), $listener);
        }
    }

    /**
     * @param string $event
     * @return void
     */
    public function fireEvent(string $event, ...$params)
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event, static::events(), true)
        ) {
            array_unshift($params, $this);
            static::$dispatcher->dispatch(static::fullEventName($event), ...$params);
        }
    }

    /**
     * @param string $event
     * @return bool
     */
    public static function hasListeners(string $event): bool
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event, static::events(), true)
        ) {
            return static::$dispatcher->hasListeners(static::fullEventName($event));
        }

        return false;
    }

    /**
     * @return array
     */
    abstract public static function events(): array;

    /**
     * @param Dispatcher $dispatcher
     * @return void
     */
    public static function setDispatcher(Dispatcher $dispatcher)
    {
        if (!static::$dispatcher instanceof ContractDispatcher) {
            static::$dispatcher = $dispatcher;
        }
    }

    /**
     * @return null
     */
    public static function getDispatcher()
    {
        return static::$dispatcher;
    }

    /**
     * @return void
     */
    public static function unsetDispatcher()
    {
        unset(static::$dispatcher);
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $name = snake_case($name);
        if (in_array($name, static::events(), true)) {
            static::registerEvent($name, $arguments[0]);
            return;
        }

        $class = static::class;
        throw new \BadMethodCallException("Call to undefined method {$class}::{$name}");
    }
}