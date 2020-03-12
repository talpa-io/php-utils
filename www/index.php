<?php

namespace App;

use Phore\FileSystem\PhoreDirectory;
use Phore\HttpClient\Ex\PhoreHttpRequestException;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;

use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
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

$app->router->get("/v1/assets/:tmid/:serviceId", function (string $tmid) {
    $config = phore_file(__DIR__ . "/../test/mock/configUrl.yml")->get_yaml();

    return phore_pluck(
        ["ulan", "machines", $tmid],
        $config,
        new \InvalidArgumentException("Machine " . $tmid . " is not defined in config"));
});

$app->router->get("/v1/assets", function (Request $request) {
    parse_str($request->queryString, $queryparams);
    $service = phore_pluck('service', $queryparams, "");
    if(in_array($service, ['Test', ""])) {
        return phore_file("/opt/test/mock/allAssets$service.json")->get_json();
    }
    throw new \Exception("Service '$service' not defined", 404);
});

/**
 ** Run the application
 **/
$app->serve();
