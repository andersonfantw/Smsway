<?php

namespace Cuby\Meteorsis\Exceptions;

class MeteorsisCouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($message): self
    {
        return new static('Meteorsis Response: '.$message);
    }

    public static function apiKeyNotProvided(): self
    {
        return new static('Meteorsis API key is missing.');
    }

    public static function serviceNotAvailable($message): self
    {
        return new static($message);
    }

    public static function phoneNumberNotProvided(): self
    {
        return new static('Meteorsis No phone number was provided.');
    }
}