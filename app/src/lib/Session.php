<?php

/**  */
namespace Test\lib;

/**
 * Class Session
 */
class Session extends ContainerAware
{
    /** @var bool  */
    private $started;

    /**
     * Session constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if ($this->started) {
            return;
        }

        if (!\session_start()) {
            throw new \Exception('Failed to start the session');
        }
        $this->started = true;
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * void
     */
    public function refreshCsrfToken()
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * void
     */
    public function close()
    {
        session_write_close();
        $this->started = false;
    }

    /**
     * @return bool
     */
    public function isUserLogged()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * @return null|integer
     */
    public function getUserId()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    /**
     * @return string
     */
    public function getUserLang()
    {
        return isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
    }

    /**
     * @param object $user
     */
    public function setUser($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['lang'] = $user->lang;
        $this->get('translator')->setLang($user->lang);
    }

    /**
     * void
     */
    public function unSetUser()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['lang']);
    }

    /**
     * @return string
     */
    public function getcsrfToken()
    {
        return $_SESSION['csrf_token'];
    }

    /**
     * @param string $enteredPassword
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($enteredPassword, $password)
    {
        return \password_verify($enteredPassword, $password);
    }
}
