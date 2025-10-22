<?php

namespace Events\Mail;

use \Bitrix\Main\Loader;
use \CUser;
use \CUserFieldEnum;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!Loader::includeModule("main") || !Loader::includeModule("iblock")) die();

class MailHandler
{
    public static function onBeforeUserInfoSentHandler(&$arFields, &$arTemplate)
    {
        if (strcmp($arTemplate["EVENT_NAME"], "USER_INFO") === 0)
        {
            $userId = $arFields["USER_ID"];
            $user = CUser::GetByID($userId)->GetNext();
            if (!isset($user["UF_USER_CLASS"])) return;
            $userClass = CUserFieldEnum::GetList([], ["ID" => $user["UF_USER_CLASS"]])->GetNext()["VALUE"];
            $arTemplate["MESSAGE"] = str_replace("#CLASS#", $userClass, $arTemplate["MESSAGE"]);
        }
    }
}