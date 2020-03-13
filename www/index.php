<?php

namespace App;

use Phore\FileSystem\PhoreDirectory;
use Phore\HttpClient\Ex\PhoreHttpRequestException;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;

use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
use Talpa\Utils\Config\Tmac;
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

$app->router->get("/v1/assets/:tmid", function (string $tmid) {
    return phore_file("/opt/test/mock/$tmid.yml")->get_yaml();
});

$app->router->get("/v1/assets/:tmid/:clientId", function (string $tmid, $clientId) {
    $tmac = new Tmac("file:///opt/test/mock?clientId=$clientId");
    return $tmac->getConfig($tmid);
});

$app->router->get("/v1/assets", function (Request $request) {
    parse_str($request->queryString, $queryparams);
    $clientId = phore_pluck('clientId', $queryparams, "");
    if(in_array($clientId, ['Test', ""])) {
        return phore_file("/opt/test/mock/allAssets$clientId.json")->get_json();
    }
    throw new \Exception("Client '$clientId' not defined", 404);
});

/**
 ** Run the application
 **/
$app->serve();
