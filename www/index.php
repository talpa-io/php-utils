<?php

namespace App;

use Phore\FileSystem\PhoreDirectory;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;

use function aclRule;

require __DIR__ . "/../vendor/autoload.php";

$app = new App();
$app->activateExceptionErrorHandlers();
$app->setOnExceptionHandler(new JsonExceptionHandler());
$app->setResponseHandler(new JsonResponseHandler());

$app->assets()->addAssetSearchPath(__DIR__ . "/assets/");


/**
 ** Configure Access Control Lists
 **/
$app->acl->addRule(aclRule()->route("/*")->ALLOW());


/**
 ** Define Routes
 **/
$app->router->get("/", function () {
    return ["fail" => "fail"];
});

$app->router->get("/test/:tmid", function (string $tmid) {
    $config = phore_file(__DIR__ . "/../test/mock/configUrl.yml")->get_yaml();

    return phore_pluck(
        ["ulan", "machines", $tmid],
        $config,
        new \InvalidArgumentException("Machine " . $tmid . " is not defined in config"));
});

/**
 ** Run the application
 **/
$app->serve();
