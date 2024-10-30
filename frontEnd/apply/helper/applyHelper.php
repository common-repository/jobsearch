<?php
namespace Jobsearch\Apply;

use Jobsearch\Helper;
use Jobsearch\Helper\Translation;

class ApplyHelper{

// Insert application inside wordpress database
    function tdb_jb_insert_apply($idJob,$nameUser,$date,$timezone,$json, $postData = []) {
        global $wpdb;

        // basic content
        $wpdb->insert(TDB_TABLE_APPLY, array('nIdJob' => $idJob, 'sName' => $nameUser,'sDate' => $date,'sTimezone' => $timezone,'sJson' => $json));
        $idApply = $wpdb->insert_id;

        // all data
        foreach($postData as $key => $content) {
            $secondLvl = "";
            $thirdLvl1 = "";
            $thirdLvl2 = "";
            $val = "";
            $countval= 0;
            $val1 = [];
            $val2 = [];
            $count = 0;

            $firstLvl = $key;
            if (is_array($content)) {
                foreach ($content as $key2 => $value2) {
                    $count ++;
                    $secondLvl = $key2;
                    if (is_array($value2)){
                        foreach ($value2 as $key3 => $value3) {
                            if(is_array($value3)){

                            } else {
                                if(isset($val1[$countval]) && isset($val2[$countval]) || $countval == 0){
                                    $countval ++;
                                }
                                if(!isset($val1[$countval])){
                                    $thirdLvl1 = $key3;
                                    $val1[$countval] = $value3;
                                } else {
                                    $thirdLvl2 = $key3;
                                    $val2[$countval] = $value3;
                                }
                            }
                        }
                    } else {
                        if($firstLvl == "name" || $firstLvl == "address" || $firstLvl == "desiredWage"){
                            if($val <> ""){
                                $val .= " " .$value2;
                            } else {
                                $val = $value2;
                            }
                            $secondLvl = "";
                        } else {
                            $val = $value2;
                        }
                    }
                }
            } else {
                $val = $content;
            }

            if($countval > 0){
                for($i=1;$i<=$countval;$i++){
                    $tmpVal1 = '';
                    $tmpVal2 = '';

                    if(isset($val1[$i])){
                        $tmpVal1 = $val1[$i];
                    }
                    if(isset($val2[$i])){
                        $tmpVal2 = $val2[$i];
                    }
                    $wpdb->insert(TDB_TABLE_APPLY_DETAIL, array('nIdApply' => $idApply, '1stlvl' => $firstLvl,'2ndlvl' => $secondLvl,
                        '3ndlvl1' => $thirdLvl1,'3ndlvl2' => $thirdLvl2,
                        'val1' => $tmpVal1, 'val2' => $tmpVal2));
                }
            } else{
                $wpdb->insert(TDB_TABLE_APPLY_DETAIL, array('nIdApply' => $idApply, '1stlvl' => $firstLvl,'2ndlvl' => $secondLvl,
                    '3ndlvl1' => $thirdLvl1,'3ndlvl2' => $thirdLvl2, 'val' => $val));
            }
        }
        return  $idApply;
    }

// Send application to api
    function tdb_jb_wp_post_apply($linkApi, $postdata, $api = 1) {
        $helper = new Helper();
        $userApi = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM,'apiKey','sValue','sName', $api);
        $parameters = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'sslverify' => false,
            'blocking' => true,
            'headers' => array("Content-type" => "application/json", "x-auth-token" => $userApi),
            'body' => $postdata,
            'cookies' => array()
        );

        $requests_response =  wp_remote_post( $linkApi,$parameters);
        if ($requests_response == FALSE) {
            if (stristr($linkApi, 'https') == true) {
                $linkGet = str_replace('https', 'http', $linkApi);
                $requests_response = wp_remote_post($linkGet, $parameters);
            }
        }

        if ( is_wp_error( $requests_response ) ) {
            $error_message = $requests_response->get_error_message();
            echo "Something went wrong: $error_message";
        }
        return $requests_response;
    }

// Send attachment to api
    Function tdb_jb_curl_send_attachement($linkApi, $file, $api = 1) {
        $helper = new Helper();
        $userApi = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM,'apiKey','sValue','sName', $api);
        $header =  ['Content-Type: application/json',"x-auth-token: $userApi"];
        $requests_response = '';

        try{
            $ch = curl_init($linkApi);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($file));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            $requests_response = curl_exec($ch);
        }catch (\Exception $e){
            $err = curl_error($ch);
        }finally{
            curl_close($ch);
        }

        if (isset($err) && $err) {
            $helper->tdb_jb_send_email("cURL Error #:" . $err);
        }

        return $requests_response;
    }

//get all value from array posted for multivalue post like language, email certification
    function tdb_jb_get_array_all_values($firstColumnName,$secondColumnName,$firstPostName,$secondPostName,$typeSanitize) {
        $countPost = 1;
        $isPost= false;
        $helper = new Helper();
        $arrayFinal = [];

        While(true) {
            if (!isset($_POST[$firstPostName.$countPost])) {
                if ($isPost == true) {
                    return $arrayFinal;
                } else {
                    return array();
                }
            }
            if (isset($_POST[$firstPostName.$countPost])) {
                if(!isset($typeSanitize[$countPost])){
                    $typeSanitize[$countPost] = '';
                }
                $arrayValue = array($firstColumnName => $helper->tdb_jb_sanitize($_POST[$firstPostName.$countPost],$typeSanitize[$countPost]));
                if(isset($_POST[$secondPostName.$countPost])){
                    $arrayType = array($secondColumnName => $helper->tdb_jb_sanitize($_POST[$secondPostName.$countPost],$typeSanitize[$countPost]));
                    $arrayFinal[] = array_merge($arrayValue , $arrayType);
                } else {
                    $arrayFinal[] = array_merge($arrayValue);
                }

                $countPost ++;
                $isPost = true;
            } else {
                if($isPost == true) {
                    return $arrayFinal;
                }
                else {
                    return array();
                }
            }
        }
    }

// Get all the post value before apply
    function tdb_jb_get_post_apply() {
        $helper = new Helper();
        $applyHelper = new ApplyHelper();
        $varPost[] = '';

        $varPost["idJob"] = $this->tdb_jb_get_form_post_param('hidden-id', 'text');
        $varPost["hidden-search-url"] = $this->tdb_jb_get_form_post_param('hidden-search-url', 'text');
        $varPost["familyname"] = $this->tdb_jb_get_form_post_param('familyname', 'text');
        $varPost["givenname"] = $this->tdb_jb_get_form_post_param('givenname', 'text');
        $varPost["gender"] = $this->tdb_jb_get_form_post_param('gender', 'text');
        $varPost["country"] = $this->tdb_jb_get_form_post_param('country', 'text');
        $varPost["postal"] = $this->tdb_jb_get_form_post_param('postal', 'text');
        $varPost["city"] = $this->tdb_jb_get_form_post_param('city', 'text');
        $varPost["street"] = $this->tdb_jb_get_form_post_param('street', 'text');
        $varPost["extended"] = $this->tdb_jb_get_form_post_param('extended', 'text');
        $varPost["region"] = $this->tdb_jb_get_form_post_param('region', 'text');
        $varPost["birthdate"] = $this->tdb_jb_get_form_post_param('birthdate', 'text');
        $varPost["nationality"] = $this->tdb_jb_get_form_post_param('nationality', 'text');
        $varPost["desiredwage"] = $this->tdb_jb_get_form_post_param('desiredwage', 'text');
        $varPost["currency"] = $this->tdb_jb_get_form_post_param('currency', 'text');
        $varPost["visaType"] = $this->tdb_jb_get_form_post_param('visaType', 'text');
        $varPost["visaCountry"] = $this->tdb_jb_get_form_post_param('visaCountry', 'text');
        $varPost["basis"] = $this->tdb_jb_get_form_post_param('basis', 'text');
        $varPost["facebook"] = $this->tdb_jb_get_form_post_param('facebook', 'text');
        $varPost["url"] = $this->tdb_jb_get_form_post_param('url', 'text');
        $varPost["linkedin"] = $this->tdb_jb_get_form_post_param('linkedin', 'text');
        $varPost["desiredjobcategory"] = $this->tdb_jb_get_form_post_param('desiredjobcategory', 'text');
        $varPost["desiredTitle"] = $this->tdb_jb_get_form_post_param('desiredTitle', 'text');
        $varPost["desiredCompanySizes"] = $this->tdb_jb_get_form_post_param('desiredCompanySizes', 'text');
        $varPost["desiredindustry"] = $this->tdb_jb_get_form_post_param('desiredindustry', 'text');
        $varPost["desiredlocation"] = $this->tdb_jb_get_form_post_param('desiredlocation', 'text');
        $varPost["excluded"] = $this->tdb_jb_get_form_post_param('excluded', 'text');
        $varPost["noticePeriod"] = $this->tdb_jb_get_form_post_param('noticePeriod', 'text');
        $varPost["neareststation"] = $this->tdb_jb_get_form_post_param('neareststation', 'text');
        $varPost["certification"] = $this->tdb_jb_get_form_post_param('certification', 'text');
        $varPost["referrer"] = $this->tdb_jb_get_form_post_param('referrer', 'text');
        $varPost["source"] = $this->tdb_jb_get_form_post_param('source', 'text');
        $varPost["sourceType"] = $this->tdb_jb_get_form_post_param('sourceType', 'text');
        $varPost["currentSalary"] = $this->tdb_jb_get_form_post_param('currentSalary', 'text');
        $varPost["currentSalaryCurrency"] = $this->tdb_jb_get_form_post_param('currentSalaryCurrency', 'text');
        $varPost["currentSalaryBasis"] = $this->tdb_jb_get_form_post_param('currentSalaryBasis', 'text');
        $varPost["currentSalaryBonus"] = $this->tdb_jb_get_form_post_param('currentSalaryBonus', 'text');
        $varPost["currentSalaryBonusCurrency"] = $this->tdb_jb_get_form_post_param('currentSalaryBonusCurrency', 'text');
        $varPost["currentSalaryBonusBasis"] = $this->tdb_jb_get_form_post_param('currentSalaryBonusBasis', 'text');
        $varPost["currentEmploymentCompany"] = $this->tdb_jb_get_form_post_param('currentEmploymentCompany', 'text');
        $varPost["currentEmploymentPosition"] = $this->tdb_jb_get_form_post_param('currentEmploymentPosition', 'text');
        $varPost["currentEmploymentDepartment"] = $this->tdb_jb_get_form_post_param('currentEmploymentDepartment', 'text');

        if (isset($_POST["desiredemploymenttypes"]) && is_array($_POST["desiredemploymenttypes"])) {
            foreach ($_POST["desiredemploymenttypes"] as $key => $value){
                $desiredEmplTypesFinal[] = $helper->tdb_jb_get_between($value,"[","]");
            }
            $varPost["desiredemploymenttypes"] = $desiredEmplTypesFinal;
        } else {
            $varPost["desiredemploymenttypes"] = '';
        }

        $arraySanitize = array(1 => 'text', 2 => 'text');
        $varPost["email"] = $applyHelper->tdb_jb_get_array_all_values("value","type","emails","typeemail",$arraySanitize);
        $arraySanitize = array(1 => 'text', 2 => 'text');
        $varPost["phoneNumbers"] = $applyHelper->tdb_jb_get_array_all_values("value","type","phonenumbers","typephone",$arraySanitize);
        $varPost["languages"] = $applyHelper->tdb_jb_get_array_all_values("languageLocale","ability","languagesCode","languagesability",$arraySanitize);
        $varPost["languageCertifications"] = $applyHelper->tdb_jb_get_array_all_values("name","value","languagecertifications","score",$arraySanitize);
        return $varPost;
    }

// copy attachment to wordpress repository and save it on database
    function tdb_jb_send_file_to_wordpress($id){
        global $wpdb;

        $upload_dir = wp_upload_dir();
        $user_dirname_main = $upload_dir['basedir'] . '/' . "jobsearch";
        $upload_dir = $user_dirname_main . '/' . $id;
        if(!file_exists($user_dirname_main)){
            wp_mkdir_p($user_dirname_main);
        }

        if(!file_exists($upload_dir)){
            wp_mkdir_p($upload_dir);
        }

        $file_count = count($_FILES["attachments"]["name"]);
        if($file_count >0 && $_FILES["attachments"]["name"]["0"] <> "") {
            for ($i = 0; $i < $file_count; $i++) {
                $file_name = $_FILES["attachments"]["name"][$i];

                $filename = strtolower($file_name) ;
                $name = explode("/", $filename) ;
                $n = count($name)-1;
                $name = $name[$n];
                $exts = explode(".", $name) ;
                $n = count($exts)-1;
                $exts = $exts[$n];

                $newname = time() . rand(); // Create a new name
                $filename = $newname.'.'.$exts; // Get the new name with the extension
                $filepath = $upload_dir . '/' . $filename; // Get the complete file path

                copy($_FILES["attachments"]["tmp_name"][$i],$filepath);
                chmod($filepath, 0644);
                $fileName = $id . "/" . $filename ;
                $wpdb->insert(TDB_TABLE_APPLY_ATTACHMENT, array('nIdApply' => $id, 'sName' => $name,'sFile' => $fileName));
            }
        }
    }

    // get a list of option of language code in iso 639-1 or 2
    function tdb_jb_get_opt_language($iso ="639-2",$filterArray = "",$firstOption ="",$selectedValue = "",$favoriteLanguage = "") {
        $helper = new Helper();
        // iso 639-1 = language used (2 caractere lower case)
        // iso 639-2 = language used (2 caractere UPER case)
        $translation = new Translation();

        $val  = "";
        $selected  = "";
        $selectedDefault = ' selected="true" ';
        $selectedContent = $selectedDefault;
        $favorite  = "";
        $favoriteArray = $helper->tdb_jb_get_array_favorite_language();
        $favoriteFinalArray = array();
        $favoriteFinalGrp1 = array();
        $favoriteFinalGrp2 = array();
        $japan = "";

        $optLabelLanguage = TDB_LANG_OPTION_FAVORITELANGUAGE;
        $optLabelOthers = TDB_LANG_OPTION_OTHERSLANGUAGE;

        $optGroupOpen1 = "";
        $optGroupOpen2 = "<optgroup label='$optLabelOthers'>";

        $favoriteGrp1  = "";
        $favoriteGrp2  = "";

        $optGroup1 = "";
        $optGroup2 = "";

        $optGroupClosed = "</optgroup>";

        if($favoriteLanguage == ""){
            $optGroupOpen1 = "";
            $optGroupOpen2 = "";
            $optGroupClosed = "";
        } else {
            $favoriteLanguageArray = explode(PHP_EOL,$favoriteLanguage);
        }


        foreach($favoriteArray as $key => $value){
            $favoriteReverted[$value] =  $key;
        }

        if($selectedValue <> "")    {
            $selectedContent = '';
        }
        $language = $helper->tdb_jb_get_current_language();
        $codes = $translation->tdb_jb_iso6391($language);

        $start = "<option value='' $selectedContent id='language-0'>$firstOption</option>";
        $id = 0;
        if(is_array($codes)){
            asort($codes);
        }
        foreach ($codes as $key => $value) {
            $id++;
            if(!empty($filterArray) && !isset($filterArray[substr($key,0,2)])){
                continue;
            }

            $keyFinal = strtolower($key) ;

            if(isset($favoriteReverted[$keyFinal])){
                if(!isset($favoriteFinalArray[$favoriteReverted[$keyFinal]])){
                    $favoriteFinalArray[$favoriteReverted[$keyFinal]] = "";
                }
                $favoriteFinalArray[$favoriteReverted[$keyFinal]] .= "<option value='$keyFinal'  id='language-$id'>$value</option>";

                if($favoriteLanguage <> ""){
                    if(!isset($favoriteFinalGrp1[$favoriteReverted[$keyFinal]])){
                        $favoriteFinalGrp1[$keyFinal] = "";
                    }
                    if(!isset($favoriteFinalGrp2[$favoriteReverted[$keyFinal]])){
                        $favoriteFinalGrp2[$favoriteReverted[$keyFinal]] = "";
                    }

                    if(strpos($favoriteLanguage, substr($key,0,2)) !== false){
                        $favoriteFinalGrp1[$keyFinal] .= "<option value='$keyFinal'  id='language-$id'>$value</option>";
                    } else {
                        $favoriteFinalGrp2[$favoriteReverted[$keyFinal]] .= "<option value='$keyFinal'  id='language-$id'>$value</option>";
                    }
                }
            }
            else{
                if(substr($key,0,2) == $language) {
                    $selected .= "<option value='$keyFinal'  id='language-$id'>$value</option>";

                    if($favoriteLanguage <> ""){
                        if(strpos($favoriteLanguage, substr($key,0,2)) !== false){
                            $optGroup1 .= "<option value='$keyFinal'  id='language-$id'>$value</option>";
                        } else {
                            $optGroup2 .= "<option value='$keyFinal'  id='language-$id'>$value</option>";
                        }
                    }
                }
                else {
                    $val .= "<option value='$keyFinal'  id='language-$id'>$value</option>";

                    if($favoriteLanguage <> ""){
                        if(strpos($favoriteLanguage, substr($key,0,2)) !== false){
                            $optGroup1 .= "<option value='$keyFinal'  id='language-$id'>$value</option>";
                        } else {
                            $optGroup2 .= "<option value='$keyFinal'  id='language-$id'>$value</option>";
                        }
                    }
                }
            }
        }

        ksort($favoriteFinalArray);
        foreach($favoriteFinalArray as $key => $value){
            $favorite.= $value;
        }
        if($favoriteLanguage <> ""){
            ksort($favoriteFinalGrp1);
            if (is_array($favoriteLanguageArray)) {
                foreach($favoriteLanguageArray as $id => $key) {
                    foreach($favoriteFinalGrp1 as $key2 => $value){
                        if($key == $key2) {
                            $favoriteGrp1 .= $value;
                        }
                    }
                }
            }

            ksort($favoriteFinalGrp2);
            foreach($favoriteFinalGrp2 as $key => $value){
                $favoriteGrp2 .= $value;
            }

            $html = $start . $japan . $favoriteGrp1 . $optGroupClosed . $optGroupOpen2 . $favoriteGrp2 . $optGroup2 .$optGroupClosed;

            if($selectedValue <> ""){
                $html = str_replace("value='$selectedValue'","value='$selectedValue'".$selectedDefault,$html);
            }

            return $html;
        }
        $html = $start . $favorite . $selected . $val;
        if($selectedValue <> ""){
            $html = str_replace("value='$selectedValue'","value='$selectedValue'".$selectedDefault,$html);
        }

        return $html;
    }

    /* generate field for birthday on apply form */
    function tdb_jb_get_calendar($requiredSpan = "",$mandatory = false, $colsize){
        $earliestYear = 1950;
        $html = '';

        $required = "" ;

        if($mandatory == true){
            $required = 'required="required"';
        }

        for($i=1; $i<=31;$i++) {
            $arrayDay[$i] = sprintf("%02d", $i);
        }
        for($i=1; $i<=12;$i++) {
            $arrayMonth[$i] = sprintf("%02d", $i);
        }

        foreach (range(date('Y'), $earliestYear) as $key) {
            $arrayYear[$key] = $key;
        }

        $html .= '<div class="tdb-jd-row tdb-jd-row-calendar-title">';
        $html .= '<div class="tdb-jd-col-6 tdb-jd-col-6-calendar">';
        $html .= '<label for="birthdate" class="tdb-jd-label tdb-jd-label-calendar">' . $requiredSpan . TDB_LANG_BIRTHDATE;
        $html .= '</label>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="tdb-jd-row tdb-jd-row-calendar-content">';
        $html .= '<div class="tdb-jd-col-'.$colsize['birthYear'] .' tdb-jd-col-4-calendar">';
        //set up the years
        $html.= "<select name ='years' id ='years' " . $required . " class='tdb-jd-form-control tdb-jd-input tdb-jd-input-calendar'>";
        $html.= "<option id='years00' value>".TDB_LANG_YEARS."</option>";
        foreach($arrayYear as $key => $value){
            $html.= "<option id='years$value' value='$value'>$value</option>";
        }
        $html.= "</select>";
        $html .= '</div>';
        $html .= '<div class="tdb-jd-col-'.$colsize['birthMonth'] .' tdb-jd-col-4-calendar">';
        //set up the month
        $html.= "<select name ='month' id ='month' " . $required . " class='tdb-jd-form-control tdb-jd-input tdb-jd-input-calendar'>";
        $html.= "<option id='month00' value>".TDB_LANG_MONTH."</option>";
        foreach($arrayMonth as $key => $value){
            $html.= "<option id='month$value' value='$value'>$value</option>";
        }
        $html.= "</select>";
        $html .= '</div>';
        $html .= '<div class="tdb-jd-col-'.$colsize['birthDay'] .' tdb-jd-col-4-calendar">';
        //set up the day
        $html.= "<select name ='days' id ='days' " . $required . " class='tdb-jd-form-control tdb-jd-input tdb-jd-input-calendar'>";
        $html.= "<option id='days00'  value >".TDB_LANG_DAY."</option>";
        foreach($arrayDay as $key => $value){
            $html.= "<option id='days$value' value='$value'>$value</option>";
        }
        $html.= "</select>";
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    //Used for the employement in the post function, allow the user to check the employment he want when he subscribed
    function tbd_jd_get_checkbox_type($array, $nameCheckBox, $requiredContent = "",$checkedContent = []) {
        $helper = new Helper();
        $acceptedLanguage = $helper->tdb_jb_get_current_language();
        $i = 0;
        $columSize = 2;
        $html = "";

        $html .= '<div class="tdb-jd-row" >';

        if(is_array($array) && !empty($array)){
            foreach ($array as $key => $content) {

                //get the default value, key
                $checkboxContent = $key;
                $bHasvalue = false;
                $checkboxDefaultContent = '';
                foreach ($content as  $language => $value) {
                    $langageSubstr = substr($language,0,2);

                    // get the translated value if language match
                    if($langageSubstr == $acceptedLanguage) {
                        $bHasvalue = true;
                        $checkboxContent = $value;
                    }

                    //get english for default value if don't have
                    if($langageSubstr == 'en') {
                        $checkboxDefaultContent = $value;
                    }
                }

                if($bHasvalue == false && $checkboxDefaultContent != ''){
                    $checkboxContent = $checkboxDefaultContent;
                }

                // add the value
                if ($i> 0) {
                    $requiredContent = "";
                }
                // css like boostrap, page width = 12 column so after need to make a new row
                if ($i>11) {
                    $i = 0;
                    $html .= "</div>";
                    $html .= '<div class="tdb-jd-row" >';
                }

                //$name = $key;
                $name = $nameCheckBox."[$key]";
                $checked = "";
                if(is_array($checkedContent) && count($checkedContent) > 0)

                    foreach($checkedContent as $checkValue){
                        if(strtolower($checkboxContent) == strtolower($checkValue)){
                            $checked = "checked";
                        }
                    }

                $html .= $helper->tdb_jb_get_col($columSize, "checkbox", $name, $checked, $checkboxContent, "tdb-jd-input-text-apply","","","","tdb-jd-input-label-apply",$requiredContent);

                $i+= $columSize;
            }
        }

        $html .= "</div>";

        return $html;
    }

    /* apply form, generate the email field block */
    function tdb_jd_generate_email_field_apply($count,$requiredContent1,$requiredContent2,$content1, $content2, $labelClass = "",$requiredSpan1 = '',$requiredSpan2 = '',$visibilityTypeField = '', $colSize = []){
        $helper = new Helper();
        $html = "";
        $html .= "<div class='tdb-jd-row tdb-jd-email-text-row' id ='emailrow$count'>";
        $html .= "<div class='tdb-jd-col-".$colSize['email']." tdb-jd-search-column tdb-jd-email-left-col' name='email-text-$count' id='email-text-$count'>";
        $html .= "<div class='tdb-jd-container tdb-jd-container-email-label'>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-label-search tdb-jd-row-email-label'>";
        $html .= "<div class='tdb-jd-col-12 tdb-jd-col-email-label tdb-jd-col-label-search $labelClass'>";
        $html .= "<label class='tdb-jd-label ' for='emails$count'>".TDB_LANG_EMAIL.$requiredSpan1."</label>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-content-search tdb-jd-row-email-content '>";
        $html .= $helper->tdb_jb_get_col(12, "email", "emails$count",$content1,"","tdb-jd-form-control tdb-jd-input tdb-jd-email-input","","","","",$requiredContent1,"","");
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        if($visibilityTypeField != ''){
            $html .= "<div class='tdb-jd-col-".$colSize['emailType']." tdb-jd-email-right-col' name='email-type-$count' id='email-type-$count'>";
            $html .= "<div class='tdb-jd-container tdb-jd-email-type-label-container'>";
            $html .= "<div class='tdb-jd-row tdb-jd-row-label-search tdb-jd-email-type-label'>";
            $html .= "<div class='tdb-jd-col-12 tdb-jd-email-type-label-col tdb-jd-col-label-search $labelClass'>";
            $html .= "<label class='tdb-jd-label tdb-jd-type-email' for='typeemail$count'>".TDB_LANG_TYPEEMAIL.$requiredSpan2."</label>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='tdb-jd-row tdb-jd-row-content-search tdb-jd-row-email-type-content'>";
            $html .= $helper->tdb_jb_get_col(12, "select", "typeemail$count",$helper->tdb_jb_get_opt_apply("email",$content2,"",""),"","tdb-jd-form-control tdb-jd-input tdb-jd-type-email","","","typeemail$count","",$requiredContent2);
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }

        $html .= "</div>";

        return $html;
    }

    /* apply form, generate the phone field block */
    function tdb_jd_generate_phone_field_apply($count, $requiredContent1,$requiredContent2, $content1, $content2, $labelClass = "", $requiredSpan1 = '', $requiredSpan2 = '', $visibilityTypeField = '', $colSize = []){
        $helper = new Helper();
        $html = "";
        $html .= "<div class='tdb-jd-row tdb-jd-phone-text-row' id ='phonerow$count'>";
        $html .= "<div class='tdb-jd-col-".$colSize['phone']." tdb-jd-search-column tdb-jd-phone-left-col' id='phone-text-$count' name='phone-text-$count'>";
        $html .= "<div class='tdb-jd-container tdb-jd-container-phone-label'>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-label-search tdb-jd-row-phone-label'>";
        $html .= "<div class='tdb-jd-col-12 tdb-jd-col-phone-label tdb-jd-col-label-search $labelClass'>";
        $html .= "<label class='tdb-jd-label' for='phonenumbers$count'>".TDB_LANG_PHONENUMBER.$requiredSpan1."</label>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-content-search tdb-jd-row-phone-content'>";
        $html .= $helper->tdb_jb_get_col(12, "tel", "phonenumbers$count",$content1,"","tdb-jd-form-control tdb-jd-input tdb-jd-phone-input","","","","",$requiredContent1,"","","");
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        if($visibilityTypeField != ''){
            $html .= "<div class='tdb-jd-col-".$colSize['phoneType']." tdb-jd-phone-right-col' id='phone-type-$count' name='phone-type-$count' >";
            $html .= "<div class='tdb-jd-container tdb-jd-phone-type-label-container'>";
            $html .= "<div class='tdb-jd-row tdb-jd-row-label-search tdb-jd-phone-type-label-row'>";
            $html .= "<div class='tdb-jd-col-12 tdb-jd-phone-type-label-col tdb-jd-col-label-search $labelClass'>";
            $html .= "<label class='tdb-jd-label tdb-jd-type-phone' for='typephone$count'>".TDB_LANG_TYPEPHONE.$requiredSpan2."</label>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='tdb-jd-row tdb-jd-row-content-search tdb-jd-phone-type-row-content'>";
            $html .= $helper->tdb_jb_get_col(12, "select", "typephone$count", $helper->tdb_jb_get_opt_apply("phone",$content2,"",""),"","tdb-jd-form-control tdb-jd-input tdb-jd-type-phone","","","typephone$count","",$requiredContent2);
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }

        $html .= "</div>";

        return $html;
    }

    /* apply form, generate the language field block */
    function tdb_jd_generate_language_field_apply($count, $requiredContent1, $requiredContent2, $content1, $content2, $labelClass = "", $requiredSpan1 = '', $requiredSpan2 = '', $colSize = []){
        $helper = new Helper();
        $html = "";
        $html .= "<div class='tdb-jd-row' id ='languageSkillrow$count'>";
        $html .= "<div class='tdb-jd-col-".$colSize['languageCertification']." tdb-jd-search-column'  id='skill-type-$count' name='skill-type-$count' >";
        $html .= "<div class='tdb-jd-container'>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-label-search'>";
        $html .= "<div class='tdb-jd-col-12 tdb-jd-col-label-search $labelClass'>";
        $html .= "<label class='tdb-jd-label' for='languagesCode$count'>".TDB_LANG_LANGUAGE.$requiredSpan1."</label>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-content-search'>";
        $html .= $helper->tdb_jb_get_col(12, "select", "languagesCode$count",$content1,"","tdb-jd-form-control tdb-jd-input","","","","",$requiredContent1,"","");
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-col-".$colSize['certifAbility']."'  id='skill-ability-$count' name='skill-ability-$count' >";
        $html .= "<div class='tdb-jd-container'>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-label-search'>";
        $html .= "<div class='tdb-jd-col-12 tdb-jd-col-label-search $labelClass'>";
        $html .= "<label class='tdb-jd-label' for='languagesability$count'>".TDB_LANG_ABILITY.$requiredSpan2."</label>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-content-search'>";
        $html .= $helper->tdb_jb_get_col(12, "select", "languagesability$count",$content2,"","tdb-jd-form-control tdb-jd-input","","","languagesability$count","",$requiredContent2);
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    function tdb_jd_generate_certification_field_apply($count, $requiredContent1, $requiredContent2, $content1, $content2, $labelClass = "", $requiredSpan1 = '', $requiredSpan2 = '', $colSize = []){
        $helper = new Helper();

        $html = "";
        $html .= "<div class='tdb-jd-row' id ='languageScorerow$count'>";
        $html .= "<div class='tdb-jd-col-".$colSize['certification']." tdb-jd-search-column' id='score-text-$count' name='score-text-$count'>";
        $html .= "<div class='tdb-jd-container'>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-label-search'>";
        $html .= "<div class='tdb-jd-col-12 tdb-jd-col-label-search $labelClass'>";
        $html .= "<label class='tdb-jd-label' for='languagecertifications$count'>".TDB_LANG_LANGUAGESCERTIF.$requiredSpan1."</label>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-content-search'>";
        $html .= $helper->tdb_jb_get_col(12, "select", "languagecertifications$count", $content1,"","tdb-jd-form-control tdb-jd-input","","","","",$requiredContent1,"","");
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-col-".$colSize['levelCertification']."'  id='score-score-$count' name='score-score-$count'>";
        $html .= "<div class='tdb-jd-container'>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-label-search'>";
        $html .= "<div class='tdb-jd-col-12 tdb-jd-col-label-search $labelClass'>";
        $html .= "<label class='tdb-jd-label' for='score$count'>".TDB_LANG_SCORE.$requiredSpan2."</label>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row tdb-jd-row-content-search'>";
        $html .= $helper->tdb_jb_get_col(12, "text", "score$count",$content2,"","tdb-jd-form-control tdb-jd-input","","","","",$requiredContent2,"","");
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    function tdb_jd_re_captcha($recaptcha){
        $helper = new Helper();

        $secret = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaSecret', 'sValue', 'sName');
        $ip = $_SERVER['REMOTE_ADDR'];

        $postvars = array("secret"=>$secret, "response"=>$recaptcha, "remoteip"=>$ip);
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }

    function tdb_jb_replace_template_var($vars, $template){
        $templateTmp = $template;
        foreach($vars as $key => $value){
            $templateTmp = str_replace('['.$key.']',$value, $templateTmp);
        }
        return $templateTmp;
    }

    /* get the template to send to the applicant an email */
    function tdb_jb_get_submit_template($lang = 'default'){
        global $wpdb;
        $mainResult = '';

        $request = "SELECT sValue FROM ". TDB_TABLE_TEMPLATE ." WHERE sName = 'submitTemplate' and sLanguage = '$lang' ";
        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $mainResult = $ligneResult->sValue;
        }

        if(trim($mainResult) != ''){
            return $mainResult;
        }

        return false;
    }

    /* get the subject to send an email to the applicant */
    function tdb_jb_get_submit_template_subject($lang = 'default'){
        global $wpdb;
        $mainResult = '';

        $exec = $wpdb->get_results("SELECT sValue FROM ". TDB_TABLE_TEMPLATE ." WHERE sName = 'submitTemplateSubject' and sLanguage = '$lang' ");

        foreach ($exec as $ligneResult) {
            $mainResult = $ligneResult->sValue;
        }

        if(trim($mainResult) != ''){
            return $mainResult;
        }

        return '';
    }

    /* send an email to the applicant */
    function tdb_jb_send_email_applicant($content, $subject, $from, $to, $cc ='', $bcc =''){
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html";
        $headers[] = "charset=UTF-8";
        $headers[] = "From: ".get_bloginfo('name')." <".$from.">";
        if(!empty($cc)){
            $headers[] = "Cc: $cc";
        }
        if(!empty($bcc)){
            $headers[] = "Bcc: $bcc";
        }

        //send email
        wp_mail( $to, $subject, '<html>' . htmlspecialchars_decode($content) . '</html>', $headers );
    }

    /* factor function to get post and sanitize it */
    function tdb_jb_get_form_post_param($paramName, $sanitizeType = '', $dataType ='', $returnDefault = ''){
        $helper = new Helper();
        if (isset($_POST[$paramName])) {
            if($helper->tdb_jb_validate_data($_POST[$paramName], $dataType)){
                return $helper->tdb_jb_sanitize($_POST[$paramName], $sanitizeType);
            }
        }

        return $returnDefault;
    }
}
