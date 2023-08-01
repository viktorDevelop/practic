<?php
namespace Itgrade\Yandexmarkup;
use Bitrix\Main\Application;
use Bitrix\Sale;
use Bitrix\Catalog;
use  Itgrade\Yandexmarkup\MarkUpTable;


\CModule::IncludeModule("catalog");
\CModule::IncludeModule("iblock");

class Mark {

    const IBLOCK_CODE = 'aspro_next_catalog-aspro_dfd';
    // const IBLOCK_ID = 'aspro_next_catalog-aspro_dfd';
    const PROPERTY_MARK_CODE = 'PRICE_MURCUP_YM';
    const TYPE_PRICE_ID = 3;
    const IBLOCK_ID = 17;

    private $mark_up_val;
    private $brand_id;
    private $section_id;

    public $arProducts = [];

    public function __construct()
    {
        $this->db = Application::getConnection();
    }

    public function clearTable()
    {
        $this->db->query('TRUNCATE TABLE MarkUpResultTable');
    }

    public function run()
    {
        // echo $this->brand_id.'<br>';
        // echo $this->section_id;
        // echo "<pre>";

       $this->getProduct($this->section_id,$this->brand_id);
       $this->setPropertyMark();
        $this->saveResult($this->section_id,$this->brand_id,$this->mark_up_val);
     }


      public function deleteMarkUp()
        {
            $this->getProduct($this->section_id,$this->brand_id);
            $this->deleteProductsEmptyMark();
        }

      public  function updatePropertyMarkUp()
      {

        $this->clearTable();
        $rs = \CIBlockElement::GetList(
            [],
            $arFilter,
            false,
            false,
            [
                'ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'PROPERTY_BREND.ID','PROPERTY_CML2_ARTICLE','PROPERTY_PRICE_MURCUP_YM'
            ]
        );

        while ($item = $rs->GetNext()) {

            $price_rps = $this->getPrice($item['ID'])['PRICE'];
            \CIBlockElement::SetPropertyValues( $item['ID'], 17, $price_rps, 'PRICE_MURCUP_YM');


        }

    }

    // заполняет свойство розничной ценой, если наценка 0, удаляет  запись из промежуточной таблицы
    private function deleteProductsEmptyMark(){

        if ($this->brand_id){
            foreach ($this->arProducts as $items){

                $this->db->query('DELETE FROM MarkUpResultTable WHERE BRAND_ID = '.$items['PROPERTY_BREND_ID']);

                $price_rps = $this->getPrice($items['ID'])['PRICE'];
                \CIBlockElement::SetPropertyValues( $items['ID'], 17, $price_rps, 'PRICE_MURCUP_YM');

            }
        }

        if ($this->section_id){
            foreach ($this->arProducts as $items){

                $this->db->query('DELETE FROM MarkUpResultTable WHERE SECTION_ID = '.$items['IBLOCK_SECTION_ID']);

                $price_rps = $this->getPrice($items['ID'])['PRICE'];
                \CIBlockElement::SetPropertyValues( $items['ID'], 17, $price_rps, 'PRICE_MURCUP_YM');

            }
        }

        if ($this->section_id && $this->brand_id) {
             
                foreach ($this->arProducts as $items){

                $this->db->query('DELETE FROM MarkUpResultTable WHERE SECTION_ID = '.$items['IBLOCK_SECTION_ID'].' AND BRAND_ID = '.$items['IBLOCK_SECTION_ID']);

                $price_rps = $this->getPrice($items['ID'])['PRICE'];
                \CIBlockElement::SetPropertyValues( $items['ID'], 17, $price_rps, 'PRICE_MURCUP_YM');

            }

        }


    }



    public function setPropertyMark()
    {

        if ($this->arProducts){

            $mark =  round($this->mark_up_val,2) / 100;
            foreach ($this->arProducts as $items){

               $price =  $this->getPrice($items['ID'])['PRICE'];
                $new_price = ($mark == 0) ? $price : ($price + $price * $mark);
                $new_price = round($new_price,0);
                \CIBlockElement::SetPropertyValues( $items['ID'], self::IBLOCK_ID, $new_price, self::PROPERTY_MARK_CODE);

            }
        }


     }
    public function saveResult($section_id ='',$brand_id = '',$mark)
    {   
         

      
        if (!empty($brand_id) && !empty($section_id) ) {
            $res =  MarkUpTable::getList(
                ['filter'=>  array("BRAND_ID"=>$brand_id,"SECTION_ID"=>$section_id )]

                
            );
        }elseif(!empty($brand_id)){
              $res =  MarkUpTable::getList(
                ['filter'=>  array("BRAND_ID"=>$brand_id )]

                
            );
        }elseif(!empty($section_id)){

             $res =  MarkUpTable::getList(
                ['filter'=>array("SECTION_ID"=>$section_id)
                    
                   
                ]
            );

        }
       $ar = $res->fetch();
         $brand_name = ($a = \CIBlockElement::GetByID($brand_id)->Fetch()['NAME']) ? $a : '';
         $section_name = ($b = \CIBlockSection::GetByID($section_id)->Fetch()['NAME']) ? $b :'';

         // echo "<pre>";
         // print_r($ar);
       if (!empty($ar)){
           MarkUpTable::update($ar['ID'],
           [
               "BRAND_ID"=>$brand_id,
               "SECTION_ID"=>$section_id,
               "SECTION_NAME"=>$section_name,
               "BRAND_NAME"=>$brand_name,
               "MARK"=>$mark
           ]);

       }else{

           MarkUpTable::add(
               [
                    "BRAND_ID"=>$brand_id,
                    "SECTION_ID"=>$section_id,
                    "SECTION_NAME"=>$section_name,
                    "BRAND_NAME"=>$brand_name,
                    "MARK"=>$mark

                ]
           );
       }

    }

    public function getProduct($section_id = 0,$brand_id = 0)
    {

        $arFilter = ['IBLOCK_CODE' => self::IBLOCK_CODE, 'INCLUDE_SUBSECTIONS' => 'Y'];
        if ($section_id)
            $arFilter['SECTION_ID'] = $section_id;

        if ($brand_id)
            $arFilter['PROPERTY_BREND'] = $brand_id;

        if ($section_id && $brand_id){

        $arFilter = [
                    'IBLOCK_CODE' => 'aspro_next_catalog-aspro_dfd',
                    'INCLUDE_SUBSECTIONS' => 'Y',     
                    'SECTION_ID'=>$section_id,
                            array("ID" => \CIBlockElement::SubQuery("ID", array('IBLOCK_CODE' => 'aspro_next_catalog-aspro_dfd', "PROPERTY_BREND" => $brand_id))),                
                    ];
        }

        $rs = \CIBlockElement::GetList(
            [],
            $arFilter,
            false,
            false,
            [
                'ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'PROPERTY_BREND.ID','PROPERTY_CML2_ARTICLE','PROPERTY_'.self::PROPERTY_MARK_CODE
            ]
        );

        while ($item = $rs->GetNext()) {


            $this->arProducts[] = $item;
        }

    }

    private  function getPrice($idProduct){

        $price_result = \CPrice::GetList(
            array(),
            array(
                "PRODUCT_ID" =>$idProduct, // id товара
                "CATALOG_GROUP_ID" => self::TYPE_PRICE_ID // это группа цены
            )
        );
        if ($arPrices = $price_result->Fetch())
        {
            return $arPrices;
        }

    }


    public function setExtraValue($mark_up_val)
    {
        $this->mark_up_val = $mark_up_val;
        return $this;
    }

    public function setBrandExtra($brand_id)
    {
        $this->brand_id = $brand_id;
        return $this;
    }

    public function setSectionExtra($section_id)
    {
        $this->section_id = $section_id;
        return $this;
    }



//    interface
    public function getFormSections()
    {
        $rs=\CIBlockSection::GetList([],
            [
                'IBLOCK_CODE' => self::IBLOCK_CODE,
                'ACTIVE' => 'Y',
                
            ],
            false,
            [
                'ID','CODE','NAME','DEPTH_LEVEL'
            ]
        );
        if(empty($rs))throw new Exception('Нет разделов');
        while($item = $rs->Fetch())
        {
            echo '<option value="'.$item['ID'].'">'.str_repeat('. ',($item['DEPTH_LEVEL']-1)*2).$item['NAME'].'</option>';
        }
    }

    public function getFormBrends()
    {
        $rs=\CIBlockElement::GetList(['NAME'=>'ASC','SORT'=>'ASC'],
            [
                'IBLOCK_CODE' => 'aspro_next_brands',
                'ACTIVE' => 'Y',
            ],
            false,
            false,
            [
                'ID','CODE','NAME'
            ]
        );
        // $rs = [];
        if(empty($rs)){
              echo '<option value="">нет брендов</option>';
        }else{

            while($item = $rs->Fetch())
            {
                echo '<option value="'.$item['ID'].'">'.$item['NAME'].'</option>';
            }
        }
    }

    // get table result
    public  function getTableResult(){
        $res = MarkUpTable::getList();

        $ar_result = $res->fetchAll();
        return $ar_result;
    }

    public function getResultBrand()
    {
         $res = MarkUpTable::getList([
            'filter'=>['!BRAND_ID'=>0]

         ]);

        $ar_result = $res->fetchAll();
        return $ar_result;
    }

    public function getResulSection()
    {
        $res = MarkUpTable::getList([
            'filter'=>['!SECTION_ID'=>0]

         ]);

        $ar_result = $res->fetchAll();
        return $ar_result;

    }




}

