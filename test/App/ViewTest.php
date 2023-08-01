<?php

namespace DudeGenuine\PHP\MVC\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testRender()
    {
        View::render('Home/index', ['PHP Login Management']);

        $this->expectOutputRegex('[body]');
        $this->expectOutputRegex('[html]');
        $this->expectOutputRegex('[Register]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Utif Milkedori]');
    }
}
