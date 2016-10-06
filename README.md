# Stud.IP Argonauts
## das Stud.IP-Plugin, um eine JSON-API-kompatible REST-Schnittstelle anzubieten

Dieses Plugin gibt Stud.IP (>=v3.1) die Möglichkeit,
REST-Schnittstellen anzubieten, die
dem [JSON-API-Standard](http://jsonapi.org/) entsprechen. Sowohl im
Stud.IP-Kern als auch in Stud.IP-Plugins können Routen definiert
werden, die zusammen eine neue REST-API bilden.

## Verwendung

Sobald dieses Plugin installiert und aktiviert ist, können die Routen
über die URL
http://mein.eigenes.studip/plugins.php/argonautsplugin/<meine route>
aufgerufen werden.

Die Beispielroute zur Auflistung aller Nutzer fände man also unter

http://mein.eigenes.studip/plugins.php/argonautsplugin/users

und würde eine Ausgabe ähnlich wie diese produzieren:

```js
{
  "meta": {
    "page": {
      "offset": 0,
      "limit": 30,
      "total": 5
    }
  },
  "links": {
    "first": "http://mein.eigenes.studip/plugins.php/argonautsplugin/users?page%5Boffset%5D=0&page%5Blimit%5D=30",
    "last": "http://mein.eigenes.studip/plugins.php/argonautsplugin/users?page%5Boffset%5D=0&page%5Blimit%5D=30"
  },
  "data": [
    {
      "type": "user",
      "id": "root@studip",
      "attributes": {
        "username": "root@studip",
        "first_name": "Root",
        "last_name": "Studip"
      },
      "relationships": {
        "contacts": {
          "links": {
            "self": "/plugins.php/argonautsplugin/user/root@studip/contacts"
          }
        }
      },
      "links": {
        "self": "/plugins.php/argonautsplugin/user/root@studip"
      }
    },
    // [... 4 weitere Nutzer ...]
  ]
}
```

- http://www.slimframework.com/docs/
- https://github.com/neomerx/json-api/tree/v0.8.10


## Tutorials

- [Wie schreibt man eine Route?](doc/howto-routes.md)
- Routen:
  - Methoden ($app->get($muster, $handler); $app->post(...); ->put(...); ->delete(...);
  - URL, (http://www.slimframework.com/docs/objects/router.html#route-placeholders)
  - Handler
    - function ($request, $response, $args)
    - an invokable class (#__invoke) (http://php.net/manual/en/language.oop5.magic.php#object.invoke)
    - "Class:method"
  - siehe http://www.slimframework.com/docs/objects/router.html
  - PSR-7  http://www.php-fig.org/psr/psr-7/

- Wie schreibt man ein Schema? https://github.com/neomerx/json-api/wiki/Schemas
- Zuordnung Stud.IP-Klasse -> Schema
