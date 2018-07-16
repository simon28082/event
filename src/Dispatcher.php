<?php

namespace CrCms\Event;

use CrCms\Event\Contracts\Dispatcher as ContractDispatcher;
use Closure;
use Illuminate\Support\Str;
use BadMethodCallException;

/**
 * Class Dispatcher
 * @package CrCms\Event
 */
class Dispatcher implements ContractDispatcher
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param array $events
     * @param string|array $listener
     * @return void
     */
    public function listen(array $events, $listener)
    {
        foreach ($events as $eventName) {
            $this->listeners[$eventName][] = $this->markListener($listener, $eventName);
        }
    }

    /**
     * @param string $eventName
     * @param string|array $listener
     * @return void
     */
    public function push(string $eventName, $listener)
    {
        $this->listeners[$eventName][] = $this->markListener($listener, $eventName);
    }

    /**
     * @param string $eventName
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    /**
     * @param string $eventName
     * @param array ...$params
     * @return bool
     */
    public function dispatch(string $eventName, ...$params): bool
    {
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listeners) {
                foreach ($listeners as $listener) {
                    if (is_array($listener) && method_exists($listener[0], $listener[1])) {
                        $result = $listener[0]->{$listener[1]}(...$params);
                    } elseif (is_object($listener)) {
                        $result = $listener(...$params);
                    } else {
                        $result = null;
                    }
                    if ($result === false) return $result;
                }
            }
        }

        return true;
    }

    /**
     * @param string $eventName
     * @return void
     */
    public function forget(string $eventName)
    {
        unset($this->listeners[$eventName]);
    }

    /**
     * @param $listeners
     * @param string $eventName
     * @return array
     */
    protected function markListener($listeners, string $eventName): array
    {
        $result = [];
        foreach ((array)$listeners as $listener) {
            $result[] = $listener instanceof Closure ?
                $this->markClosureListener($listener) :
                $this->markClassListener($listener, $eventName);
        }

        return $result;
    }

    /**
     * @param Closure $listener
     * @return Closure
     */
    protected function markClosureListener(Closure $listener): Closure
    {
        return function ($eventSource) use ($listener) {
            return $listener($eventSource);
        };
    }

    /**
     * @param string $listener
     * @param string $eventName
     * @return array
     */
    protected function markClassListener(string $listener, string $eventName): array
    {
        $format = explode('@', $listener);
        $listener = new $format[0];
        if (!isset($format[1])) {
            $method = Str::camel(explode(':', $eventName)[1]);
            if (method_exists($listener, $method)) {
                $format[1] = $method;
            } else if (method_exists($listener, 'handle')) {
                $format[1] = 'handle';
            } else {
                $class = static::class;
                throw new BadMethodCallException("Call to undefined method {$class}::{$method}");
            }
        }

        return [$listener, $format[1], $format[0] . '@' . $format[1]];
    }
}