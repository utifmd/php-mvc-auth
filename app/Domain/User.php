<?php

namespace DudeGenuine\PHP\MVC\Domain;

class User
{
    public function __construct(public string $id, public string $name, public string $password)
    {

    }
}