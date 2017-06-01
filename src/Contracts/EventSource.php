<?php

namespace CrCms\Event\Contracts;

/**
 * Interface EventSource
 * @package Simon\Event\Contracts
 */
interface EventSource
{
    /**
     * @return object
     */
    public function source();
}