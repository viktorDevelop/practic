<?php
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Config\Option;
use  Itgrade\Yandexmarkup\MarkUpTable;

Loc::loadMessages(__FILE__);

class itgrade_yandexmarkup extends CModule
{
    public $MODULE_ID = 'itgrade.yandexmarkup';

    public function __construct()
    {
        $arModuleVersion = array();

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $MODULE_ID = 'itgrade.yandexmarkup';
        $this->MODULE_ID = 'itgrade.yandexmarkup';
        $this->MODULE_NAME = 'наценки для яндекс маркета';
        $this->MODULE_DESCRIPTION = 'наценки для яндекс маркета';
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = 'itgrade';
        $this->PARTNER_URI = '';
    }

    public function InstallFiles()
    {


        return false;
    }

    // Событие отрисовки
    public function InstallEvents()
    {


        return false;
    }

    public function doInstall()
    {
        if (CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {

            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->installDB();
            $this->InstallEvents();
        } else {

            $APPLICATION->ThrowException(
                'ишибка при удалении'
            );
        }

    }

    public function doUninstall()
    {
        $this->uninstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)){

            if (!MarkUpTable::getEntity()->getConnection()->isTableExists(MarkUpTable::getTableName())) {
                MarkUpTable::getEntity()->createDbTable();
            }

        }

        return false;
    }

    public function uninstallDB()
    {
         if (Loader::includeModule($this->MODULE_ID))
         {
             $connection = Application::getInstance()->getConnection();
             if ($connection->isTableExists(MarkUpTable::getTableName())){

                 $connection->dropTable(MarkUpTable::getTableName());
             }
         }


        return false;
    }

    public function UnInstallFiles()
    {

//        Directory::deleteDirectory(
//            Application::getDocumentRoot() . "/bitrix/js/" . $this->MODULE_ID
//        );
//
//        Directory::deleteDirectory(
//            Application::getDocumentRoot() . "/bitrix/css/" . $this->MODULE_ID
//        );

        return false;
    }

    public function UnInstallEvents()
    {

        // EventManager::getInstance()->unRegisterEventHandler(
        // 	"main",
        // 	"OnBeforeEndBufferContent",
        // 	$this->MODULE_ID,
        // 	"itgrade.yandexmarkup\Main",
        // 	"appendScriptsToPage"
        // );

        return false;
    }
}
