<?php

namespace DudeGenuine\PHP\MVC\App;

class View
{

    public static function render(string $view, $model): void
    {
        require __DIR__ . '/../View/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/footer.php';
    }
}