<?php

namespace Argonauts\Schema;

class User extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'user';

    public function getId($user)
    {
        return $user->username;
    }

    public function getAttributes($user)
    {
        return [
            'username'   => $user->username,
            'first_name' => $user->Vorname,
            'last_name'  => $user->Nachname,
            'avatar'     => \Avatar::getAvatar($user->id)->getURL(\Avatar::NORMAL)
        ];
    }
}
