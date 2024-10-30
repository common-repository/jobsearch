<?php
namespace Jobsearch\Apply;

use Jobsearch\Helper;

class ApplyForm{

// Show apply form
    function tdb_jb_apply_form($attributes, $urlArray) {
        global $gLanguages;
        global $gVisaType;
        global $gTypes;
        global $gIndustries;
        global $gCategories;
        global $gCategoriesFiltered;
        global $gLocation;
        global $gSource;
        $applyHelper = new ApplyHelper();
        $helper = new Helper();
        $colSize = $helper->tdb_jb_get_list_col_resized_register_fields();

        $api = $attributes['api'];
        $idUrl = "";
        $url = "";
        $urlHome = "";
        $displayLanguage = 'style = "display:none"';
        $displayCertification = 'style = "display:none"';
        $displayPhone = 'style = "display:none"';
        $displayEmail = 'style = "display:none"';
        $bPrivacyPolicy = false;
        $bRecaptcha = false;
        $bCategory = false ;

        // get external source if it has
        if(isset($_GET["tdb-source"])){
            $helper->tdb_jb_set_session();
            $bValid = false;
            foreach($gSource as $key => $value){
                if(strcasecmp($key, $_GET["tdb-source"]) == 0){
                    $_SESSION["tdb-source"] = $key;
                    $bValid = true;
                    break;
                }
            }
            if($bValid == false){
                $_SESSION["tdb-source"] = null;
            }
        }

        $apiUrl = '';
        if($api > 1){
            $apiUrl = "&tdb-id-api=$api";
        }

        $jobId = $attributes['jobId'];

        // Get id if wordpress use id link
        if(isset($_GET["tdb-id-job"]) && $jobId > 0){
            if($helper->tdb_jb_validate_data($_GET["tdb-id-job"],'int')){
                if(strpos($urlArray["home"],'?') == true) {
                    $idUrl = "&tdb-id-job=".$jobId;
                }
                else {
                    $idUrl = "?tdb-id-job=".$jobId;
                }
            }

            $url = $urlArray["home"].$idUrl.$apiUrl;

            $urlHome = $urlArray["home"];
            if(isset($_SESSION["searchUrl"]) && $_SESSION["searchUrl"] <> ""){
                $urlHome = $_SESSION["searchUrl"];
            }
        } else {
            if($jobId && $api){
                $url = str_replace('apply', '', $urlArray["home"]);
                if(substr($url, -1) == '/'){
                    $url = substr($url, 0, -1);
                }

                if(isset($_SESSION["searchUrl"]) && $_SESSION["searchUrl"] <> ""){
                    $urlHome = $_SESSION["searchUrl"];
                } else {
                    $urlHome = $helper->tdb_get_search_url();
                }
            }
        }

        $reverseLanguageSkill = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'reverseLanguageSkillCheck', 'sValue', 'sName');
        $privacyPolicyText = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'privacyPolicyText', 'sValue', 'sName');
        $certif = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'favoriteCertif', 'sValue', 'sName');
        $recaptchaChecked = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaApply', 'sValue', 'sName');
        $recaptchaKey = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaKey', 'sValue', 'sName');
        $recaptchaSecret = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaSecret', 'sValue', 'sName');

        $sourceDetail = '';
        if(isset($_GET["tdb-page-title"]) && $_GET["tdb-page-title"] <> ''){
            $sourceDetail = $_GET["tdb-page-title"];
        }

        $language = $helper->tdb_jb_get_current_language();
        $paramRequired = $helper->tdb_jb_get_list_required_fields();
        $paramApply = $helper->tdb_jb_get_list_apply();

        // Get all class of paramRequired
        foreach ($paramRequired as $key => $value) {
            if(!empty($value)) {
                $requiredSpan[$key] = " <span class='tdb-jd-fb-required'>*</span>";
                $requiredContent[$key] = ' required="required" aria-required="true" ';
            }
            else {
                $requiredSpan[$key] = "";
                $requiredContent[$key] = "";
            }
        }

        if(!empty($privacyPolicyText)){
            $privacyPolicyText = TDB_LANG_PRIVACYPOLICYTEXT. "<br>"."<a href='$privacyPolicyText' target='_blank'>".TDB_LANG_READPRIVACYPOLICY."</a>";
            $bPrivacyPolicy = true;
        }

        if($recaptchaChecked <> '' && !empty($recaptchaKey) && !empty($recaptchaSecret) ){
           $bRecaptcha = true;
        }

        $requiredSpan["privacyPolicy"] = " <span class='tdb-jd-fb-required'>*</span>";
        $requiredAttachment = '';
        if($requiredSpan['attachment']){
            $requiredAttachment = 'required';
        }

        if(is_array($gCategories) && count($gCategories)> 0){
            $bCategory = true;
        }

        $favoriteLanguageArray = $helper->tdb_jb_get_array_favorite_language();
        $favoriteLanguageContent = $helper->tdb_jb_set_content_favorite_language($favoriteLanguageArray);

        $favoriteNationalityArray = $helper->tdb_jb_get_array_favorite_nationality('favoriteNationalityContent');
        $favoriteCountryArray = $helper->tdb_jb_get_array_favorite_nationality('favoriteCountryContent');
        $favoriteNationalityArrayOther = $helper->tdb_jb_get_array_favorite_nationality('favoriteNationalityContentOthers');
        $favoriteNationalityContent = $helper->tdb_jb_set_content_favorite_Nationality($favoriteNationalityArray);
        $favoriteCountryContent = $helper->tdb_jb_set_content_favorite_Nationality($favoriteCountryArray);
        $favoriteNationalityOtherContent = $helper->tdb_jb_set_content_favorite_Nationality($favoriteNationalityArrayOther);

        $postdata = $applyHelper->tdb_jb_get_post_apply();
        if($postdata['hidden-search-url'] != '' ){
            $urlHome = $postdata['hidden-search-url'];
        } else {
            if(isset($_SESSION["searchUrl"]) && $_SESSION["searchUrl"] <> ""){
                $urlHome = $_SESSION["searchUrl"];
                $postdata['hidden-search-url'] = $_SESSION["searchUrl"];
            }
        }

        //Email
        $countEmail = 1;
        $htmlEmail = "";
        $content1 = '';
        $content2 = '';
        $bType = false;

        if(isset($postdata["emails"]) && is_array($postdata["emails"]) && count($postdata["emails"]) > 0 ) {
            foreach($postdata["emails"] as $count => $value){
                foreach($value as $type => $content) {
                    switch($type){
                        case "type":
                            $bType = true;
                            $content2 = $content;
                            break;
                        case "value":
                            $bVal = true;
                            $content1 = $content;
                            break;
                        default:
                    }

                    if($bVal == true && $bType == true){
                        if($countEmail>1){
                            $htmlEmail .= $applyHelper->tdb_jd_generate_email_field_apply($countEmail,'','',$content1, $content2,'tdb-jd-sublabel', '', '', $paramApply["emailsType"], $colSize);
                        } else {
                            $htmlEmail .= $applyHelper->tdb_jd_generate_email_field_apply($countEmail,$requiredContent["email"],$requiredContent["emailType"],$content1, $content2,'',$requiredSpan["email"],$requiredSpan["emailType"],$paramApply["emailsType"], $colSize);
                        }
                        $content1 = '';
                        $content2 = '';
                        $bVal = false;
                        $bType = false;
                    }
                }
                $countEmail ++;
            }
        } else {
            $htmlEmail .= $applyHelper->tdb_jd_generate_email_field_apply(1,$requiredContent["email"],$requiredContent["emailType"],'', $helper->tdb_jb_get_opt_apply("email","","",""),'',$requiredSpan["email"],$requiredSpan["emailType"],$paramApply["emailsType"], $colSize);
        }

        //Phone
        $countPhone = 1;
        $htmlPhone = "";
        $content1 = '';
        $content2 = '';
        $bType = false;

        if(isset($postdata["phoneNumbers"]) && is_array($postdata["phoneNumbers"]) && count($postdata["phoneNumbers"]) > 0 ) {
            foreach($postdata["phoneNumbers"] as $count => $value){
                foreach($value as $type => $content) {
                    switch($type){
                        case "type":
                            $bType = true;
                            $content2 = $content;
                            break;
                        case "value":
                            $bVal = true;
                            $content1 = $content;
                            break;
                        default:
                    }

                    if($bVal == true && $bType == true){
                        if($countPhone>1){
                            $htmlPhone .= $applyHelper->tdb_jd_generate_phone_field_apply($countPhone,'','',$content1, $content2,'tdb-jd-sublabel', '', '', $paramApply["phoneType"], $colSize);
                        } else {
                            $htmlPhone .= $applyHelper->tdb_jd_generate_phone_field_apply($countPhone,$requiredContent["phone"],$requiredContent["phoneType"],$content1, $content2,'',$requiredSpan["phone"],$requiredSpan["phoneType"],$paramApply["phoneType"], $colSize);
                        }
                        $content1 = '';
                        $content2 = '';
                        $bVal = false;
                        $bType = false;
                    }
                }
                $countPhone ++;
            }
        } else {
            $htmlPhone .= $applyHelper->tdb_jd_generate_phone_field_apply(1,$requiredContent["phone"],$requiredContent["phoneType"],'', $helper->tdb_jb_get_opt_apply("phone","","",""),'',$requiredSpan["phone"],$requiredSpan["phoneType"],$paramApply["phoneType"], $colSize);
        }

        //Language
        $countLanguage = 1;
        $htmlLanguage = "";
        $languageStartTmp = "";
        $content1 = [];
        $content2 = [];
        if(isset($postdata["languages"][0])){
            $languageArray = $postdata["languages"][0];
            $languageStartTmp = $languageArray["languageLocale"];
        }

        if(isset($postdata["languages"]) && is_array($postdata["languages"]) && count($postdata["languages"])>0 && $languageStartTmp <> "" ) {
            foreach($postdata["languages"] as $count => $value){
                $content1[$countLanguage] = '';
                $content2[$countLanguage] = '';
                foreach($value as $type => $content) {
                    switch($type){
                        case "ability":
                            $content1[$countLanguage] = $helper->tdb_jb_get_opt_select($gLanguages,"","",$content,$reverseLanguageSkill);
                            break;
                        case "languageLocale":
                            $content2[$countLanguage] = $applyHelper->tdb_jb_get_opt_language("639-2","","",$content,$favoriteLanguageContent);
                            break;
                        default:
                    }
                }

                $countLanguage ++;
            }

            for($i=1;$i<$countLanguage;$i++){
                if($i>1){
                    $htmlLanguage .= $applyHelper->tdb_jd_generate_language_field_apply($i,'','',$content2[$i], $content1[$i],'tdb-jd-sublabel', '', '', $colSize);
                } else{
                    $htmlLanguage .= $applyHelper->tdb_jd_generate_language_field_apply($i,$requiredContent["language"],$requiredContent["languageAbility"],$content2[$i], $content1[$i],'',$requiredSpan["language"],$requiredSpan["languageAbility"], $colSize);
                }
            }

        } else {
            $content1 = $applyHelper->tdb_jb_get_opt_language("639-2","","","",$favoriteLanguageContent);
            $content2 = $helper->tdb_jb_get_opt_select($gLanguages,"","","",$reverseLanguageSkill);
            $htmlLanguage .= $applyHelper->tdb_jd_generate_language_field_apply($countLanguage,$requiredContent["language"],$requiredContent["languageAbility"],$content1, $content2,'',$requiredSpan["language"],$requiredSpan["languageAbility"], $colSize);
        }

        //Certification
        $countCertification = 1;
        $htmlCertification = "";
        $certificationTmp = "";
        $content1 = '';
        $content2 = '';
        if(isset($postdata["languageCertifications"][0])){
            $languageArray = $postdata["languageCertifications"][0];
            $certificationTmp = $languageArray["name"];
        }

        if(isset($postdata["languageCertifications"]) && is_array($postdata["languageCertifications"]) && count($postdata["languageCertifications"]) > 0 && $certificationTmp <> "") {
            foreach($postdata["languageCertifications"] as $count => $value){

                foreach($value as $type => $content) {

                    switch($type){
                        case "name":
                            $content1 = $helper->tdb_jb_get_opt_apply("certif",$content,$certif,"");
                            $bType = true;
                            break;
                        case "value":
                            $bVal = true;
                            $content2 = $content;
                            break;
                        default:
                    }

                    if($bVal == true && $bType == true){

                        if($countLanguage>1){
                            $htmlCertification .= $applyHelper->tdb_jd_generate_certification_field_apply($countCertification,'','',$content1, $content2, 'tdb-jd-sublabel', '', '', $colSize);
                        } else{
                            $htmlCertification .= $applyHelper->tdb_jd_generate_certification_field_apply($countCertification,$requiredContent["languageCertification"],$requiredContent["languageScore"],$content1, $content2,'',$requiredSpan["languageCertification"],$requiredSpan["languageScore"], $colSize);
                        }

                        $content1 = '';
                        $content2 = '';
                        $bVal = false;
                        $bType = false;
                    }

                }
                $countCertification ++;
            }
        } else {
            $content1 = $helper->tdb_jb_get_opt_apply("certif","",$certif,"");
            $content2 = "";
            $htmlCertification .= $applyHelper->tdb_jd_generate_certification_field_apply($countCertification,$requiredContent["languageCertification"],$requiredContent["languageScore"],$content1, $content2,'',$requiredSpan["languageCertification"],$requiredSpan["languageScore"], $colSize);
        }

        if($countLanguage > 1){
            $displayLanguage = '';
        }
        if($countCertification > 1){
            $displayCertification = '';
        }
        if($countEmail > 1){
            $displayEmail = '';
        }
        if($countPhone > 1){
            $displayPhone = '';
        }

        if($paramApply["languages"] == "" && $paramApply["languageCertifications"] == ""){
            $nbSkill = 0 ;
        } else {
            $nbSkill = 1;
        }

        if($paramApply["facebook"] == "" && $paramApply["linkedin"] == "" && $paramApply["url"] == ""){
            $nbSocial = 0 ;
        } else {
            $nbSocial = 1;
        }

        if($paramApply["desiredEmploymentTypes"] == ""
            && $paramApply["desiredIndustry"] == ""
            && $paramApply["desiredLocation"] == ""
            && $paramApply["desiredJobCategory"] == ""){
            $nbEmployement = 0 ;
        } else {
            $nbEmployement = 1;
        }

        if($jobId <> ""){
            $formLanguage = TDB_LANG_APPLY;
        } else {
            $formLanguage = TDB_LANG_REGISTER;
        }

        $excludedCategoriesContent = $helper->tdb_jb_get_array_category('excludedCategories');
        $excludedCategories = $helper->tdb_jb_set_content_category($excludedCategoriesContent);
        $displayCategoriesContent = $helper->tdb_jb_get_array_category('displayCategories');
        $displayCategories = $helper->tdb_jb_set_content_category($displayCategoriesContent);

        if(is_array($gCategoriesFiltered) && ((count($excludedCategories) > 0 ) || (count($displayCategories) > 0 ))){
            $gCategories = $gCategoriesFiltered;
        }

        // $nonce = wp_create_nonce('tdb_jb_frontend_apply');
        $nonce = wp_nonce_field( 'tdb_jb_frontend_apply', 'tdb_jb_frontend_post', false, true );

        $bodyArray = array(
            "api"=> $api,
            "colSize"=> $colSize,
            "langApplicationForm"=> $formLanguage,
            "langReturnJob"=>TDB_LANG_RETURNJOBDETAIL,
            "langReturnSearch"=>TDB_LANG_RETURNTOSEARCH,
            "langPersonal"=>TDB_LANG_PERSONALINFO,
            "langFamilyName"=>TDB_LANG_FAMILYNAME,
            "langGivenName"=>TDB_LANG_GIVENNAME,
            "langGender"=>TDB_LANG_GENDER,
            "langGenderM"=>TDB_LANG_MALE,
            "langGenderF"=>TDB_LANG_FEMALE,
            "langAddEmail"=>TDB_LANG_ADDEMAIL,
            "langAddPhone"=> TDB_LANG_ADDPHONE,
            "langRemove"=> TDB_LANG_REMOVE,
            "langContact"=>TDB_LANG_CONTACTDETAIL,
            "langAdress"=>TDB_LANG_ADDRESS,
            "langLanguage1"=>TDB_LANG_LANGUAGE,
            "langLanguage"=>TDB_LANG_CERTIFS,
            "langLanguageSkill"=>TDB_LANG_ADDLANGUAGE,
            "langAddCertif"=>TDB_LANG_ADDCERTIFICATION,
            "langSalary"=>TDB_LANG_SALARY,
            "langEmployement"=>TDB_LANG_DESIREDEMPLOYEMENT,
            "langEmployementType"=>TDB_LANG_EMPLOYEMENTTYPE,
            "langExtra"=>TDB_LANG_EXTRA,
            "langFile"=>TDB_LANG_RESUME,
            "langVisa"=>TDB_LANG_VISA,
            "langSocialMedia"=>TDB_LANG_SOCIALMEDIA,
            "langUpload"=>TDB_LANG_FILEUPLOAD,
            "langSubmit"=> TDB_LANG_SUBMIT,
            "langAddressPostal"=> TDB_LANG_ADDRESSPOSTAL,
            "langCategory"=> TDB_LANG_DESIREDCATEGORY,
            "langLocation"=> TDB_LANG_DESIREDLOCATION,
            "langIndustry"=> TDB_LANG_DESIREDINDUSTRY,
            "langEmployment"=> TDB_LANG_EMPLOYEMENT,
            "urlJob"=>$url,
            "urlHome"=> $urlHome,
            "url"=>$url,
            "id"=>$jobId,
            "requiredSpanEmployementType"=>$requiredSpan["employementType"],
            "requiredSpanLocation"=>$requiredSpan["desiredLocation"],
            "requiredSpanIndustry"=>$requiredSpan["desiredIndustry"],
            "requiredSpanLanguageAbility"=>$requiredSpan["languageAbility"],
            "requiredSpanCategory"=>$requiredSpan["desiredJobCategory"],
            "requiredSpanLanguageCertification"=>$requiredSpan["languageCertification"],
            "requiredSpanLanguageScore"=>$requiredSpan["languageScore"],
            "requiredSpanEmail"=> $requiredSpan["email"],
            "requiredSpanEmailType"=> $requiredSpan["emailType"],
            "requiredSpanPhone"=> $requiredSpan["phone"],
            "requiredSpanPhoneType"=> $requiredSpan["phoneType"],
            "required"=>"tdb-jd-fb-required",
            "requiredSpanAttachment"=>$requiredSpan["attachment"],
            "requiredSpanGender"=>$requiredSpan["gender"],
            "requiredContentGender"=>$requiredContent["gender"],
            "requiredEmail"=>$requiredSpan["email"],
            "requiredTypeEmail"=>$requiredSpan["emailType"],
            "requiredCurrentSalary"=>$requiredSpan["currentSalary"],
            "requiredCurrentSalaryBonus"=>$requiredSpan["currentSalaryBonus"],
            "requiredAttachment"=> $requiredAttachment,
            "recaptcha"=> $bRecaptcha,
            "recaptchaKey"=> $recaptchaKey,
            "email" => $htmlEmail,
            "telephone" => $htmlPhone,
            "languageAbility" => $htmlLanguage,
            "languageCertification" => $htmlCertification,
            "mail"=>$helper->tdb_jb_get_col($colSize['email'], "tel", "emails1","","","tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["email"]),
            "mailType"=>$helper->tdb_jb_get_col($colSize['emailType'], "select", "typeemail1",$helper->tdb_jb_get_opt_apply("email"),"","tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["emailType"]),
            "tel"=>$helper->tdb_jb_get_col($colSize['phone'], "tel", "phonenumbers1","","","tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["phone"]),
            "telType"=>$helper->tdb_jb_get_col($colSize['phoneType'], "select", "typephone1",$helper->tdb_jb_get_opt_apply("phone"),"","tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["phoneType"]),
            "nationality"=>$helper->tdb_jb_get_col($colSize['nationality'], "select", "nationality",$helper->tdb_jb_get_opt_3166("","",$postdata["nationality"],$favoriteNationalityContent),TDB_LANG_NATIONALITY,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["nationality"],$requiredSpan["nationality"]),
            "visaCountry"=>$helper->tdb_jb_get_col($colSize['visaCountry'], "select", "visaCountry",$helper->tdb_jb_get_opt_3166("","",$postdata["visaCountry"],$favoriteCountryContent,$favoriteNationalityOtherContent),TDB_LANG_VISACOUNTRY,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["visaCountry"],$requiredSpan["visaCountry"],'','','tdb-jd-visa-country'),
            "visaType"=> $helper->tdb_jb_get_col($colSize['visa'], "select", "visaType", $helper->tdb_jb_get_opt_select($gVisaType,"",$postdata["visaType"]),TDB_LANG_STATUTOFRESIDENCE,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["languageAbility"]),
            "birthday"=>$applyHelper->tdb_jb_get_calendar($requiredSpan["birthdate"],$requiredContent["birthdate"], $colSize),
            "postal"=>$helper->tdb_jb_get_col($colSize['addressPostal'], "text", "postal",$postdata["postal"],TDB_LANG_POSTALCODE,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["postal"],$requiredSpan["postal"]),
            "country"=>$helper->tdb_jb_get_col($colSize['addressCountry'], "select", "country",$helper->tdb_jb_get_opt_3166("","",$postdata["country"],$favoriteCountryContent,$favoriteNationalityOtherContent),TDB_LANG_COUNTRY,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["country"],$requiredSpan["country"]),
            "city"=>$helper->tdb_jb_get_col($colSize['addressCity'], "text", "city",$postdata["city"],TDB_LANG_CITY,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["city"],$requiredSpan["city"]),
            "extended"=>$helper->tdb_jb_get_col($colSize['addressExtended'], "text", "extended",$postdata["extended"],TDB_LANG_EXTENDED,"tdb-jd-form-control tdb-jd-input","","","",""),
            "region"=>$helper->tdb_jb_get_col($colSize['addressRegion'], "text", "region",$postdata["region"],TDB_LANG_REGION,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["region"],$requiredSpan["region"]),
            "street"=>$helper->tdb_jb_get_col($colSize['addressStreet'], "text", "street",$postdata["street"],TDB_LANG_STREET,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["street"],$requiredSpan["street"]),
            "currentSalary"=>$helper->tdb_jb_get_col($colSize['currentSalaryAmount'], "number", "currentSalary", $postdata["currentSalary"],TDB_LANG_CURRENTSALARY,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["currentSalary"],$requiredSpan["currentSalary"]),
            "currentSalaryCurrency"=>$helper->tdb_jb_get_col($colSize['currentSalaryCurrency'], "select", "currentSalaryCurrency", $helper->tdb_jb_get_opt_sql($language,TDB_TABLE_CURRENCY,"sName","sTranslate",strtolower($postdata["currentSalaryCurrency"])),TDB_LANG_CURRENCY,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["currentSalaryCurrency"],$requiredSpan["currentSalaryCurrency"]),
            "currentSalaryBonusCurrency"=>$helper->tdb_jb_get_col($colSize["bonusSalaryCurrency"], "select", "currentSalaryBonusCurrency", $helper->tdb_jb_get_opt_sql($language,TDB_TABLE_CURRENCY,"sName","sTranslate",strtolower($postdata["currentSalaryBonusCurrency"])),TDB_LANG_CURRENCY,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["currentSalaryBonusCurrency"],$requiredSpan["currentSalaryBonusCurrency"]),
            "currentSalaryBasis"=>$helper->tdb_jb_get_col($colSize["currentSalaryBasis"], "select", "currentSalaryBasis", $helper->tdb_jb_get_opt_sql($language,TDB_TABLE_BASIS,"sName","sTranslate",strtolower($postdata["currentSalaryBasis"])),TDB_LANG_BASIS,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["currentSalaryBasis"],$requiredSpan["currentSalaryBasis"]),
            "currentSalaryBonus"=>$helper->tdb_jb_get_col($colSize["bonusSalaryAmount"], "number", "currentSalaryBonus", $postdata["currentSalaryBonus"],TDB_LANG_CURRENTSALARYBONUS,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["currentSalaryBonus"],$requiredSpan["currentSalaryBonus"]),
            "currentEmploymentCompany"=>$helper->tdb_jb_get_col($colSize["currentCompany"], "text", "currentEmploymentCompany", $postdata["currentEmploymentCompany"],TDB_LANG_EMPLOYMENTCOMPANY,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["currentEmploymentCompany"],$requiredSpan["currentEmploymentCompany"]),
            "currentEmploymentPosition"=>$helper->tdb_jb_get_col($colSize["currentPosition"], "text", "currentEmploymentPosition", $postdata["currentEmploymentPosition"],TDB_LANG_EMPLOYMENTPOSITION,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["currentEmploymentPosition"],$requiredSpan["currentEmploymentPosition"]),
            "currentEmploymentDepartment"=>$helper->tdb_jb_get_col($colSize["currentDepartment"], "text", "currentEmploymentDepartment", $postdata["currentEmploymentDepartment"],TDB_LANG_EMPLOYMENTDEPARTMENT,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["currentEmploymentDepartment"],$requiredSpan["currentEmploymentDepartment"]),
            "desiredWage"=>$helper->tdb_jb_get_col($colSize["desiredSalaryAmount"], "number", "desiredwage", $postdata["desiredwage"],TDB_LANG_DESIREDWAGE,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["desiredWage"],$requiredSpan["desiredWage"]),
            "currency"=>$helper->tdb_jb_get_col($colSize["desiredSalaryCurrency"], "select", "currency", $helper->tdb_jb_get_opt_sql($language,TDB_TABLE_CURRENCY,"sName","sTranslate",strtolower($postdata["currency"])),TDB_LANG_CURRENCY,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["currency"],$requiredSpan["currency"]),
            "basis"=>$helper->tdb_jb_get_col($colSize["desiredSalaryBasis"], "select", "basis", $helper->tdb_jb_get_opt_sql($language,TDB_TABLE_BASIS,"sName","sTranslate",strtolower($postdata["basis"])),TDB_LANG_BASIS,"tdb-jd-custom-select tdb-jd-input","","","","",$requiredContent["basis"],$requiredSpan["basis"]),
            "checkBoxEmployement"=>$applyHelper->tbd_jd_get_checkbox_type($gTypes,"desiredemploymenttypes",$requiredContent["employementType"],$postdata["desiredemploymenttypes"]),
            "checkBoxIndustry"=>$applyHelper->tbd_jd_get_checkbox_type($gIndustries,"desiredindustry",$requiredContent["desiredIndustry"],$postdata["desiredindustry"]),
            "checkBoxLocation"=>$applyHelper->tbd_jd_get_checkbox_type($gLocation,"desiredlocation",$requiredContent["desiredLocation"],$postdata["desiredlocation"]),
            "checkBoxCategory"=>$applyHelper->tbd_jd_get_checkbox_type($gCategories,"desiredjobcategory",$requiredContent["desiredJobCategory"],$postdata["desiredjobcategory"]),
            "desiredIndustry"=>$helper->tdb_jb_get_col($colSize["desiredIndustry"], "select", "desiredindustry",$helper->tdb_jb_get_opt_select($gIndustries,"",$postdata["desiredindustry"]),TDB_LANG_DESIREDINDUSTRY,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["desiredIndustry"],$requiredSpan["desiredIndustry"]),
            "desiredLocation"=>$helper->tdb_jb_get_col($colSize["desiredLocation"], "select", "desiredlocation",$helper->tdb_jb_get_opt_select($gLocation,"",$postdata["desiredlocation"]),TDB_LANG_DESIREDLOCATION,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["desiredLocation"],$requiredSpan["desiredLocation"]),
            "linkedinUrl"=>$helper->tdb_jb_get_col($colSize["linkedin"], "text", "linkedin",$postdata["linkedin"],TDB_LANG_LINKEDIN,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["linkedin"],$requiredSpan["linkedin"]),
            "linkUrl"=>$helper->tdb_jb_get_col($colSize["urlRegister"], "text", "url",$postdata["url"],TDB_LANG_URL,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["url"],$requiredSpan["url"]),
            "facebookUrl"=>$helper->tdb_jb_get_col($colSize["facebook"], "text", "facebook",$postdata["facebook"],TDB_LANG_FACEBOOK,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["facebook"],$requiredSpan["facebook"]),
            "bCategory" => $bCategory,
            "desiredCategory"=>$helper->tdb_jb_get_col($colSize["desiredCategory"], "select", "desiredjobcategory",$helper->tdb_jb_get_opt_select($gCategories,"",$postdata["desiredjobcategory"]),TDB_LANG_DESIREDCATEGORY,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["desiredJobCategory"],$requiredSpan["desiredJobCategory"]),
            "noticePeriod"=>$helper->tdb_jb_get_col($colSize["noticedPeriod"], "text", "noticePeriod", $postdata["noticePeriod"],TDB_LANG_NOTICED_PERIOD,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["noticedPeriod"],$requiredSpan["noticedPeriod"]),
            "certification"=>$helper->tdb_jb_get_col($colSize["certificationText"], "textarea", "certification", $postdata["certification"],TDB_LANG_CERTIF,"tdb-jd-form-control tdb-jd-input tdb-jd-certification","","","","",$requiredContent["certification"],$requiredSpan["certification"],"","","","","",4),
            "nearestStation"=>$helper->tdb_jb_get_col($colSize["nearestStation"], "text", "neareststation", $postdata["neareststation"],TDB_LANG_NEARESTSTATION,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["nearestStation"],$requiredSpan["nearestStation"]),
            "referrer"=>$helper->tdb_jb_get_col($colSize["findUs"], "text", "referrer", $postdata["referrer"],TDB_LANG_REFFERERHOWDIDYOUEAR,"tdb-jd-form-control tdb-jd-input","","","","",$requiredContent["referrer"],$requiredSpan["referrer"]),
            "classRequired" => $requiredSpan["privacyPolicy"],
            "labelPolicy" => $privacyPolicyText,
            "privacyPolicyRequired" => $bPrivacyPolicy,
            "checkboxPolicy" => $helper->tdb_jb_get_col(1, "checkbox", "privacyPolicy", "", "", "tdb-jd-input-text-apply","","","","tdb-jd-input-label-apply","required","","","","tdb-jd-privacy-policy-check"),
            "postIdJob" => $postdata["idJob"],
            "postFamilyname" => $postdata["familyname"],
            "postGivenname" => $postdata["givenname"],
            "postGender" => $postdata["gender"],
            "searchUrl" => $postdata["hidden-search-url"],
            "applyGender" => $paramApply["gender"] ,
            "applyEmails" => $paramApply["emails"],
            "applyPhoneNumbers" => $paramApply["phoneNumbers"],
            "applyAddress" => $paramApply["address"],
            "applyBirthdate" => $paramApply["birthdate"],
            "applyNationality" => $paramApply["nationality"],
            "applyLanguages" => $paramApply["languages"],
            "applyLanguageCertifications" => $paramApply["languageCertifications"],
            "applyDesiredWage" => $paramApply["desiredWage"],
            "applyCurrentSalary" => $paramApply["currentSalary"],
            "applyCurrentSalaryBonus" => $paramApply["currentSalaryBonus"],
            "applyCurrentEmploymentDepartment" => $paramApply["currentEmploymentDepartment"],
            "applyCurrentEmploymentPosition" => $paramApply["currentEmploymentPosition"],
            "applyCurrentEmploymentCompany" => $paramApply["currentEmploymentCompany"],
            "applyDesiredEmploymentTypes" => $paramApply["desiredEmploymentTypes"],
            "applyPhoneType" => $paramApply["phoneType"],
            "applyEmailType" => $paramApply["emailsType"],
            "applyDesiredJobCategory" => $paramApply["desiredJobCategory"],
            "applyDesiredIndustry" => $paramApply["desiredIndustry"],
            "applyDesiredLocation" => $paramApply["desiredLocation"],
            "applyNeareststation" => $paramApply["neareststation"],
            "applyCertification" => $paramApply["certification"],
            "applyReferrer" => $paramApply["referrer"],
            "applyVisa" => $paramApply["visa"],
            "applyNoticePeriod" => $paramApply["noticePeriod"],
            "applyFacebook" => $paramApply["facebook"],
            "applyLinkedin" => $paramApply["linkedin"],
            "sourceDetail" => $sourceDetail,
            "applyUrl" => $paramApply["url"],
            "applyAttachment" => $paramApply["attachment"],
            "nbEmployement" => $nbEmployement,
            "nbSkill" => $nbSkill,
            "displayLanguage" => $displayLanguage,
            "displayCertification" => $displayCertification,
            "displayPhone" => $displayPhone,
            "displayEmail" => $displayEmail,
            "nbSocial" => $nbSocial,
            "nonce" => $nonce
        );
        $helper->tdb_jb_show_template(TDB_APPLY_FORM_TPL,$bodyArray);
    }
}
