<?php

namespace Agents\Reviews;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Type\DateTime;
use \CEventLog;
use \CIBlockElement;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!Loader::includeModule("main") || !Loader::includeModule("iblock")) die();

class ReviewsAgent
{
    public static function Agent_ex_610($lastTimeExec = "")
    {
        $currentTimeExec = new DateTime();

        if (!empty($lastTimeExec)) {
            $reviewsCount = CIBlockElement::GetList([], ["IBLOCK_ID" => REVIEWS_IBLOCK_ID, ">TIMESTAMP_X" => $lastTimeExec], []);

            CEventLog::Add([
                "SEVERITY" => "INFO",
                "AUDIT_TYPE_ID" => "ex2_610",
                "DESCRIPTION" => GetMessage("REVIEWS_AGENT_EX2_610", [
                    "#LAST_TIME_EXEC#" => $lastTimeExec,
                    "#REVIEWS_CHANGED_COUNT#" => $reviewsCount
                ])
            ]);
        }
        return "\\" . __METHOD__ . "(\"" . $currentTimeExec->toString() . "\")";
    }
}