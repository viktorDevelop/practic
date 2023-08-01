<?php
AddEventHandler("main", "OnBuildGlobalMenu", "yandexMarkUpMenu");

function yandexMarkUpMenu(&$arGlobalMenu, &$arModuleMenu)
{
    IncludeModuleLangFile(__FILE__);
    $moduleName = "itgrade.yandexmarkup";

    global $APPLICATION;


    if($APPLICATION->GetGroupRight($moduleName) > "D")
    {
        $arModuleMenu[] = [
            'parent_menu' => 'global_menu_services',
            'section' => 'SectionMarkup',
            'sort' => 6,
            'text' => 'Наценки',
            'title' => 'Наценки яндекс маркет',
            'items_id' => 'menu_markup',
            'items' => [
                [
                    'text' => 'Наценки   для яндекс маркет',
                    "url" => "/bitrix/admin/settings.php?lang=".LANGUAGE_ID."&mid=".$moduleName."&mid_menu=1",
                    'title' => 'Наценки прайс листа'
                ]
            ]
        ];

        $arModuleMenu[] = $arMenu;
    }
}
