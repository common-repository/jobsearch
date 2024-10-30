<?php
namespace Jobsearch;

class jobFormSearch{
    public function tdb_jb_show($attributes, $urlArray)   {
        global $gTypes;
        global $gCategories;
        global $gCategoriesFiltered;
        global $gIndustries;
        global $gLanguages;
        global $gLocation;
        global $gJobLanguages;

        $nbLocation = 0;
        $nbCategory = 0;
        $nbIndustry = 0;
        $nbType = 0;

        $helper = new Helper();
        $language = $helper->tdb_jb_get_current_language();

        $searchVisible = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchVisible', 'sValue', 'sName');
        if($searchVisible <> '' && $attributes['urlSearch'] == ''){
            return "";
        }

        $currencyFavorite = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'favoriteCurrency', 'sValue', 'sName');
        $currencyFilter = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchShowOneCurrency', 'sValue', 'sName');
        $basisFavorite = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'favoriteBasis', 'sValue', 'sName');
        $basisFilter = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchShowOneBasis', 'sValue', 'sName');
        $searchHideSalary = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideSalary', 'sValue', 'sName');
        $searchHideLocation = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideLocation', 'sValue', 'sName');
        $searchHideReset = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideReset', 'sValue', 'sName');
        $searchHideCurrency = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideCurrency', 'sValue', 'sName');
        $searchHideBasis = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideBasis', 'sValue', 'sName');
        $searchHideMaxWage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideMaxWage', 'sValue', 'sName');
        $searchHideMinWage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideMinWage', 'sValue', 'sName');
        $searchHideLanguage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideLanguage', 'sValue', 'sName');
        $searchAdvancedButton = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchAdvancedButton', 'sValue', 'sName');
        $belowLanguageCheck = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'belowLanguageCheck', 'sValue', 'sName');
        $searchHideAddLanguage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideAddLanguage', 'sValue', 'sName');
        $reverseLanguageSkill = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'reverseLanguageSkillCheck', 'sValue', 'sName');
        $searchFieldIsMultiple = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchFieldIsMultiple', 'sValue', 'sName');
        $displaySearchType = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displaySearchType', 'sValue', 'sName');
        $displaySearchIndustry = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displaySearchIndustry', 'sValue', 'sName');
        $displaySearchCategory = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displaySearchCategory', 'sValue', 'sName');

        $containerHiddenCss = '';
        if($searchAdvancedButton <> ''){
            $searchAdvancedButton = '1';

            if(isset($_GET['advancedSearch'])){
                $containerHiddenCss = '';
            } else {
                $containerHiddenCss = ' tdb-jd-hidden';
            }
        }

        $favoriteLanguageSearchArray = $helper->tdb_jb_get_array_favorite_language_search();
        $favoriteLanguageSearchContent = $helper->tdb_jb_set_content_favorite_language($favoriteLanguageSearchArray);

        $getParameter = [];
        $getParameter["keyword"] = '';
        $getParameter["type"][] = '';
        $getParameter["wageFrom"] = '';
        $getParameter["wageTo"] = '';
        $getParameter["wageBasis"] = '';
        $getParameter["currency"] = '';
        $getParameter["industry"][] = '';
        $getParameter["category"] = '';
        $getParameter["language"][] = '';
        $getParameter["location"] = '';
        $getParameter["tags"][] = '';
        $getParameter["limit"] = TDB_NB_JOB_TO_SHOW;

        // Get all parameter
        $getParameter["keyword"] = $helper->tdb_jb_get_form_param('keyword', 'text');
        $getParameter["type"] = $helper->tdb_jb_get_form_param('type', 'text', 'type');
        $getParameter["category"] = $helper->tdb_jb_get_form_param('category', 'array', 'category');
        $getParameter["industry"] = $helper->tdb_jb_get_form_param('industry', 'array', 'industry');
        $getParameter["wageFrom"] = $helper->tdb_jb_get_form_param('wageFrom', 'text', 'amount');
        $getParameter["wageTo"] = $helper->tdb_jb_get_form_param('wageTo', 'text', 'amount');
        $getParameter["wageBasis"] = $helper->tdb_jb_get_form_param('wageBasis', 'text', 'basis');
        $getParameter["currency"] = $helper->tdb_jb_get_form_param('currency', 'text', 'currency');
        $getParameter["joblanguage"] = $helper->tdb_jb_get_form_param('joblanguage', 'text');
        $getParameter["offset"] = $helper->tdb_jb_get_form_param('offset', 'text', 'int');
        $getParameter["location"] = $helper->tdb_jb_get_form_param('location', 'text', 'location');
        $getParameter["tags"] = $helper->tdb_jb_get_form_param('tags', 'array', 'tags');

        $i = 1;
        While (isset($_GET["language".$i])) {
            if($helper->tdb_jb_validate_data($_GET["language".$i])){
                $getParameter["language".$i] = $helper->tdb_jb_sanitize($_GET["language".$i],'');
            }
            $i++;
        }

        //Language
        $countLanguage = 1;
        $htmlLanguage = "";
        $languageNameTmp = "";
        if(isset($_GET["language1"])){
            $languageArray = $_GET["language1"];
            if(isset($languageArray["language"])){
                $languageNameTmp = $languageArray["language"];
            }
        }

        // Setup all dynamic field if they have some value ( language and language skill)
        if(isset($_GET["language1"]) && is_array($_GET["language1"]) && count($_GET["language1"])> 0 && $languageNameTmp <> "" ) {

            while(isset($_GET["language".$countLanguage])){

                if($helper->tdb_jb_validate_data($_GET["language".$countLanguage],'')){
                    $max = "";
                    $min = "";

                    if(isset($_GET["language".$countLanguage]["language"]) && $_GET["language".$countLanguage]["language"] <> ""){
                        $languageSearch = $_GET["language".$countLanguage]["language"];
                        if(isset($_GET["language".$countLanguage]["max"])){
                            $max = $helper->tdb_jb_sanitize($_GET["language".$countLanguage]["max"],'text');
                        }
                        if(isset($_GET["language".$countLanguage]["min"])){
                            $min = "checked";
                        }

                        if($countLanguage > 1){
                            $classLabel = 'tdb-jd-sublabel';
                        } else {
                            $classLabel = '';
                        }

                        $htmlLanguage .= $helper->tdb_jd_generate_language_search_block($countLanguage,$gJobLanguages,$favoriteLanguageSearchContent,$gLanguages,$reverseLanguageSkill,$belowLanguageCheck, $classLabel,$languageSearch,$max,$min);
                    }
                }

                $countLanguage ++;
            }
        } else {
            $htmlLanguage = $helper->tdb_jd_generate_language_search_block($countLanguage,$gJobLanguages,$favoriteLanguageSearchContent,$gLanguages,$reverseLanguageSkill,$belowLanguageCheck,'','','','');
        }

        if(is_array($gLocation)){
            $nbLocation = count($gLocation);
        }
        if(is_array($gCategories)){
            $nbCategory = count($gCategories);
        }

        $excludedCategoriesContent = $helper->tdb_jb_get_array_category('excludedCategories');
        $excludedCategories = $helper->tdb_jb_set_content_category($excludedCategoriesContent);
        $displayCategoriesContent = $helper->tdb_jb_get_array_category('displayCategories');
        $displayCategories = $helper->tdb_jb_set_content_category($displayCategoriesContent);

        if(is_array($gCategoriesFiltered) && ((count($excludedCategories) > 0 ) || (count($displayCategories) > 0 ))){
            $nbCategory = count($gCategoriesFiltered);
            $gCategories = $gCategoriesFiltered;
        }
        if(is_array($gIndustries)){
            $nbIndustry = count($gIndustries);
        }
        if(is_array($gTypes)){
            $nbType = count($gTypes);
        }

        $widthCategory = 4;
        $widthType = 4;
        $widthIndustry = 4;

        $multipleCss = 'tdb-jd-custom-select tdb-jd-custom-select-multi';
        $multipleField = 'multiple';
        $simpleCss = 'tdb-jd-custom-select tdb-jd-input' ;


        if($searchFieldIsMultiple <> ''){
            $simpleCss = '';
        } else {
            $multipleCss = '';
            $multipleField = '';
        }

        if($displaySearchType == ''){
            $nbType = 0;
        }

        if($displaySearchIndustry == ''){
            $nbIndustry = 0;
        }

        if($displaySearchCategory == ''){
            $nbCategory = 0;
        }

        if(($nbCategory == 0 || $nbIndustry == 0 || $nbType == 0) && $searchFieldIsMultiple <> ''){
            $widthCategory = 6;
            $widthType = 6;
            $widthIndustry = 6;
        }

        if($nbCategory == 0  && $nbIndustry == 0 && $nbType == 0){
            $isMultipleFieldEmpty = true;
        } else {
            $isMultipleFieldEmpty = false;
        }

        if($nbLocation > 0){
            $keywordCol = 6;
        } else {
            $keywordCol = 12;
        }

        //tags
        $htmlTags1 = '';
        $htmlTags2 = '';
        $htmlTags3 = '';
        $htmlTags4 = '';
        $tags1 = $helper->getTagsField(1, $getParameter["tags"]);
        $tags2 = $helper->getTagsField(2, $getParameter["tags"]);
        $tags3 = $helper->getTagsField(3, $getParameter["tags"]);
        $tags4 = $helper->getTagsField(4, $getParameter["tags"]);

        if($tags1 || $tags2 || $tags3 || $tags4){
            $countTagsGroup = 0;
            $columnWidth = 6;
            if ($tags1 != '') $countTagsGroup++;
            if ($tags2 != '') $countTagsGroup++;
            if ($tags3 != '') $countTagsGroup++;
            if ($tags4 != '') $countTagsGroup++;

            switch($countTagsGroup){
                case 1:
                case 2:
                $columnWidth = 6;
                    break;
                case 3:
                    $columnWidth = 4;
                    break;
                case 4:
                    $columnWidth = 3;
                    break;
            }
            if($tags1){
                $title = $helper->getTagsTitle(1);
                $htmlTags1 = $helper->tdb_jb_get_col($columnWidth, "select", "tags[]", $tags1, $title,"$multipleCss $simpleCss","$multipleField","","tags1","","","","","","tdb-jd-tags");
            }
            if($tags2){
                $title = $helper->getTagsTitle(2);
                $htmlTags2 = $helper->tdb_jb_get_col($columnWidth, "select", "tags[]", $tags2, $title,"$multipleCss $simpleCss","$multipleField","","tags2","","","","","","tdb-jd-tags");
            }
            if($tags3){
                $title = $helper->getTagsTitle(3);
                $htmlTags3 = $helper->tdb_jb_get_col($columnWidth, "select", "tags[]", $tags3, $title,"$multipleCss $simpleCss","$multipleField","","tags3","","","","","","tdb-jd-tags");
            }
            if($tags4){
                $title = $helper->getTagsTitle(4);
                $htmlTags4 = $helper->tdb_jb_get_col($columnWidth, "select", "tags[]", $tags4, $title,"$multipleCss $simpleCss","$multipleField","","tags4","","","","","","tdb-jd-tags");
            }
        }

        //check if display is activate
        //check if it has groups
        // check if it has tags inside group
        //Get translation
        //set up multiple or single field
        //check number of group to display and set up column width

        $nonce = wp_nonce_field( 'tdb_jb_frontend_search', 'tdb_jb_frontend_search', false, true );
        $searchArray = array('urlForm' => $urlArray["url"],
            'langSearch' => TDB_LANG_SEARCH,
            'langAdvanced' => TDB_LANG_ADVANCEDSEARCH,
            'langReset' => TDB_LANG_RESET,
            'langAdd' => TDB_LANG_ADDLANGUAGE,
            'langLanguage' => TDB_LANG_LANGUAGE,
            'langSalary' => TDB_LANG_SALARY,
            'isMultipleFieldEmpty' => $isMultipleFieldEmpty,
            'nbCategory' => $nbCategory,
            'nbIndustry' => $nbIndustry,
            'nbType' => $nbType,
            'keyword' => $helper->tdb_jb_get_col($keywordCol, "text", "keyword",$getParameter["keyword"],TDB_LANG_KEYWORD,"tdb-jd-input-text tdb-jd-input","","","","","","","",TDB_LANG_SEARCHJOBS),
            'type' => $helper->tdb_jb_get_col($widthType, "select","type",$helper->tdb_jb_get_opt_select($gTypes,TDB_LANG_SELECTEMPLOYEMENTTYPE,$getParameter["type"]),TDB_LANG_TYPE,"$multipleCss $simpleCss","$multipleField","","","","","","","","tdb-jd-type"),
            'industry' => $helper->tdb_jb_get_col($widthIndustry, "select", "industry[]",$helper->tdb_jb_get_opt_select($gIndustries,TDB_LANG_SELECTINDUSTRY,$getParameter["industry"]),TDB_LANG_INDUSTRY,"$multipleCss $simpleCss","$multipleField","","","","","","","","tdb-jd-industry"),
            'category' => $helper->tdb_jb_get_col($widthCategory, "select", "category[]",$helper->tdb_jb_get_opt_select($gCategories,TDB_LANG_ANY,$getParameter["category"]),TDB_LANG_CATEGORY,"$multipleCss $simpleCss","$multipleField","","","","","","","","tdb-jd-category"),
            'wageFrom' => $helper->tdb_jb_get_col(4, "number", "wageFrom",$getParameter["wageFrom"],"","tdb-jd-input-text tdb-jd-input","","","","","","","",TDB_LANG_MINWAGE),
            'wageTo' => $helper->tdb_jb_get_col(4, "number", "wageTo",$getParameter["wageTo"],"","tdb-jd-input-text tdb-jd-input","","","","","","","",TDB_LANG_MAXWAGE),
            'wageBasis' => $helper->tdb_jb_get_col(2, "select", "wageBasis",$helper->tdb_jb_get_opt_sql($language,TDB_TABLE_BASIS,"sName","sTranslate","",TDB_LANG_BASIS,$getParameter["wageBasis"],$basisFavorite,$basisFilter),"","tdb-jd-custom-select tdb-jd-input","","","","","","","",TDB_LANG_BASIS,"","","","",'tdb-basis-container'),
            "location" => $helper->tdb_jb_get_col(6, "select", "location",$helper->tdb_jb_get_opt_select($gLocation,TDB_LANG_CHOOSELOCATION,$getParameter["location"]),TDB_LANG_LOCATION,"tdb-jd-custom-select tdb-jd-input"),
            'currency' => $helper->tdb_jb_get_col(2, "select", "currency",$helper->tdb_jb_get_opt_sql($language,TDB_TABLE_CURRENCY,"sName","sTranslate","",TDB_LANG_CURRENCY,$getParameter["currency"],$currencyFavorite,$currencyFilter),"","tdb-jd-custom-select tdb-jd-input","","","","","","","",TDB_LANG_CURRENCY,"","","","",'tdb-currency-container'),
            'tags1' => $htmlTags1,
            'tags2' => $htmlTags2,
            'tags3' => $htmlTags3,
            'tags4' => $htmlTags4,
            'language' => $htmlLanguage,
            'nbLocation' => $nbLocation,
            'urlSearch' => $attributes['urlSearch'],
            'searchHidden' => $attributes['searchHidden'],
            'searchHideSalary' => $searchHideSalary,
            'searchHideLocation' => $searchHideLocation,
            'searchHideReset' => $searchHideReset,
            'searchHideBasis' => $searchHideBasis,
            'searchHideCurrency' => $searchHideCurrency,
            'searchHideMaxWage' => $searchHideMaxWage,
            'searchHideMinWage' => $searchHideMinWage,
            'searchHideLanguage' => $searchHideLanguage,
            'searchHideAddLanguage' => $searchHideAddLanguage,
            'searchAdvancedButton' => $searchAdvancedButton,
            'searchPosition' => $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchPosition', 'sValue', 'sName'),
            'containerHiddenCss' => $containerHiddenCss,
            'nonce' => $nonce
        );
        // Show Data

        $helper->tdb_jb_show_template(TDB_SEARCH_TPL,$searchArray);
    }
}