<?php

namespace Cuby\Notifications\Channels\Meteorsis\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    /**
     * @return static
     */
    public static function configurationNotSet(): self
    {
        return new static('In order to send notification via Meteorsis you need to add credentials in the `meteorsis` key of `config.services`.');
    }
}
