<?php

namespace Events\Users;

use \Bitrix\Main\Loader;
use \CUser;
use \CUserFieldEnum;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!Loader::includeModule("main") || !Loader::includeModule("iblock")) {
    die("Main or iblock module are not included.");
}

class UsersHandler
{
    private static $oldUserClass;

    public static function onBeforeUserUpdateHandler(&$arFields)
    {
        $rsUser = CUser::GetByID($arFields["ID"])->GetNext();
        $userClass = CUserFieldEnum::GetList([], [
            "ID" => $rsUser["UF_USER_CLASS"],
            "USER_FIELD_ID" => "UF_USER_CLASS"
        ])->GetNext();
        echo "<pre>";print_r($userClass);echo "</pre>";die();
    }

    public static function onAfterUserUpdateHandler(&$arFields)
    {

    }
}