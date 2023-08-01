<?php

namespace DudeGenuine\PHP\MVC\App {
    function header(string $value): void
    {
        echo $value;
    }
}
namespace DudeGenuine\PHP\MVC\Controller {
    use DudeGenuine\PHP\MVC\Config\Database;
    use DudeGenuine\PHP\MVC\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        protected function setUp(): void
        {
            $userRepository = new UserRepository(Database::getConnection());
            $this->userController = new UserController();

            $userRepository->deleteAll();
            putenv("mode=test");
        }
        function testViewRegister()
        {
            $this->userController->viewRegister();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Utif Milkedori]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
        }
        function testSubmitRegister()
        {
            $_POST['id'] = "utifmd";
            $_POST['name'] = "Utif Milkedori";
            $_POST['password'] = "121212";

            $this->userController->submitRegister();
            $this->expectOutputRegex('[Location: /users/login]');
        }
    }
}
