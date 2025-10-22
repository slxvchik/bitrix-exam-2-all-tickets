<?php

namespace Events\Users;

use \Bitrix\Main\Mail\Event;
use \Bitrix\Main\Loader;
use \CUser;
use \CUserFieldEnum;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!Loader::includeModule("main") || !Loader::includeModule("iblock")) {
    die();
}

class UsersHandler
{
    private static $oldUserClass;

    public static function onBeforeUserUpdateHandler(&$arFields)
    {
        $rsUser = CUser::GetByID($arFields["ID"])->GetNext();
        $userClass = CUserFieldEnum::GetList([], [
            "ID" => $rsUser["UF_USER_CLASS"],
            "USER_FIELD_ID" => UF_USER_CLASS
        ])->GetNext();
        self::$oldUserClass = $userClass["VALUE"];
    }

    public static function onAfterUserUpdateHandler(&$arFields)
    {
        if (!isset($arFields["RESULT_MESSAGE"]))
        {
            $userClass = CUserFieldEnum::GetList([], [
                "ID" => $arFields["UF_USER_CLASS"],
                "USER_FIELD_ID" => UF_USER_CLASS
            ])->GetNext();
            if (self::$oldUserClass !== $userClass["VALUE"])
            {
                Event::Send([
                    "EX2_AUTHOR_INFO",
                    SITE_ID,
                    "C_FIELDS" => [
                        "OLD_USER_CLASS" => self::$oldUserClass,
                        "NEW_USER_CLASS" => $userClass["VALUE"]
                    ]
                ]);
            }
        }
    }
}