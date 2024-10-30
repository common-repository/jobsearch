<?php

namespace Jobsearch;

use Jobsearch\Helper\Migration;

class SQL{
    // Create table if not exist
// Table name = name of the table who have to be created
// VarcharSize - array who have the size of the column transmitted, if empty, default value(80)
// Column name, array of column who have to be created
    function tdb_jb_create_table($tableName,$varcharSize=[],$columnName = []) {

        global $wpdb;
        $maxColumn = 15;
        $charset_collate = $wpdb->get_charset_collate();
        $cpt = 0;

        for($i = 0; $i<$maxColumn;$i++){
            $varcharSizeTmp[$i] = "80";
        }

        for($i = 0 ; $i<=$maxColumn; $i++){
            if(isset($varcharSize[$i])){
                $varcharSizeTmp[$i] = $varcharSize[$i];
            }
        }

        if(count($columnName) > 0){
            $sql = "CREATE TABLE IF NOT EXISTS ".$tableName."(" ;

            foreach($columnName as $column){
                $cpt ++;
                if (stristr($column, 'Id') == TRUE) {
                    $type =	"int";
                }
                else {
                    $type =	"varchar($varcharSizeTmp[$cpt])";
                }

                if($cpt == 1){
                    $primaryKey =  " PRIMARY KEY ($column) , UNIQUE KEY $column ($column)  ) ".$charset_collate.";";
                    $sql .=	$column ." $type NOT NULL AUTO_INCREMENT, ";
                } else {
                    $sql .=	$column."  ".$type.",";
                }
            }

            $sql .=	$primaryKey;
            return $wpdb->query($sql);
        }
        return false;
    }

    function tdb_jb_update_table($tableName,$varcharSize=[],$columnName = [], $defaultValue = "") {

        global $wpdb;
        $maxColumn = 15;
        $charset_collate = $wpdb->get_charset_collate();
        $cpt = 0;

        for($i = 0; $i<$maxColumn;$i++){
            $varcharSizeTmp[$i] = "80";
        }

        for($i = 0 ; $i<=$maxColumn; $i++){
            if(isset($varcharSize[$i])){
                $varcharSizeTmp[$i] = $varcharSize[$i];
            }
        }
//
        if(count($columnName) > 0){
              foreach($columnName as $column){
                $cpt ++;
                if (stristr($column, 'Id') == TRUE) {
                    $type =	"int";
                }
                else {
                    $type =	"varchar($varcharSizeTmp[$cpt])";
                }

                  $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$tableName' AND TABLE_SCHEMA ='$wpdb->dbname' AND COLUMN_NAME ='$column' ";
                  $wpdb->query($sql);

                  if($wpdb->query($sql) <= 0){
                      $sql = "ALTER TABLE $tableName ADD COLUMN $column $type" ;
                      if(is_numeric($defaultValue)){
                          $sql .= " DEFAULT $defaultValue";
                      } else {
                          $sql .= " DEFAULT '$defaultValue'";
                      }
                      $wpdb->query($sql);
                  }
            }
        }
    }

    function tdb_jb_create_database() {
        // Create table
        $this->tdb_jb_create_table(TDB_TABLE_MIGRATION, array() ,array("nId","sName"));
        $this->tdb_jb_create_table(TDB_TABLE_LAST_UPDATE, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_TYPE, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_CATEGORY, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_INDUSTRY, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_COMPANY, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_VISA, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_BASIS, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_CURRENCY, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_EDUCATION, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_EXPERIMENT, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        $this->tdb_jb_create_table(TDB_TABLE_SKILL, array() ,array("nId","sName","sTranslate","nIdLanguage"));
        unset($varcharSizeApply);
        $varcharSizeApply[2] = "40";
        $varcharSizeApply[3] = "300";
        $this->tdb_jb_create_table(TDB_TABLE_PARAM,$varcharSizeApply ,array("nId","sName","sValue","nIdApi"));
        unset($varcharSizeApply);
        $varcharSizeApply[3] = "100";
        $varcharSizeApply[4] = "20";
        $varcharSizeApply[5] = "20";
        $varcharSizeApply[6] = "5000";
        $this->tdb_jb_create_table(TDB_TABLE_APPLY,$varcharSizeApply ,array("nId","nIdJob","sName","sDate","sTimezone","sJson"));
        unset($varcharSizeApply);
        $varcharSizeApply[3] = "150";
        $varcharSizeApply[4] = "250";
        $this->tdb_jb_create_table(TDB_TABLE_APPLY_ATTACHMENT,$varcharSizeApply,array("nId","nIdApply","sName","sFile"));
        unset($varcharSizeApply);
        $varcharSizeApply[2] = "300";
        $this->tdb_jb_create_table(TDB_TABLE_API,$varcharSizeApply,array("nId","sName"));
        $this->tdb_jb_create_table(TDB_TABLE_LANG_USED,array(),array('nId','sLanguageName'));
        unset($varcharSizeApply);
        $varcharSizeApply[1] = "80";
        $varcharSizeApply[2] = "100";
        $varcharSizeApply[3] = "100";
        $varcharSizeApply[4] = "100";
        $varcharSizeApply[5] = "100";
        $varcharSizeApply[6] = "300";
        $varcharSizeApply[7] = "300";
        $varcharSizeApply[8] = "300";
        $this->tdb_jb_create_table(TDB_TABLE_APPLY_DETAIL,array(),array("nId","nIdApply","1stlvl","2ndlvl","3ndlvl1","3ndlvl2","val","val1","val2"));
        $this->tdb_jb_create_table(TDB_TABLE_VERSION,array(),array("nId","sVersion"));
        unset($varcharSizeApply);
        $varcharSizeApply[2] = "40";
        $varcharSizeApply[3] = "5000";
        $varcharSizeApply[4] = "40";
        $this->tdb_jb_create_table(TDB_TABLE_TEMPLATE,$varcharSizeApply ,array("nId","sName","sValue","sLanguage"));
    }

// Insert all default value on activation if not exist
    function tdb_jb_update_database() {

        //Update database
        $this->tdb_jb_insert_line(TDB_TABLE_API, array('sName' => 'Default'));

        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '1 day',"sTranslate" => '1 day','nIdLanguage' => 1),'sName','1 day','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '3 days',"sTranslate" => '3 days','nIdLanguage' => 1),'sName','3 days','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '7 days',"sTranslate" => '7 days','nIdLanguage' => 1),'sName','7 days','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '1 day',"sTranslate" => '1日','nIdLanguage' => 2),'sName','1 day','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '3 days',"sTranslate" => '3日','nIdLanguage' => 2),'sName','3 days','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '7 days',"sTranslate" => '7日','nIdLanguage' => 2),'sName','7 days','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '1 day',"sTranslate" => '1天','nIdLanguage' => 3),'sName','1 day','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '3 days',"sTranslate" => '3天','nIdLanguage' => 3),'sName','3 days','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '7 days',"sTranslate" => '7天','nIdLanguage' => 3),'sName','7 days','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '1 day',"sTranslate" => '1 día','nIdLanguage' => 4),'sName','1 day','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '3 days',"sTranslate" => '3 días','nIdLanguage' => 4),'sName','3 days','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_LAST_UPDATE, array('sName' => '7 days',"sTranslate" => '7 días','nIdLanguage' => 4),'sName','7 days','nIdLanguage',4);

        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can work',"sTranslate" => 'can work','nIdLanguage' => 1),'sName','can work','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can t work',"sTranslate" => 'can t work','nIdLanguage' => 1),'sName','can t work','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can work',"sTranslate" => '働ける','nIdLanguage' => 2),'sName','can work','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can t work',"sTranslate" => 'うまくいかない','nIdLanguage' => 2),'sName','can t work','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can work',"sTranslate" => '能行得通','nIdLanguage' => 3),'sName','can work','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can t work',"sTranslate" => '无法工作','nIdLanguage' => 3),'sName','can t work','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can work',"sTranslate" => 'puede trabajar','nIdLanguage' => 4),'sName','can work','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_VISA, array('sName' => 'can t work',"sTranslate" => 'no puede trabajar','nIdLanguage' => 4),'sName','can t work','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'weekly',"sTranslate" => 'weekly','nIdLanguage' => 1),'sName','weekly','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'monthly',"sTranslate" => 'monthly','nIdLanguage' => 1),'sName','monthly','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'yearly',"sTranslate" => 'yearly','nIdLanguage' => 1),'sName','yearly','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'weekly',"sTranslate" => '週給','nIdLanguage' => 2),'sName','weekly','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'monthly',"sTranslate" => '月給','nIdLanguage' => 2),'sName','monthly','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'yearly',"sTranslate" => '年収','nIdLanguage' => 2),'sName','yearly','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'weekly',"sTranslate" => '每周','nIdLanguage' => 3),'sName','weekly','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'monthly',"sTranslate" => '每月一次','nIdLanguage' => 3),'sName','monthly','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'yearly',"sTranslate" => '每年','nIdLanguage' => 3),'sName','yearly','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'weekly',"sTranslate" => 'semanal','nIdLanguage' => 4),'sName','weekly','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'monthly',"sTranslate" => 'mensual','nIdLanguage' => 4),'sName','monthly','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_BASIS, array('sName' => 'yearly',"sTranslate" => 'anual','nIdLanguage' => 4),'sName','yearly','nIdLanguage',4);

        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'USD',"sTranslate" => 'USD','nIdLanguage' => 1),'sName','USD','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'JPY',"sTranslate" => 'JPY','nIdLanguage' => 1),'sName','JPY','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'EUR',"sTranslate" => 'EUR','nIdLanguage' => 1),'sName','EUR','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'USD',"sTranslate" => 'ドル','nIdLanguage' => 2),'sName','USD','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'JPY',"sTranslate" => '円','nIdLanguage' => 2),'sName','JPY','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'EUR',"sTranslate" => 'ユーロ','nIdLanguage' => 2),'sName','EUR','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'USD',"sTranslate" => '美元','nIdLanguage' => 3),'sName','USD','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'JPY',"sTranslate" => '日元','nIdLanguage' => 3),'sName','JPY','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'EUR',"sTranslate" => '欧元','nIdLanguage' => 3),'sName','EUR','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'USD',"sTranslate" => 'USD','nIdLanguage' => 4),'sName','USD','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'JPY',"sTranslate" => 'JPY','nIdLanguage' => 4),'sName','JPY','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_CURRENCY, array('sName' => 'EUR',"sTranslate" => 'EUR','nIdLanguage' => 4),'sName','EUR','nIdLanguage',4);

        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'small',"sTranslate" => 'Small','nIdLanguage' => 1),'sName','small','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'medium',"sTranslate" => 'Medium','nIdLanguage' => 1),'sName','medium','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'large',"sTranslate" => 'Large','nIdLanguage' => 1),'sName','large','nIdLanguage',1);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'small',"sTranslate" => '小さい','nIdLanguage' => 2),'sName','small','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'medium',"sTranslate" => '中','nIdLanguage' => 2),'sName','medium','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'large',"sTranslate" => '大','nIdLanguage' => 2),'sName','large','nIdLanguage',2);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'small',"sTranslate" => '小','nIdLanguage' => 3),'sName','small','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'medium',"sTranslate" => '介质','nIdLanguage' => 3),'sName','medium','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'large',"sTranslate" => '大','nIdLanguage' => 3),'sName','large','nIdLanguage',3);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'small',"sTranslate" => 'pequeña','nIdLanguage' => 4),'sName','small','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'medium',"sTranslate" => 'medio','nIdLanguage' => 4),'sName','medium','nIdLanguage',4);
        $this->tdb_jb_insert_line(TDB_TABLE_COMPANY, array('sName' => 'large',"sTranslate" => 'grande','nIdLanguage' => 4),'sName','large','nIdLanguage',4);

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'Link', 'sValue' => '','nIdApi' => 1),'sName','Link');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'LinkSearch', 'sValue' => '/api/public/job/search','nIdApi' => 1),'sName','LinkSearch');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'LinkDetailJob', 'sValue' => '/api/public/job','nIdApi' => 1),'sName','LinkDetailJob');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'LinkCreate', 'sValue' => '/api/application','nIdApi' => 1),'sName','LinkCreate');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'LinkFile', 'sValue' => '/api/application','nIdApi' => 1),'sName','LinkFile');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'LinkOption', 'sValue' => '/api/public/job/list-options','nIdApi' => 1),'sName','LinkOption');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'LinkSearchOption', 'sValue' => '/api/public/job/search-options','nIdApi' => 1),'sName','LinkSearchOption');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'LinkImage', 'sValue' => '/api/public/image/job','nIdApi' => 1),'sName','LinkImage');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'ColorH1', 'sValue' => '','nIdApi' => 1),'sName','ColorH1');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'ColorH2', 'sValue' => '','nIdApi' => 1),'sName','ColorH2');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'ColorH3', 'sValue' => '','nIdApi' => 1),'sName','ColorH3');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'ColorH4', 'sValue' => '','nIdApi' => 1),'sName','ColorH4');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'ColorH5', 'sValue' => '','nIdApi' => 1),'sName','ColorH5');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnSubmitBackground', 'sValue' => '','nIdApi' => 1),'sName','BtnSubmitBackground');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnSubmitFont', 'sValue' => '','nIdApi' => 1),'sName','BtnSubmitFont');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnAddDelBackground', 'sValue' => '','nIdApi' => 1),'sName','BtnAddDelBackground');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnAddDelFont', 'sValue' => '','nIdApi' => 1),'sName','BtnAddDelFont');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnMoreBackground', 'sValue' => '','nIdApi' => 1),'sName','Link');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnMoreFont', 'sValue' => '','nIdApi' => 1),'sName','BtnMoreFont');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnBackBackground', 'sValue' => '','nIdApi' => 1),'sName','BtnBackBackground');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'BtnBackFont', 'sValue' => '','nIdApi' => 1),'sName','BtnBackFont');
//        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'linkFont', 'sValue' => '','nIdApi' => 1),'sName','linkFont');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'locationParam', 'sValue' => '1','nIdApi' => 1),'sName','locationParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'amountParam', 'sValue' => '1','nIdApi' => 1),'sName','amountParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'maxAmountParam', 'sValue' => '','nIdApi' => 1),'sName','maxAmountParam');

        //video param
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'summaryVideo', 'sValue' => '','nIdApi' => 1),'sName','summaryVideo');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'videoVideo', 'sValue' => '','nIdApi' => 1),'sName','videoVideo');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'defaultImageCheckVideo', 'sValue' => '','nIdApi' => 1),'sName','defaultImageCheckVideo');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currencyParam', 'sValue' => '1','nIdApi' => 1),'sName','currencyParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'maxCurrencyParam', 'sValue' => '','nIdApi' => 1),'sName','maxCurrencyParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'basisParam', 'sValue' => '1','nIdApi' => 1),'sName','basisParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'maxBasisParam', 'sValue' => '','nIdApi' => 1),'sName','maxBasisParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'negotiableParam', 'sValue' => '1','nIdApi' => 1),'sName','negotiableParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'typeParam', 'sValue' => '1','nIdApi' => 1),'sName','typeParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'type_detailParam', 'sValue' => '1','nIdApi' => 1),'sName','type_detailParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'companyParam', 'sValue' => '1','nIdApi' => 1),'sName','companyParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressParam', 'sValue' => '1','nIdApi' => 1),'sName','addressParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'emailParam', 'sValue' => '1','nIdApi' => 1),'sName','emailParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'requirementsParam', 'sValue' => '1','nIdApi' => 1),'sName','requirementsParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'education_levelParam', 'sValue' => '1','nIdApi' => 1),'sName','education_levelParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languageParam', 'sValue' => '1','nIdApi' => 1),'sName','languageParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'holidaysParam', 'sValue' => '1','nIdApi' => 1),'sName','holidaysParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'conditionsParam', 'sValue' => '1','nIdApi' => 1),'sName','conditionsParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'selling_pointsParam', 'sValue' => '1','nIdApi' => 1),'sName','selling_pointsParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'established_yearParam', 'sValue' => '1','nIdApi' => 1),'sName','established_yearParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'capitalParam', 'sValue' => '1','nIdApi' => 1),'sName','capitalParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'revenueParam', 'sValue' => '1','nIdApi' => 1),'sName','revenueParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'employee_countParam', 'sValue' => '1','nIdApi' => 1),'sName','employee_countParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'publicly_heldParam', 'sValue' => '1','nIdApi' => 1),'sName','publicly_heldParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'publicly_held_detailsParam', 'sValue' => '1','nIdApi' => 1),'sName','publicly_held_detailsParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'working_hoursParam', 'sValue' => '1','nIdApi' => 1),'sName','working_hoursParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'probationPeriodParam', 'sValue' => '1','nIdApi' => 1),'sName','probationPeriodParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'benefitsParam', 'sValue' => '1','nIdApi' => 1),'sName','benefitsParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'categoryParam', 'sValue' => '1','nIdApi' => 1),'sName','categoryParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'industryParam', 'sValue' => '1','nIdApi' => 1),'sName','industryParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'wage_detailsParam', 'sValue' => '1','nIdApi' => 1),'sName','wage_detailsParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'required_visasParam', 'sValue' => '1','nIdApi' => 1),'sName','required_visasParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'tagsParam', 'sValue' => '1','nIdApi' => 1),'sName','tagsParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'descriptionParam', 'sValue' => '1','nIdApi' => 1),'sName','descriptionParam');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'reason_for_hiring_detailsParam', 'sValue' => '1','nIdApi' => 1),'sName','reason_for_hiring_detailsParam');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteCurrency', 'sValue' => '1','nIdApi' => 1),'sName','favoriteCurrency');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteBasis', 'sValue' => '1','nIdApi' => 1),'sName','favoriteBasis');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteCertif', 'sValue' => '1','nIdApi' => 1),'sName','favoriteCertif');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'genderRequired', 'sValue' => '','nIdApi' => 1),'sName','genderRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'emailRequired', 'sValue' => '','nIdApi' => 1),'sName','emailRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'emailTypeRequired', 'sValue' => '','nIdApi' => 1),'sName','emailTypeRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'phoneRequired', 'sValue' => '','nIdApi' => 1),'sName','phoneRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'phoneTypeRequired', 'sValue' => '','nIdApi' => 1),'sName','phoneTypeRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'nationalityRequired', 'sValue' => '','nIdApi' => 1),'sName','nationalityRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'birthdateRequired', 'sValue' => '','nIdApi' => 1),'sName','birthdateRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'postalRequired', 'sValue' => '','nIdApi' => 1),'sName','postalRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'countryRequired', 'sValue' => '','nIdApi' => 1),'sName','countryRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'regionRequired', 'sValue' => '','nIdApi' => 1),'sName','regionRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'cityRequired', 'sValue' => '','nIdApi' => 1),'sName','cityRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'streetRequired', 'sValue' => '','nIdApi' => 1),'sName','streetRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languageRequired', 'sValue' => '','nIdApi' => 1),'sName','languageRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languageAbilityRequired', 'sValue' => '','nIdApi' => 1),'sName','languageAbilityRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languageCertificationRequired', 'sValue' => '','nIdApi' => 1),'sName','languageCertificationRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languageScoreRequired', 'sValue' => '','nIdApi' => 1),'sName','languageScoreRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredWageRequired', 'sValue' => '','nIdApi' => 1),'sName','desiredWageRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currencyRequired', 'sValue' => '','nIdApi' => 1),'sName','currencyRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'basisRequired', 'sValue' => '','nIdApi' => 1),'sName','basisRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'employementTypeRequired', 'sValue' => '','nIdApi' => 1),'sName','employementTypeRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredIndustryRequired', 'sValue' => '','nIdApi' => 1),'sName','desiredIndustryRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredLocationRequired', 'sValue' => '','nIdApi' => 1),'sName','desiredLocationRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'noticedPeriodRequired', 'sValue' => '','nIdApi' => 1),'sName','noticedPeriodRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'urlRequired', 'sValue' => '','nIdApi' => 1),'sName','urlRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'linkedinRequired', 'sValue' => '','nIdApi' => 1),'sName','linkedinRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'facebookRequired', 'sValue' => '','nIdApi' => 1),'sName','facebookRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredJobCategoryRequired', 'sValue' => '','nIdApi' => 1),'sName','desiredJobCategoryRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'certificationRequired', 'sValue' => '','nIdApi' => 1),'sName','certificationRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'nearestStationRequired', 'sValue' => '','nIdApi' => 1),'sName','nearestStationRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'referrerRequired', 'sValue' => '','nIdApi' => 1),'sName','referrerRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'sourceRequired', 'sValue' => '','nIdApi' => 1),'sName','sourceRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'sourceTypeRequired', 'sValue' => '','nIdApi' => 1),'sName','sourceTypeRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'attachmentRequired', 'sValue' => '','nIdApi' => 1),'sName','attachmentRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'visaCountryRequired', 'sValue' => '','nIdApi' => 1),'sName','visaCountryRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'visaTypeRequired', 'sValue' => '','nIdApi' => 1),'sName','visaTypeRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryRequired', 'sValue' => '','nIdApi' => 1),'sName','currentSalaryRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryCurrencyRequired', 'sValue' => '','nIdApi' => 1),'sName','currentSalaryCurrencyRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryBasisRequired', 'sValue' => '','nIdApi' => 1),'sName','currentSalaryBasisRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryBonusRequired', 'sValue' => '','nIdApi' => 1),'sName','currentSalaryBonusRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryBonusCurrencyRequired', 'sValue' => '','nIdApi' => 1),'sName','currentSalaryBonusCurrencyRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentEmploymentDepartmentRequired', 'sValue' => '','nIdApi' => 1),'sName','currentEmploymentDepartmentRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentEmploymentPositionRequired', 'sValue' => '','nIdApi' => 1),'sName','currentEmploymentPositionRequired');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentEmploymentCompanyRequired', 'sValue' => '','nIdApi' => 1),'sName','currentEmploymentCompanyRequired');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteLanguageContent', 'sValue' => '1:ja;2:en;3:zh;4:fr;5:ko','nIdApi' => 1),'sName','favoriteLanguageContent');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteNationalityContent', 'sValue' => '1:JP;2:GB;3:CN;4:VN;5:TH;6:BI;7:ID','nIdApi' => 1),'sName','favoriteNationalityContent');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteCountryContent', 'sValue' => '1:JP;2:GB;3:CN;4:VN;5:TH;6:BI;7:ID','nIdApi' => 1),'sName','favoriteCountryContent');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteNationalityContentOthers', 'sValue' => '1:JP;2:GB;3:CN;4:VN;5:TH;6:BI;7:ID','nIdApi' => 1),'sName','favoriteNationalityContentOthers');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'favoriteLanguageSearchContent', 'sValue' => '1:ja;2:en','nIdApi' => 1),'sName','favoriteLanguageSearchContent');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchPosition', 'sValue' => '1','nIdApi' => 1),'sName','searchPosition');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchMaxSalary', 'sValue' => '','nIdApi' => 1),'sName','searchMaxSalary');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'apiKey', 'sValue' => '','nIdApi' => 1),'sName','apiKey');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'apiJobPage', 'sValue' => '','nIdApi' => 1),'sName','apiJobPage');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'apiSearch', 'sValue' => '','nIdApi' => 1),'sName','apiSearch');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'nbPageToShow', 'sValue' => '10','nIdApi' => 1),'sName','nbPageToShow');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'shortDescriptionMaxCharacters', 'sValue' => '250','nIdApi' => 1),'sName','shortDescriptionMaxCharacters');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'nbJobToShowWidget', 'sValue' => '5','nIdApi' => 1),'sName','nbJobToShowWidget');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'templateUsed', 'sValue' => '1','nIdApi' => 1),'sName','templateUsed');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'privacyPolicyText', 'sValue' => "http://googlr.com",'nIdApi' => 1),'sName','privacyPolicyText');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'urlApply', 'sValue' => '','nIdApi' => 1),'sName','urlApplyText');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'privacyPolicyMandatory', 'sValue' => '','nIdApi' => 1),'sName','privacyPolicyMandatory');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'sourceType', 'sValue' => '','nIdApi' => 1),'sName','sourceType');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'genderApply', 'sValue' => '1','nIdApi' => 1),'sName','genderApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'emailsApply', 'sValue' => '1','nIdApi' => 1),'sName','emailsApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'phoneNumbersApply', 'sValue' => '1','nIdApi' => 1),'sName','phoneNumbersApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'phoneTypeApply', 'sValue' => '1','nIdApi' => 1),'sName','phoneTypeApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'emailsTypeApply', 'sValue' => '1','nIdApi' => 1),'sName','emailsTypeApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressApply', 'sValue' => '1','nIdApi' => 1),'sName','addressApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'birthdateApply', 'sValue' => '1','nIdApi' => 1),'sName','birthdateApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'nationalityApply', 'sValue' => '1','nIdApi' => 1),'sName','nationalityApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languagesApply', 'sValue' => '1','nIdApi' => 1),'sName','languagesApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languageCertificationsApply', 'sValue' => '1','nIdApi' => 1),'sName','languageCertificationsApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredWageApply', 'sValue' => '1','nIdApi' => 1),'sName','desiredWageApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredEmploymentTypesApply', 'sValue' => '1','nIdApi' => 1),'sName','desiredEmploymentTypesApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredJobCategoryApply', 'sValue' => '1','nIdApi' => 1),'sName','desiredJobCategoryApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredIndustryApply', 'sValue' => '1','nIdApi' => 1),'sName','desiredIndustryApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredLocationApply', 'sValue' => '1','nIdApi' => 1),'sName','desiredLocationApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'neareststationApply', 'sValue' => '1','nIdApi' => 1),'sName','neareststationApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'certificationApply', 'sValue' => '1','nIdApi' => 1),'sName','certificationApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'referrerApply', 'sValue' => '1','nIdApi' => 1),'sName','referrerApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'visaApply', 'sValue' => '1','nIdApi' => 1),'sName','visaApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'noticePeriodApply', 'sValue' => '1','nIdApi' => 1),'sName','noticePeriodApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'facebookApply', 'sValue' => '1','nIdApi' => 1),'sName','facebookApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'linkedinApply', 'sValue' => '1','nIdApi' => 1),'sName','linkedinApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'urlApply', 'sValue' => '1','nIdApi' => 1),'sName','urlApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'attachmentApply', 'sValue' => '1','nIdApi' => 1),'sName','attachmentApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'recaptchaApply', 'sValue' => '','nIdApi' => 1),'sName','recaptchaApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'recaptchaKey', 'sValue' => "",'nIdApi' => 1),'sName','recaptchaKey');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'recaptchaSecret', 'sValue' => "",'nIdApi' => 1),'sName','recaptchaSecret');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryApply', 'sValue' => '1','nIdApi' => 1),'sName','currentSalaryApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryBonusApply', 'sValue' => '1','nIdApi' => 1),'sName','currentSalaryBonusApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentEmploymentDepartmentApply', 'sValue' => '1','nIdApi' => 1),'sName','currentEmploymentDepartmentApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentEmploymentPositionApply', 'sValue' => '1','nIdApi' => 1),'sName','currentEmploymentPositionApply');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentEmploymentCompanyApply', 'sValue' => '1','nIdApi' => 1),'sName','currentEmploymentCompanyApply');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'widgetChoosenCategory', 'sValue' => '','nIdApi' => 1),'sName','widgetChoosenCategory');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'widgetChoosenMaximumDateJob', 'sValue' => '5','nIdApi' => 1),'sName','widgetChoosenCategory');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideSalary', 'sValue' => '','nIdApi' => 1),'sName','searchHideSalary');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideLocation', 'sValue' => '','nIdApi' => 1),'sName','searchHideLocation');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideReset', 'sValue' => '','nIdApi' => 1),'sName','searchHideReset');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideCurrency', 'sValue' => '','nIdApi' => 1),'sName','searchHideCurrency');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideBasis', 'sValue' => '','nIdApi' => 1),'sName','searchHideBasis');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchMaxSalary', 'sValue' => '','nIdApi' => 1),'sName','searchMaxSalary');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideMaxWage', 'sValue' => '','nIdApi' => 1),'sName','searchHideMaxWage');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideMinWage', 'sValue' => '','nIdApi' => 1),'sName','searchHideMinWage');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideLanguage', 'sValue' => '','nIdApi' => 1),'sName','searchHideLanguage');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchHideAddLanguage', 'sValue' => '','nIdApi' => 1),'sName','searchHideAddLanguage');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchAdvancedButton', 'sValue' => '','nIdApi' => 1),'sName','searchAdvancedButton');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchShowOneCurrency', 'sValue' => '','nIdApi' => 1),'sName','searchShowOneCurrency');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchShowOneBasis', 'sValue' => '','nIdApi' => 1),'sName','searchShowOneBasis');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'titleSortBy', 'sValue' => '1','nIdApi' => 1),'sName','titleSortBy');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'dateSortBy', 'sValue' => '1','nIdApi' => 1),'sName','dateSortBy');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'salarySortBy', 'sValue' => '1','nIdApi' => 1),'sName','salarySortBy');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchVisible', 'sValue' => '','nIdApi' => 1),'sName','SearchVisible');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'belowLanguageCheck', 'sValue' => '1','nIdApi' => 1),'sName','belowLanguageCheck');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'reverseLanguageSkillCheck', 'sValue' => '','nIdApi' => 1),'sName','reverseLanguageSkillCheck');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'autoNationalityCountry', 'sValue' => '','nIdApi' => 1),'sName','autoNationalityCountry');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displaySearchType', 'sValue' => '1','nIdApi' => 1),'sName','displaySearchType');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displaySearchCategory', 'sValue' => '1','nIdApi' => 1),'sName','displaySearchCategory');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displaySearchIndustry', 'sValue' => '1','nIdApi' => 1),'sName','displaySearchIndustry');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'searchFieldIsMultiple', 'sValue' => '1','nIdApi' => 1),'sName','searchFieldIsMultiple');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'rewriteUrl', 'sValue' => '1','nIdApi' => 1),'sName','rewriteUrl');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'customizeDesign', 'sValue' => '','nIdApi' => 1),'sName','customizeDesign');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'descriptionCleanedCheck', 'sValue' => '','nIdApi' => 1),'sName','descriptionCleanedCheck');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'tagGroup1', 'sValue' => '','nIdApi' => 1),'sName','tagGroup1');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'tagGroup2', 'sValue' => '','nIdApi' => 1),'sName','tagGroup2');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'tagGroup3', 'sValue' => '','nIdApi' => 1),'sName','tagGroup3');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'tagGroup4', 'sValue' => '','nIdApi' => 1),'sName','tagGroup4');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displayTagGroup1', 'sValue' => '','nIdApi' => 1),'sName','displayTagGroup1');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displayTagGroup2', 'sValue' => '','nIdApi' => 1),'sName','displayTagGroup2');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displayTagGroup3', 'sValue' => '','nIdApi' => 1),'sName','displayTagGroup3');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displayTagGroup4', 'sValue' => '','nIdApi' => 1),'sName','displayTagGroup4');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'displayCategories', 'sValue' => '','nIdApi' => 1),'sName','displayCategories');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'excludedCategories', 'sValue' => '','nIdApi' => 1),'sName','excludedCategories');

        // register col size
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'familyNameRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','familyNameRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'givenNameRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','givenNameRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'birthYearRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','birthYearRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'birthMonthRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','birthMonthRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'birthDayRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','birthDayRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'genderRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','genderRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressPostalRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','addressPostalRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressCountryRegisterColSize', 'sValue' => '3','nIdApi' => 1),'sName','addressCountryRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressRegionRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','addressRegionRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressCityRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','addressCityRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressExtendedRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','addressExtendedRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addressStreetRegisterColSize', 'sValue' => '7','nIdApi' => 1),'sName','addressStreetRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'nearestStationRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','nearestStationRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'emailRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','emailRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'emailTypeRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','emailTypeRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'phoneRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','phoneRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'phoneTypeRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','phoneTypeRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addEmailRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','addEmailRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'addPhoneRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','addPhoneRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'delEmailRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','delEmailRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'delPhoneRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','delPhoneRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'nationalityRegisterColSize', 'sValue' => '6','nIdApi' => 1),'sName','nationalityRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'visaRegisterColSize', 'sValue' => '3','nIdApi' => 1),'sName','visaRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'visaCountryRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','visaCountryRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'languageCertificationRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','languageCertificationRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'certifAbilityRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','certifAbilityRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'certificationRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','certificationRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'levelCertificationRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','levelCertificationRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'certificationTextRegisterColSize', 'sValue' => '7','nIdApi' => 1),'sName','certificationTextRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentCompanyRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','currentCompanyRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentPositionRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','currentPositionRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentDepartmentRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','currentDepartmentRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryAmountRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','currentSalaryAmountRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryCurrencyRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','currentSalaryCurrencyRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'currentSalaryBasisRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','currentSalaryBasisRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'bonusSalaryAmountRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','bonusSalaryAmountRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'bonusSalaryCurrencyRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','bonusSalaryCurrencyRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'bonusSalaryBasisRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','bonusSalaryBasisRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredSalaryAmountRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','desiredSalaryAmountRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredSalaryCurrencyRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','desiredSalaryCurrencyRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredSalaryBasisRegisterColSize', 'sValue' => '2','nIdApi' => 1),'sName','desiredSalaryBasisRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredEmploymentRegisterColSize', 'sValue' => '12','nIdApi' => 1),'sName','desiredEmploymentRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredLocationRegisterColSize', 'sValue' => '12','nIdApi' => 1),'sName','desiredLocationRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredIndustryRegisterColSize', 'sValue' => '12','nIdApi' => 1),'sName','desiredIndustryRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'desiredCategoryRegisterColSize', 'sValue' => '12','nIdApi' => 1),'sName','desiredIndustryRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'findUsRegisterColSize', 'sValue' => '6','nIdApi' => 1),'sName','findUsRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'noticedPeriodRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','noticedPeriodRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'facebookRegisterColSize', 'sValue' => '6','nIdApi' => 1),'sName','facebookRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'linkedinRegisterColSize', 'sValue' => '6','nIdApi' => 1),'sName','linkedinRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'urlRegisterColSize', 'sValue' => '6','nIdApi' => 1),'sName','urlRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'resumeRegisterColSize', 'sValue' => '4','nIdApi' => 1),'sName','resumeRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'privacyPolicyLabelRegisterColSize', 'sValue' => '11','nIdApi' => 1),'sName','privacyPolicyLabelRegisterColSize');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'privacyPolicyCheckRegisterColSize', 'sValue' => '1','nIdApi' => 1),'sName','privacyPolicyCheckRegisterColSize');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'socialLogo', 'sValue' => '','nIdApi' => 1),'sName','socialLogo');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'defaultImage', 'sValue' => '','nIdApi' => 1),'sName','defaultImage');

        $this->tdb_jb_insert_line(TDB_TABLE_LANG_USED, array('sLanguageName' => 'en'),'sLanguageName','en');
        $this->tdb_jb_insert_line(TDB_TABLE_LANG_USED, array('sLanguageName' => 'ja'),'sLanguageName','ja');
        $this->tdb_jb_insert_line(TDB_TABLE_LANG_USED, array('sLanguageName' => 'zh'),'sLanguageName','zh');
        $this->tdb_jb_insert_line(TDB_TABLE_LANG_USED, array('sLanguageName' => 'es'),'sLanguageName','es');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'jobTitleTag', 'sValue' => 'h1','nIdApi' => 1),'sName','jobTitleTag');

        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'ccTemplate', 'sValue' => '','nIdApi' => 1),'sName','ccTemplate');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'bccTemplate', 'sValue' => '','nIdApi' => 1),'sName','bccTemplate');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'fromTemplate', 'sValue' => '','nIdApi' => 1),'sName','fromTemplate');
        $this->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'templateActivateCheck', 'sValue' => '','nIdApi' => 1),'sName','templateActivateCheck');

        $this->tdb_jb_insert_line(TDB_TABLE_TEMPLATE, array('sName' => 'submitTemplate', 'sValue' => '','sLanguage' => 'default'),'sName','submitTemplate');
        $this->tdb_jb_insert_line(TDB_TABLE_TEMPLATE, array('sName' => 'submitTemplateSubject', 'sValue' => '','sLanguage' => 'default'),'sName','submitTemplate');
    }

// Delete table
    function tdb_jb_delete_Table($table) {
        global $wpdb;

        $sql = "DROP TABLE IF EXISTS $table;";
        $wpdb->query($sql);
    }

// Check if the value sended doesn t exist, if not, create the value
    function tdb_jb_insert_line($table,$array,$columnName1 = "", $valueColumn1 = "",$columnName2 = "", $valueColumn2 = ""){
        global $wpdb;
        $bExist = false;

        if($columnName1 <> "" && $valueColumn1 <> ""){
            $request = "SELECT $columnName1 FROM  $table WHERE $columnName1  = '$valueColumn1'";
            if($columnName2 <> "" && $valueColumn2 <> ""){
                $request .= " AND $columnName2 = $valueColumn2";
            }
            $request .= ";";
            //execute request
            $wpdb->get_results($request);

            if($wpdb->num_rows > 0){
                $bExist = true;
            }

        }
        if($bExist == false){
            $wpdb->insert($table, $array);
        }
    }

    // Check the latest version and update database if new version
    function tdb_jb_update_api(){
        global $wpdb;

        $columnName = 'MAX(sVersion)';

        $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".TDB_TABLE_VERSION."' AND column_name = 'sVersion'"  );


        // if column doesn't exist, check and create database with new value
        if(empty($row)){
            $this->tdb_jb_create_database();
            $this->tdb_jb_update_database();
        } else {
            $version = "";

            $request = "SELECT ".$columnName." FROM " . TDB_TABLE_VERSION .";" ;

            $exec = $wpdb->get_results($request);

            // Check the version, if not exist, create database
            foreach ($exec as $ligneResult) {
                $version = $ligneResult->$columnName;
            }

            if($version <> TDB_VERSION){
                $wpdb->delete( TDB_TABLE_VERSION, array( 'sVersion' => $version ) );
                $this->tdb_jb_insert_line(TDB_TABLE_VERSION, array('sVersion' => TDB_VERSION));
                $this->tdb_jb_create_database();
                $this->tdb_jb_update_database();

                $migration = new Migration();
                $migration->migrate();
            }

            //Add idApi
            if($version <1.0) {
                unset($varcharSizeApply);
                $varcharSizeApply[2] = "40";
                $varcharSizeApply[3] = "300";
                $this->tdb_jb_update_table(TDB_TABLE_PARAM,$varcharSizeApply ,array("nId","sName","sValue","nIdApi"),1);
            }
        }
    }

    // Check if the value sent exists, if not, create the value
    function tdb_jb_update_line($table, $columnUpdateName, $columnUpdateValue, $columnWhereName, $columnWhereValue){
        global $wpdb;

        $wpdb->update($table, array($columnUpdateName=>$columnUpdateValue), array($columnWhereName=>$columnWhereValue));

        return $wpdb->num_rows;
    }
}
