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

## Tutorials

Um eine eigene Kernroute zu schreiben, muss man folgende Schritte
ausführen:

- Routing: In der Slim-Applikation wird für eine URL und eine Request-Methode ein Route-Handler eingetragen.
- Handler: Der Route-Handler muss implementiert werden.
- Schema: Eventuell müssen neue Schemata implementiert und registriert weren.

### Routing

Route-Handler werden in der Datei argonauts/lib/JsonApiRoutemap.php eingetragen. Um zum Beispiel eine `resource object`
abzufragen, soll laut JSON-API-Spezifikation ein GET-Request abgeschickt werden. Wenn man zum Beispiel die Liste aller
Veranstaltungen unter der URL '/courses' abfragen wollte, würde man in der JsonApiRoutemap-Datei folgende Zeile in der
Methode JsonApiRoutemap::authorizedRoutes eintragen:

```php
<?php

namespace Argonauts;

// [...]

class JsonApiRoutemap
{

// [...]

    /**
     * Hier werden autorisierte (Kern-)Routen explizit vermerkt.
     * Außerdem wird über die \PluginEngine allen JsonApiPlugins die
     * Möglichkeit gegeben, sich hier einzutragen.
     */
    public function authorizedRoutes()
    {

        // [...]
        $this->app->get('/courses', CoursesIndex::class);
        // [...]
    }
}
```

Die relevante Zeile

```php
$this->app->get('/courses', CoursesIndex::class);
```

enthält drei Informationen:

**Request-Methode**

Der Aufruf von `->get` registriert den Route-Handler für GET-Requests. Für POST-, DELETE- oder PATCH-Requests wird
stattdessen die \Slim\App::post, \Slim\App::delete und \Slim\App::patch verwendet.

**URL-Pattern**

Der Route-Handler im Beispiel wird für GET-Requests an '/courses' registriert. Es gibt verschiedene Möglichkeiten, das
URL-Pattern festzulegen.

**Route-Handler**

Der Route-Handler kann auf verschiedene Weise angegeben werden. Es kann entweder eine anonyme Funktion, ein anderes
Callable, ein String wie "Klassenname:Methode" oder wie hier im Beispiel und sehr empfohlen einfach nur der Name einer
Klasse, die intern `__invoke` implementiert.

Sehr ausführliche Dokumentation über das Routing findet sich (Slim-Userguide)[http://www.slimframework.com/docs/objects/router.html].

## Tutorials

- https://github.com/neomerx/json-api/tree/v0.8.10



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
