<?php

namespace DudeGenuine\PHP\MVC\App {
    function header(string $path)
    {
        echo $path;
    }
}
namespace DudeGenuine\PHP\MVC\Middleware {

    use DudeGenuine\PHP\MVC\Config\Database;
    use DudeGenuine\PHP\MVC\Domain\Session;
    use DudeGenuine\PHP\MVC\Domain\User;
    use DudeGenuine\PHP\MVC\Repository\SessionRepository;
    use DudeGenuine\PHP\MVC\Repository\UserRepository;
    use DudeGenuine\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class UnAuthorizedMiddlewareTest extends TestCase
    {
        private UnAuthorizedMiddleware $unAuthorizedMiddleware;
        private SessionRepository $sessionRepository;
        private UserRepository $userRepository;

        protected function setUp(): void
        {
            $this->unAuthorizedMiddleware = new UnAuthorizedMiddleware();
            putenv("mode=test");
            $connection = Database::getConnection();

            $this->sessionRepository = new SessionRepository($connection);
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository($connection);
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->unAuthorizedMiddleware->before();

            $this->expectOutputRegex("[Location: /users/login]");
        }
        public function testBeforeDashboard()
        {
            $user = new User(
                id: "utifmd", name: "Utif Milkedori", password: "121212"
            );
            $this->userRepository->save($user);

            $session = new Session(id: uniqid(), userId: $user->id);

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::COOKIE_NAME] = $session->id;

            $this->unAuthorizedMiddleware->before();

            $this->expectOutputRegex("[]");
        }
    }
}