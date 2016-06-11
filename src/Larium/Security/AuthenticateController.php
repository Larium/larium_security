<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security;

trait AuthenticateController
{
    protected $current_user;

    protected $ability;

    /**
     * Returns name of the class that uses AuthenticateResource trait.
     *
     * @return string
     */
    abstract public function getAuthenticateClass();

    /**
     * Returns the login url to redirect.
     *
     * @return string
     */
    abstract public function getLoginUrl();

    public function setCurrentUser($user)
    {
        $this->session->user_id = $user != null ? $user->getId() : null;
        $this->current_user = $user ?: false;
    }

    public function getCurrentUser()
    {
        if (null === $this->current_user
            || false === $this->current_user
        ) {
            $this->current_user = $this->loginFromSession();
        }

        $this->getView()->assign('current_user', $this->current_user);

        return $this->current_user;
    }

    public function getAbility()
    {
        if (null == $this->ability) {
            $this->ability = new \Ability($this->getCurrentUser());
        }

        return $this->ability;
    }

    public function isLoggedIn()
    {
        $user = $this->getCurrentUser();
        return $user !== false && $user !== null;
    }

    public function isLoginRequired()
    {
        if (false === $this->getLoginRequired()) {
            return false;
        }

        if ($this->isLoggedIn()) {
            return false;
        } else {
            $this->accessDenied();

            return true;
        }
    }

    public function getLoginRequired()
    {
        if (isset($this->login_required)) {
            return $this->login_required;
        }

        return false;
    }

    private function loginFromSession()
    {
        if ($this->session->user_id) {
            $class = $this->getAuthenticateClass();

            return $class::findResource($this->session->user_id);
        }

        return false;
    }

    protected function redirectBackOrDefault($default)
    {
        $uri = isset($this->session->back_url)
            ? $this->session->back_url
            : $default;

        return $this->redirect($uri);
    }

    protected function accessDenied()
    {
        $this->redirect($this->getLoginUrl());
    }
}
