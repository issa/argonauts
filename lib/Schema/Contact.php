<?php

namespace Argonauts\Schema;

class Contact extends \Neomerx\JsonApi\Schema\SchemaProvider
{
    protected $resourceType = 'contact';

    public function getId($contact)
    {
        return $contact->id;
    }

    public function getAttributes($contact)
    {
        return [
            'owner_id' => $contact->owner_id,
            'user_id' => $contact->user_id,
        ];
    }
}
