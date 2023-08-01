<?php
namespace Itgrade\Yandexmarkup;

use Bitrix\Main;
use Bitrix\Main\Entity;

IncludeModuleLangFile(__FILE__);

class MarkUpTable extends Entity\DataManager {

    static $module_id = "itgrade.yandexmarkup";

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'MarkUpResultTable';
    }



    public static function getMap()
    {
        return array(

            new Entity\IntegerField('BRAND_ID'),
            new Entity\StringField('BRAND_NAME'),
            new Entity\IntegerField('SECTION_ID'),
            new Entity\StringField('SECTION_NAME'),
            new Entity\FloatField('MARK'),

            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
            )),

        );
    }
}