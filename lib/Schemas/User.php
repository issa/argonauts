<?php

namespace Argonauts\Schemas;

use Neomerx\JsonApi\Document\Link;

class User extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    /**
     * Hier wird der Typ des Schemas festgelegt.
     * {@inheritdoc}
     */
    protected $resourceType = 'user';

    /**
     * Diese Method entscheidet über die JSON-API-spezifische ID von
     * \User-Objekten.
     * {@inheritdoc}
     */
    public function getId($user)
    {
        return studip_utf8encode($user->username);
    }

    /**
     * Hier können (ausgewählte) Instanzvariablen eines \User-Objekts
     * für die Ausgabe vorbereitet werden.
     * {@inheritdoc}
     */
    public function getAttributes($user)
    {
        return [
            'username' => studip_utf8encode($user->username),
            'first_name' => studip_utf8encode($user->Vorname),
            'last_name' => studip_utf8encode($user->Nachname),
//            'avatar' => \Avatar::getAvatar($user->id)->getURL(\Avatar::NORMAL),
        ];
    }

    /**
     * In dieser Methode können Relationships zu anderen Objekten
     * spezifiziert werden. In diesem Beispiel kleben die Kontakte
     * eines Nutzers bei Bedarf am \User.
     * {@inheritdoc}
     */
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
