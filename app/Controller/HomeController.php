<?php

namespace DudeGenuine\PHP\MVC\Controller;

use DudeGenuine\PHP\MVC\App\View;

class HomeController
{
    function view(): void
    {
        $model = [
            "title" => "Home",
            "content" => "Home screen"
        ];
        View::render('Home/index', $model);
    }
}