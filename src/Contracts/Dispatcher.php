<?php

namespace CrCms\Event\Contracts;

interface Dispatcher
{
    public function listen(array $events, $listener);

    public function push(string $eventName,$listener);

    public function hasListeners(string $eventName);

    public function dispatch(string $eventName);

    public function forget(string $eventName);
}