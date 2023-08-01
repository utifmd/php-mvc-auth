<?php

namespace DudeGenuine\PHP\MVC\Model;

class UserRegisterRequest
{
    public function __construct(
        public string $id,
        public string $name,
        public string $password
    ){

    }
}