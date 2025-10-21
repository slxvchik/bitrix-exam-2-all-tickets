<?php

use \CUser;
use \CIBlockElement;
use \Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!Loader::includeModule("main") || !Loader::includeModule("iblock")) {
	die();
}

$catalogElementIds = [];

foreach ($arResult['ITEMS'] as $key => $arItem)
{
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

	$arResult['ITEMS'][$key] = $arItem;

	$catalogElementIds[] = $arItem["ID"];
}

$rsUsers = CUser::GetList(
	"",
	"",
	[
		"ACTIVE" => "Y",
		"UF_AUTHOR_STATUS" => UF_AUTHOR_STATUS
	]
);

$publishedUserIds = [];
while($arUser = $rsUsers->GetNext()) {
	$publishedUserIds[] = $arUser["ID"];
}

$reviewsCount = 0;
if (count($publishedUserIds) > 0) {
	$rsReviews = CIBlockElement::GetList(
		[],
		[
			"PROPERTY_AUTHOR" => $publishedUserIds,
			"PROPERTY_PRODUCT.ID" => $catalogElementIds,
			"IBLOCK_ID" => REVIEWS_IBLOCK_ID,
			"ACTIVE" => "Y",
		],
		false,
		false,
		[
			"ID",
			"NAME",
			"PROPERTY_AUTHOR",
			"PROPERTY_PRODUCT.ID"
		]
	);
	
	while($arReview = $rsReviews->GetNext()) {
		$arResult["EXTRA"]["REVIEWS"][$arReview["PROPERTY_PRODUCT_ID"]][] = $arReview["NAME"];
		$reviewsCount++;
	}
}

$ex2meta = $APPLICATION->GetProperty("ex2_meta");
if (strlen($ex2meta) > 0) {
	$APPLICATION->SetPageProperty("ex2_meta", str_replace("#count#", $reviewsCount, $ex2meta));
}

if ($reviewsCount > 0 && is_array($arResult["EXTRA"]["REVIEWS"])) {
	$firstReviewName = $arResult["EXTRA"]["REVIEWS"][array_key_first($arResult["EXTRA"]["REVIEWS"])][0];
	$reviewTemplate = '<div id="filial-special" class="information-block">
					<div class="top"></div>
					<div class="information-block-inner">
						<h3>Дополнительно</h3>
						<div class="special-product">
							<div class="special-product-title">
								'.$firstReviewName.'
							</div>
						</div>
					</div>
					<div class="bottom"></div>
				</div>';
	$APPLICATION->SetPageProperty("ex2_meta_reviews_name", $reviewTemplate);
}
