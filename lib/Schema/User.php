<?php

namespace Argonauts\Schema;

use Neomerx\JsonApi\Document\Link;

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
            'username' => $user->username,
            'first_name' => $user->Vorname,
            'last_name' => $user->Nachname,
//            'avatar' => \Avatar::getAvatar($user->id)->getURL(\Avatar::NORMAL),
        ];
    }

    public function getRelationships($user, $isPrimary, array $includeList)
    {
        $relationships = [
            'contacts' => [
                self::LINKS => ['self' => new Link('/users/'.$user->id.'/contacts')],
            ],
        ];

        if (in_array('contacts', $includeList)) {
            $relationships['contacts'][self::DATA] = $user->contacts->map(function ($i) {
                return $i;
            });
        } else {
            $relationships['contacts'][self::SHOW_DATA] = false;
        }

        /*

        # TODO: Nutzer, die aus dem Chat ausgetreten sind, tauchen hier, aber nicht unter users auf
        if (in_array('authors', $includeList)) {
          $relationships['authors']  = [
              self::DATA => $this->prepareAuthors($chat)
          ];
        }
        */

        return $relationships;
    }

    public function xgetIncludePaths()
    {
        return ['contacts'];
    }
}
