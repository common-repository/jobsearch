<?php

namespace Jobsearch;

use function Couchbase\defaultDecoder;
use Jobsearch\Helper\Translation;
use Smarty;

class Helper {
    //Global function
    // Clean the value(space, upper case for the value of the option in the search form
    function tdb_jb_clean_opt_value($value) {
        if(is_string($value)){
            $valueChanged = strtolower($value);
            $valueChanged = rtrim($valueChanged);
            $valueChanged = str_replace("  "," ",$valueChanged);
            $valueChanged = str_replace(" ","+",$valueChanged);

            return $valueChanged;
        }
        return $value;
    }

    // Get parameter value from the database wordpress, need to set the table, content name and column name
    function tdb_jb_get_parameters($table, $parameterName, $columnNameValue, $columnNameParam, $api = null) {
        global $wpdb;

        $Value = "";
        $request = "SELECT ".$columnNameValue
            . " FROM " . $table
            ." WHERE " . $columnNameParam . " = '" . $parameterName . "'" ;

        if($api){
            $request .= " AND nIdApi = " . $api;
        }

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $Value .= $ligneResult->$columnNameValue;
        }

        return $Value;
    }

    // Get parameter value for template(mailing) from the database wordpress, need to set the table, content name and column name
    function tdb_jb_get_template_parameters($table, $parameterName, $columnNameValue, $columnNameParam, $language = 'default') {
        global $wpdb;

        $Value = "";
        $request = "SELECT ".$columnNameValue
            . " FROM " . $table
            ." WHERE " . $columnNameParam . " = '" . $parameterName . "'"
            ." AND sLanguage = '" . $language . "';";

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $Value .= $ligneResult->$columnNameValue;
        }

        return $Value;
    }

    // get list of opt option for the search form, add a filter to just show one language with one code, for example
    // english en only and not american english etc...
    private function tdb_jb_get_opt_language_form($iso ="639-2",$filterArray = "",$firstOption ="",$selectedOption = "",$useAllFavorite = false, $favoriteLanguage = "") {
        // iso 639-1 = language used (2 caractere lower case)
        // iso 639-2 = language used (2 caractere UPER case)
        $val  = "";
        $selected  = "";
        $selectedDefault = ' selected="true" ';
        $selectedContent = $selectedDefault;
        $translation = new Translation();
        if($selectedOption <> ""){
            $selectedContent = '';
        }

        $favorite  = "";
        $favoriteArray = $this->tdb_jb_get_array_favorite_language();
        $favoriteFinalArray = array();
        foreach($favoriteArray as $key => $value){
            $favoriteReverted[$value] =  $key;
        }

        $language = $this->tdb_jb_get_current_language();

        $codes = $translation->tdb_jd_standard();
        $start = "<option value=''  id='language-0'>$firstOption</option>";
        $id = 0;

        foreach ($codes as $key => $value) {
            $id++;

            if(!empty($filterArray) && !isset($filterArray[substr($key,0,2)]) && $useAllFavorite == false){
                continue;
            }

            if($favoriteLanguage <> '' && (strpos($favoriteLanguage, strtolower(substr($key,0,2))) === false)){
                continue;
            }

            if(strlen($key)<> 2){
                if(stripos($key,"_") !== false){
                    $keyFinal = substr($key,0,2) ;
                }else{
                    $keyFinal = $translation->tdb_jb_convert_6392_to_6391($key) ;
                }

                if($keyFinal == ""){
                    continue;
                }
            }
            else
            {
                $keyFinal = strtolower(substr($key,0,2)) ;
            }

            $valueFinal = \Locale::getDisplayLanguage($keyFinal, $language);
            if(isset($favoriteReverted[$keyFinal])){
                if(!isset($favoriteFinalArray[$favoriteReverted[$keyFinal]])){
                    $favoriteFinalArray[$favoriteReverted[$keyFinal]] = "";
                }
                $favoriteFinalArray[$favoriteReverted[$keyFinal]] .=
                    "<option value='$keyFinal'  id='language-$id'>$valueFinal</option>";
            }
            else{
                if($useAllFavorite == false && !empty($favoriteReverted)){
                    if(substr($key,0,2) == $language) {
                        $selected .= "<option value='$keyFinal'  id='language-$id'>$valueFinal</option>";
                    }
                    else {
                        $val .= "<option value='$keyFinal'  id='language-$id'>$valueFinal</option>";
                    }
                }
            }
        }

        ksort($favoriteFinalArray);
        foreach($favoriteFinalArray as $key => $value){
            $favorite.= $value;
        }

        $html = $start . $favorite . $selected . $val;

        if($selectedOption <> ""){
            $html = str_replace("value='$selectedOption'","value='$selectedOption'".$selectedDefault,$html);
        }
        return $html;
    }

    // get the country language code option (used in admin and pro version)
    function tdb_jb_get_opt_3166($typePage="",$firstOption = "",$selectOption = "",$favoriteCountry = "",$favoriteCountryOther = "") {
        $val  = "";
        $selected  = "";
        $japanStart = "";
        $japan = "";
        $translation = new Translation();

        $optLabelCountry = TDB_LANG_OPTION_FAVORITECOUNTRY;
        $optLabelOthers = TDB_LANG_OPTION_OTHERSCOUNTRY;

        $optGroupOpen1 = "<optgroup label='$optLabelCountry'>";
        $optGroupOpen2 = "<optgroup label='$optLabelOthers'>";

        $optGroupList1 = "";
        $optGroupList2 = "";

        $optGroup1 = "";
        $optGroup2 = "";
        $optGroupOther2 = "";
        $optGroupArray1 = [];
        $optGroupArray2 = [];
        $optGroupArrayOther2 = [];

        $optGroupClosed = "</optgroup>";

        if($favoriteCountry == ""){
            $optGroupOpen1 = "";
            $optGroupOpen2 = "";
            $optGroupClosed = "";
        } else {
            $favoriteCountryArray = explode(PHP_EOL,$favoriteCountry);
        }

        if($favoriteCountryOther <> ""){
            $favoriteCountryOtherArray = explode(PHP_EOL,$favoriteCountryOther);
        }

        $val = "";
        $selected = "";
        $bIsSelected = false;

        $selectedContent = 'selected';
        $selectedDefault = $selectedContent;
        if($selectOption <> ""){
            $selectedDefault = '';
        }

        $language = $this->tdb_jb_get_current_language();
        $plugin =  plugin_dir_path( __DIR__ );

        $repLanguage3166 = $plugin . "languages/3166/".$language.".json";

        //translated file
        if(file_exists($repLanguage3166)) {
            $codes = json_decode(file_get_contents($repLanguage3166),true);
            $codes = $codes["Names"];
        }//standard english file
        else {
            $repLanguage3166 = $plugin . "languages/3166.php";
            $codes = include($repLanguage3166);
        }

        $start = "<option value='' $selectedDefault id='language-0'>".$firstOption."</option>";
        $id = 0;

        asort($codes);
        foreach ($codes as $key => $value) {
            $id++;
            if($key == $selectOption) {
                $selectedContent = 'selected="true"';
            } else {
                $selectedContent = '';
            }

            if(substr($key,0,2) == 'ja') {
                $japanStart .= "<option value='$key' $selectedContent id='language-$id'>$value</option>";

                if($favoriteCountry <> ""){
                    if(strpos($favoriteCountry, strtoupper(substr($key,0,2))) !== false){
                        $optGroupArray1[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                    } else {
                        if(strpos($favoriteCountryOther, strtoupper(substr($key,0,2))) !== false){
                            $optGroupArrayOther2[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                        } else {
                            $optGroupArray2[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                        }
                    }
                }
            } else {
                if(substr($key,0,2) == $language) {
                    $selected .= "<option value='$key' $selectedContent id='language-$id'>$value</option>";

                    if($favoriteCountry <> ""){
                        if(strpos($favoriteCountry, strtoupper(substr($key,0,2))) !== false){
                            $optGroupArray1[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";

                        } else {
                            if(strpos($favoriteCountryOther, strtoupper(substr($key,0,2))) !== false){
                                $optGroupArrayOther2[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                            } else {
                                $optGroupArray2[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                            }
                        }
                    }
                } else {
                    if($favoriteCountryOther <> "" && strpos($favoriteCountryOther, strtoupper(substr($key,0,2))) !== false){

                    } else {
                        $val .= "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                    }

                    if($favoriteCountry <> ""){
                        if(strpos($favoriteCountry, strtoupper(substr($key,0,2))) !== false){
                            $optGroupArray1[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                        } else {
                            if(strpos($favoriteCountryOther, strtoupper(substr($key,0,2))) !== false){
                                $optGroupArrayOther2[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                            } else {
                                $optGroupArray2[$key] = "<option value='$key' $selectedContent id='language-$id'>$value</option>";
                            }
                        }
                    }
                }
            }
        }

        if($favoriteCountry <> ""){
            //revert list
            foreach ($favoriteCountryArray as $id => $key){
                if(is_array($optGroupArray1)  && !empty($optGroupArray1)){
                    foreach($optGroupArray1 as $key2 => $value){
                        if($key == $key2) {
                            $optGroup1 .= $value;
                        }
                    }
                }
            }

            if(is_array($optGroupArray2)  && !empty($optGroupArray2)){
                foreach($optGroupArray2 as $key2 => $value){
                    $optGroup2 .= $value;
                }
            }
            if(is_array($optGroupArrayOther2)  && !empty($optGroupArrayOther2)){
                foreach($optGroupArrayOther2 as $key2 => $value){
                    $optGroupOther2 .= $value;
                }
            }

            return    $start .  $optGroup1 . $optGroupOpen2 . $optGroupOther2 . $optGroup2 . $optGroupClosed;
        }

        if(is_array($optGroupArrayOther2)  && !empty($optGroupArrayOther2)){
            foreach($optGroupArrayOther2 as $key2 => $value){
                $optGroupOther2 .= $value;
            }
        }

        return $start . $japanStart . $selected . $optGroupOther2 . $val;
    }

    // Return html code with all the data transmitted
    // $colsize = empty or between 1 and 12, set up the size of the column
    // $contentType = html flag, set up an html flag like select, input type ...
    // $nameContent = Name of the flag
    // $valueContent = value of the flag (option for select, checked for checkbox)
    // $label = Label text, add label field if not empty
    // $classContent = Class css of the html flag
    // $multiple = in case of select box with multiple choice, ad multiple at the end
    // $TypeLabel = by default, label have <label> flag, if that value is not empty, label flag change to the new flag sended
    // $idName = by default, id = name, if special id have to be set, this value have to be set up
    // $classLabel = if need special class for the label
    // $requiredContent = if the content have to be required, this value have to be sent
    // $requiredSpan = the class of the required span have to be sent
    // $function = if some jquery function
    // $placeHolder = default value
    // $divExtraClass = set up the class of the main div
    // $maxLength = Size of the content
    // $extraContent = extra content for checkbox
    function tdb_jb_get_col($colsize, $contentType, $nameContent, $valueContent = "",$label="",$classContent = "",
                            $multiple = "", $TypeLabel = "",$idName = "",$classLabel = "", $requiredContent = "",
                            $requiredSpan = "",$function = "", $placeHolder = "",$divExtraClass = "",$maxLength = "", $extraContent ="",$rowAttribute="", $divExtraId = "") {

        $html = "";
        $holder = "";
        $divClass = "";
        $divId = "";
        $length = "";
        $row = "";
        $noColDiv = false;

        if($idName <> "") {
            $id = $idName;
        }
        else{
            $pos = strpos($nameContent,"[");
            if ($pos !== false) {
                $id = substr($nameContent,0,($pos--));
            }
            else{
                $id = $nameContent;
            }
        }

        if($rowAttribute <> ""){
            $row = 'rows="'.$rowAttribute.'"';
        }

        if(isset($colsize) and $colsize != ''){
            $divClass = "class = 'tdb-jd-col-$colsize $divExtraClass'";
        } else {
            if($divExtraClass <>""){
                $divClass = "class = '$divExtraClass'";
            } else {
                $noColDiv = true;
            }
        }

        if($divExtraId <> ""){
            $divId = "id = '$divExtraId'";
        }

        if($maxLength <> "" and is_int($maxLength)){
            if($contentType == "number"){
                $length = "max = $maxLength";
            }
            else{
                $length = "maxlength = $maxLength";
            }
        }

        if($noColDiv == false){
            $html.= "<div $divId $divClass >";
        }

        if(isset($label) && $label <> "" && $nameContent <> "" && $contentType <> "checkbox" ) {
            if ($TypeLabel == "") {
                $html.= '<label for="'.$nameContent.'" class = "tdb-jd-label" >'.$label.$requiredSpan.'</label>';
            }
            else{
                $html.= '<'.$TypeLabel.' class = "'.$classLabel.'" >'.$label.'</'.$TypeLabel.'>';
            }
        }
        if(!empty($placeHolder)) {
            $holder = 'placeholder="'.$placeHolder.'"';
        }

        $classFinal = "";
        if( $classContent <> "" ){
            $classFinal = "class='$classContent '";
        }

        switch ($contentType) {
            case "date":
            case "number":
            case "email":
            case "tel":
            case "text":
                if(isset($valueContent)) {
                    $html.= "<input type ='$contentType' id='$id' name ='$nameContent' $classFinal value='$valueContent' $requiredContent $holder $length />";
                }
                else {
                    $html.= "<input type ='$contentType' id='$id' name ='$nameContent' $classFinal  $requiredContent $holder $length />";
                }
                break;
            case "textarea":
                $html.= "<textarea $row id='$id' name ='$nameContent' $classFinal $requiredContent $holder >$valueContent</textarea>";
                break;
            case "select":
                $html.= "<select id='$id' name ='$nameContent' $classFinal $requiredContent  $multiple>";
                $html.= $valueContent;
                $html.= '</select>';
                break;
            case "div":
                $html.= "<label class = 'tdb-jd-label'>$valueContent</label>";
                break;
            case "checkbox":
                $checked = "";
                $functionJs = "";
                if ($valueContent <> "") {
                    $checked = "checked";
                }
                if ($function <> "") {
                    $functionJs = ' onClick="'.$function.';" ';
                }
                $html.= "<input type ='$contentType' name='$nameContent' $checked value='$nameContent'  $classFinal id='$nameContent'  $requiredContent $functionJs $extraContent />";
                if(isset($label) && $nameContent <>"" ) {
                    if ($TypeLabel == "") {
                        $html.= '<label for="'.$nameContent.'" class = "tdb-jd-emptypes">'.$label.'</label>';
                    }
                    else{
                        $html.= '<'.$TypeLabel.' class = "'.$classLabel.'" >'.$label.'</'.$TypeLabel.'>';
                    }
                }
                break;
            case "p":
            case "h1":
            case "h2":
            case "h3":
            case "h4":
            case "h5":
            case "h6":
                $html.= '<'.$contentType.' name="'.$nameContent.'" id="'.$id.'" class="'.$classContent.'" >'.$valueContent.'</'.$contentType.'>';
                break;
            case "color":
                $html.= '<input class="jscolor" value = "'.$valueContent.'" name="'.$nameContent.'" id="'.$id.'">';
                break;
            case "upload":
                $html.= "<input type='file' id='$id' name='$nameContent'></input>";
                break;
            default:
        }
        if($noColDiv == false){
            $html.= '</div>';
        }

        return $html;
    }

    // get option for select box from parameter who are in the database
    function tdb_jb_get_opt_sql($filter,$table,$columName1,$columName2,$keyToBeSelected ="",$defaultValue = "", $stringGetValue ="",$firstValue = "",$filterValue = "") {
        $val = "";
        $selected = "";
        $first = "";
        $bIsSelected = false;

        $sql = new SQL();

        $selectedContent = 'selected="true"';
        $selectedDefault = $selectedContent;

        if($stringGetValue <>"") {
            $selectedDefault = '';
        }

        $start =  "<option value = '' selected='true' $selectedDefault >$defaultValue</option>";

        foreach ($this->tdb_jb_get_table_value($table,$filter,$columName1,$columName2) as $post => $translate) {
            $bIsSelected = false;

            if($this->tdb_jb_clean_opt_value($post) == $firstValue) {
                $selectedFilter = "";
                if($stringGetValue == $this->tdb_jb_clean_opt_value($post))  {
                    $selectedFilter = $selectedContent;
                }

                $first = "<option $selectedFilter value='".$this->tdb_jb_clean_opt_value($post)."'>$translate</option>";
            } else {
                if($filterValue == ""){
                    if($stringGetValue <> "") {
                        if ($stringGetValue == $this->tdb_jb_clean_opt_value($post)) {
                            if($this->tdb_jb_clean_opt_value($post) == $keyToBeSelected) {
                                $selected = "<option  value='".$this->tdb_jb_clean_opt_value($post)."' $selectedContent>$translate</option>";
                            }
                            else {
                                $val .= "<option value='".$this->tdb_jb_clean_opt_value($post)."' $selectedContent>$translate</option>";
                            }
                            $bIsSelected = true;
                        }
                    }

                    if($bIsSelected == false){
                        if($this->tdb_jb_clean_opt_value($post) == $keyToBeSelected) {
                            $selected = "<option $selectedContent  value='".$this->tdb_jb_clean_opt_value($post)."'>$translate</option>";
                        }
                        else {
                            $val .= "<option value='".$this->tdb_jb_clean_opt_value($post)."'>$translate</option>";
                        }
                    }
                }
            }
        }
        return $start . $first . $selected . $val;
    }

    // get current language send back the code use by the customer browser for the translation
    function tdb_jb_get_current_language() {
        return substr(get_locale(),0,2); // ==> use wordpress language set ==> server setting
    }

    // return a list of option for select with the array data we send
    function tdb_jb_get_opt_select($array,$defaultValue = "",$arrayGetValue =[],$getValueNumber = "",$reverseList = "") {
        $bIsSelected = false;
        $bInserted = false;
        $valueTmp = "";
        $selectedContent = 'selected="true"';
        $selectedDefault = $selectedContent;
        $val =[];

        $acceptedLanguage = $this->tdb_jb_get_current_language();
        $count = 1;
        if (is_array($arrayGetValue) || is_object($arrayGetValue)) {
            foreach ($arrayGetValue as $key => $value) {
                if (empty($value)) {
                    unset($arrayGetValue[$key]);
                }
            }
        }

        //to get the correct position, it have to be +2 because 1st is blank
        if($getValueNumber <> "" && $getValueNumber > 0){
            $getValueNumber = (int)$getValueNumber + 1;
        }

        if((is_array($arrayGetValue) && count($arrayGetValue)>0) || $getValueNumber <> "" ) {
            $selectedDefault = '';
        }
        if(is_string($arrayGetValue) && $arrayGetValue<>""  ) {
            $selectedDefault = '';
            $valueTmp = $arrayGetValue;
        }

        $html = "<option value = '' >$defaultValue</option>";

        if(is_array($array) && !empty($array)){
            foreach ($array as $key => $content) {
                $bIsSelected = false ;
                $bInserted = false ;
                foreach ($content as  $language => $value) {
                    $langageSubstr = substr($language,0,2);
                    if($langageSubstr == $acceptedLanguage) {
                        if(is_array($arrayGetValue) && count($arrayGetValue)>0 ) {
                            foreach ($arrayGetValue as $keyGet => $valueGet) {
                                if ($valueGet == $key ) {
                                    $val[$key] = "<option value='$key' $selectedContent>$value</option>";
                                    $bIsSelected = true;
                                    $bInserted = true;
                                    $count ++;
                                }
                            }
                        }
                        if ($valueTmp == $key && $valueTmp <> "") {
                            $val[$key] = "<option value='$key' $selectedContent>$value</option>";
                            $bIsSelected = true;
                            $bInserted = true;
                            $count ++;
                        }
                        if ($getValueNumber<> "" && $count == (int)$getValueNumber) {
                            $val[$key] = "<option value='$key' $selectedContent>$value</option>";
                            $bIsSelected = true;
                            $bInserted = true;
                            $count ++;
                        }
                        if($bIsSelected == false){
                            $val[$key] =  "<option value='$key' >$value</option>";
                            $bInserted = true;
                            $count ++;
                        }

                    }
                }

                if($bInserted == false){
                    if(isset($content["en"])){
                        $val[$key] =  "<option value='$key' >".$content["en"]."</option>";
                    }
                    elseif (isset($content["ja"])){
                        $val[$key] =  "<option value='$key' >".$content["ja"]."</option>";
                    } else {
                        if(isset($content["zh"]))
                            $val[$key]=  "<option value='$key' >".$content["zh"]."</option>";
                    }
                }
            }
        }

        if(is_array($val) && !empty($val)){
            if($reverseList <> ""){
                $val = array_reverse($val);
            }

            foreach($val as $key => $value){
                $html .= $value;
            }
        }

        return $html;
    }

    // return a list of option for select with the array data we send
    function tdb_jb_get_opt($array, $defaultValue = "") {
        $valueTmp = "";
        $selectedContent = 'selected="true"';
        $val =[];
        $html = "";

        if(is_array($array) && !empty($array)){
            foreach ($array as $key) {
                if($defaultValue == $key){
                    $selected = $selectedContent;
                } else {
                    $selected = '';
                }

                $html .= "<option value='$key' $selected>$key</option>";

            }
        }
        return $html;
    }

    // Get list of table parameters(parameters the user want to show to the customer)
    function tdb_jb_get_list_parameters() {
        $param = array();

        $param["description"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'descriptionParam','sValue','sName'));
        $param["location"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'locationParam','sValue','sName'));
        $param["amount"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'amountParam','sValue','sName'));
        $param["maxAmount"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'maxAmountParam','sValue','sName'));
        $param["currency"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currencyParam','sValue','sName'));
        $param["maxCurrency"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'maxCurrencyParam','sValue','sName'));
        $param["basis"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'basisParam','sValue','sName'));
        $param["maxBasis"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'maxBasisParam','sValue','sName'));
        $param["negotiable"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'negotiableParam','sValue','sName'));
        $param["wage_details"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'wage_detailsParam','sValue','sName'));
        $param["type"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'typeParam','sValue','sName'));
        $param["type_detail"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'type_detailParam','sValue','sName'));
        $param["requirements"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'requirementsParam','sValue','sName'));
        $param["required_visas"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'required_visasParam','sValue','sName'));
        $param["reason_for_hiring_details"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'reason_for_hiring_detailsParam','sValue','sName'));
        $param["language"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languageParam','sValue','sName'));
        $param["education_level"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'education_levelParam','sValue','sName'));
        $param["holidays"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'holidaysParam','sValue','sName'));
        $param["conditions"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'conditionsParam','sValue','sName'));
        $param["working_hours"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'working_hoursParam','sValue','sName'));
        $param["category"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'categoryParam','sValue','sName'));
        $param["industry"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'industryParam','sValue','sName'));
        $param["benefits"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'benefitsParam','sValue','sName'));
        $param["selling_points"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'selling_pointsParam','sValue','sName'));

        return $param;
    }

    // Get list of table parameters for the video
    function tdb_jb_get_list_video_parameters() {
        $param = array();

        $param["video"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'videoVideo','sValue','sName'));
        $param["summary"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'summaryVideo','sValue','sName'));
        $param["defaultImageCheck"] = preg_replace('/\s\s+/', '', $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'defaultImageCheckVideo','sValue','sName'));

        return $param;
    }

    // Get list of table parameters(parameters the user want to show to the customer) (admin and apply)
    function tdb_jb_get_list_apply() {
        $param = array();

        $param["gender"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'genderApply','sValue','sName');
        $param["birthdate"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'birthdateApply','sValue','sName');
        $param["address"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'addressApply','sValue','sName');
        $param["neareststation"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'neareststationApply','sValue','sName');
        $param["emails"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'emailsApply','sValue','sName');
        $param["emailsType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'emailsTypeApply','sValue','sName');
        $param["phoneNumbers"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'phoneNumbersApply','sValue','sName');
        $param["phoneType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'phoneTypeApply','sValue','sName');
        $param["nationality"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'nationalityApply','sValue','sName');
        $param["visa"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'visaApply','sValue','sName');
        $param["languages"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languagesApply','sValue','sName');
        $param["languageCertifications"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languageCertificationsApply','sValue','sName');
        $param["certification"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'certificationApply','sValue','sName');
        $param["currentSalary"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryApply','sValue','sName');
        $param["currentSalaryBonus"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryBonusApply','sValue','sName');
        $param["currentEmploymentDepartment"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentEmploymentDepartmentApply','sValue','sName');
        $param["currentEmploymentPosition"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentEmploymentPositionApply','sValue','sName');
        $param["currentEmploymentCompany"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentEmploymentCompanyApply','sValue','sName');
        $param["desiredWage"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredWageApply','sValue','sName');
        $param["desiredEmploymentTypes"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredEmploymentTypesApply','sValue','sName');
        $param["desiredIndustry"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredIndustryApply','sValue','sName');
        $param["desiredJobCategory"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredJobCategoryApply','sValue','sName');
        $param["desiredLocation"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredLocationApply','sValue','sName');
        $param["referrer"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'referrerApply','sValue','sName');
        $param["noticePeriod"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'noticePeriodApply','sValue','sName');
        $param["facebook"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'facebookApply','sValue','sName');
        $param["linkedin"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'linkedinApply','sValue','sName');
        $param["recaptcha"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'recaptchaApply','sValue','sName');
        $param["url"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'urlApply','sValue','sName');
        $param["attachment"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'attachmentApply','sValue','sName');

        return $param;
    }

    // Get list of table parameters(parameters the user want to show in the filter search)
    function tdb_jb_get_list_sort_by() {
        $param = array();

        $param["title"]  = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'titleSortBy','sValue','sName');
        $param["date"]   = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'dateSortBy','sValue','sName');
        $param["salary"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'salarySortBy','sValue','sName');

        return $param;
    }

    // Get list of table parameters(parameters the user want to be mandatory to the customer)
    function tdb_jb_get_list_required_fields() {
        $param = array();

        $param["email"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'emailRequired','sValue','sName');
        $param["emailType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'emailTypeRequired','sValue','sName');
        $param["gender"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'genderRequired','sValue','sName');
        $param["birthdate"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'birthdateRequired','sValue','sName');
        $param["postal"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'postalRequired','sValue','sName');
        $param["country"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'countryRequired','sValue','sName');
        $param["region"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'regionRequired','sValue','sName');
        $param["city"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'cityRequired','sValue','sName');
        $param["street"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'streetRequired','sValue','sName');
        $param["nearestStation"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'nearestStationRequired','sValue','sName');
        $param["phone"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'phoneRequired','sValue','sName');
        $param["phoneType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'phoneTypeRequired','sValue','sName');
        $param["nationality"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'nationalityRequired','sValue','sName');
        $param["visaType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'visaTypeRequired','sValue','sName');
        $param["visaCountry"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'visaCountryRequired','sValue','sName');
        $param["currentSalary"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryRequired','sValue','sName');
        $param["currentSalaryCurrency"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryCurrencyRequired','sValue','sName');
        $param["currentSalaryBasis"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryBasisRequired','sValue','sName');
        $param["currentSalaryBonus"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryBonusRequired','sValue','sName');
        $param["currentSalaryBonusCurrency"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryBonusCurrencyRequired','sValue','sName');
        $param["currentEmploymentDepartment"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentEmploymentDepartmentRequired','sValue','sName');
        $param["currentEmploymentPosition"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentEmploymentPositionRequired','sValue','sName');
        $param["currentEmploymentCompany"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentEmploymentCompanyRequired','sValue','sName');
        $param["language"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languageRequired','sValue','sName');
        $param["languageAbility"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languageAbilityRequired','sValue','sName');
        $param["languageCertification"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languageCertificationRequired','sValue','sName');
        $param["languageScore"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languageScoreRequired','sValue','sName');
        $param["certification"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'certificationRequired','sValue','sName');
        $param["desiredWage"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredWageRequired','sValue','sName');
        $param["currency"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currencyRequired','sValue','sName');
        $param["basis"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'basisRequired','sValue','sName');
        $param["employementType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'employementTypeRequired','sValue','sName');
        $param["desiredIndustry"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredIndustryRequired','sValue','sName');
        $param["desiredJobCategory"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredJobCategoryRequired','sValue','sName');
        $param["desiredLocation"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredLocationRequired','sValue','sName');
        $param["referrer"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'referrerRequired','sValue','sName');
        $param["noticedPeriod"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'noticedPeriodRequired','sValue','sName');
        $param["facebook"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'facebookRequired','sValue','sName');
        $param["linkedin"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'linkedinRequired','sValue','sName');
        $param["url"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'urlRequired','sValue','sName');
        $param["attachment"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'attachmentRequired','sValue','sName');

        return $param;
    }

    // Get list of table parameters(parameters the user want to be mandatory to the customer)
    function tdb_jb_get_list_col_resized_register_fields() {
        $param["familyName"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'familyNameRegisterColSize','sValue','sName');
        $param["givenName"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'givenNameRegisterColSize','sValue','sName');
        $param["birthYear"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'birthYearRegisterColSize','sValue','sName');
        $param["birthMonth"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'birthMonthRegisterColSize','sValue','sName');
        $param["birthDay"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'birthDayRegisterColSize','sValue','sName');
        $param["gender"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'genderRegisterColSize','sValue','sName');
        $param["addressPostal"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'addressPostalRegisterColSize','sValue','sName');
        $param["addressCountry"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'addressCountryRegisterColSize','sValue','sName');
        $param["addressExtended"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'addressExtendedRegisterColSize','sValue','sName');
        $param["addressRegion"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'addressRegionRegisterColSize','sValue','sName');
        $param["addressCity"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'addressCityRegisterColSize','sValue','sName');
        $param["addressStreet"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'addressStreetRegisterColSize','sValue','sName');
        $param["nearestStation"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'nearestStationRegisterColSize','sValue','sName');
        $param["email"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'emailRegisterColSize','sValue','sName');
        $param["emailType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'emailTypeRegisterColSize','sValue','sName');
        $param["phone"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'phoneRegisterColSize','sValue','sName');
        $param["phoneType"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'phoneTypeRegisterColSize','sValue','sName');
        $param["nationality"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'nationalityRegisterColSize','sValue','sName');
        $param["visa"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'visaRegisterColSize','sValue','sName');
        $param["visaCountry"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'visaCountryRegisterColSize','sValue','sName');
        $param["languageCertification"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'languageCertificationRegisterColSize','sValue','sName');
        $param["certifAbility"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'certifAbilityRegisterColSize','sValue','sName');
        $param["certification"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'certificationRegisterColSize','sValue','sName');
        $param["levelCertification"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'levelCertificationRegisterColSize','sValue','sName');
        $param["certificationText"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'certificationTextRegisterColSize','sValue','sName');
        $param["currentCompany"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentCompanyRegisterColSize','sValue','sName');
        $param["currentPosition"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentPositionRegisterColSize','sValue','sName');
        $param["currentDepartment"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentDepartmentRegisterColSize','sValue','sName');
        $param["currentSalaryAmount"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryAmountRegisterColSize','sValue','sName');
        $param["currentSalaryCurrency"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryCurrencyRegisterColSize','sValue','sName');
        $param["currentSalaryBasis"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'currentSalaryBasisRegisterColSize','sValue','sName');
        $param["bonusSalaryAmount"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'bonusSalaryAmountRegisterColSize','sValue','sName');
        $param["bonusSalaryCurrency"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'bonusSalaryCurrencyRegisterColSize','sValue','sName');
        $param["desiredSalaryAmount"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredSalaryAmountRegisterColSize','sValue','sName');
        $param["desiredSalaryCurrency"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredSalaryCurrencyRegisterColSize','sValue','sName');
        $param["desiredSalaryBasis"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'desiredSalaryBasisRegisterColSize','sValue','sName');
        $param["findUs"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'findUsRegisterColSize','sValue','sName');
        $param["noticedPeriod"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'noticedPeriodRegisterColSize','sValue','sName');
        $param["facebook"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'facebookRegisterColSize','sValue','sName');
        $param["linkedin"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'linkedinRegisterColSize','sValue','sName');
        $param["urlRegister"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'urlRegisterColSize','sValue','sName');
        $param["resumeRegister"] = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'resumeRegisterColSize','sValue','sName');

        $param = $this->tdb_init_column_size($param);
        return $param;
    }

    // Get list of table parameters(parameters the user want to be mandatory to the customer)
    function tdb_init_column_size($param) {
        if(empty($param["familyName"])) $param["familyName"] = 4;
        if(empty($param["givenName"])) $param["givenName"] = 4;
        if(empty($param["birthYear"])) $param["birthYear"] = 2;
        if(empty($param["birthMonth"])) $param["birthMonth"] = 2;
        if(empty($param["birthDay"])) $param["birthDay"] = 2;
        if(empty($param["gender"])) $param["gender"] = 4;
        if(empty($param["addressPostal"])) $param["addressPostal"] = 2;
        if(empty($param["addressCountry"])) $param["addressCountry"] = 3;
        if(empty($param["addressExtended"])) $param["addressExtended"] = 4;
        if(empty($param["addressRegion"])) $param["addressRegion"] = 4;
        if(empty($param["addressCity"])) $param["addressCity"] = 4;
        if(empty($param["addressStreet"])) $param["addressStreet"] = 7;
        if(empty($param["nearestStation"])) $param["nearestStation"] = 4;
        if(empty($param["email"])) $param["email"] = 4;
        if(empty($param["emailType"])) $param["emailType"] = 4;
        if(empty($param["phone"])) $param["phone"] = 4;
        if(empty($param["phoneType"])) $param["phoneType"] = 4;
        if(empty($param["nationality"])) $param["nationality"] = 6;
        if(empty($param["visa"])) $param["visa"] = 3;
        if(empty($param["visaCountry"])) $param["visaCountry"] = 4;
        if(empty($param["certifAbility"])) $param["certifAbility"] = 2;
        if(empty($param["languageCertification"])) $param["languageCertification"] = 4;
        if(empty($param["levelCertification"])) $param["levelCertification"] = 2;
        if(empty($param["certification"])) $param["certification"] = 7;
        if(empty($param["currentCompany"])) $param["currentCompany"] = 4;
        if(empty($param["currentPosition"])) $param["currentPosition"] = 4;
        if(empty($param["currentDepartment"])) $param["currentDepartment"] = 4;
        if(empty($param["currentSalaryAmount"])) $param["currentSalaryAmount"] = 4;
        if(empty($param["currentSalaryCurrency"])) $param["currentSalaryCurrency"] = 2;
        if(empty($param["currentSalaryBasis"])) $param["currentSalaryBasis"] = 2;
        if(empty($param["bonusSalaryAmount"])) $param["bonusSalaryAmount"] = 4;
        if(empty($param["bonusSalaryCurrency"])) $param["bonusSalaryCurrency"] = 2;
        //if(empty($param["bonusSalaryBasis"])) $param["bonusSalaryBasis"] = 2;
        if(empty($param["desiredSalaryAmount"])) $param["desiredSalaryAmount"] = 4;
        if(empty($param["desiredSalaryCurrency"])) $param["desiredSalaryCurrency"] = 2;
        if(empty($param["desiredSalaryBasis"])) $param["desiredSalaryBasis"] = 2;
        if(empty($param["desiredEmployment"])) $param["desiredEmployment"] = 12;
        if(empty($param["desiredLocation"])) $param["desiredLocation"] = 12;
        if(empty($param["desiredCategory"])) $param["desiredCategory"] = 12;
        if(empty($param["desiredIndustry"])) $param["desiredIndustry"] = 12;
        if(empty($param["findUs"])) $param["findUs"] = 6;
        if(empty($param["noticedPeriod"])) $param["noticedPeriod"] = 4;
        if(empty($param["facebook"])) $param["facebook"] = 6;
        if(empty($param["linkedin"])) $param["linkedin"] = 6;
        if(empty($param["urlRegister"])) $param["urlRegister"] = 6;
        if(empty($param["resumeRegister"])) $param["resumeRegister"] = 4;
        if(empty($param["privacyPolicyLabel"])) $param["privacyPolicyLabel"] = 11;
        if(empty($param["privacyPolicyCheck"])) $param["privacyPolicyCheck"] = 1;

        return $param;
    }

    // Get list of favorite language from table parameters(these language will be find in first on the search form)
    function tdb_jb_get_array_favorite_language(){
        $arrayLanguageFavorite= array();

        $string = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'favoriteLanguageContent','sValue','sName');

        if(!empty($string)){
            $rowArray = explode(";", $string);
            foreach($rowArray as $row => $value){
                $content = explode(":",$value);
                if(isset($content[0]) && isset($content[1])){
                    $key = $content[0];
                    $lang = $content[1];
                    $arrayLanguageFavorite[$key] = $lang;
                }
            }
        }
        return $arrayLanguageFavorite;
    }

    // Get list of favorite language from table parameters(these language will be find in first on the search form)
    function tdb_jb_get_array_favorite_language_search(){
        $arrayLanguageFavorite= array();

        $string = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'favoriteLanguageSearchContent','sValue','sName');

        if(!empty($string)){
            $rowArray = explode(";", $string);
            foreach($rowArray as $row => $value){
                $content = explode(":",$value);
                if(isset($content[0]) && isset($content[1])){
                    $key = $content[0];
                    $lang = $content[1];
                    $arrayLanguageFavorite[$key] = $lang;
                }
            }
        }
        return $arrayLanguageFavorite;
    }

    // Get list of favorite language from table parameters(these language will be find in first on the search form)
    function tdb_jb_get_array_favorite_Nationality($column){
        $arrayNationalityFavorite= array();

        $string = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,$column,'sValue','sName');

        if(!empty($string)){
            $rowArray = explode(";", $string);
            foreach($rowArray as $row => $value){
                $content = explode(":",$value);
                if(isset($content[0]) && isset($content[1])){
                    $key = $content[0];
                    $lang = $content[1];
                    $arrayNationalityFavorite[$key] = $lang;
                }
            }
        }
        return $arrayNationalityFavorite;
    }

    // for save all the url data, home page of the plugin, add link etc, getbetween allow to take content between some kind of value
    // For example between https and ?id (mandatory if wordpress work with pagelink ?p=123
    function tdb_jb_get_between($string, $start = "", $end = ""){
        if (strpos($string, $start) >= 0) { // required if $start not exist in $string
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return substr($firstSubStr, 0, $endCharCount);
        } else {
            return '';
        }
    }

    // Get every data about the current page( on the init of the plugin) to know every information about the url
    // who has to be used
    function tdb_jb_get_page_link() {
        $urlArray = array();
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];

        $urlWordpress = "";

        $url = $protocol.$host.$uri;
        $urlArray["url"] = $url;
        $urlArray["method"] = "?";
        $urlArray["get"] = "";
        $wpUri = explode("/",$uri);

        if(isset($wpUri[1]) && strpos($host,'www')==false){
            $urlArray["indeed"] = $protocol.$host."/".$wpUri[1]."/indeed_export.xml";
        } else {
            $urlArray["indeed"] = $protocol.$host."/indeed_export.xml";
        }

        $tmpGet = explode('?', $urlArray["url"]);
        if(isset($tmpGet[1]) && $tmpGet <> false && (strpos($urlArray["url"],'?p=') === false && strpos($urlArray["url"],'?page_id=') === false)){
            $get = "?" . $tmpGet[1];
            $urlArray["get"] = $get;
        }

        // url when wordpress permalink looks like ?p=123
        if (strpos($url,'?p=') !== false || strpos($url,'?page_id=') !== false){
            $tmp = explode("?",$uri);
            $urlWordpress = $tmp[0];
            if(isset($tmp[1]) && ( strpos($tmp[1],'p=')!== false || strpos($tmp[1],'page_id=')!== false)){

                $tmp = explode("&",$tmp[1]);
                $urlWordpress .= '?'.$tmp[0];
            }

            $urlArray["home"] = $protocol.$host.$urlWordpress;
            $urlArray["method"] = "&";
        } else {
            if (strpos($url,'?')==true){
                $tmp = explode("?",$uri);
                $urlArray["home"] = $protocol.$host.$tmp[0];
            } else  {
                $urlArray["home"] = $protocol.$host.$uri;
            }
        }

        if (strpos($url,'?') !== false && $urlWordpress == "" ) {
            // Case list job
            if (strpos($url,'?tdb-id-job') == true || strpos($url,'&tdb-id-job') == true){
                if (strpos($url,'?tdb-id-job') == true) {
                    $urlArray["cleanUrl"] = $protocol.$this->tdb_jb_get_between($url,$protocol,"&");
                }
                else {
                    $urlArray["cleanUrl"] = $protocol.$this->tdb_jb_get_between($url,$protocol,"?tdb-id-job");
                }
                $urlArray["method"] = "&";
            }
            else {
                $tmp = explode("?",$uri);
                $urlArray["cleanUrl"] = $tmp[0];
            }
        }
        else {
            $urlArray["cleanUrl"] = $url;
        }

        return $urlArray;
    }

    // Get the data from the api to get list or detail of job
    function tdb_jb_get_content($link, $api = null) {
        $userApi = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,'apiKey','sValue','sName', $api);
        try{
            $ch = curl_init($link);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "content-type: application/x-www-form-urlencoded",
                "x-auth-token: $userApi"));

            $requests_response = curl_exec($ch);
        }
        catch (\Exception $e){
            $err = curl_error($ch);
        }finally{
            curl_close($ch);
        }

        if (isset($err) && $err) {
            $this->tdb_jb_send_email("cURL Error #:" . $err);
        } else {
            return $requests_response;
        }
    }

    // Show the template
    function tdb_jb_show_template($templateToShow,$arrayTemplate = []){
        $template = new Smarty();
        $template->clearAllAssign();
        if(count($arrayTemplate)> 0){
            $template->assign( $arrayTemplate);
        }
        $template->display($templateToShow);
    }

    // Send email to the admin of the website if some curl error
    function tdb_jb_send_email($message){
        $admin_email = get_option('admin_email');
        $subject = "Jobsearch plugin error";
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: Webmaster <DONOTREPLY@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";

        if($admin_email <> "" && $message <> ""){
            wp_mail($admin_email,$subject,$message,$headers);
        }
    }

    // Check if the hexadecimal color set up in the admin panel is correct
    function tdb_jb_check_valid_colorhex($colorCode) {
        // If user accidentally passed along the # sign, strip it off
        $colorCode = ltrim($colorCode, '#');
        if (
            ctype_xdigit($colorCode) &&
            (strlen($colorCode) == 6 || strlen($colorCode) == 3))
            return true;

        else return false;
    }

    // Set up a session in start of the page if not exist
    function tdb_jb_set_session(){
        if(session_id() == '' || !isset($_SESSION)) {
            // session isn't started
            session_start();
        }
    }

    //Sanitize get or post field
    function tdb_jb_sanitize($value, $key, $field = "", $id = ""){
        switch($key){
            case 'email':
                return esc_html(sanitize_email($value));
                break;
            case 'file':
                return esc_html(sanitize_file_name($value));
                break;
            case 'key':
                return esc_html(sanitize_key($value));
                break;
            case 'post':
                if(isset($field) <> "" && isset($id) <> ""){
                    return esc_html(sanitize_post_field($value));
                } else {
                    return $value;
                }
                break;
            case 'text':
                return esc_html(sanitize_textarea_field($value));
                break;
            case 'url':
                return esc_url($value);
                break;
            case 'array':
                return $this->cleanArray($value);
                break;
            default:
                return $value;
        }
    }

    //Get the url of the api and add the http protocol
    function tdb_jb_get_api_link($typeLink, $api = 1){
        $prefixUrl = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'Link', 'sValue', 'sName', $api);

        $url = $prefixUrl.$this->tdb_jb_get_parameters(TDB_TABLE_PARAM, $typeLink, 'sValue', 'sName');
        if($url <> ''){
            $url = preg_replace('#^https?://#', '', rtrim($url,'/'));
            return TDB_HTTP_PROTOCOL . '://' .  $url;
        }
        return $url;
    }

    // Return the id sql value  of the current language used for the plugin
    function tdb_jb_get_id_language($Language) {
        global $wpdb;

        $value = "";
        $columnName1 = "nId";
        $columnName2 = "sLanguageName";

        $request = "SELECT ".$columnName1 . " FROM " . TDB_TABLE_LANG_USED ." WHERE " . $columnName2 . " = '". $Language . "';" ;

        //execute request
        $exec = $wpdb->get_results($request);
        $i = 0;

        foreach ($exec as $ligneResult) {
            $value = $ligneResult->$columnName1;
        }
        return $value;
    }

    // Return sql value for the current language used( For example currency(code and translation))
    private function tdb_jb_get_table_value ($table, $Language, $columnName1, $columnName2 = '', $columnName3 = '',$columnName4 = '') {

        global $wpdb;

        // Get Language used
        $idLangage = $this->tdb_jb_get_id_language($Language);

        if ($idLangage <= 0 || $idLangage == "") {
            $idLangage = 1 ;
        }
        $result = array();

        $request = "SELECT ".$columnName1 ;

        if ($columnName2 <> '') {
            $request .= ", ".$columnName2 ;
        }

        if ($columnName3 <> '') {
            $request .= ", ".$columnName3 ;
        }

        if ($columnName4 <> '') {
            $request .= ", ".$columnName4 ;
        }

        $request .= " FROM " . $table;
        $request .= " WHERE  nIdLanguage = " . $idLangage;

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $result[ $ligneResult->$columnName1] = $ligneResult->$columnName2;
        }
        return $result;
    }

    //Get the url of the api and add the http protocol
    function tdb_jb_validate_data($data = '',$type = '',$cat='',$minLength = '', $maxLength =''){
        $dataValidate = true;
        global $gCategories;
        global $gVisaType;
        global $gLanguages;
        global $gSource;
        global $gTypes;
        global $gIndustries;
        global $gLocation;
        global $gTags;

        $language = $this->tdb_jb_get_current_language();
        $basisArray = $this->tdb_jb_get_table_value(TDB_TABLE_BASIS,$language,"sName","sTranslate");
        $currencyArray = $this->tdb_jb_get_table_value(TDB_TABLE_CURRENCY,$language,"sName","sTranslate");;

        //test empty
        if(!isset($data) || empty($data)){
            $dataValidate = false;
        }

        // max size
        if($maxLength <> ''){
            if (strlen($data) > $maxLength){
                $dataValidate = false;
            }
        }

        // min size
        if($minLength <> ''){
            if (strlen($data) < $minLength){
                $dataValidate = false;
            }
        }

        switch($type){
            case 'name':
                //check english name
                if (strlen($data) == strlen(utf8_decode($data))) {
                    if (!preg_match("/^[a-zA-Z ]*$/",$data)) {
                        $dataValidate = false;
                    }
                }
                break;
            case 'int':
                if(!is_numeric($data)){
                    $dataValidate = false;
                }
                break;
            case 'amount':
                if(!preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data)){
                    $dataValidate = false;
                }
                break;
            case 'email':
                if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
                    $dataValidate = false;
                }
                break;
            case 'url':
                if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$data)) {
                    $dataValidate = false;
                }
                break;
            case 'array':
                if(is_array($data)){
                    if (count($data) == 0){
                        $dataValidate = false;
                    }
                }
                break;
            case 'file':
                if(validate_file($data)<= 0){
                    $dataValidate = false;
                }
                break;
            default:
        }

        if($cat <> ''){
            switch($cat){
                case 'category' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gCategories)){
                        $dataValidate = false;
                    }
                    break;
                case 'visa' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gVisaType)){
                        $dataValidate = false;
                    }
                    break;
                case 'language' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gLanguages)){
                        $dataValidate = false;
                    }
                    break;
                case 'source' :
                    if(!$this->tdb_jb_in_arrayi($data,$gSource)){
                        $dataValidate = false;
                    }
                    break;
                case 'type' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gTypes)){
                        $dataValidate = false;
                    }
                    break;
                case 'industry' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gIndustries)){
                        $dataValidate = false;
                    }
                    break;
                case 'location' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gLocation)){
                        $dataValidate = false;
                    }
                    break;
                case 'currency' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$currencyArray)){
                        $dataValidate = false;
                    }
                    break;
                case 'basis' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$basisArray)){
                        $dataValidate = false;
                    }
                    break;
                case 'tags' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gTags)){
                        $dataValidate = false;
                    }
                    break;
                case 'tagGroup' :
                    if(!$this->tdb_jb_array_in_arrayi($data,$gTags)){
                        $dataValidate = false;
                    }
                    break;
                default:
            }
        }
        return $dataValidate;
    }

    private function tdb_jb_in_arrayi($needle, $haystack) {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }

    /* Compare the data with the value from api, check the two array to see if value are equal */
    function tdb_jb_array_in_arrayi($needle, $haystack) {
        if(is_array($needle)){
            foreach($needle as $keyNeedle => $valueNeedle){
                if(!is_array($valueNeedle)){
                    if(is_array($haystack)){
                        foreach($haystack as $keyHayStack => $valueHayStack){
                            if(!is_array($keyHayStack)){
                                if(strtolower($valueNeedle) == strtolower($keyHayStack)){
                                    return true;
                                }
                                //for tags
                                if(is_array($valueHayStack)){
                                    foreach($valueHayStack as $keyHayStack2 => $valueHayStack2){
                                        if(strtolower($valueNeedle) == strtolower($keyHayStack2)){
                                            return true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    foreach($valueNeedle as $keyNeedle2 => $valueNeedle2){
                        if(!is_array($valueNeedle2)){
                            foreach($haystack as $keyHayStack => $valueHayStack){
                                if(!is_array($keyHayStack)){
                                    if(strtolower($valueNeedle) == strtolower($keyHayStack)){
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if(is_array($haystack)){
                foreach($haystack as $keyHayStack => $valueHayStack){
                    if(!is_array($keyHayStack)){
                        if(strtolower($needle) == strtolower($keyHayStack)){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    // redirect to thanks page if it have
    function tdb_jb_redirect($url){
        if (!headers_sent()) {
            header('Location: '.$url);
            exit;
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="'.$url.'";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
            echo '</noscript>'; exit;
        }
    }

    // Return the formated favorite language for the admin panel
    function tdb_jb_set_content_favorite_language($favoriteArray){
        $finalString = "";

        foreach ($favoriteArray as $key=> $value){
            $finalString.= "\n".$value;
        }
        return substr($finalString,1);
    }

    // Return the formated favorite language for the admin panel
    function tdb_jb_set_content_favorite_Nationality($favoriteArray){
        $finalString = "";

        foreach ($favoriteArray as $key=> $value){
            $finalString.= "\n".$value;
        }
        return substr($finalString,1);
    }

    /* get the list of category included or excluded on database, value are 'displayCategories' or 'excludedCategories' */
    function tdb_jb_get_array_category($column){
        $arrayCategory = array();

        $string = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM,$column,'sValue','sName');

        if(!empty($string)){
            $rowArray = explode(";", $string);
            foreach($rowArray as $row => $value){
                $content = explode(":",$value);
                if(isset($content[0]) && isset($content[1])){
                    $key = $content[0];
                    $lang = $content[1];
                    $arrayCategory[$key] = $lang;
                }
            }
        }
        return $arrayCategory;
    }

    /* set the array of category on format cat[val] = val with the category get from database */
    function tdb_jb_set_content_category($category){
        $categoryArray = [] ;

        foreach ($category as $key=> $value){
            $categoryArray[$value] = $value;
        }
        return $categoryArray;
    }

    // Get all the language option for application ( type phone, certification and email)
    function tdb_jb_get_opt_apply($type,$selectedValue = "", $firstValue = "",$defaultValue = "") {
        $first = "";
        $content = "";
        $translation = new Translation();
        $arrayEmail["work"] = TDB_LANG_WORK;
        $arrayEmail["home"] = TDB_LANG_HOME;
        $arrayEmail["other"] = TDB_LANG_OPTION_OTHERLOWER;
        $arrayEmail["work other"] = TDB_LANG_WORKOTHER;
        $arrayEmail["home other"] = TDB_LANG_HOMEOTHER;

        $arrayPhone["work"] = TDB_LANG_WORK;
        $arrayPhone["home"] = TDB_LANG_HOME;
        $arrayPhone["other"] = TDB_LANG_OPTION_OTHERLOWER;
        $arrayPhone["mobile"] = TDB_LANG_MOBILE;
        $arrayPhone["work other"] = TDB_LANG_WORKOTHER;
        $arrayPhone["home other"] = TDB_LANG_HOMEOTHER;
        $arrayPhone["work mobile"] = TDB_LANG_WORKMOBILE;
        $arrayPhone["home mobile"] = TDB_LANG_HOMEMOBILE;

        $arrayCertif["JLPT"] = TDB_LANG_JLPT;
        $arrayCertif["TOEIC"] = TDB_LANG_TOEIC;
        $arrayCertif["TOEFL"] = TDB_LANG_TOEFL;

        $result = '<option value="" >'.$defaultValue.'</option>';
        switch  ($type) {
            case "email":
                foreach($arrayEmail as $key => $value){
                    if($firstValue == $key){
                        $first = "<option value='$key' >$value</option>";
                    }
                    else {
                        $content .= "<option value='$key' >$value</option>";
                    }
                }
                break;
            case "phone":
                foreach($arrayPhone as $key => $value){
                    if($firstValue == $key){
                        $first = "<option value='$key' >$value</option>";
                    }
                    else {
                        $content .= "<option value='$key' >$value</option>";
                    }
                }
                break;
            case "certif":
                foreach($arrayCertif as $key => $value){
                    if($firstValue == $key){
                        $first = "<option value='$key' >$value</option>";
                    }
                    else {
                        $content .= "<option value='$key' >$value</option>";
                    }
                }
                break;
            default:
                break;
        }

        $result .= $first .  $content;

        if($selectedValue <> ""){
            $result = str_replace("value='$selectedValue'","value='$selectedValue' selected='true'",$result);
            $result = str_replace('value="'.$selectedValue.'"','value="'.$selectedValue.'" selected="true"',$result);
        }
        return $result ;
    }

    function tdb_get_search_url(){
        $api = get_query_var( 'job-api' );
        $search_page = $this->tdb_jb_get_api_search($api);
        $page = get_permalink( $search_page );
        if(isset($page) && $page != ''){
            return $page ;
        }
        return '';
    }

    /* search form, generate the language field block */
    function tdb_jd_generate_language_search_block($countLanguage,$gJobLanguages,$favoriteLanguageSearchContent,$gLanguages,$reverseLanguageSkill,$belowLanguageCheck,$labelClass = "tdb-jd-col-label-search",$languageSearch = "",$max = "",$min = "", $colSize = []){

        $htmlLanguage = "";
        if($languageSearch <> "" || $max <> "" || $min){
            $belowLanguageCheck = $min;
        }

        $htmlLanguage .= "<div class='tdb-jd-row' id ='languageSkillRow$countLanguage'>";
        $htmlLanguage .= "<div class='tdb-jd-col-3 tdb-jd-search-column'>";
        $htmlLanguage .= "<div class='tdb-jd-container'>";
        $htmlLanguage .= "<div class='tdb-jd-row tdb-jd-row-label-search'>";
        $htmlLanguage .= "<div class='tdb-jd-col-12 $labelClass'>";
        $htmlLanguage .= "<label class='tdb-jd-label' for='language$countLanguage'>".TDB_LANG_LANGUAGE."</label>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "<div class='tdb-jd-row tdb-jd-row-content-search'>";
        $htmlLanguage .= $this->tdb_jb_get_col(12, "select", "language$countLanguage"."[language]",$this->tdb_jb_get_opt_language_form("",$gJobLanguages,"",$languageSearch,false,$favoriteLanguageSearchContent),"","tdb-jd-custom-select tdb-jd-input ");
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "<div class='tdb-jd-col-3'>";
        $htmlLanguage .= "<div class='tdb-jd-container'>";
        $htmlLanguage .= "<div class='tdb-jd-row tdb-jd-row-label-search'>";
        $htmlLanguage .= "<div class='tdb-jd-col-12 $labelClass'>";
        $htmlLanguage .= "<label class='tdb-jd-label' for='languageSkillMax$countLanguage"."[max]'>".TDB_LANG_LEVEL."</label>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "<div class='tdb-jd-row tdb-jd-row-content-search'>";
        $htmlLanguage .= $this->tdb_jb_get_col(12, "select", "language$countLanguage"."[max]",$this->tdb_jb_get_opt_select($gLanguages,"","",$max,$reverseLanguageSkill),"","tdb-jd-custom-select tdb-jd-input","","","languageSkillMax$countLanguage");
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "<div class='tdb-jd-col-3 tdb-jd-below '>";
        $htmlLanguage .= "<div class='tdb-jd-container'>";
        $htmlLanguage .= "<div class='tdb-jd-row tdb-jd-row-label-search'>";
        $htmlLanguage .= "<div class='tdb-jd-col-12 $labelClass'>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "<label class='tdb-jd-label' for='language$countLanguage"."[max]'></label>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "<div class='tdb-jd-row tdb-jd-row-content-search'>";
        $htmlLanguage .= $this->tdb_jb_get_col(12, "checkbox", "language$countLanguage"."[min]",$belowLanguageCheck,TDB_LANG_BELOW,"tdb-jd-emptypes","","","languageSkillMin$countLanguage","tdb-jd-input-label-apply","","","","","tdb-jd-below");
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";
        $htmlLanguage .= "</div>";

        return $htmlLanguage;
    }

    // Get parameter value from the database wordpress, need to set the table, content name and column name
    function tdb_jb_get_api_url($apiId) {
        global $wpdb;

        $request = "SELECT sValue"
            . " FROM " . TDB_TABLE_PARAM
            ." WHERE nIdApi = " . $apiId
            ." AND sName = 'Link';" ;
        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            return $ligneResult->sValue;
        }

        return false;
    }

    function tdb_jb_get_linkApi(){
        global $wpdb;

        $apiLinkArray = [];
        $request = "SELECT sValue, nIdApi FROM " . TDB_TABLE_PARAM ." WHERE sName ='Link' ORDER BY nIdApi;" ;

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $apiLinkArray[$ligneResult->nIdApi] = $ligneResult->sValue;
        }
        return $apiLinkArray;
    }

    function tdb_jb_get_jobPage(){
        global $wpdb;

        $apiKeyArray = [];
        $request = "SELECT sValue, nIdApi FROM " . TDB_TABLE_PARAM ." WHERE sName ='apiPage' ORDER BY nIdApi;" ;

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $apiKeyArray[$ligneResult->nIdApi] = $ligneResult->sValue;
        }
        return $apiKeyArray;
    }

    /* get the tdb link for the job page */
    function tdb_jb_get_job_page_api($api = 1){
        global $wpdb;

        $request = "SELECT sValue FROM " . TDB_TABLE_PARAM ." WHERE sName ='apiPage' AND nIdApi = ".$api.";" ;

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            return $ligneResult->sValue;
        }

        return false;
    }

    /* get the tdb link for the search page */
    private function tdb_jb_get_api_search($api = 1){
        global $wpdb;

        $request = "SELECT sValue FROM " . TDB_TABLE_PARAM ." WHERE sName ='apiSearch' AND nIdApi = ".$api.";" ;

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            return $ligneResult->sValue;
        }

        return false;
    }

    /* Meta for seo like linkedin */
    function tdb_jd_open_graph_generate(Job $job = null){
        $websiteLogo = $this->tdb_jb_get_wp_image_url($this->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'socialLogo', 'sValue', 'sName'));
        if(empty($websiteLogo)){
            if(file_exists(get_template_directory_uri() . '/images/logo.png')){
                $websiteLogo =  get_template_directory_uri() . '/images/logo.png';
            } elseif(esc_url( get_theme_mod('logo', ''))){
                $websiteLogo =  esc_url( get_theme_mod('logo', ''));
            }
        }

        $siteName =  get_bloginfo('name');
        $meta = '';
        if($job){
            $meta.= $this->tdb_jd_generate_meta_og('title', $job->get_title());
            $meta.= $this->tdb_jd_generate_meta_og('type');
            $meta.= $this->tdb_jd_generate_meta_og('url', $job->get_url());
            if(!empty($websiteLogo)){
                $width = '200';
                $height = '200';
                $arraySize = getimagesize($websiteLogo);

                if(isset($arraySize[0]) AND isset($arraySize[1])){
                    if($arraySize[0] >= 200 AND $arraySize[0] <= 500 AND $arraySize[1] >= 200 AND $arraySize[1] <= 500){
                        $width = $arraySize[0];
                        $height = $arraySize[1];
                    }
                }
                $meta.= $this->tdb_jd_generate_meta_og('image', $websiteLogo, $width, $height);
            }
            $meta.= $this->tdb_jd_generate_meta_og('description', $job->get_short_description_clean());
            $meta.= $this->tdb_jd_generate_meta_og('site_name', $siteName);
            $meta.= $this->tdb_jd_generate_meta_og('updated_time',strtotime($job->get_published_date()));
        } else {
            $meta.= $this->tdb_jd_generate_meta_og('type');
            if(!empty($websiteLogo)){
                $width = '200';
                $height = '200';
                $arraySize = getimagesize($websiteLogo);

                if(isset($arraySize[0]) AND isset($arraySize[1])){
                    if($arraySize[0] >= 200 AND $arraySize[0] <= 500 AND $arraySize[1] >= 200 AND $arraySize[1] <= 500){
                        $width = $arraySize[0];
                        $height = $arraySize[1];
                    } else{
                        if($arraySize[0]<= 200){
                            $width = 200;
                        } else if($arraySize[0] >= 500) {
                            $width = 500;
                        } else {
                            $width = $arraySize[0];
                        }

                        if($arraySize[1]<= 200){
                            $height = 200;
                        } else if($arraySize[1] >= 500) {
                            $height = 500;
                        } else {
                            $height = $arraySize[1];
                        }
                    }
                }
                $meta.= $this->tdb_jd_generate_meta_og('image', $websiteLogo, $width, $height);
                $meta.= $this->tdb_jd_generate_meta_og('site_name', $siteName);
            }
        }
        echo $meta;
    }

    /* Meta for seo like linkedin */
    private function tdb_jd_generate_meta_og($type, $content = '', $width = '200', $height = '200'){
        $meta = '';
        $metaOg = '';
        $metaTwitter = '';

        switch ($type){
            case 'title':
                $meta = "<meta name='title' content='$content'/>";
                $metaOg ="<meta property='og:title' content='$content'/>";
                $metaTwitter ="<meta property='twitter:title' content='$content'/>";
                break;
            case 'type':
                $metaOg ="<meta property='og:type' content='website'/>";
                $metaTwitter ="<meta property='twitter:card' content='summary'/>";
                break;
            case 'image':
                $metaOg ="<meta  property='og:image' content='$content'/>";
                $metaOg .="<meta property='og:image:secure_url' content='$content'/>";
                $metaOg .="<meta property='og:image:width' content='$width'/>";
                $metaOg .="<meta property='og:image:height' content='$height'/>";
                $metaTwitter ="<meta property='twitter:image' content='$content'/>";
                break;
            case 'url':
                $metaOg ="<meta property='og:url' content='$content'/>";
                $metaTwitter ="<meta property='twitter:url' content='$content'/>";
                break;
            case 'description':
                $meta ="<meta name='description' content='$content'/>";
                $metaOg ="<meta property='og:description' content='$content'/>";
                $metaTwitter ="<meta property='twitter:description' content='$content'/>";
                break;
            case 'site_name':
                $meta ="<meta name='author' content='$content'/>";
                $metaOg ="<meta property='og:site_name' content='$content'/>";
                $metaTwitter ="<meta property='twitter:site' content='$content'/>";
                break;
            case 'updated_time':
                $metaOg ="<meta property='og:updated_time' content='$content'/>";
                break;
            default:
                break;
        }
        return $meta . $metaOg  . $metaTwitter;
    }

    /* Meta for seo like linkedin */
    public function tdb_jd_generate_meta_follow(){
        $meta = '<meta name="robots" content="index, follow">';

        return $meta;
    }

    /* Meta for seo like linkedin */
    function tdb_jb_get_wp_image_url($attachmentId){

        if(wp_get_attachment_url($attachmentId)){
            return wp_get_attachment_url($attachmentId);
        } else{
            return '';
        }
    }

    /* tags for the search form (number is the tag group order, 1 to 4 now, get parameters is the search result*/
    function getTagsField($number, $tagsGetParameters){
        global $gGroupTags;
        global $gTags;

        $defaultValue = TDB_LANG_ANY;
        $html = '';
        $option = '';

        if($number <= 0 && $number > 4){
            return '';
        }
        $displayTagGroup = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displayTagGroup'.$number, 'sValue', 'sName');
        if($displayTagGroup <> ''){
            $tagGroupSelected = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'tagGroup'.$number, 'sValue', 'sName');
            if($tagGroupSelected){
                //check if it has taggroup from api
                if(isset($gGroupTags[$tagGroupSelected])){

                    // check if it has tag from api
                    if(!isset($gTags[$tagGroupSelected]) && count($gTags[$tagGroupSelected]) <= 0){
                        return '';
                    }

                    $html = "<option value=''>$defaultValue</option>";
                    // set up the list option
                    foreach($gTags[$tagGroupSelected] as $id => $name){
                        if(is_array($tagsGetParameters) && in_array($id, $tagsGetParameters)){
                            $selected = "selected='true' ";
                        } else {
                            $selected = '';
                        }

                        $option .= "<option value='$id'  id='tags-$id' $selected>$name</option>";
                    }
                    $html.= $option;
                }

            }
        }
        return $html;
    }

    /* tags title for the search form */
    function getTagsTitle($number){
        global $gGroupTags;
        $title = '';
        $tagGroupSelected = $this->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'tagGroup'.$number, 'sValue', 'sName');
        if($tagGroupSelected){
            //check if it has taggroup from api
            if(isset($gGroupTags[$tagGroupSelected])){
                //get the title - placeholder
                $language = $this->tdb_jb_get_current_language();
                if(isset($gGroupTags[$tagGroupSelected][0][$language])){
                    $title = $gGroupTags[$tagGroupSelected][0][$language];
                } else {
                    $title = $tagGroupSelected;
                }
            }
        }
        return $title;
    }

    function cleanArray($array){
        $arrayTmp = $array;
        if(is_array($array)){
            if(isset($array[0])){
                if(empty($array[0])){
                    unset($arrayTmp[0]);
                }
            }
        }
        return $arrayTmp;
    }

    /* get page which use shortcode to update the rewrite url */
    function tdb_jb_get_pages_by_shortcode(){
        global $wpdb;
        $query = "SELECT DISTINCT ID, post_title, post_name, guid FROM ".$wpdb->posts." WHERE (post_type = 'page' OR post_type = 'post') AND (post_content LIKE '%[jobsearch_%' OR post_content LIKE '%[tdb_%') AND post_status = 'publish'";
        return $wpdb->get_results ($query);
    }

    function tdb_jb_get_form_param($paramName, $sanitizeType = '', $dataType ='', $returnDefault = ''){
        if (isset($_GET[$paramName])) {
            if($this->tdb_jb_validate_data($_GET[$paramName],'',$dataType)){
                return $this->tdb_jb_sanitize($_GET[$paramName], $sanitizeType);
            }
        }

        return $returnDefault;
    }

    /* Generate the button to go back to search page */
    function tdb_jb_return_home_page(){
        $helper = new Helper();
        if(isset($_SESSION["searchUrl"]) && $_SESSION["searchUrl"] <> ""){
            $url = $_SESSION["searchUrl"];
        } else {
            $url = $helper->tdb_get_search_url();
        }
        echo '<small>' ;
        echo '<a class="tdb-jd-button-back" href="'.$url.'">'.TDB_LANG_RETURNTOSEARCH.'</a>' ;
        echo '</small>' ;
    }
}
