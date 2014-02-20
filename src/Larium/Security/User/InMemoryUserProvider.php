<?php

namespace Larium\Security\User;

class InMemoryUserProvider implements UserProviderInterface
{
    protected $users = array();

    public function __construct(array $users)
    {
        foreach ($users as $user) {
            $this->users[strtolower($user['username'])] = $this->create_user($user);
        }
    }

    public function getByUsername($username)
    {
        $username = strtolower($username);
        if (array_key_exists($username, $this->users)) {
            return $this->users[$username];
        }

        throw new UserNotFoundException();
    }

    protected function create_user(array $params)
    {
        return new User($params['username'], $params['password']);
    }
}
