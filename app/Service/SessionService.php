<?php

namespace DudeGenuine\PHP\MVC\Service;

use DudeGenuine\PHP\MVC\Domain\Session;
use DudeGenuine\PHP\MVC\Domain\User;
use DudeGenuine\PHP\MVC\Repository\SessionRepository;
use DudeGenuine\PHP\MVC\Repository\UserRepository;

class SessionService
{
    public const COOKIE_NAME = "X-DUDE-GENUINE-SESSION";
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
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
            time() * (60 * 60 * 24 * 30),
            "/"
        );
        return $session;
    }

    function current(): ?User
    {
        $sessionId = $_SESSION[self::COOKIE_NAME] ?? "";
        $session = $this->sessionRepository->findById($sessionId);
        if ($session == null) return null;

        return $this->userRepository->findById($session->userId);
    }

    function destroy(): void
    {
        $sessionId = $_SESSION[self::COOKIE_NAME] ?? "";
        $this->sessionRepository->deleteById($sessionId);
        setcookie(self::COOKIE_NAME, '', 1, '/');
    }
}