<?php

namespace DudeGenuine\PHP\MVC\App {
    function header(string $value): void
    {
        echo $value;
    }
}
namespace DudeGenuine\PHP\MVC\Service {
    function setcookie(string $name, string $value): void
    {
        echo "$name: $value";
    }
}