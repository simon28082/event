## Example

### Set the scheduler
```$xslt
Example::setDispatcher(new \CrCms\Event\Dispatcher);
```

### Register event
```$xslt
Example::registerEvent('event1',$listener);
```

### Push event listener
```$xslt
Example::pushEvent('event1',$listener);
```

### Listening method

```$xslt

//first
$listener => ExampleListener1::class@listen

//second
$listener => ExampleListener2::class

//third
$listener => function($object) {
    ...
}

class ExampleListener1
{
    public function listen($object)
    {
        ...
    }
}

class ExampleListener2
{
    public function handle($object)
    {
        ...
    }
}

function($object) {
    ...
}

```

### Event trigger

```$xslt
class Example {

    use CrCms\Event\HasEvents;
    
    # Set events
    public static function events() : array
    {
        return ['event1','event2'];
    }
    
    public function example()
    {
        # Trigger event
        Example::fireEvent('event1',$listener);
    }
}

```


