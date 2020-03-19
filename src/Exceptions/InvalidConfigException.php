<?php

namespace KuznetsovZfort\Fixture\Exceptions;

use Exception;

/**
 * Class InvalidConfigException represents an exception caused by incorrect object configuration.
 *
 * @package KuznetsovZfort\Fixture\Exceptions
 */
class InvalidConfigException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid Configuration';
    }
}
