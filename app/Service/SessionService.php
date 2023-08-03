<?php

namespace DudeGenuine\PHP\MVC\Service;

use DudeGenuine\PHP\MVC\Domain\Session;
use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Repository\SessionRepository;
use DudeGenuine\PHP\MVC\Repository\UserRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class SessionService
{
    public const COOKIE_NAME = "X-DUDE-GENUINE-SESSION";
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    private Logger $logger;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
        $this->logger = new Logger(SessionService::class);
        $this->logger->pushHandler(new StreamHandler('info.log'));
    }

    function create(string $userId): Session
    {
        $session = new Session(
            id: uniqid(), userId: $userId
        );
        $this->sessionRepository->save($session);
        setcookie(
            self::COOKIE_NAME,
            $session->id,
            time() + 3600,
            "/"
        );
        return $session;
    }

    function current(): ?User
    {
        $sessionId = $_COOKIE[self::COOKIE_NAME] ?? "";
        $this->logger->info("Session ID: ". $sessionId);

        $session = $this->sessionRepository->findById($sessionId);
        if ($session == null) return null;

        $user = $this->userRepository->findById($session->userId);
        $this->logger->info("User ID: ". $user->id);
        return $user;
    }

    function destroy(): void
    {
        $sessionId = $_COOKIE[self::COOKIE_NAME] ?? "";
        $this->sessionRepository->deleteById($sessionId);
        setcookie(self::COOKIE_NAME, '', 1, '/');
    }
}