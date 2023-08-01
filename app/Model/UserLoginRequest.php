<?php

namespace DudeGenuine\PHP\MVC\Model;

class UserLoginRequest
{
    public function __construct(
        public string $id,
        public string $password
    ){

    }
}