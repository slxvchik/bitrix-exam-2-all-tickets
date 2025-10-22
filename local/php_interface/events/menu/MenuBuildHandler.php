<?php

namespace Events\Menu;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class MenuBuildHandler
{
    public static function onBuildContentManagerGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu)
    {
        global $USER;
        if (in_array(CONTENT_MANAGER_GROUP_ID, $USER->GetUserGroupArray())) {
            $globalMenuContent = $aGlobalMenu["global_menu_content"];
            $aGlobalMenu = [];
            $aGlobalMenu["global_menu_content"] = $globalMenuContent;

            $aModuleMenu = array_filter($aModuleMenu, function($aModuleMenuItem) {
                return $aModuleMenuItem["parent_menu"] === "global_menu_content";
            });

            $aGlobalMenu["global_menu_quick_access"] = [
                "menu_id" => "quick_access",
                "text" => GetMessage("ON_BUILD_MENU_QUICK_ACCESS"),
                "sort" => 1000,
                "items_id" => "global_menu_quick_access"
            ];
            $aModuleMenu[] = [
                "parent_menu" => "global_menu_quick_access",
                "text" => GetMessage("ON_BUILD_MENU_QUICK_ACCESS_FIRST_LINK"),
                "title" => GetMessage("ON_BUILD_MENU_QUICK_ACCESS_FIRST_LINK"),
                "url" => "https://test1"
            ];
            $aModuleMenu[] = [
                "parent_menu" => "global_menu_quick_access",
                "text" => GetMessage("ON_BUILD_MENU_QUICK_ACCESS_SECOND_LINK"),
                "title" => GetMessage("ON_BUILD_MENU_QUICK_ACCESS_SECOND_LINK"),
                "url" => "https://test2"
            ];
        }
    }
}