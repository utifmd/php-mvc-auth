<?php

namespace DudeGenuine\PHP\MVC\Model;

class UserRegisterResponse
{
    public function __construct(public string $id, public string $name, public string $password)
    {
    }
}