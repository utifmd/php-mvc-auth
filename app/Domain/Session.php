<?php

namespace DudeGenuine\PHP\MVC\Domain;

class Session
{
    public function __construct(
        public string $id,
        public string $user_id
    ){

    }
}