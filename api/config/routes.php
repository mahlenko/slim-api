<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * @var App $app
 */
$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write(json_encode(array_merge([
        'version' => doubleval(App::VERSION)
    ], $request->getQueryParams())));

    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/test/mail/layout', function (Request $request, Response $response, array $args) use ($app) {
    /**
     * @var Twig\Environment $twig
     * @psalm-suppress PossiblyNullReference
     */
    $twig = $app->getContainer()->get(Twig\Environment::class);

    $response->getBody()->write($twig->render('mail-test.twig'));
    return $response->withHeader('content-type', 'text/html');
});
