<?php

namespace App\Common\Exception;


class EntityNotFoundException extends DomainException
{
    const STATUS_CODE = 404;

    const MESSAGE = "Entity not found";
}