<?php

namespace Jobsearch\Admin;

use Jobsearch\Helper;
use Jobsearch\Helper\Translation;

class Admin{
    // Show admin page set up
    function tdb_jb_set_adminPage() {
        global $gSource;
        global $gCategories;
        global $gGroupTags;

        $tagJobTitleList = array('h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5');

        $helper = new Helper();
        $adminHelper = new AdminHelper();

        $language = $helper->tdb_jb_get_current_language();
        $translation = new Translation();
        $nonceCheck = false ;
        // Field who should not be updated ( send button, hidden field)
        $noAutoUpdateField = array();
        $noAutoUpdateField["apiLink"] = "apiLink";
        $noAutoUpdateField["Link"] = "Link";
        $noAutoUpdateField["apiKey"] = "apiKey";
        $noAutoUpdateField["apiPage"] = "apiPage";
        $noAutoUpdateField["apiSearch"] = "apiSearch";
        $noAutoUpdateField["update"] = "update";
        $noAutoUpdateField["color"] = "color";
        $noAutoUpdateField["favorite"] = "favorite";
        $noAutoUpdateField["hiddenField"] = "hiddenField";
        $noAutoUpdateField["jobSearchSend"] = "jobSearchSend";
        $noAutoUpdateField["requiredField"] = "requiredField";
        $noAutoUpdateField["checkAllRequired"] = "checkAllRequired";
        $noAutoUpdateField["checkAllShow"] = "checkAllShow";
        $noAutoUpdateField["favoriteLanguage"] = "favoriteLanguage";
        $noAutoUpdateField["favoriteNationality"] = "favoriteNationality";
        $noAutoUpdateField["hiddenUpdtParam"] = "hiddenUpdtParam";
        $noAutoUpdateField["colorPicker"] = "colorPicker";
        $noAutoUpdateField["tdb_jb_frontend_post"] = "tdb_jb_frontend_post";

        // security check
        if(isset($_POST['tdb_jb_admin_post'])){
            if(wp_verify_nonce($_POST['tdb_jb_admin_post'], 'tdb_jb_admin_post') <> 1 || !current_user_can('edit_others_posts')){
                $nonceCheck = true;
            }
        }

        // Update all other parameter
        if (isset($_POST["hiddenUpdtParam"]) && $nonceCheck == false) {
            // Update translation language
            $adminHelper->tdb_jb_updt_database();

            $adminHelper->tdb_jb_clear_param_sql($helper->tdb_jb_sanitize("Param","text"));
            $adminHelper->tdb_jb_clear_param_sql($helper->tdb_jb_sanitize("Required","text"));
            $adminHelper->tdb_jb_clear_param_sql($helper->tdb_jb_sanitize('SortBy',"text"));
            $adminHelper->tdb_jb_clear_param_sql($helper->tdb_jb_sanitize("Apply","text"));
            $adminHelper->tdb_jb_clear_param_sql($helper->tdb_jb_sanitize("Video","text"));

            $arrayTemplate["text"]["submit_default"] = '';
            $arrayTemplate["subject"]["submit_default"] = '';

            /*Update file logo if it has*/
            $result = $adminHelper->tdb_jb_upload_image('socialLogo');
            if($result != false){
                $arrayParam["socialLogo"] = $result;
            }
            $result = $adminHelper->tdb_jb_upload_image('defaultImage');
            if($result != false){
                $arrayParam["defaultImage"] = $result;
            }


            //Update template param
            if(isset($_POST["submit_default"])){
                if($helper->tdb_jb_validate_data($_POST["submit_default"])){
                    $arrayTemplate["text"]["submit_default"] = $helper->tdb_jb_sanitize($_POST["submit_default"],'text');
                }
                if($helper->tdb_jb_validate_data($_POST["submit_template_subject_default"])){
                    $arrayTemplate["subject"]["submit_default"] = $helper->tdb_jb_sanitize($_POST["submit_template_subject_default"],'text');
                }
            }

            if ( function_exists('pll_the_languages') ) {
                $translationsSlugs = pll_the_languages(array('raw'=>1));
            } else {
                $translationsSlugs = null;
            }
            if(is_array($translationsSlugs)) {
                foreach ($translationsSlugs as $translationSlug) {
                    $slug = $translationSlug['slug'];
                    if(isset($_POST["submit_$slug"])){
                        if($helper->tdb_jb_validate_data($_POST["submit_$slug"])){
                            $arrayTemplate["text"]["submit_$slug"] =htmlentities(wpautop($helper->tdb_jb_sanitize($_POST["submit_$slug"], '')));
                        } else {
                            $arrayTemplate["text"]["submit_$slug"] = '';
                        }
                    } else {
                        $arrayTemplate["text"]["submit_$slug"] = '';
                    }

                    if(isset($_POST["submit_template_subject_$slug"])){
                        if($helper->tdb_jb_validate_data($_POST["submit_template_subject_$slug"])){
                            $arrayTemplate["subject"]["submit_$slug"] = $helper->tdb_jb_sanitize($_POST["submit_template_subject_$slug"],'text');
                        } else {
                            $arrayTemplate["subject"]["submit_$slug"] = '';
                        }
                    } else {
                        $arrayTemplate["subject"]["submit_$slug"] = '';
                    }
                }
            }

            //Delete then update all link from api
            $adminHelper->tdb_remove_link();
            $countApi = 1;

            while(isset($_POST["apiLink".$countApi])){
                $link = '';

                if($helper->tdb_jb_validate_data($_POST["apiLink".$countApi])){
                    if (stristr(substr($_POST["apiLink".$countApi], -1, 1),"/") == true) {
                        $link = $helper->tdb_jb_sanitize(substr($_POST["apiLink".$countApi], 0, (strlen($_POST["apiLink".$countApi]) - 1)),'text');
                    } else {
                        $link = $helper->tdb_jb_sanitize($_POST["apiLink".$countApi],'text');
                    }
                }

                $key = $adminHelper->tdb_jb_update_param($helper, "apiKey".$countApi, 'text');
                $page = $adminHelper->tdb_jb_update_param($helper, "apiPage".$countApi, 'text');
                $search = $adminHelper->tdb_jb_update_param($helper, "apiSearch".$countApi, 'text');

                $adminHelper->tdb_insert_link($countApi, $link, $key, $page, $search);
                $countApi++;
            }

            $arrayParam["nbPageToShow"] = $adminHelper->tdb_jb_update_param($helper, "nbPageToShow", 'text', 'int');
            $arrayParam["shortDescriptionMaxCharacters"] = $adminHelper->tdb_jb_update_param($helper, "shortDescriptionMaxCharacters", 'text', 'int');
            $arrayParam["nbJobToShowWidget"] = $adminHelper->tdb_jb_update_param($helper, "nbJobToShowWidget", 'text');
            $arrayParam["favoriteCurrency"] = $adminHelper->tdb_jb_update_param($helper, "favoriteCurrency", 'text', 'currency');
            $arrayParam["favoriteBasis"] = $adminHelper->tdb_jb_update_param($helper, "favoriteBasis", 'text', 'basis');
            $arrayParam["favoriteCertif"] = $adminHelper->tdb_jb_update_param($helper, "favoriteCertif", 'text');

            // field to show
            foreach ($_POST as $key => $value) {
                if (!isset($noAutoUpdateField[$key])) {
                    $arrayParam[$key] = $helper->tdb_jb_sanitize($value,'text');
                }
            }

            $arrayParam["searchHideReset"] = $adminHelper->tdb_jb_update_param($helper, "searchHideReset", 'text');
            $arrayParam["searchHideSalary"] = $adminHelper->tdb_jb_update_param($helper, "searchHideSalary", 'text');
            $arrayParam["searchHideLocation"] = $adminHelper->tdb_jb_update_param($helper, "searchHideLocation", 'text');
            $arrayParam["searchHideCurrency"] = $adminHelper->tdb_jb_update_param($helper, "searchHideCurrency", 'text');
            $arrayParam["searchHideBasis"] = $adminHelper->tdb_jb_update_param($helper, "searchHideBasis", 'text');
            $arrayParam["searchMaxSalary"] = $adminHelper->tdb_jb_update_param($helper, "searchMaxSalary", 'text');
            $arrayParam["searchHideMaxWage"] = $adminHelper->tdb_jb_update_param($helper, "searchHideMaxWage", 'text');
            $arrayParam["searchHideMinWage"] = $adminHelper->tdb_jb_update_param($helper, "searchHideMinWage", 'text');
            $arrayParam["searchHideLanguage"] = $adminHelper->tdb_jb_update_param($helper, "searchHideLanguage", 'text');
            $arrayParam["searchHideAddLanguage"] = $adminHelper->tdb_jb_update_param($helper, "searchHideAddLanguage", 'text');
            $arrayParam["searchAdvancedButton"] = $adminHelper->tdb_jb_update_param($helper, "searchAdvancedButton", 'text');
            $arrayParam["searchVisible"] = $adminHelper->tdb_jb_update_param($helper, "searchVisible", 'text');
            $arrayParam["searchShowOneCurrency"] = $adminHelper->tdb_jb_update_param($helper, "searchShowOneCurrency", 'text');
            $arrayParam["searchShowOneBasis"] = $adminHelper->tdb_jb_update_param($helper, "searchShowOneBasis", 'text');
            $arrayParam["displaySearchType"] = $adminHelper->tdb_jb_update_param($helper, "displaySearchType", 'text');
            $arrayParam["displaySearchCategory"] = $adminHelper->tdb_jb_update_param($helper, "displaySearchCategory", 'text');
            $arrayParam["displaySearchIndustry"] = $adminHelper->tdb_jb_update_param($helper, "displaySearchIndustry", 'text');
            $arrayParam["displayTagGroup1"] = $adminHelper->tdb_jb_update_param($helper, "displayTagGroup1", 'text');
            $arrayParam["displayTagGroup2"] = $adminHelper->tdb_jb_update_param($helper, "displayTagGroup2", 'text');
            $arrayParam["displayTagGroup3"] = $adminHelper->tdb_jb_update_param($helper, "displayTagGroup3", 'text');
            $arrayParam["displayTagGroup4"] = $adminHelper->tdb_jb_update_param($helper, "displayTagGroup4", 'text');
            $arrayParam["searchFieldIsMultiple"] = $adminHelper->tdb_jb_update_param($helper, "searchFieldIsMultiple", 'text');
            $arrayParam["rewriteUrl"] = $adminHelper->tdb_jb_update_param($helper, "rewriteUrl", 'text');
            $arrayParam["belowLanguageCheck"] = $adminHelper->tdb_jb_update_param($helper, "belowLanguageCheck", 'text');
            $arrayParam["reverseLanguageSkillCheck"] = $adminHelper->tdb_jb_update_param($helper, "reverseLanguageSkillCheck", 'text');
            $arrayParam["descriptionCleanedCheck"] = $adminHelper->tdb_jb_update_param($helper, "descriptionCleanedCheck", 'text');
            $arrayParam["searchPosition"] = $adminHelper->tdb_jb_update_param($helper, "searchPosition", 'text');
            $arrayParam["templateActivateCheck"] = $adminHelper->tdb_jb_update_param($helper, "templateActivateCheck", 'text');

            $arrayParam["attachmentStorageLocalEnableCheck"] = $adminHelper->tdb_jb_update_param($helper, "attachmentStorageLocalEnableCheck", 'text');

            $arrayParam["ccTemplate"] = $adminHelper->tdb_jb_update_clean_param($helper, "ccTemplate", 'text');
            $arrayParam["fromTemplate"] = $adminHelper->tdb_jb_update_clean_param($helper, "fromTemplate", 'text');
            $arrayParam["bccTemplate"] = $adminHelper->tdb_jb_update_clean_param($helper, "bccTemplate", 'text');
            $arrayParam["privacyPolicyText"] = $adminHelper->tdb_jb_update_clean_param($helper, "privacyPolicyText", 'text');
            $arrayParam["urlApply"] = $adminHelper->tdb_jb_update_clean_param($helper, "urlApplyText", 'text');
            $arrayParam["recaptchaKey"] = $adminHelper->tdb_jb_update_clean_param($helper, "recaptchaKey", 'text');
            $arrayParam["recaptchaSecret"] = $adminHelper->tdb_jb_update_clean_param($helper, "recaptchaSecret", 'text');

            $arrayParam["templateUsed"] = $adminHelper->tdb_jb_update_param($helper, "templateUsed", 'text');

            $arrayParam["favoriteLanguageContent"] = $adminHelper->tdb_jb_update_multiple_textarea_param($helper, "favoriteLanguageContent", 'text', false);
            $arrayParam["favoriteLanguageSearchContent"] = $adminHelper->tdb_jb_update_multiple_textarea_param($helper, "favoriteLanguageSearchContent", 'text', false);
            $arrayParam["favoriteNationalityContent"] = $adminHelper->tdb_jb_update_multiple_textarea_param($helper, "favoriteNationalityContent", 'text');
            $arrayParam["favoriteNationalityContentOthers"] = $adminHelper->tdb_jb_update_multiple_textarea_param($helper, "favoriteNationalityContentOthers", 'text');
            $arrayParam["favoriteCountryContent"] = $adminHelper->tdb_jb_update_multiple_textarea_param($helper, "favoriteCountryContent", 'text');

            $arrayParam["displayCategories"] = $adminHelper->tdb_jb_update_category_param($helper, "displayCategories");
            $arrayParam["excludedCategories"] = $adminHelper->tdb_jb_update_category_param($helper, "excludedCategories");

//            $arrayParam["BtnSubmitBackground"] = $adminHelper->tdb_jb_update_css_param($helper, "BtnSubmitBackground", 'text');
//            $arrayParam["BtnSubmitFont"] = $adminHelper->tdb_jb_update_css_param($helper, "BtnSubmitFont", 'text');
//            $arrayParam["BtnMoreBackground"] = $adminHelper->tdb_jb_update_css_param($helper, "BtnMoreBackground", 'text');
//            $arrayParam["BtnMoreFont"] = $adminHelper->tdb_jb_update_css_param($helper, "BtnMoreFont", 'text');
//            $arrayParam["linkFont"] = $adminHelper->tdb_jb_update_css_param($helper, "linkFont", 'text');

            $arrayParam["videoVideo"] = $adminHelper->tdb_jb_update_param($helper, "videoVideo", 'text');
            $arrayParam["summaryVideo"] = $adminHelper->tdb_jb_update_param($helper, "summaryVideo", 'text');
            $arrayParam["defaultImageCheckVideo"] = $adminHelper->tdb_jb_update_param($helper, "defaultImageCheckVideo", 'text');

            // update all param
            $adminHelper->tdb_jb_updt_param($arrayParam);
            // update tpl for mailing, content and subject
            $adminHelper->tdb_jb_updt_tpl($arrayTemplate['text'], 'Template');
            // update sujet
            $adminHelper->tdb_jb_updt_tpl($arrayTemplate['subject'], 'TemplateSubject');
        }

        // Get all parameter to show on admin page
        $attachmentStorageLocalEnable = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'attachmentStorageLocalEnable', 'sValue', 'sName');
        $sourceType = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'sourceType', 'sValue', 'sName');
        $tagGroup1 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'tagGroup1', 'sValue', 'sName');
        $tagGroup2 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'tagGroup2', 'sValue', 'sName');
        $tagGroup3 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'tagGroup3', 'sValue', 'sName');
        $tagGroup4 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'tagGroup4', 'sValue', 'sName');
        $jobTitleTag = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'jobTitleTag', 'sValue', 'sName');
//        $submitBackgroung = str_replace("#","",$helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'BtnSubmitBackground', 'sValue', 'sName'));
//        $submitFont = str_replace("#","",$helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'BtnSubmitFont', 'sValue', 'sName'));
//        $btnMoreBackground = str_replace("#","",$helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'BtnMoreBackground', 'sValue', 'sName'));
//        $btnMoreFont = str_replace("#","",$helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'BtnMoreFont', 'sValue', 'sName'));
//        $linkFont = str_replace("#","",$helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'linkFont', 'sValue', 'sName'));
        $widgetChoosenMaximumDateJob = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'widgetChoosenMaximumDateJob', 'sValue', 'sName');
        $widgetChoosenCategory = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'widgetChoosenCategory', 'sValue', 'sName');
        $certifFavorite = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'favoriteCertif', 'sValue', 'sName');
        $currencyFavorite = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'favoriteCurrency', 'sValue', 'sName');
        $basisFavorite = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'favoriteBasis', 'sValue', 'sName');
        $autoNationalityCountry = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'autoNationalityCountry', 'sValue', 'sName');
        $templateUsed = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'templateUsed', 'sValue', 'sName');
        $privacyPolicyText = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'privacyPolicyText', 'sValue', 'sName');
        $urlApplyText = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'urlApplyText', 'sValue', 'sName');
        $recaptchaKey = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaKey', 'sValue', 'sName');
        $recaptchaSecret = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'recaptchaSecret', 'sValue', 'sName');
        $socialLogo = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'socialLogo', 'sValue', 'sName');
        $defaultImage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'defaultImage', 'sValue', 'sName');
        $nbPageToShow = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'nbPageToShow', 'sValue', 'sName');
        $shortDescriptionMaxCharacters = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'shortDescriptionMaxCharacters', 'sValue', 'sName');
        $searchPosition = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchPosition', 'sValue', 'sName');
        $nbJobToShowWidget = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'nbJobToShowWidget', 'sValue', 'sName');
        $searchHideCurrency = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideCurrency', 'sValue', 'sName');
        $searchHideBasis = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideBasis', 'sValue', 'sName');
        $searchMaxSalary = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchMaxSalary', 'sValue', 'sName');
        $searchHideMinWage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideMinWage', 'sValue', 'sName');
        $searchHideLanguage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideLanguage', 'sValue', 'sName');
        $searchHideAddLanguage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideAddLanguage', 'sValue', 'sName');
        $searchHideMaxWage = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideMaxWage', 'sValue', 'sName');
        $searchHideSalary = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideSalary', 'sValue', 'sName');
        $searchHideLocation = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideLocation', 'sValue', 'sName');
        $searchHideReset = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchHideReset', 'sValue', 'sName');
        $searchAdvancedButton = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchAdvancedButton', 'sValue', 'sName');
        $searchVisible = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchVisible', 'sValue', 'sName');
        $searchShowOneCurrency = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchShowOneCurrency', 'sValue', 'sName');
        $searchShowOneBasis = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchShowOneBasis', 'sValue', 'sName');
        $displaySearchType = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displaySearchType', 'sValue', 'sName');
        $displayTagGroup1 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displayTagGroup1', 'sValue', 'sName');
        $displayTagGroup2 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displayTagGroup2', 'sValue', 'sName');
        $displayTagGroup3 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displayTagGroup3', 'sValue', 'sName');
        $displayTagGroup4 = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displayTagGroup4', 'sValue', 'sName');
        $displaySearchIndustry = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displaySearchIndustry', 'sValue', 'sName');
        $displaySearchCategory = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'displaySearchCategory', 'sValue', 'sName');
        $searchFieldIsMultiple = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'searchFieldIsMultiple', 'sValue', 'sName');
        $rewriteUrl = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'rewriteUrl', 'sValue', 'sName');
        $templateActivate = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'templateActivateCheck', 'sValue', 'sName');
        $fromTemplate = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'fromTemplate', 'sValue', 'sName');
        $ccTemplate = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'ccTemplate', 'sValue', 'sName');
        $bccTemplate = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'bccTemplate', 'sValue', 'sName');

        $defaultImagePath = $helper->tdb_jb_get_wp_image_url($defaultImage);

        $displayCategoriesContent = $helper->tdb_jb_get_array_category('displayCategories');
        $excludedCategoriesContent = $helper->tdb_jb_get_array_category('excludedCategories');
        $displayCategories = $helper->tdb_jb_set_content_category($displayCategoriesContent);
        $excludedCategories = $helper->tdb_jb_set_content_category($excludedCategoriesContent);

        $socialLogoPath = $helper->tdb_jb_get_wp_image_url($socialLogo);
        if($socialLogo <> '' AND $socialLogoPath == false){
            $adminHelper->tdb_jb_updt_param(['socialLogo' => '']);
            $socialLogoPath = '';
        }

        if($defaultImage <> '' AND $defaultImagePath == false){
            $adminHelper->tdb_jb_updt_param(['defaultImage' => '']);
            $defaultImagePath = '';
        }

        $otherLanguageHtmlTemplate = '';
        $submitHtmlTemplate = '';
        //generate default
            // get The language from plugin polylang
            //get polylang slug for rewrite rule if is set
        if ( function_exists('pll_the_languages') ) {
            $translationsSlugs = pll_the_languages(array('raw'=>1));
            if(is_array($translationsSlugs)) {
                // generate tab
                $otherLanguageHtmlTemplate .= '<h4>'.TDB_LANG_EMAILTEMPLATE.'</h4>';
                $otherLanguageHtmlTemplate .= '<h5>'.TDB_LANG_SHORTCODEEMAILTITLE.' : </h5>';
                $otherLanguageHtmlTemplate .= '<p>'.TDB_LANG_SHORTCODEEMAIL.'</p>';
                $otherLanguageHtmlTemplate .= '<div class="tdb-jd-tab-template" >';
                $class = 'active';
                foreach ($translationsSlugs as $translationSlug) {
                    $slug = $translationSlug['slug'];
                    $otherLanguageHtmlTemplate .= "<a class='tdb-jd-template-tablinks $class' id='tdb-jd-$slug'>$slug</a>";
                    $class = '';
                }
                $otherLanguageHtmlTemplate .= '</div>';
                $styleBlock = 'style="display: inherit;"';
                $styleNone = 'style="display: none;"';
                $class = 'active';
                $count = 1;
                foreach ($translationsSlugs as $translationSlug) {
                    if($count == 1){
                        $style = $styleBlock;
                    } else {
                        $style = $styleNone;
                    }
                    $slug = $translationSlug['slug'];
                    $valueText = $helper->tdb_jb_get_template_parameters(TDB_TABLE_TEMPLATE, 'submitTemplate', 'sValue', 'sName', $slug);
                    $valueSubject = $helper->tdb_jb_get_template_parameters(TDB_TABLE_TEMPLATE, 'submitTemplateSubject', 'sValue', 'sName', $slug);
                    $otherLanguageHtmlTemplate .= "<div id ='tdb-jd-tab-link-template-$slug' class='tdb-jd-tabcontent-template $class' $style>";
                    $otherLanguageHtmlTemplate .= $adminHelper->tdb_jd_generate_template_field('submit', $slug, $valueText, $valueSubject);
                    $otherLanguageHtmlTemplate .= '</div>';
                    $class = '';
                    $count++;
                }
            }
        } else {
            $defaultTemplate = $helper->tdb_jb_get_template_parameters(TDB_TABLE_TEMPLATE, 'submitTemplate', 'sValue', 'sName', 'default');
            $defaultSubject = $helper->tdb_jb_get_template_parameters(TDB_TABLE_TEMPLATE, 'submitTemplateSubject', 'sValue', 'sName', 'default');
            $submitHtmlTemplate = $adminHelper->tdb_jd_generate_template_field('submit','default',$defaultTemplate, $defaultSubject);
        }


        // Field who have to be shown on detail page (xxxParam)
        $param = $helper->tdb_jb_get_list_parameters();
        $paramVideo = $helper->tdb_jb_get_list_video_parameters();
        // Field who have to be shown on apply form (xxxApply)
        $apply = $helper->tdb_jb_get_list_apply();
        // Field who have to be shown on search filter (xxxFilter)
        $sortBy = $helper->tdb_jb_get_list_sort_by();
        // Mandatory field on apply form (xxxRequired)
        $paramRequired = $helper->tdb_jb_get_list_required_fields();
        $paramColsized = $helper->tdb_jb_get_list_col_resized_register_fields();

        $favoriteLanguageArray = $helper->tdb_jb_get_array_favorite_language();
        $favoriteLanguageContent = $helper->tdb_jb_set_content_favorite_language($favoriteLanguageArray);
        $favoriteNationalityArray = $helper->tdb_jb_get_array_favorite_nationality('favoriteNationalityContent');
        $favoriteCountryContentArray = $helper->tdb_jb_get_array_favorite_nationality('favoriteCountryContent');
        $favoriteNationalityOtherArray = $helper->tdb_jb_get_array_favorite_nationality('favoriteNationalityContentOthers');
        $favoriteNationalityContent = $helper->tdb_jb_set_content_favorite_Nationality($favoriteNationalityArray);
        $favoriteCountryContent = $helper->tdb_jb_set_content_favorite_Nationality($favoriteCountryContentArray);
        $favoriteNationalityContentOthers = $helper->tdb_jb_set_content_favorite_Nationality($favoriteNationalityOtherArray);
        $favoriteLanguageSearchArray = $helper->tdb_jb_get_array_favorite_language_search();
        $favoriteLanguageSearchContent = $helper->tdb_jb_set_content_favorite_language($favoriteLanguageSearchArray);

        if ($searchPosition <> 1) {
            $searchPosition = 0;
        }

        if($searchHideCurrency <> ''){
            $searchHideCurrency = 'checked';
        }

        if($searchHideBasis <> ''){
            $searchHideBasis = 'checked';
        }

        if($searchMaxSalary <> ''){
            $searchMaxSalary = 'checked';
        }

        if($searchHideMinWage <> ''){
            $searchHideMinWage = 'checked';
        }

        if($searchHideLanguage <> ''){
            $searchHideLanguage = 'checked';
        }

        if($searchHideAddLanguage <> ''){
            $searchHideAddLanguage = 'checked';
        }

        if($searchHideMaxWage <> ''){
            $searchHideMaxWage = 'checked';
        }

        if($searchHideSalary <> ''){
            $searchHideSalary = 'checked';
        }

        if($searchHideLocation <> ''){
            $searchHideLocation = 'checked';
        }

        if($searchHideReset <> ''){
            $searchHideReset = 'checked';
        }

        if($searchAdvancedButton <> ''){
            $searchAdvancedButton = 'checked';
        }

        if($searchVisible <> ''){
            $searchVisible = 'checked';
        }

        if($searchShowOneCurrency <> ''){
            $searchShowOneCurrency = 'checked';
        }

        if($searchShowOneBasis <> ''){
            $searchShowOneBasis = 'checked';
        }

        if($displaySearchType <> ''){
            $displaySearchType = 'checked';
        }

        if($displayTagGroup1 <> ''){
            $displayTagGroup1 = 'checked';
        }

        if($displayTagGroup2 <> ''){
            $displayTagGroup2 = 'checked';
        }

        if($displayTagGroup3 <> ''){
            $displayTagGroup3 = 'checked';
        }

        if($displayTagGroup4 <> ''){
            $displayTagGroup4 = 'checked';
        }

        if($displaySearchIndustry <> ''){
            $displaySearchIndustry = 'checked';
        }

        if($displaySearchCategory <> ''){
            $displaySearchCategory = 'checked';
        }

        if($searchFieldIsMultiple <> ''){
            $searchFieldIsMultiple = 'checked';
        }

        if($rewriteUrl <> ''){
            $rewriteUrl = 'checked';
        }

        if($templateActivate <> ''){
            $templateActivate = 'checked';
        }

        // add add button
        // delete button
        // generate tab html for api
        $apiTab = $adminHelper->tdb_jb_get_api_data(TDB_TABLE_API, 'nId', 'sName');
        $apiTabHtml = "<div id ='navigationApi' class='tdb-jd-navigation-api'>";
        $checked = "checked";
        $compteurApi = 1;
        foreach($apiTab as $id => $name){
                $apiTabHtml .= "<input id='tabApi$id' type='radio' name='tabApi$id' class='tdb-jd-input-tab' $checked>";
                $apiTabHtml .= "<label for='tabApi$id' class='tdb-jd-label-tab'>$name</label>";
                $checked = "";
                $compteurApi = $id;
        }
        $compteurApi ++;
        $apiTabHtml .= "<input id='tabApi$compteurApi' type='radio' name='tabApi0' class='tdb-jd-input-tab'>";
        $apiTabHtml .= "<label for='tabApi$compteurApi' class='tdb-jd-label-tab tdb-jd-add-api'>+</label>";

        if($compteurApi >2){
            $apiTabHtml .= "<input id='tabApi0' type='radio' name='tabApi0' class='tdb-jd-input-tab '>";
            $apiTabHtml .= "<label for='tabApi0' class='tdb-jd-label-tab tdb-jd-del-api'>-</label>";
        }
        $apiTabHtml .= "</div>";

        $belowLanguageCheck = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'belowLanguageCheck', 'sValue', 'sName');
        if($belowLanguageCheck <> ''){
            $belowLanguageCheck = 'checked';
        }
        $reverseLanguageSkillCheck = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'reverseLanguageSkillCheck', 'sValue', 'sName');
        if($reverseLanguageSkillCheck <> ''){
            $reverseLanguageSkillCheck = 'checked';
        }

        $descriptionCleanedCheck = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'descriptionCleanedCheck', 'sValue', 'sName');
        if($descriptionCleanedCheck <> ''){
            $descriptionCleanedCheck = 'checked';
        }

        $attachmentStorageLocalEnableCheck = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'attachmentStorageLocalEnableCheck', 'sValue', 'sName');
        if($attachmentStorageLocalEnableCheck <> ''){
            $attachmentStorageLocalEnableCheck = 'checked';
        }

        //Get list of template available
        $templateArray = [];
        $nbTemplate = 1 ;
        $plugin =  plugin_dir_path( __DIR__ );
        $repSearch = $plugin . "templates/search/searchForm";
        $repList = $plugin . "templates/job/list/jobListBody";
        $repDetail = $plugin . "templates/job/detail/jobDetailBody";
        $repApply = $plugin . "templates/apply/form/jobApplyBody";
        $repResult = $plugin . "templates/apply/result/jobResultBody";

        $i = 1;
        while(file_exists($repSearch.$i.".tpl") || file_exists($repList.$i.".tpl")
            || file_exists($repDetail.$i.".tpl") || file_exists($repApply.$i.".tpl")
            || file_exists($repResult.$i.".tpl")){
            $nbTemplate = $i;
            $i++;
        }

        $templateArray[1] = TDB_LANG_DEFAULT;

        if($nbTemplate >1){
            for($i = 2 ; $i<= $nbTemplate; $i++){
                $templateArray[$i] = TDB_LANG_TEMPLATE . ($i - 1);
            }
        }

        $templatePosition[1] = TDB_LANG_ATBOTTOM;
        $templatePosition[0] = TDB_LANG_ATTOP;
        $maxLengthColor = 6;

        $givenNameRequired = $helper->tdb_jb_get_col(2, "checkbox", "givenNameReq", "checked",  TDB_LANG_GIVENNAME, "tdb-jd-input-text tdb-jd-input","","","","","","","","","","","disabled");
        $familyNameRequired = $helper->tdb_jb_get_col(2, "checkbox", "familyReq", "checked",  TDB_LANG_FAMILYNAME, "tdb-jd-input-text tdb-jd-input","","","","","","","","","","","disabled");
        $searchHideSalaryCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchHideSalary", $searchHideSalary, TDB_LANG_HIDESALARYSEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchHideLocationCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchHideLocation", $searchHideLocation, TDB_LANG_HIDELOCATIONSEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchHideResetCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchHideReset", $searchHideReset, TDB_LANG_HIDERESET, "tdb-jd-input-text tdb-jd-input");
        $searchHideBasisCheck  = $helper->tdb_jb_get_col(2, "checkbox", "searchHideBasis", $searchHideBasis, TDB_LANG_HIDEBASISSEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchMaxSalaryCheck  = $helper->tdb_jb_get_col(2, "checkbox", "searchMaxSalary", $searchMaxSalary, TDB_LANG_SEARCHMAXSALARY, "tdb-jd-input-text tdb-jd-input");
        $searchHideCurrencyCheck  = $helper->tdb_jb_get_col(2, "checkbox", "searchHideCurrency", $searchHideCurrency, TDB_LANG_HIDECURRENCYSEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchHideMaxWageCheck  = $helper->tdb_jb_get_col(2, "checkbox", "searchHideMaxWage", $searchHideMaxWage, TDB_LANG_HIDEMAXWAGESEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchHideMinWageCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchHideMinWage", $searchHideMinWage, TDB_LANG_HIDEMINWAGESEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchHideLanguageCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchHideLanguage", $searchHideLanguage, TDB_LANG_HIDELANGUAGE, "tdb-jd-input-text tdb-jd-input");
        $searchHideAddLanguageCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchHideAddLanguage", $searchHideAddLanguage, TDB_LANG_HIDEADDLANGUAGE, "tdb-jd-input-text tdb-jd-input");
        $searchAdvancedButtonCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchAdvancedButton", $searchAdvancedButton, TDB_LANG_ADVANCEDSEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchVisibleCheck = $helper->tdb_jb_get_col(2, "checkbox", "searchVisible", $searchVisible, TDB_LANG_HIDESEARCH, "tdb-jd-input-text tdb-jd-input");
        $searchShowOneCurrencyCheck = $helper->tdb_jb_get_col(4, "checkbox", "searchShowOneCurrency", $searchShowOneCurrency, TDB_LANG_USECURRENCY, "tdb-jd-input-text tdb-jd-input");
        $searchShowOneBasisCheck = $helper->tdb_jb_get_col(4, "checkbox", "searchShowOneBasis", $searchShowOneBasis, TDB_LANG_USEBASIS, "tdb-jd-input-text tdb-jd-input");
        $displaySearchTypeCheck = $helper->tdb_jb_get_col(4, "checkbox", "displaySearchType", $displaySearchType, TDB_LANG_DISPLAYTYPE, "tdb-jd-input-text tdb-jd-input");
        $displayTagGroup1Check = $helper->tdb_jb_get_col(4, "checkbox", "displayTagGroup1", $displayTagGroup1, TDB_LANG_DISPLAY, "tdb-jd-input-text tdb-jd-input");
        $displayTagGroup2Check = $helper->tdb_jb_get_col(4, "checkbox", "displayTagGroup2", $displayTagGroup2, TDB_LANG_DISPLAY, "tdb-jd-input-text tdb-jd-input");
        $displayTagGroup3Check = $helper->tdb_jb_get_col(4, "checkbox", "displayTagGroup3", $displayTagGroup3, TDB_LANG_DISPLAY, "tdb-jd-input-text tdb-jd-input");
        $displayTagGroup4Check = $helper->tdb_jb_get_col(4, "checkbox", "displayTagGroup4", $displayTagGroup4, TDB_LANG_DISPLAY, "tdb-jd-input-text tdb-jd-input");
        $displaySearchIndustryCheck = $helper->tdb_jb_get_col(4, "checkbox", "displaySearchIndustry", $displaySearchIndustry, TDB_LANG_DISPLAYINDUSTRY, "tdb-jd-input-text tdb-jd-input");
        $displaySearchCategoryCheck = $helper->tdb_jb_get_col(4, "checkbox", "displaySearchCategory", $displaySearchCategory, TDB_LANG_DISPLAYCATEGORY, "tdb-jd-input-text tdb-jd-input");
        $searchFieldIsMultipleCheck = $helper->tdb_jb_get_col(4, "checkbox", "searchFieldIsMultiple", $searchFieldIsMultiple, TDB_LANG_SEARCHFIELDMULTIPLE, "tdb-jd-input-text tdb-jd-input");
        $rewriteUrlCheck = $helper->tdb_jb_get_col(4, "checkbox", "rewriteUrl", $rewriteUrl, TDB_LANG_REWRITEURL, "tdb-jd-input-text tdb-jd-input");
        $templateActivateCheck = $helper->tdb_jb_get_col(4, "checkbox", "templateActivateCheck", $templateActivate, TDB_LANG_TEMPLATEACTIVATE, "tdb-jd-input-text tdb-jd-input");
        $belowLanguageCheckHtml = $helper->tdb_jb_get_col(4, "checkbox", "belowLanguageCheck", $belowLanguageCheck, TDB_LANG_BELOWADMIN, "tdb-jd-input-text tdb-jd-input");
        $reverseLanguageSkillCheckHtml = $helper->tdb_jb_get_col(4, "checkbox", "reverseLanguageSkillCheck", $reverseLanguageSkillCheck, TDB_LANG_REVERSE_LANGUAGE_SKILL, "tdb-jd-input-text tdb-jd-input");
        $descriptionCleanedHtml = $helper->tdb_jb_get_col(4, "checkbox", "descriptionCleanedCheck", $descriptionCleanedCheck, TDB_LANG_DESCRIPTION_CLEANED, "tdb-jd-input-text tdb-jd-input");
        $attachmentStorageLocalEnableHtml = $helper->tdb_jb_get_col(4, "checkbox", "attachmentStorageLocalEnableCheck", $attachmentStorageLocalEnableCheck, TDB_LANG_ATTACHMENT_STORAGE_LOCAL_ENABLE, "tdb-jd-input-text tdb-jd-input");

        $displayLink = 'style = "display:none"';

        $countLink = 1;
        $htmlLink = "";

        $shortcodeContent = TDB_LANG_SHORTCODECONTENT;

        $apiLinks = $helper->tdb_jb_get_linkApi();
        $apiKeys = $adminHelper->tdb_jb_get_KeyApi();
        $apiPages = $helper->tdb_jb_get_jobPage();
        $apiSearch = $adminHelper->tdb_jb_get_SearchApi();
        if(count($apiLinks) >= 1) {
            foreach($apiLinks as $api => $value){
                if(isset($apiLinks[$countLink])){
                    $link = $apiLinks[$countLink];
                } else {
                    $link = '';
                }
                if(isset($apiKeys[$countLink])){
                    $key = $apiKeys[$countLink];
                } else {
                    $key = '';
                }
                if(isset($apiPages[$countLink])){
                    $page = $apiPages[$countLink];
                } else {
                    $page = '';
                }
                if(isset($apiSearch[$countLink])){
                    $search = $apiSearch[$countLink];
                } else {
                    $search = '';
                }
                $htmlLink .= $adminHelper->tdb_jd_generate_api_field($countLink,$link,$key,$page,$search);
                $countLink ++;
            }
        }

        if($countLink > 2){
            $displayLink = '';
        }
        $nonce = wp_nonce_field( 'tdb_jb_admin_post', 'tdb_jb_admin_post', false, true);
        $adminArray = array(
            "langAddLink" => TDB_LANG_ADDLINK,
            "langRemove" => TDB_LANG_REMOVE,
            "langTabLink" => TDB_LANG_TABLINK,
            "langTabJob" => TDB_LANG_TABJOB,
            "langTabDesign" => TDB_LANG_TABDESIGN,
            "langTabSearch" => TDB_LANG_TABSEARCH,
            "langTabDetail" => TDB_LANG_TABDETAIL,
            "langTabApply" => TDB_LANG_TABAPPLY,
            "langTabTemplate" => TDB_LANG_TABTEMPLATE,
            "langTabWidget" => TDB_LANG_TABWIDGET,
            "langDefaultImage" => TDB_LANG_DEFAULTIMAGE,
            "langTabShortcode" => TDB_LANG_TABSHORTCODE,
            "langVideoShowExplain" => TDB_LANG_VIDEOSHOWEXPLAIN,
            "langParameter" => TDB_LANG_PARAMETERS,
            "langUpdateDatabase" => TDB_LANG_FETCHLATEST,
            "langUpdateParameter" => TDB_LANG_UPDATEAPIUPPER,
            "langHideSalary" => TDB_LANG_HIDESALARY,
            "langPrivacyPolicy" => TDB_LANG_PRIVACYPOLICYLINK,
            "langUrlApply" => TDB_LANG_URL_APPLY_TEXT,
            "langRecaptchaKey" => TDB_LANG_RECAPTCHAKEY,
            "langRecaptchaSecret" => TDB_LANG_RECAPTCHASECRET,
            "langRecaptchaExplain" => TDB_LANG_RECAPTCHAEXPLAIN,
            "langdisplaySearchType" => TDB_LANG_DISPLAYTYPE,
            "langdisplaySearchCategory" => TDB_LANG_DISPLAYCATEGORY,
            "langdisplaySearchIndustry" => TDB_LANG_DISPLAYINDUSTRY,
            "langTemplateApplicant" => TDB_LANG_TEMPLATEAPPLICANT,
            "langDisplayField" => TDB_LANG_DISPLAYIFIELD,
            "langHideField" => TDB_LANG_HIDEFIELD,
            "langTabSocial" => TDB_LANG_SOCIAL,
            "langColSize" => TDB_LANG_COLSIZE,
            "langExplainColSize" => TDB_LANG_EXPLAINCOLSIZE,
            "colsizefield" => $adminHelper->tdb_jb_get_colsized_field($paramColsized),
            "langsearchFieldIsMultiple" => TDB_LANG_SEARCHFIELDMULTIPLE,
            "langRewriteUrl" => TDB_LANG_REWRITEURL,
            "langTemplateActivate" => TDB_LANG_TEMPLATEACTIVATE,
            "langSortBy" => TDB_LANG_SORTBY,
            "langExplainTemplate" => TDB_LANG_EMAILTEMPLATEEXPLAIN,
            "apiLink" => $htmlLink,
            "nbPageToShow" => $helper->tdb_jb_get_col(4, "number", "nbPageToShow", $nbPageToShow, TDB_LANG_JOBTOSHOWPERPAGE, "tdb-jd-input-text tdb-jd-input"),
            "shortDescriptionMaxCharacters" => $helper->tdb_jb_get_col(4, "number", "shortDescriptionMaxCharacters", $shortDescriptionMaxCharacters, TDB_LANG_NBCHARACTERS_DESCRIPTION, "tdb-jd-input-text tdb-jd-input"),
            "nbJobToShowWidget" => $helper->tdb_jb_get_col(4, "number", "nbJobToShowWidget", $nbJobToShowWidget, TDB_LANG_NBJOBTOSHOW, "tdb-jd-input-text tdb-jd-input"),
            "langContentUpdate" => TDB_LANG_CUSTOMIZEDESIGN,
            "langPreferedListOption" => TDB_LANG_CHOOSEFIRSTOPTION,
            "langApplyExplain" => TDB_LANG_CHOOSESHOWNFIELD,
            "langAttention" => TDB_LANG_ATTENTION,
            "langAttachmentStorage" => TDB_LANG_ATTACHMENT_STORAGE,
            "langAttachmentStorageLocalEnable" => TDB_LANG_ATTACHMENT_STORAGE_LOCAL_ENABLE,
            "langAttachmentStorageLocalWarning" => TDB_LANG_ATTACHMENT_STORAGE_LOCAL_WARNING,
            "langMandatoryExplain" => TDB_LANG_CHOOSEMANDATORY,
            "langElemShowExplain" => TDB_LANG_CHOOSEELEMENTS,
            "langButton" => TDB_LANG_BUTTON,
            "langFont" => TDB_LANG_LINK,
            "langOther" => TDB_LANG_OPTION_OTHER,
            "langWidget" => TDB_LANG_WIDGET,
            "langShortcode" => TDB_LANG_SHORTCODE,
            "langTagJobTitle" => TDB_LANG_TAGJOBTITLE,
            "langNationalityCountryVisa" => TDB_LANG_COUNTRY_VISA,
            "colorPicker" => $helper->tdb_jb_get_col(3, "color", "colorPicker", "", TDB_LANG_PICKCOLOR,"tdb-jd-color tdb-jd-input-text tdb-jd-input"),
//            "submitBackground" => $helper->tdb_jb_get_col(3, "text", "BtnSubmitBackground", $submitBackgroung, TDB_LANG_BUTTONSUBMITBACKGROUNDCOLOR,"tdb-jd-input-text tdb-jd-input","","","","","","","","","",$maxLengthColor),
//            "submitFont" => $helper->tdb_jb_get_col(3, "text", "BtnSubmitFont", $submitFont, TDB_LANG_BUTTONSUBMITFONTCOLOR, "tdb-jd-input-text tdb-jd-input","","","","","","","","","",$maxLengthColor),
//            "btnMoreBackground" => $helper->tdb_jb_get_col(3, "text", "BtnMoreBackground", $btnMoreBackground, TDB_LANG_MOREBACKGROUNDCOLOR, "tdb-jd-input-text tdb-jd-input","","","","","","","","","",$maxLengthColor),
//            "btnMoreFont" => $helper->tdb_jb_get_col(3, "text", "BtnMoreFont", $btnMoreFont, TDB_LANG_MOREFONTCOLOR, "tdb-jd-input-text tdb-jd-input","","","","","","","","","",$maxLengthColor),
//            "linkFont" => $helper->tdb_jb_get_col(3, "text", "linkFont", $linkFont, TDB_LANG_LINKFONTCOLOR, "tdb-jd-input-text tdb-jd-input","","","","","","","","","",$maxLengthColor),
            "langUpdateElemShow" => TDB_LANG_JOBINFORMATIONVISIBILITY,
            "langUpdateVideoShow" => TDB_LANG_JOBVIDEOVISIBILITY,
            "langUpdateTranslation" => TDB_LANG_TRANSLATION,
            "checkAllShow" => $helper->tdb_jb_get_col(2, "checkbox", "checkAllShow", "", TDB_LANG_CHECKALL, "tdb-jd-input-text tdb-jd-input"),
            "checkAllApply" => $helper->tdb_jb_get_col(2, "checkbox", "checkAllApply", "", TDB_LANG_CHECKALL, "tdb-jd-input-text tdb-jd-input"),
            "getShowField" => $adminHelper->tdb_jb_get_show_field($param),
            "getVideoField" => $adminHelper->tdb_jb_get_show_field($paramVideo, 'Video'),
            "getSortByField" => $adminHelper->tdb_jb_get_sort_by_field($sortBy),
            "applyField" => $adminHelper->tdb_jb_get_apply_field($apply),
            "langPreferedContent" => TDB_LANG_PREFEREDLISTOPTION,
            "langApplyField" => TDB_LANG_APPLYFIELD,
            "requiredGivenName" => $givenNameRequired,
            "requiredFamilyName" => $familyNameRequired,
            "favoriteCertif" => $helper->tdb_jb_get_col(3, "select", "favoriteCertif",$helper->tdb_jb_get_opt_apply("certif",$certifFavorite), TDB_LANG_CERTIF, "tdb-jd-input-text tdb-jd-input"),
            "favoriteCurrency" => $helper->tdb_jb_get_col(4, "select", "favoriteCurrency",$helper->tdb_jb_get_opt_sql($language, TDB_TABLE_CURRENCY, "sName", "sTranslate",$currencyFavorite) , TDB_LANG_CURRENCY, "tdb-jd-input-text tdb-jd-input"),
            "favoriteBasis" => $helper->tdb_jb_get_col(4, "select", "favoriteBasis",$helper->tdb_jb_get_opt_sql($language, TDB_TABLE_BASIS, "sName", "sTranslate",$basisFavorite) , TDB_LANG_BASIS, "tdb-jd-input-text tdb-jd-input"),
            "langRequiredField" => TDB_LANG_CHOOSEMANDATORYFIELDS,
            "required" => $adminHelper->tdb_jb_get_required_field($paramRequired),
            "checkAllRequired" => $helper->tdb_jb_get_col(2, "checkbox", "checkAllRequired", "", TDB_LANG_CHECKALL, "tdb-jd-input-text tdb-jd-input"),
            "langOrderFavoriteLanguage" => TDB_LANG_ORDERFAVORITELANGUAGE,
            "langOrderFavoriteCountryOther" => TDB_LANG_ORDERFAVORITECOUNTRYOTHER,
            "langOrderFavoriteNationality" => TDB_LANG_ORDERFAVORITENATIONALITY,
            "langOrderFavoriteCountry" => TDB_LANG_ORDERFAVORITECOUNTRY,
            "langOrderFavoriteLanguageSearch" => TDB_LANG_SEARCHFILTER,
            "langExplainFavoriteLanguage" => TDB_LANG_ONECODELINE6391,
            "langExplainFavoriteNationality" => TDB_LANG_ONECODELINE6392,
            "langExplainFavoriteLanguageSearch" => TDB_LANG_SEARCH,
            "langCategories" => TDB_LANG_CATEGORIES,
            "langTags" => TDB_LANG_TAGS,
            "favoriteLanguageContent" => $helper->tdb_jb_get_col(6, "textarea", "favoriteLanguageContent", $favoriteLanguageContent, "", "tdb-jd-textearea","","","","","","","","en\nja"),
            "favoriteNationalityContent" => $helper->tdb_jb_get_col(6, "textarea", "favoriteNationalityContent", $favoriteNationalityContent, "", "tdb-jd-textearea","","","","","","","","GB\nJP"),
            "favoriteCountryContent" => $helper->tdb_jb_get_col(6, "textarea", "favoriteCountryContent", $favoriteCountryContent, "", "tdb-jd-textearea","","","","","","","","GB\nJP"),
            "favoriteNationalityContentOthers" => $helper->tdb_jb_get_col(6, "textarea", "favoriteNationalityContentOthers", $favoriteNationalityContentOthers, "", "tdb-jd-textearea","","","","","","","","GB\nJP"),
            "favoriteLanguageSearchContent" => $helper->tdb_jb_get_col(6, "textarea", "favoriteLanguageSearchContent", $favoriteLanguageSearchContent, "", "tdb-jd-textearea","","","","","","","","en\nja"),
            "langUpdate" => TDB_LANG_UPDATEAPILOWER,
            "langTemplate"=> TDB_LANG_TEMPLATEUSED,
            "nbTemplate" => $nbTemplate,
            "template"=> $helper->tdb_jb_get_col(3, "select", "templateUsed",$adminHelper->tdb_jb_get_opt_select_template($templateArray,$templateUsed) , TDB_LANG_TEMPLATE, "tdb-jd-input-text tdb-jd-input"),
            "langPositionSearchButton"=> TDB_LANG_FORMBUTTONPOS,
            "getpositionButton"=> $helper->tdb_jb_get_col(3, "select", "searchPosition",$adminHelper->tdb_jb_get_opt_select_template($templatePosition,$searchPosition) , "", "tdb-jd-input-text tdb-jd-input"),
            "privacyPolicyText" => $helper->tdb_jb_get_col(6, "text", "privacyPolicyText", $privacyPolicyText, "", "tdb-jd-input-text"),
            "urlApplyText" => $helper->tdb_jb_get_col(6, "text", "urlApplyText", $urlApplyText, "", "tdb-jd-input-text"),
            "recaptchaKey" => $helper->tdb_jb_get_col(4, "text", "recaptchaKey", $recaptchaKey, TDB_LANG_RECAPTCHAKEY, "tdb-jd-input-text"),
            "recaptchaSecret" => $helper->tdb_jb_get_col(4, "text", "recaptchaSecret", $recaptchaSecret, TDB_LANG_RECAPTCHASECRET, "tdb-jd-input-text"),
            "sourceType" => $helper->tdb_jb_get_col(3, "select", "sourceType",$helper->tdb_jb_get_opt_select($gSource,"",$sourceType),TDB_LANG_TYPESOURCE,"tdb-jd-custom-select tdb-jd-input"),
            "tagGroup1" => $helper->tdb_jb_get_col(3, "select", "tagGroup1",$adminHelper->tdb_jb_get_opt_tag_select($gGroupTags,$tagGroup1),'',"tdb-jd-custom-select tdb-jd-input"),
            "tagGroup2" => $helper->tdb_jb_get_col(3, "select", "tagGroup2",$adminHelper->tdb_jb_get_opt_tag_select($gGroupTags,$tagGroup2),'',"tdb-jd-custom-select tdb-jd-input"),
            "tagGroup3" => $helper->tdb_jb_get_col(3, "select", "tagGroup3",$adminHelper->tdb_jb_get_opt_tag_select($gGroupTags,$tagGroup3),'',"tdb-jd-custom-select tdb-jd-input"),
            "tagGroup4" => $helper->tdb_jb_get_col(3, "select", "tagGroup4",$adminHelper->tdb_jb_get_opt_tag_select($gGroupTags,$tagGroup4),'',"tdb-jd-custom-select tdb-jd-input"),
            "displayCategories" => $helper->tdb_jb_get_col(3, "select", "displayCategories[]",$helper->tdb_jb_get_opt_select($gCategories,'',$displayCategories),TDB_LANG_CATEGORIESDISPLAYED,"tdb-jd-custom-select tdb-jd-input tdb-jd-custom-select tdb-jd-custom-select-multi ","multiple"),
            "excludedCategories" => $helper->tdb_jb_get_col(3, "select", "excludedCategories[]",$helper->tdb_jb_get_opt_select($gCategories,'',$excludedCategories),TDB_LANG_CATEGORIESEXCLUDED,"tdb-jd-custom-select tdb-jd-input tdb-jd-custom-select tdb-jd-custom-select-multi ","multiple"),
            "jobTitleTag" => $helper->tdb_jb_get_col(3, "select", "jobTitleTag",$helper->tdb_jb_get_opt($tagJobTitleList,$jobTitleTag),'',"tdb-jd-custom-select tdb-jd-input"),
            "displayTagGroup1" => $displayTagGroup1Check,
            "displayTagGroup2" => $displayTagGroup2Check,
            "displayTagGroup3" => $displayTagGroup3Check,
            "displayTagGroup4" => $displayTagGroup4Check,
            "widgetChoosenMaximumDateJob" => $helper->tdb_jb_get_col(4, "number", "widgetChoosenMaximumDateJob",$widgetChoosenMaximumDateJob,TDB_LANG_WIDGETMAXIMUM,"tdb-jd-custom-select tdb-jd-input"),
            "widgetChoosenCategory" => $helper->tdb_jb_get_col(4, "select", "widgetChoosenCategory",$helper->tdb_jb_get_opt_select($gCategories,"",$widgetChoosenCategory),TDB_LANG_WIDGETCATEGORY,"tdb-jd-custom-select tdb-jd-input"),
            "socialLogo" => $helper->tdb_jb_get_col(4, "upload", "socialLogo","",TDB_LANG_UPLOADLOGO,"tdb-jd-input"),
            "defaultImage" => $helper->tdb_jb_get_col(4, "upload", "defaultImage","",TDB_LANG_UPLOADIMAGE,"tdb-jd-input"),
            "pathSocialLogo" => $socialLogoPath,
            "pathDefaultImage" => $defaultImagePath,
            "hideSalary" => $searchHideSalaryCheck,
            "hideLocation" => $searchHideLocationCheck,
            "hideReset" => $searchHideResetCheck,
            "hideCurrency" => $searchHideCurrencyCheck,
            "hideBasis" => $searchHideBasisCheck,
            "searchMaxSalary" => $searchMaxSalaryCheck,
            "hideMaxWage" => $searchHideMaxWageCheck,
            "hideMinWage" => $searchHideMinWageCheck,
            "hideLanguage" => $searchHideLanguageCheck,
            "hideAddLanguage" => $searchHideAddLanguageCheck,
            "searchAdvancedButton" => $searchAdvancedButtonCheck,
            "searchVisible" => $searchVisibleCheck,
            "searchShowOneCurrency" => $searchShowOneCurrencyCheck,
            "searchShowOneBasis" => $searchShowOneBasisCheck,
            "displaySearchType" => $displaySearchTypeCheck,
            "displaySearchIndustry" => $displaySearchIndustryCheck,
            "displaySearchCategory" => $displaySearchCategoryCheck,
            "displayLink" => $displayLink,
            "searchFieldIsMultiple" => $searchFieldIsMultipleCheck,
            "rewriteUrl" => $rewriteUrlCheck,
            "attachmentStorageLocalEnableCheck" => $attachmentStorageLocalEnableHtml,
            "templateActivate" => $templateActivateCheck,
            "fromTemplate" => $helper->tdb_jb_get_col(4, "text", "fromTemplate", $fromTemplate, TDB_LANG_FROMTEMPLATE, "tdb-jd-input-text"),
            "bccTemplate" => $helper->tdb_jb_get_col(4, "text", "bccTemplate", $bccTemplate, TDB_LANG_BCCTEMPLATE, "tdb-jd-input-text"),
            "ccTemplate" => $helper->tdb_jb_get_col(4, "text", "ccTemplate", $ccTemplate, TDB_LANG_CCTEMPLATE, "tdb-jd-input-text"),
            "defaultTemplate" => $submitHtmlTemplate,
            "otherTemplate" => $otherLanguageHtmlTemplate,
            "belowLanguageCheckHtml" => $belowLanguageCheckHtml,
            "reverseLanguageSkillCheck" => $reverseLanguageSkillCheckHtml,
            "descriptionCleanedCheck" => $descriptionCleanedHtml,
            "apiTab" => $apiTabHtml,
            "autoNationalityCountry" => $helper->tdb_jb_get_col(4, "select", "autoNationalityCountry",$helper->tdb_jb_get_opt_3166("autoNationalityCountry","",$autoNationalityCountry), TDB_LANG_COUNTRY, "tdb-jd-input-text tdb-jd-input"),
            "shortCodeContent" => $shortcodeContent,
            "nonce" => $nonce
        );
        $helper->tdb_jb_show_template(TDB_ADMIN_TPL,$adminArray);    }
}
