<?php

namespace CrCms\Event\Contracts;

/**
 * Interface Dispatcher
 * @package CrCms\Event\Contracts
 */
interface Dispatcher
{
    /**
     * @param array $events
     * @param string|array $listener
     * @return void
     */
    public function listen(array $events, $listener);

    /**
     * @param string $eventName
     * @param string|array $listener
     * @return void
     */
    public function push(string $eventName, $listener);

    /**
     * @param string $eventName
     * @return bool
     */
    public function hasListeners(string $eventName): bool;

    /**
     * @param string $eventName
     * @return bool
     */
    public function dispatch(string $eventName): bool;

    /**
     * @param string $eventName
     * @return void
     */
    public function forget(string $eventName);
}