<?php

require_once __DIR__.'/../../ArgonautsPlugin.class.php';
require_once __DIR__.'/routes/SimpleRoute.php';
require_once __DIR__.'/routes/AllowUnrecognizedRoute.php';
require_once __DIR__.'/routes/PostWithBodyRoute.php';

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;
use Argonauts\Middlewares\Authentication;
use Argonauts\Middlewares\JsonApi as JsonApiMiddleware;

class ExampleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        DBManager::getInstance()
            ->setConnection('studip', $this->getModule('Db')->dbh);
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        $this->assertFalse(false);
        $db = \DBManager::get();
        $stmt = $db->prepare('SELECT 17');

        $stmt->execute([]);

        $this->assertEquals(17, current($stmt->fetch()));
    }

    public function testAppFactory()
    {
        $plugin = new ArgonautsPlugin();
        $this->assertEquals('ArgonautsPlugin', get_class($plugin));

        $factory = new \Argonauts\AppFactory();
        $app = $factory->makeApp($plugin);
        $this->assertEquals('Slim\App', get_class($app));
    }

    public function testExampleRoute()
    {
        $app = $this->appFactory();

        $app->get(
            '/testRoute',
            function (RequestInterface $request, ResponseInterface $response, $args) {
                $response->getBody()->write('Hello, dummy');

                return $response;
            }
        );

        // Prepare request and response objects
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/testRoute',
            'REQUEST_METHOD' => 'GET',
        ]);
        list($req, $res) = $this->prepareReqAndRes($env);

        // Invoke app
        $app($req, $res);
        $this->assertEquals('Hello, dummy', (string) $res->getBody());
    }

    public function testExampleJsonApiRoute()
    {
        $app = $this->appFactory();

        $app->get('/users', 'Argonauts\Test\SimpleRoute:index')->add(new JsonApiMiddleware($app));

        $env = Environment::mock([
            'SCRIPT_NAME' => '/plugins.php',
            'REQUEST_URI' => '/users',
            'REQUEST_METHOD' => 'GET',
        ]);
        list($req, $res) = $this->prepareReqAndRes($env);

        // Invoke app
        $response = $app($req, $res);

        $this->assertEquals(
            '{"data":{"type":"user","id":"root@studip","attributes":{"username":"root@studip","first_name":"Root","last_name":"Studip"},"relationships":{"contacts":{"links":{"self":"plugins.php\/argonautsplugin\/user\/root@studip\/contacts"}}},"links":{"self":"plugins.php\/argonautsplugin\/user\/root@studip"}}}',
            (string) $response->getBody()
        );
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreatedResponse()
    {
        $app = $this->appFactory();

        $app->post('/users', 'Argonauts\Test\SimpleRoute:create')->add(new JsonApiMiddleware($app));

        $env = Environment::mock([
            'SCRIPT_NAME' => '/plugins.php',
            'REQUEST_URI' => '/users',
            'REQUEST_METHOD' => 'POST',
        ]);
        list($req, $res) = $this->prepareReqAndRes($env);

        // Invoke app
        $response = $app($req, $res);

        $body = (string) $response->getBody();
        $jsonBody = json_decode($body, true);
        $this->assertTrue(array_key_exists('data', $jsonBody));

        $data = $jsonBody['data'];
        $this->assertEquals('user', $data['type']);
        $this->assertEquals('new@user', $data['id']);
        $this->assertEquals(
            ['username' => 'new@user', 'first_name' => '', 'last_name' => ''],
            $data['attributes']
        );
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testCodeResponse()
    {
        $app = $this->appFactory();

        $app->delete('/users', 'Argonauts\Test\SimpleRoute:destroy')->add(new JsonApiMiddleware($app));

        $env = Environment::mock([
            'SCRIPT_NAME' => '/plugins.php',
            'REQUEST_URI' => '/users',
            'REQUEST_METHOD' => 'DELETE',
        ]);
        list($req, $res) = $this->prepareReqAndRes($env);

        // Invoke app
        $response = $app($req, $res);

        $this->assertEquals('', (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @expectedException Neomerx\JsonApi\Exceptions\JsonApiException
     * @expectedExceptionMessage JSON API error
     */
    public function testUnrecognizedParams()
    {
        $app = $this->appFactory();

        $app->get('/users', 'Argonauts\Test\SimpleRoute:index')->add(new JsonApiMiddleware($app));

        $env = Environment::mock([
            'SCRIPT_NAME' => '/plugins.php',
            'REQUEST_URI' => '/users?foo=bar',
            'QUERY_STRING' => 'foo=bar',
            'REQUEST_METHOD' => 'GET',
        ]);

        // Invoke app
        $this->sendMockRequest($app, $env);
    }

    public function testAllowUnrecognizedParams()
    {
        $app = $this->appFactory();

        $app->get('/allow', 'Argonauts\Test\AllowUnrecognizedRoute:index')
            ->add(new JsonApiMiddleware($app));

        $env = Environment::mock([
            'SCRIPT_NAME' => '/plugins.php',
            'REQUEST_URI' => '/allow?foo=bar',
            'QUERY_STRING' => 'foo=bar',
            'REQUEST_METHOD' => 'GET',
        ]);

        // Invoke app
        $response = $this->sendMockRequest($app, $env);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPostWithBody()
    {
        $app = $this->appFactory();

        $app->post('/resource', 'Argonauts\Test\PostWithBodyRoute')
            ->add(new JsonApiMiddleware($app));

        $env = Environment::mock(
            [
                'SCRIPT_NAME' => '/plugins.php',
                'REQUEST_URI' => '/resource',
                'QUERY_STRING' => '',
                'REQUEST_METHOD' => 'POST',
                'HTTP_CONTENT_TYPE' => 'application/vnd.api+json',
            ]
        );

        // Invoke app
        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $body->write(json_encode(['data' => ['type' => 'articles', 'id' => '1']]));
        $response = $this->sendMockRequest($app, $env, $body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            '{"meta":{"data":{"type":"articles","id":"1"}}}',
            (string) $response->getBody()
        );
    }

    public function testUnauthenticatedRoute()
    {
        $app = $this->appFactory();

        $authenticator = function () {
            return null;
        };

        $app->get('/unauth', 'Argonauts\Test\SimpleRoute:index')
            ->add(new Authentication($authenticator))
            ->add(new JsonApiMiddleware($app));

        $env = Environment::mock([
            'SCRIPT_NAME' => '/plugins.php',
            'REQUEST_URI' => '/unauth',
            'REQUEST_METHOD' => 'GET',
        ]);

        // Invoke app
        $response = $this->sendMockRequest($app, $env);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testAuthenticatedRoute()
    {
        $app = $this->appFactory();

        $authenticator = function ($username, $password) {
            if ($username === 'test_user' && $password === 'testing') {
                return new User();
            }

            return null;
        };

        $app->get('/auth', 'Argonauts\Test\SimpleRoute:index')
            ->add(new Authentication($authenticator))
            ->add(new JsonApiMiddleware($app));

        $env = Environment::mock(
            [
                'SCRIPT_NAME' => '/plugins.php',
                'REQUEST_URI' => '/auth',
                'REQUEST_METHOD' => 'GET',
                'PHP_AUTH_USER' => 'test_user',
                'PHP_AUTH_PW' => 'testing',
            ]
        );

        // Invoke app
        $response = $this->sendMockRequest($app, $env);
        $this->assertNotEquals(401, $response->getStatusCode());
    }

    // ***** PRIVATE *****

    private function appFactory()
    {
        $plugin = new ArgonautsPlugin();
        $factory = new \Argonauts\AppFactory();

        return $factory->makeApp($plugin);
    }

    private function prepareReqAndRes($env, $body = null)
    {
        // Prepare request and response objects
        $uri = Uri::createFromEnvironment($env);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = $body ?: new RequestBody();
        $req = new Request($env->get('REQUEST_METHOD'), $uri, $headers, $cookies, $serverParams, $body);
        $res = new Response();

        return [$req, $res];
    }

    private function sendMockRequest($app, $env, $body = null)
    {
        $container = $app->getContainer();
        $container['environment'] = $env;
        list($request, $response) = $this->prepareReqAndRes($env, $body);
        $container['request'] = $request;

        return $app($request, $response);
    }
}
