<?php

namespace CrCms\Event\Contracts;

/**
 * Interface EventSource
 * @package CrCms\Event\Contracts
 */
interface EventSource
{
    /**
     * @return mixed
     */
    public function source();
}