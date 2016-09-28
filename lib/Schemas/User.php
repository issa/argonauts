<?php

namespace Argonauts\Schemas;

use Neomerx\JsonApi\Document\Link;

class User extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'user';

    public function getId($user)
    {
        return studip_utf8encode($user->username);
    }

    public function getAttributes($user)
    {
        return [
            'username' => studip_utf8encode($user->username),
            'first_name' => studip_utf8encode($user->Vorname),
            'last_name' => studip_utf8encode($user->Nachname),
//            'avatar' => \Avatar::getAvatar($user->id)->getURL(\Avatar::NORMAL),
        ];
    }

    public function getRelationships($user, $isPrimary, array $includeList)
    {
        $relationships = [
            'contacts' => [
                self::LINKS => ['self' => new Link('/user/'.$user->username.'/contacts')],
            ],
        ];

        if ($isPrimary && in_array('contacts', $includeList)) {
            $relationships['contacts'][self::DATA] = $user->contacts;
        } else {
            $relationships['contacts'][self::SHOW_DATA] = false;
        }

        return $relationships;
    }
}
