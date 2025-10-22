<?php

namespace Events\Index;

use \Bitrix\Main\Loader;
use \CIBlockElement;
use \CUser;
use \CUserFieldEnum;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!Loader::includeModule("main") || !Loader::includeModule("iblock")) die();
class IndexHandler
{
    public static function onBeforeReviewsIndexHandler($arFields)
    {
        if ($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == REVIEWS_IBLOCK_ID)
        {
            $review = CIBlockElement::GetProperty(
                $arFields["PARAM2"],
                $arFields["ITEM_ID"],
                [],
                ["CODE" => "AUTHOR"]
            )->GetNext();

            if (isset($review["VALUE"])) {
                $user = CUser::GetByID($review["VALUE"])->GetNext();

                if (isset($user["UF_USER_CLASS"])) {
                    $userClass = CUserFieldEnum::GetList([], ["ID" => $user["UF_USER_CLASS"]])->GetNext();

                    if (isset($userClass["VALUE"])) {
                        $arFields["TITLE"] .= ". " . GetMessage("REVIEWS_INDEX_HANDLER_TITLE", ["#REVIEW_NAME#" => $userClass["VALUE"]]);
                    }
                }
            }
        }
        return $arFields;
    }
}