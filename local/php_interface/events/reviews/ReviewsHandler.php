<?php

namespace Events\Reviews;

use \CIBlockElement;
use \CEventLog;
use \Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!Loader::includeModule("main") || !Loader::includeModule("iblock")) {
    die();
}

class ReviewsHandler
{
    private static $oldAuthorId;

    public static function onBeforeReviewAddHandler(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == REVIEWS_IBLOCK_ID)
        {
            $arFields["PREVIEW_TEXT"] = str_replace("#del#", "", $arFields["PREVIEW_TEXT"]);
            return self::checkLengthPreviewText($arFields["PREVIEW_TEXT"]);
        }
    }
    
    public static function onBeforeReviewUpdateHandler(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == REVIEWS_IBLOCK_ID)
        {
            $rsOldAuthorId = CIBlockElement::GetProperty(REVIEWS_IBLOCK_ID, $arFields["ID"], [], "AUTHOR")->GetNext();
            if (isset($rsOldAuthorId["VALUE"]))
            {
                self::$oldAuthorId = $rsOldAuthorId["VALUE"];
            }
            $arFields["PREVIEW_TEXT"] = str_replace("#del#", "", $arFields["PREVIEW_TEXT"]);
            return self::checkLengthPreviewText($arFields["PREVIEW_TEXT"]);
        }
    }

    public static function onAfterReviewUpdateHandler(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == REVIEWS_IBLOCK_ID && !isset($arFields["RESULT_MESSAGE"]))
        {
            $rsNewAuthorId = CIBlockElement::GetProperty(REVIEWS_IBLOCK_ID, $arFields["ID"], [], "AUTHOR")->GetNext();
            if (isset($rsNewAuthorId["VALUE"]))
            {
                $newAuthorId = $rsNewAuthorId["VALUE"];
                if (self::$oldAuthorId != $newAuthorId) {
                    CEventLog::Add(
                        [
                            "SEVERITY" => "INFO",
                            "AUDIT_TYPE_ID" => "ex2_590",
                            "ITEM_ID" => $arFields["ID"],
                            "DESCRIPTION" => GetMessage("REVIEWS_HANDLER_SHORT_AUTHOR_CHANGED", [
                                "#REVIEW_ID#" => $arFields["ID"],
                                "#OLD_AUTHOR_ID#" => self::$oldAuthorId ?: "NULL",
                                "#NEW_AUTHOR_ID#" => $newAuthorId ?: "NULL"
                            ]),
                        ]
                    );
                }
            }
        }
    }

    private static function checkLengthPreviewText($previewText)
    {
        if (strlen($previewText) < 5) {
            global $APPLICATION;
            $APPLICATION->ThrowException(
                GetMessage(
                    "REVIEWS_HANDLER_SHORT_PREVIEW_TEXT_ERROR",
                    [
                        "#PREVIEW_TEXT_LENGTH#" => strlen($previewText)
                    ]
                )
            );
            return false;
        }
        return true;
    }
}