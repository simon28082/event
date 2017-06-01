<?php

namespace CrCms\Event;


class Dispatcher implements \CrCms\Event\Contracts\Dispatcher
{

    protected $listeners = [];

//    protected $eventSource = null;

//    public function __construct(EventSource $eventSource = null)
//    {
//        if ($eventSource) $this->eventSource($eventSource);
//    }
//
//    public function eventSource(EventSource $eventSource) : self
//    {
//        $this->eventSource = $eventSource->source();
//        return $this;
//    }
//
//    public function getEventSource() : EventSource
//    {
//        return $this->eventSource;
//    }


    public function listen(array $events, $listener)
    {
        foreach ($events as $eventName) {
            $this->listeners[$eventName][] = $this->markListener($listener,$eventName);
        }
    }

    public function push(string $eventName, $listener)
    {
        $this->listeners[$eventName][] = $this->markListener($listener,$eventName);
    }

    public function hasListeners(string $eventName) : bool
    {
        return !empty($this->listeners[$eventName]);
    }

    public function dispatch(string $eventName, ...$params)
    {
        foreach ($this->listeners[$eventName] as $listeners) {
            foreach ($listeners as $listener) {
                if (is_array($listener)) {
                    $result = $listener[0]->{$listener[1]}(...$params);
                } else {
                    $result = $listener(...$params);
                }

                if ($result === false) {
                    return $result;
                }
            }
        }
    }

    public function forget(string $eventName)
    {
        unset($this->listeners[$eventName]);
    }

    protected function markListener($listeners,string $eventName)
    {
        $result = [];
            foreach ((array)$listeners as $listener) {
                if ($listener instanceof \Closure) {
                    $result[] = $this->markClosureListener($listener);
                } else {
                    $result[] = $this->markClassListener($listener,$eventName);
                }
            }


        return $result;
    }

    protected function markClosureListener(\Closure $listener) : \Closure
    {
        return function($eventSource) use ($listener){
            return $listener($eventSource);
        };
    }


    protected function markClassListener(string $listener,string $eventName) : array
    {
        $format = explode('@',$listener);

//        if (!isset($format[1])) {
//            return $format;
//        } else {
//            $classMethods = get_class_methods($listener);
//
//            $method = camel_case(explode(':',$eventName)[1]);
//            if (in_array($method,$classMethods,true)) {
//
//            }
//        }
//
//        if (isset($format[1])) {
//            $listener = new $format[0];
//            if (!method_exists($listener,$format[1])) {
//                throw new \BadMethodCallException('not exists');
//            }
//        } else {
//            $method = camel_case(explode(':',$eventName)[1]);
//            if (method_exists($listener,$method)) {
//                $format[1] = $method;
//            } else if (method_exists($listener,'handle')) {
//                $format[1] = 'handle';
//            } else {
//                return $format;
//            }
//        }


//
//
        $listener = new $listener;
        if (!isset($format[1])) {
            $method = camel_case(explode(':',$eventName)[1]);
            if (method_exists($listener,$method)) {
                $format[1] = $method;
            } else if (method_exists($listener,'handle')) {
                $format[1] = 'handle';
            } else {
                throw new \BadMethodCallException('method not exists');
            }
        }

        return [$listener,$format[1],$format[0].'@'.$format[1]];
    }

}