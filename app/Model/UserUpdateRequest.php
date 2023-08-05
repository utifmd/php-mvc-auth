<?php

namespace DudeGenuine\PHP\MVC\Model;

class UserUpdateRequest
{
    public function __construct(public string $id, public string $name, public string $password)
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    public function __toString(): string
    {
        return "password: " . $this->password . ", name: " . $this->name . "id: " . $this->id;
    }
}