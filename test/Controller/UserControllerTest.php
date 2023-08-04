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
namespace DudeGenuine\PHP\MVC\Controller {
    use DudeGenuine\PHP\MVC\Config\Database;
    use DudeGenuine\PHP\MVC\Model\UserRegisterRequest;
    use DudeGenuine\PHP\MVC\Repository\SessionRepository;
    use DudeGenuine\PHP\MVC\Repository\UserRepository;
    use DudeGenuine\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        protected function setUp(): void
        {
            $connection = Database::getConnection();
            $this->userController = new UserController();

            $sessionRepository = new SessionRepository($connection);
            $sessionRepository->deleteAll();

            $userRepository = new UserRepository($connection);
            $userRepository->deleteAll();

            putenv("mode=test");
        }

        function setUpRegister()
        {
            $_POST['id'] = "utifmd";
            $_POST['name'] = "Utif Milkedori";
            $_POST['password'] = "121212";
            $this->userController->submitRegister();
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
            $this->setUpRegister();
            $this->expectOutputRegex('[Location: /users/login]');
        }
        function testViewLogin()
        {
            $this->setUpRegister();
            $this->userController->viewLogin();

            $this->expectOutputRegex('[Login]');
            $this->expectOutputRegex('[Utif Milkedori]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
        }
        function testSubmitLogin()
        {
            $this->setUpRegister();

            $_POST['id'] = "utifmd";
            $_POST['password'] = "121212";

            $this->userController->submitLogin();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[".SessionService::COOKIE_NAME.":]");
        }
        function testSubmitWrongPasswordLogin()
        {
            $this->setUpRegister();

            $_POST['id'] = "utifmd";
            $_POST['password'] = "121213";

            $this->userController->submitLogin();
            $this->expectOutputRegex("[Login failed]");
            $this->expectOutputRegex("[id or username does not match]");
        }
        function testSubmitInvalidLoginForm()
        {
            $_POST['id'] = "";
            $_POST['password'] = "";

            $this->userController->submitLogin();
            $this->expectOutputRegex("[Login failed]");
            $this->expectOutputRegex("[Invalid input format]");
        }
        function testSubmitLoginNotFound()
        {
            $_POST['id'] = "notFound";
            $_POST['password'] = "121212";

            $this->userController->submitLogin();
            $this->expectOutputRegex("[Login failed]");
            $this->expectOutputRegex("[user not found]");
        }

        public function testViewProfile()
        {
            $this->setUpRegister();

            $this->userController->submitLogin();

            $this->userController->viewProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[By Utif Milkedori]");
            $this->expectOutputRegex("[id ". $_POST['id']. "]");
            $this->expectOutputRegex("[name ". $_POST['name']. "]");
            $this->expectOutputRegex("[Update Profile]");
        }

        public function testUpdateProfileSuccess()
        {
            $this->setUpRegister();

            $this->userController->submitLogin();

            $_POST['name'] = "Charlie Chaplain";
            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[By Utif Milkedori]");
            $this->expectOutputRegex("[id ". $_POST['id']. "]");
            $this->expectOutputRegex("[name ". $_POST['name']. "]");
            $this->expectOutputRegex("[Update Profile]");
        }
        public function testUpdateProfileFailedInvalidInput()
        {
            $this->setUpRegister();

            $this->userController->submitLogin();

            $_POST['name'] = "";
            $this->userController->updateProfile();

            $this->expectOutputRegex("[Location: /users/profile]");
            $this->expectOutputRegex("[Invalid input format");
            $this->expectOutputRegex("[Update Profile]");
        }

        public function testLogout()
        {
            $_POST['id'] = "utifmd";
            $_POST['name'] = "Utif Milkedori";
            $_POST['password'] = "121212";

            $this->userController->submitRegister();

            $this->userController->submitLogin();

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[".SessionService::COOKIE_NAME.": ]");
        }
    }
}
