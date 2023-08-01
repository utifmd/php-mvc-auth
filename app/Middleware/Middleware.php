<?php

namespace DudeGenuine\PHP\MVC\Middleware;

interface Middleware
{

    function before(): void;

}