<?php

use \Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

include_once __DIR__."/constants.php";

Loader::registerAutoLoadClasses(
    null,
    [
        "Events\\Reviews\\ReviewsHandler" => "/local/php_interface/events/reviews/ReviewsHandler.php",
        "Events\\Users\\UsersHandler" => "/local/php_interface/events/users/UsersHandler.php"
    ]
);

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", ["Events\\Reviews\\ReviewsHandler", "onBeforeReviewAddHandler"]);
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", ["Events\\Reviews\\ReviewsHandler", "onBeforeReviewUpdateHandler"]);
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", ["Events\\Reviews\\ReviewsHandler", "onAfterReviewUpdateHandler"]);

AddEventHandler("main", "OnBeforeUserUpdate", ["Events\\Users\\UsersHandler", "onBeforeUserUpdateHandler"]);
AddEventHandler("main", "OnAfterUserUpdate", ["Events\\Users\\UsersHandler", "onAfterUserUpdateHandler"]);