<?php

namespace DudeGenuine\PHP\MVC\Model;

class UserChangePasswordRequest
{
    public function __construct(
        public string $id, public string $oldPassword, public string $newPassword)
    {
//        $this->newPassword = password_hash($this->newPassword, PASSWORD_BCRYPT);
    }
}