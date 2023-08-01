<?php
//$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../..';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/iblock/prolog.php");

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

if (isset($_REQUEST["CUR_LOAD_SESS_ID"]) && strlen($_REQUEST["CUR_LOAD_SESS_ID"]) > 0)
    $CUR_LOAD_SESS_ID = $_REQUEST["CUR_LOAD_SESS_ID"];
else
    $CUR_LOAD_SESS_ID = "CL" . time();

// use Itgrade\Tools\Catalog\Extraym;


use \Itgrade\Yandexmarkup\Mark;
CModule::IncludeModule('itgrade.yandexmarkup');
$markup = new Mark();


// $oExtra = new Extraym();


$APPLICATION->SetTitle('Наценка');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
    <div id="tbl_iblock_extra_result_div">
        <?
        if($_POST['Button_submit']):
            try {
                if ((!empty($sectionId = $_POST['sectionId']) || !empty($brandId = $_POST['brandId'])) || !empty($_POST['MARKUP'])) {
                    try {

                        if (empty($markup_ex = $_POST['MARKUP'])){

                           $markup->setExtraValue($markup)
                               ->setBrandExtra((int)$brandId)
                               ->setSectionExtra((int)$sectionId)
                               ->deleteMarkUp();
                        } else{

                            $markup->setExtraValue($markup_ex)
                                ->setBrandExtra((int)$brandId)
                                ->setSectionExtra((int)$sectionId)
                                ->run();


                        }

               // echo "<pre>"; print_r($_POST); die();

                        ?>

                        <?
                    } catch (Exception $e) {
                        throw new Exception($e);
                    }
                } elseif(empty($del_id = $_POST['DELETE'])) throw new Exception('Не заполнены поля!');
            }catch (Exception $e)
            {
                echo (new CAdminMessage(
                    array(
                        'TYPE' => 'ERROR',
                        'MESSAGE' => 'Ошибка присвоения наценки',
                        'DETAILS' => $e->getMessage()
                    )
                ))->Show();
            }
        endif;
        ?>
    </div>
<?
 

if ($_POST['update_price']){
    $markup->clearTable();
    $markup->updatePropertyMarkUp();
}

//$oExtra->updatePropertyMarkUp();
?>
    <form method="POST" action="<? echo($APPLICATION->GetCurPage()); ?>?lang=ru&mid=itgrade.yandexmarkup&mid_menu=1"
          ENCTYPE="multipart/form-data" name="dataload" id="dataload">
        <?
        $aTabs = [
            [
                "DIV" => "editSections",
                "TAB" => 'Наценка раздела',
                "ICON" => "iblock",
                "TITLE" => 'Добавить наценку раздела и бренда',
            ],
//            [
//                "DIV" => "getSections",
//                "TAB" => 'Наценки товаров',
//                "ICON" => "iblock",
//                "TITLE" => 'Наценки раздела и бренда',
//            ],
            [
                "DIV" => "getSectionsBrands",
                "TAB" => 'Наценки разделы, бренды',
                "ICON" => "iblock",
                "TITLE" => 'Наценки разделы, бренды',
            ]
        ];

        $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
        $tabControl->Begin();
        $tabControl->BeginNextTab();

        ?>
        <table>
            <tr>
                <td class="adm-filter-item">
                    Раздел каталога:
                </td>
                <td class="adm-filter-item">
                    <select name="sectionId">
                        <option value="">- не выбрано -</option>
                        <?
                        $markup->getFormSections();
                        ?>
                    </select>
                </td>
                <td class="adm-filter-item">
                    Бренд:
                </td>
                <td class="adm-filter-item">
                    <select name="brandId">
                        <option value="">Не выбрано</option>
                        <?
                        $markup->getFormBrends();
                        ?>
                    </select>
                </td>
                <td class="adm-filter-item">
                    <label for="Markup">Наценка (%)</label>
                </td>
                <td class="adm-filter-item">
                    <input id="Markup" name="MARKUP" type="number" min="0" step="0.01">
                </td>
            </tr>

            <?$tabControl->EndTab();?>

            <?//$tabControl->BeginNextTab();?>

<!--            <table>-->
                <?
                //          echo "<pre>"; print_r($oExtra->getTableResult());
                ?>
<!--                <tr class="adm-filter-item">-->
<!---->
<!--                    <td class="adm-filter-item">название товара</td>-->
<!--                    <td class="adm-filter-item">  розничная цена </td>-->
<!--                    <td class="adm-filter-item"> розничная цена c наценкой</td>-->
<!--                    <td class="adm-filter-item">   наценка </td>-->
<!--                </tr>-->
                <?//foreach ($markup->getTableResult() as $key=>$item):?>
<!--                    <tr class="adm-filter-item">-->
<!--                        <td class="adm-filter-item">--><?//=$item['NAME']?><!--</td>-->
<!--                        <td class="adm-filter-item">--><?//=$item['PRICE']?><!--</td>-->
<!--                        <td class="adm-filter-item">--><?//=$item['EXTRA']?><!--</td>-->
<!--                        <td class="adm-filter-item">--><?//=$item['MARK']?><!--</td>-->
<!--                    </tr>-->
                <?//endforeach?>
                <?//$tabControl->EndTab();?>

                <?$tabControl->BeginNextTab();?>
                <table style="display: flex" >
                    <?
                    //                          echo "<pre>";
                    //                          print_r($oExtra->getSectionList());
                    //                          print_r($oExtra->getBrandList());
                    ?>
                    <tbody>


                    <tr class="adm-filter-item">

                        <td class="adm-filter-item"> Разделы с наценками </td>
                        <td class="adm-filter-item"> наценка (%) </td>

                    </tr>
                    <?foreach ($markup->getResulSection() as $key=>$item):?>
                        <?if(empty($item['SECTION_NAME'])){
                               unset($item);
                        }?>
                        
                            <td class="adm-filter-item"><?=$item['SECTION_NAME']?></td>
                            <td class="adm-filter-item"><?=$item['MARK']?></td>

                        </tr>
                    <?endforeach?>
                    </tbody>
                    <tbody>
                    <tr class="adm-filter-item">

                        <td class="adm-filter-item"> Бренды с наценками </td>
                        <td class="adm-filter-item"> наценка (%) </td>

                    </tr>
                    <?foreach ($markup->getResultBrand() as $key=>$item):?>
                      
                        <tr class="adm-filter-item">
                            <td class="adm-filter-item"><?=$item['BRAND_NAME']?></td>
                            <td class="adm-filter-item"><?=$item['MARK']?></td>

                        </tr>
                    <?endforeach?>
                    </tbody>
                    <?$tabControl->EndTab();?>

                    <?$tabControl->Buttons();?>

                    <input type="submit" name="Button_submit" value="Сохранить наценки" class="adm-btn-save">

                    <input type="submit" name="update_price" value="Сбросить все наценки" class="adm-btn-save">
                </table>
                <?$tabControl->End();?>
            </table>
    </form>

<?php
//$markup->saveTableResult();
//$ar = $markup->getTableResult();

//$ar = $markup->arProducts;
//echo "<pre>";
//print_r($ar);



//доделать
//присвоение начальной цены в свойство
//

?>
<?require($DOCUMENT_ROOT . "/bitrix/modules/main/include/epilog_admin.php");

