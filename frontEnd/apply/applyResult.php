<?php
namespace Jobsearch\Apply;

use Jobsearch\Helper;
use Jobsearch\Helper\Translation;

class ApplyResult{
    // Result page after the candidate apply
    function tdb_jb_get_result_apply($attributes, $urlArray) {
        global $gSource;

        $helper = new Helper();
        $applyHelper = new ApplyHelper();
        $applyForm = new ApplyForm();

        $api = $attributes['api'];

        if (isset($_POST["hidden-api"])) {
            if($helper->tdb_jb_validate_data($_POST["hidden-api"],'int')){
                $api = $helper->tdb_jb_sanitize($_POST["hidden-api"],"text");
            }
        }

        $linkApi  = $helper->tdb_jb_get_api_link('LinkCreate', $attributes['api']);
        $linkFile  = $helper->tdb_jb_get_api_link('LinkFile', $api);

        $birthdate ="";

        $desiredEmplTypesFinal = array();
        $desiredIndustryFinal = array();
        $desiredCategoryFinal = array();
        $desiredLocationFinal = array();
        $messageAttachment = "";
        $postStatus = "";
        $file_count = 0;
        $id = "";
        $status = "";
        $message = "";
        $privacyPolicy = false;
        $botCheck = false;
        $recaptchaCheck = false;
        $nonceCheck = false;
        $errorField = [];
        $bError = false;
        $day = "";
        $currentSalaryBasis = "";
        $currentSalaryCurrency = "";
        $currentSalaryBonusCurrency = "";
        $externalSource = '';

        if(isset($_SESSION["tdb-source"]) && $_SESSION["tdb-source"] != ''){
            $externalSource = $_SESSION["tdb-source"];
        }

        //security check
        if (isset($_POST["tdbinput"]) && $_POST["tdbinput"] <> '') {
            $botCheck = true;
        }
        if (isset($_POST["tdbname"]) && $_POST["tdbname"] <> '') {
            $botCheck = true;
        }
        //end security check

        $recaptchaChecked = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaApply', 'sValue', 'sName');
        $recaptchaKey = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaKey', 'sValue', 'sName');
        if($recaptchaChecked <> '' && !empty($recaptchaKey) ){
            $recaptcha = $_POST['g-recaptcha-response'];
            $res = $applyHelper->tdb_jd_re_captcha($recaptcha);
            if(!$res['success']){
                $recaptchaCheck = true;
            }
        }

        if(isset($_POST['tdb_jb_frontend_post'])){
            if(wp_verify_nonce($_POST['tdb_jb_frontend_post'], 'tdb_jb_frontend_apply') <> 1){
                $nonceCheck = true;
            }
        }


        $idJob = $applyHelper->tdb_jb_get_form_post_param('hidden-id', 'text', 'int');
        $familyName = $applyHelper->tdb_jb_get_form_post_param('familyname', 'text', 'text');
        $source = $applyHelper->tdb_jb_get_form_post_param('hidden-source-detail', 'text', 'text');
        $givenName = $applyHelper->tdb_jb_get_form_post_param('givenname', 'text', 'text');
        $gender = $applyHelper->tdb_jb_get_form_post_param('gender', 'text');
        $country = $applyHelper->tdb_jb_get_form_post_param('country', 'text');
        $postal = $applyHelper->tdb_jb_get_form_post_param('postal', 'text');
        $city = $applyHelper->tdb_jb_get_form_post_param('city', 'text');
        $extended = $applyHelper->tdb_jb_get_form_post_param('extended', 'text');
        $region = $applyHelper->tdb_jb_get_form_post_param('region', 'text');
        $street = $applyHelper->tdb_jb_get_form_post_param('street', 'text');
        $years = $applyHelper->tdb_jb_get_form_post_param('years', 'text');
        $month = $applyHelper->tdb_jb_get_form_post_param('month', 'text');
        $days = $applyHelper->tdb_jb_get_form_post_param('days', 'text');

        if($years != '' || $month != '' || $days != ''){
            $birthdate = $years . "-" . $month . "-" . $days;
        }

        $nationality = $applyHelper->tdb_jb_get_form_post_param('nationality', 'text');
        $desiredWage = $applyHelper->tdb_jb_get_form_post_param('desiredwage', 'text');
        $currency = strtoupper($applyHelper->tdb_jb_get_form_post_param('currency', 'text'));
        $basis = $applyHelper->tdb_jb_get_form_post_param('basis', 'text');
        $noticePeriod = $applyHelper->tdb_jb_get_form_post_param('noticePeriod', 'text');
        $url = $applyHelper->tdb_jb_get_form_post_param('url', 'text', 'url');
        $facebook = $applyHelper->tdb_jb_get_form_post_param('facebook', 'text', 'url');
        $linkedin = $applyHelper->tdb_jb_get_form_post_param('linkedin', 'text', 'url');
        $visaCountry = $applyHelper->tdb_jb_get_form_post_param('visaCountry', 'text');
        $visaType = $applyHelper->tdb_jb_get_form_post_param('visaType', 'text');
        $currentSalary = $applyHelper->tdb_jb_get_form_post_param('currentSalary', 'text');
        if($currentSalary != ''){
            $currentSalaryCurrency = strtoupper($applyHelper->tdb_jb_get_form_post_param('currentSalaryCurrency', 'text'));
            $currentSalaryBasis = $applyHelper->tdb_jb_get_form_post_param('currentSalaryBasis', 'text');
        }
        $currentSalaryBonus = $applyHelper->tdb_jb_get_form_post_param('currentSalaryBonus', 'text');
        if($currentSalaryBonus != ''){
            $currentSalaryBonusCurrency = strtoupper($applyHelper->tdb_jb_get_form_post_param('currentSalaryBonusCurrency', 'text'));
        }
        $currentEmploymentCompany = $applyHelper->tdb_jb_get_form_post_param('currentEmploymentCompany', 'text');
        $currentEmploymentPosition = $applyHelper->tdb_jb_get_form_post_param('currentEmploymentPosition', 'text');
        $currentEmploymentDepartment = $applyHelper->tdb_jb_get_form_post_param('currentEmploymentDepartment', 'text');
        $nearestStation = $applyHelper->tdb_jb_get_form_post_param('neareststation', 'text');
        $certification = $applyHelper->tdb_jb_get_form_post_param('certification', 'text');
        $referrer = $applyHelper->tdb_jb_get_form_post_param('referrer', 'text');

        if($visaType <> '' && $visaCountry == ''){
            $visaCountry =  $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'autoNationalityCountry', 'sValue', 'sName');
        }

        if (isset($_POST["desiredemploymenttypes"])) {
            if($helper->tdb_jb_validate_data($_POST["desiredemploymenttypes"])){
                $desiredEmploymentTypes = $_POST["desiredemploymenttypes"];
                foreach ($desiredEmploymentTypes as $key => $value){
                    $desiredEmplTypesFinal[] = $helper->tdb_jb_sanitize($helper->tdb_jb_get_between($value,"[","]"),"text");
                }
            }
        }
        if (isset($_POST["desiredindustry"])) {
            if($helper->tdb_jb_validate_data($_POST["desiredindustry"])){
                $desiredIndustry = $_POST["desiredindustry"];
                foreach ($desiredIndustry as $key => $value){
                    $desiredIndustryFinal[] = $helper->tdb_jb_sanitize($helper->tdb_jb_get_between($value,"[","]"),"text");
                }
            }
        }
        if (isset($_POST["desiredlocation"])) {
            if($helper->tdb_jb_validate_data($_POST["desiredlocation"])){
                $desiredLocation = $_POST["desiredlocation"];
                foreach ($desiredLocation as $key => $value){
                    $desiredLocationFinal[] = $helper->tdb_jb_sanitize($helper->tdb_jb_get_between($value,"[","]"),"text");
                }
            }
        }
        if (isset($_POST["desiredjobcategory"])) {
            if($helper->tdb_jb_validate_data($_POST["desiredjobcategory"])){
                $desiredCategory = $_POST["desiredjobcategory"];
                foreach ($desiredCategory as $key => $value){
                    $desiredCategoryFinal[] = $helper->tdb_jb_sanitize($helper->tdb_jb_get_between($value,"[","]"),"text");
                }
            }
        }


        if (isset($_POST["privacyPolicy"])) {
            $privacyPolicy = true;
        }

        if($externalSource != ''){
            $sourceType = $externalSource;
        } else {
            $sourceType = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'sourceType', 'sValue', 'sName');
        }

        $postdata["name"] = array("familyName" => $familyName , "givenName" => $givenName);
        if($idJob <> ""){
            $postdata["job"] = $idJob;
        }

        $postdata["gender"] = $gender;
        $arraySanitize = array(1 => 'text', 2 => 'text');
        $postdata["emails"] = $applyHelper->tdb_jb_get_array_all_values("value","type","emails","typeemail",$arraySanitize);
        $arraySanitize = array(1 => 'text', 2 => 'text');
        $postdata["phoneNumbers"] = $applyHelper->tdb_jb_get_array_all_values("value","type","phonenumbers","typephone",$arraySanitize);

        if($city <> "" || $country <> "" || $postal <> "" || $street <> "") {
            $postalCodeArray = array("postalCode" => $postal);
            $countryArray = array("country" => $country);
            $streetArray = array("street" => $street);
            $cityArray = array("city" => $city);
            $extendedArray = array("extended" => $extended);
            $regionArray = array("region" => $region);

            $postdata['address'] = array_merge($postalCodeArray,$countryArray,$streetArray,$cityArray,$extendedArray,$regionArray);
        }

        if($birthdate != ''){
            $postdata['birthdate'] = $birthdate;
        }

        $postdata['nationality'] = strtoupper($nationality);
        $arraySanitize = array(1 => 'text', 2 => 'text');
        $postdata["languages"] = $applyHelper->tdb_jb_get_array_all_values("languageLocale","ability","languagesCode","languagesability",$arraySanitize);

        $postdata["languageCertifications"] = $applyHelper->tdb_jb_get_array_all_values("name","value","languagecertifications","score",$arraySanitize);

        if($desiredWage <> "" ) {
            $amountArray = array("amount" => $desiredWage);
            $currencyArray = array("currency" => $currency);
            $basisArray = array("basis" => $basis);

            $postdata["desiredWage"] = array_merge($amountArray,$currencyArray,$basisArray);
        }
        if(is_array($desiredEmplTypesFinal) && count($desiredEmplTypesFinal)> 0){
            $postdata['desiredEmploymentTypes'] = $desiredEmplTypesFinal;
        }
        if(is_array($desiredIndustryFinal) && count($desiredIndustryFinal)> 0){
            $postdata['desiredIndustries'] = $desiredIndustryFinal;
        }
        if(is_array($desiredLocationFinal) && count($desiredLocationFinal)> 0){
            $postdata['desiredLocations'] = $desiredLocationFinal;
        }
        if(is_array($desiredCategoryFinal) && count($desiredCategoryFinal)> 0){
            $postdata['desiredJobCategories'] = $desiredCategoryFinal;
        }

        $postdata['nearestStation'] = $nearestStation;
        $postdata['certification'] = $certification;
        $postdata['referrer']  = $referrer;

        if (empty($gSource) == FALSE && $sourceType <> "") {
            $postdata['sourceType'] = $sourceType;
        }

        if ($source <> "") {
            $postdata['source'] = $source;
        }

        if($noticePeriod <>""){
            $postdata["noticePeriod"] = $noticePeriod;
        }
        if($url <>""){
            $postdata["url"] = $url;
        }
        if($facebook <>""){
            $postdata["facebook"] = $facebook;
        }
        if($linkedin <>""){
            $postdata["linkedin"] = $linkedin;
        }

        if($visaCountry <>"" ){
            $VisaCountryArray = array("country" => $visaCountry);
            $VisaTypeArray = array("type" => $visaType);
            $arrayTmpVisa[]= array_merge($VisaCountryArray , $VisaTypeArray);
            $postdata["visas"] =  $arrayTmpVisa;
        }

        if($currentSalary <> "" || $currentSalaryCurrency <> "" || $currentSalaryBasis <> ""
            || $currentEmploymentDepartment <> "" || $currentEmploymentPosition <> ""
            || $currentSalaryBonusCurrency <> "" || $currentSalaryBonus) {

            if($currentSalary <> ""){
                $currentWageArray = [];
                $currentBonusArray = [];
                $currentEmploymentDepartmentArray = [];
                $currentEmploymentPositionArray = [];
                $currentEmploymentCompanyArray = [];

                $currentAmountArray = array("amount" => $currentSalary);
                $currentCurrencyArray = array("currency" => $currentSalaryCurrency);
                $currentBasisArray = array("basis" => $currentSalaryBasis);

                $currentWageArray["wage"] = array_merge($currentAmountArray,$currentCurrencyArray,$currentBasisArray);
            }
            if($currentSalaryBonus <> ""){
                $currentBonusAmountArray = array("amount" => $currentSalaryBonus);
                $currentBonusCurrencyArray = array("currency" => $currentSalaryBonusCurrency);

                $currentBonusArray["bonus"] = array_merge($currentBonusAmountArray,$currentBonusCurrencyArray);
            }
            if($currentEmploymentDepartment <> ""){
                $currentEmploymentDepartmentArray = array("department" => $currentEmploymentDepartment);
            }
            if($currentEmploymentPosition <> ""){
                $currentEmploymentPositionArray = array("position" => $currentEmploymentPosition);
            }
            if($currentEmploymentCompany <> ""){
                $currentEmploymentCompanyArray['companyName'] = array("companyName" => $currentEmploymentCompany);
            }

            $arrayEmployment[] = array_merge($currentEmploymentCompanyArray, $currentWageArray, $currentBonusArray, $currentEmploymentDepartmentArray, $currentEmploymentPositionArray);
            $postdata['employments'] = $arrayEmployment;
        }

        $postdata['privacyPolicy']  = $privacyPolicy;

        if($botCheck == false) {

            $json = json_encode($postdata, JSON_FORCE_OBJECT + JSON_ERROR_UTF8);

            $date = date('m/d/Y h:i', time());
            $timezone = date_default_timezone_get();
            // save the application on WordPress database
            $idWp = $applyHelper->tdb_jb_insert_apply($idJob, $familyName . ' ' . $givenName, $date, $timezone, $json, $postdata);
            // if attachment
            if (count($_FILES["attachments"]["name"]) > 0 && $_FILES["attachments"]["name"]["0"] <> "") {
                $localStorage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'attachmentStorageLocalEnableCheck', 'sValue', 'sName');
                if($localStorage <> ''){
                    $applyHelper->tdb_jb_send_file_to_wordpress($idWp);
                }
            }

            if($nonceCheck == false && $recaptchaCheck == false){
                // Send the application
                $requests_response = $applyHelper->tdb_jb_wp_post_apply($linkApi, $json, $api);
                $jsonResponse = json_decode($requests_response["body"], true);

                if (isset($jsonResponse["status"])) {
                    $status = $jsonResponse["status"];
                }
                if (isset($jsonResponse["message"])) {
                    $message = $jsonResponse["message"];
                }
                // get the result
                if (!empty($status) && $botCheck == false && $recaptchaCheck == false) {
                    $postStatus = $status;
                    switch ($status) {
                        // error
                        case "400":
                            $bError = true;
                            foreach ($jsonResponse["data"] as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $errorMessage) {
                                        if (is_array($errorMessage)) {
                                            foreach ($errorMessage as $errorMessage2) {
                                                if (is_array($errorMessage2)) {
                                                    foreach ($errorMessage2 as $errorMessage3) {
                                                        $errorField[$key] = $errorMessage3;
                                                    }
                                                } else {
                                                    $errorField[$key] = $errorMessage2;
                                                }
                                            }
                                        } else {
                                            $errorField[$key] = $errorMessage;
                                        }
                                    }
                                } else {
                                    $errorField[$key] = $value;
                                }
                            }
                            break;
                        // success
                        case "201":
                            if (isset($jsonResponse["data"]["id"])) {
                                $id = $jsonResponse["data"]["id"];
                            }
                            $message = TDB_LANG_THANKAPPLY;
                            //attachment part
                            $messageAttachment = "";
                            // in case the application is successful and an ID is sent back
                            if ($id > 0) {
                                $linkFile .= "/" . $id . "/attachment";
                                $file_count = count($_FILES["attachments"]["name"]);
                                // if attachment
                                if ($file_count > 0 && $_FILES["attachments"]["name"]["0"] <> "") {
                                    //copy file into WordPress and save data in WordPress database

                                    if ($file_count > 1) {
                                        for ($i = 0; $i < $file_count; $i++) {
                                            $file_name = $_FILES["attachments"]["name"][$i];
                                            $UrlArray = base64_encode(file_get_contents($_FILES['attachments']['tmp_name'][$i]));
                                            $attachmentsUrlArray[$i] = array('name' => $file_name, 'file' => $UrlArray);
                                        }
                                    } elseif ($file_count == 1) {

                                        $file_name = $_FILES["attachments"]["name"]["0"];
                                        $UrlArray = base64_encode(file_get_contents($_FILES['attachments']['tmp_name']["0"]));
                                        $attachmentsUrlArray[0] = array('name' => $file_name, 'file' => $UrlArray);
                                    }

                                    // Send attachments
                                    $fileUrlArray = array('attachments' => $attachmentsUrlArray);
                                    $responseAttachement = $applyHelper->tdb_jb_curl_send_attachement($linkFile, $fileUrlArray, $api);
                                    $jsonResponseAttachment = json_decode($responseAttachement, true);
                                    $attachmentStatus = $jsonResponseAttachment['status'];
                                    switch ($attachmentStatus) {
                                        // Case sent
                                        case "201":
                                            $messageAttachment = TDB_LANG_THANKATTACHMENT;
                                            break;
                                        // Case error
                                        default:
                                            $bError = true;
                                            if (isset ($jsonResponseAttachment['message'])) {
                                                $messageAttachment = $jsonResponseAttachment['message'];
                                            } else {
                                                $messageAttachment = TDB_LANG_ANERRORATTACHMENT;
                                            }
                                    }
                                }
                            }
                            break;
                        default:
                            $bError = true;
                            if (isset($jsonResponse["message"]) && $jsonResponse["message"] != '') {
                                $message = $jsonResponse['message'];
                            } else {
                                $message = TDB_LANG_ANERROROCCURED;
                            }
                    }
                }
                // if redirect available and apply is success then redirect to the thanks page
                if ($postStatus == "201") {
                    // check if template mail can be use
                    $templateActivate = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'templateActivateCheck', 'sValue', 'sName');
                    if($templateActivate <> ''){
                        //get the var for the template
                        $urlJob = '';
                        if(isset($_GET["tdb-id-job"]) && $_GET["tdb-id-job"] > 0){
                            $apiUrl = '';
                            if(isset($_GET["tdb-id-api"]) && $_GET["tdb-id-api"] > 1){
                                $api = $_GET["tdb-id-api"];
                                $apiUrl = "&tdb-id-api=$api";
                            }

                            if($helper->tdb_jb_validate_data($_GET["tdb-id-job"],'int')){
                                $jobId = $helper->tdb_jb_sanitize($_GET["tdb-id-job"],'text');
                                if(strpos($urlArray["home"],'?') == true) {
                                    $idUrl = "&tdb-id-job=".$jobId;
                                }
                                else {
                                    $idUrl = "?tdb-id-job=".$jobId;
                                }
                            }
                            $urlJob = $urlArray["home"].$idUrl.$apiUrl;
                        } else {
                            $jobId = get_query_var( 'job-id' );
                            $api = get_query_var( 'job-api' );

                            if($jobId && $api){
                                $urlJob = str_replace('apply', '', $urlArray["home"]);
                                if(substr($urlJob, -1) == '/'){
                                    $urlJob = substr($urlJob, 0, -1);
                                }
                            }
                        }
                        //if use check language used
                        if ( function_exists('pll_the_languages') ) {
                            $lang = pll_current_language();
                        } else {
                            $lang = 'default';
                        }
                        //get template
                        $template = $applyHelper->tdb_jb_get_submit_template($lang);
                        if($template != false){
                            // update var inside template

                            $replaceVars['job-url'] = $urlJob;
                            $replaceVars['given-name'] = $givenName;
                            $replaceVars['family-name'] = $familyName;

                            //replace dynamic var
                            $template = $applyHelper->tdb_jb_replace_template_var($replaceVars, $template);
                            $email = $_POST['emails1'];
                            $from = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'fromTemplate', 'sValue', 'sName');
                            $subject = $applyHelper->tdb_jb_get_submit_template_subject($lang);

                            if(trim($template) != '' && trim($email) != '' && trim($from) != '' && trim($subject) != ''){
                                $cc =  $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'ccTemplate', 'sValue', 'sName');
                                $bcc = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'bccTemplate', 'sValue', 'sName');
                                $to = $email;
                                $body = $template;

                                $applyHelper->tdb_jb_send_email_applicant($body, $subject, $from, $to, $cc, $bcc);
                            }
                        }
                    }

                    if ($attributes['redirect-link'] <> "") {
                        $helper->tdb_jb_redirect($attributes['redirect-link']);
                    }
                }
            }
        }

        if($recaptchaCheck == true){
            $bError = true;
            $message .= '<br>'.TDB_LANG_CAPTCHAINVALID;
        }

        if($bError == true){
            $applicationSent = TDB_LANG_YOURAPPLYNOTSENT;
        } else {
            $applicationSent = TDB_LANG_YOURAPPLYSENT;
        }

        $bodyArray = array(
            "langMessage" => $message,
            "langApplicationSent" => $applicationSent,
            "langMessageAttachment" => $messageAttachment,
            "langLabelMessage" => TDB_LANG_MESSAGE,
            "langLabelMessageAttachment" => TDB_LANG_ATTACHMENT,
            "langLabelError" => TDB_LANG_ERRORSEND,
            "langLabelStatus" => TDB_LANG_STATUS,
            "error" => $errorField,
            "bError" => $bError,
            "status" => $postStatus,
            "nbFile" => $file_count);

        $helper->tdb_jb_show_template(TDB_APPLY_RESULT_TPL,$bodyArray);

        //if some error, just reload the form with the data send by the user
        if($postStatus <> "201" || $bError == true){
            $applyForm->tdb_jb_apply_form($attributes, $urlArray);
        }
    }
}
