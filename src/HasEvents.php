<?php

namespace CrCms\Event;

use CrCms\Event\Contracts\Dispatcher as ContractDispatcher;
use Illuminate\Support\Str;
use BadMethodCallException;

/**
 * Class HasEvents
 * @package CrCms\Event
 */
trait HasEvents
{
    /**
     * @var ContractDispatcher
     */
    protected static $dispatcher;

    /**
     * @var array
     */
    protected static $events = [];

    /**
     * @param string $eventName
     * @return string
     */
    protected static function fullEventName(string $eventName): string
    {
        return static::class . ":{$eventName}";
    }

    /**
     * @param string $class
     * @return void
     */
    public static function observer(string $class)
    {
        array_map(function ($event) use ($class) {
            static::registerEvent($event, $class . '@' . Str::camel($event));
        }, static::$events);
    }

    /**
     * @param string $event
     * @param $listener
     * @return void
     */
    public static function registerEvent(string $event, $listener)
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event, static::$events, true)
        ) {
            static::$dispatcher->listen([static::fullEventName($event)], $listener);
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
            in_array($event, static::$events, true)
        ) {
            static::$dispatcher->push(static::fullEventName($event), $listener);
        }
    }

    /**
     * @param string $event
     * @return mixed
     */
    public function fireEvent(string $event, ...$params)
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event, static::$events, true)
        ) {
            array_unshift($params, $this);
            return static::$dispatcher->dispatch(static::fullEventName($event), ...$params);
        }

        return null;
    }

    /**
     * @param string $event
     * @return bool
     */
    public static function hasListeners(string $event): bool
    {
        if (
            static::$dispatcher instanceof ContractDispatcher &&
            in_array($event, static::$events, true)
        ) {
            return static::$dispatcher->hasListeners(static::fullEventName($event));
        }

        return false;
    }

    /**
     * @return array
     */
    public static function events(array $events): array
    {
        return array_merge(
            array_keys($events),
            array_keys(static::$events)
        );
    }

    /**
     * @param ContractDispatcher $dispatcher
     */
    public static function setDispatcher(ContractDispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * @return ContractDispatcher
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
        static::$dispatcher = null;
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $name = Str::snake($name);
        if (in_array($name, static::$events, true)) {
            static::registerEvent($name, $arguments[0]);
            return;
        }

        $class = static::class;
        throw new BadMethodCallException("Call to undefined method {$class}::{$name}");
    }
}