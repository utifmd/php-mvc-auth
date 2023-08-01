<?php

namespace DudeGenuine\PHP\MVC\Controller;

use DudeGenuine\PHP\MVC\App\View;

class HomeController
{
    function index(): void
    {
        $model = [
            "title" => "Belajar PHP MVC",
            "content" => "Selamat Belajar PHP MVC dari Programmer Zaman Now"
        ];

        View::render('Home/index', $model);
    }
}