<?php

namespace Jobsearch\Job;

use Jobsearch\Helper;
use Jobsearch\Job;

class jobList{
    // show job in list
    function tdb_jb_show($attributes, $urlArray) {
        $helper = new Helper();
        $jobHelper = new JobHelper();

        $linkApi  = $helper->tdb_jb_get_api_link('LinkSearch', $attributes['api']);

        $getParameter = [];
        $getParameter["limit"] = TDB_NB_JOB_TO_SHOW;

        $searchMaxSalary = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'maxAmountParam', 'sValue', 'sName');
        if($searchMaxSalary <> ''){
            $searchMaxSalary = 'checked';
        }

        $displayCategoriesContent = $helper->tdb_jb_get_array_category('displayCategories');
        $excludedCategoriesContent = $helper->tdb_jb_get_array_category('excludedCategories');
        $displayCategories = $helper->tdb_jb_set_content_category($displayCategoriesContent);
        $excludedCategories = $helper->tdb_jb_set_content_category($excludedCategoriesContent);

        $nonceCheck = false;
        if(isset($_GET['tdb_jb_frontend_search'])){
            if(wp_verify_nonce($_GET['tdb_jb_frontend_search'], 'tdb_jb_frontend_search') <> 1){
                $nonceCheck = true;
            }
        }

        // Get all parameter to filter job
        $getParameter["keyword"] = $helper->tdb_jb_get_form_param('keyword', 'text');
        $getParameter["type"] = $helper->tdb_jb_get_form_param('type', 'text', 'type');
        $getParameter["category"] = $helper->tdb_jb_get_form_param('category', '', 'category', array());
        $getParameter["industry"] = $helper->tdb_jb_get_form_param('industry', '', 'industry', array());
        $getParameter["wageFrom"] = $helper->tdb_jb_get_form_param('wageFrom', 'text', 'amount');
        $getParameter["wageTo"] = $helper->tdb_jb_get_form_param('wageTo', 'text', 'amount');
        $getParameter["wageBasis"] = $helper->tdb_jb_get_form_param('wageBasis', 'text', 'basis');
        $getParameter["currency"] = $helper->tdb_jb_get_form_param('currency', 'text', 'currency');
        $getParameter["jobLanguage"] = $helper->tdb_jb_get_form_param('jobLanguage', '', '', $attributes['jobLanguage']);
        $getParameter["offset"] = $helper->tdb_jb_get_form_param('offset', 'text', 'int', 0);
        $getParameter["location"] = $helper->tdb_jb_get_form_param('location', 'text', 'location');
        $getParameter["tag"] = $helper->tdb_jb_get_form_param('tag', 'text');
        $getParameter["tags"] = $helper->tdb_jb_get_form_param('tags', 'array', '', array());
        $getParameter["sortField"] = $helper->tdb_jb_get_form_param('sortField', 'text');
        $getParameter["sortOrder"] = $helper->tdb_jb_get_form_param('sortOrder', 'text', '', 0);
        $getParameter["advancedSearch"] = $helper->tdb_jb_get_form_param('advancedSearch', 'text');

        $i = 1;
        While (isset($_GET["language".$i])) {
            if($helper->tdb_jb_validate_data($_GET["language".$i])){
                $getParameter["language".$i] = $helper->tdb_jb_sanitize($_GET["language".$i],'');
            }
            $i++;
        }

        $language=array();
        $i= 1;

        //get language, if isset min, (set min to 0 and get max if it have
        // if no min but isset max, min = max
        // if no min and no max, just set language
        While(isset($_GET["language".$i])) {
            if($helper->tdb_jb_validate_data($_GET["language".$i],'')){
                if(isset($_GET["language".$i]["min"])){
                    $_GET["language".$i]["min"] = 0;
                } else {
                    if(isset($_GET["language".$i]["max"])){
                        $_GET["language".$i]["min"] = $_GET["language".$i]["max"];
                    }
                }
                $language[][$i] = array_merge($_GET["language".$i]);
            }
            $i++;
        }
        if($i>1){
            $getParameter["language"] = $language;
        }

        // Set up the link to send to the api
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'keyword', $getParameter["keyword"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'jobLanguage', $getParameter["jobLanguage"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'type', $getParameter["type"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'featured', $attributes['featured']);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'company', $attributes['companyId']);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'tag', $attributes['tag']);

        foreach ( $excludedCategories as $value) {
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'excludeCategory[]',$value);
        }

        if (!empty($getParameter["category"])){
            foreach ( $getParameter["category"] as $value) {
                $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'category[]',$value);
            }
        } else {
            foreach ( $displayCategories as $value) {
                $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'category[]',$value);
            }
        }

        foreach ( $getParameter["industry"] as $value) {
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'industry[]',$value);
        }

        foreach ($getParameter["tags"] as $value) {
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'tags[]',$value);
        }

        foreach ($language as $key => $array) {
            foreach ($array as $value) {
                $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'languages['.$key.'][language]',$value["language"]);
                if(isset($value["min"]) && isset($value["max"]) && $value["min"] <> $value["max"]) {
                    if (isset($value["min"])) {
                        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'languages[' . $key . '][min]', '0');
                    }
                    if (isset($value["max"])) {
                        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'languages[' . $key . '][max]', $value["max"]);
                    }
                } else {
                    if (isset($value["max"])) {
                        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'languages[' . $key . '][ability]', $value["max"]);
                    }
                    if (isset($value["min"])) {
                        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'languages[' . $key . '][min]', '0');
                    }
                }
            }
        }

        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'wageFrom',$getParameter["wageFrom"]);
        if($searchMaxSalary == 'checked'){
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'maxWageTo',$getParameter["wageTo"]);
        } else {
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'wageTo',$getParameter["wageTo"]);
        }
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'wageBasis',$getParameter["wageBasis"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'currency',$getParameter["currency"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'offset',$getParameter["offset"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'limit',$getParameter["limit"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'location',$getParameter["location"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'tag',$getParameter["tag"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'sortField', $getParameter["sortField"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'sortOrder', $getParameter["sortOrder"]);

        // send to the api
        $section = $helper->tdb_jb_get_content($linkApi, $attributes['api']);

        // decode json
        $json = json_decode($section);

        if(isset($json->data->count)){
            $nbElementJson =$json->data->count;
        }
        else {
            $nbElementJson = 0;
        }
        $nbElemTxt = $nbElementJson." ".TDB_LANG_RESULTS." ". TDB_LANG_FOUND ;

        if($nbElementJson > 0 && $nonceCheck == false) {
            $sortBy = $helper->tdb_jb_get_list_sort_by();
            $countFilter = 0;

            $val = "<option>".TDB_LANG_SORTBY."</option>";

            if(isset($sortBy['title']) && $sortBy['title'] <> '' ){
                $val .= "<option  value ='titleA' >".TDB_LANG_TITLE.' '.TDB_LANG_ASC."</option>";
                $val .= "<option  value ='titleD' >".TDB_LANG_TITLE.' '.TDB_LANG_DESC."</option>";;
                $countFilter++;
            }
            if(isset($sortBy['date']) && $sortBy['date'] <> '' ){
                $val .= "<option  value ='dateA' >".TDB_LANG_DATE.' '.TDB_LANG_ASC."</option>";
                $val .= "<option  value ='dateD' >".TDB_LANG_DATE.' '.TDB_LANG_DESC."</option>";;
                $countFilter++;
            }
            if(isset($sortBy['salary']) && $sortBy['salary'] <> '' ){
                $val .= "<option  value ='salaryA' >".TDB_LANG_SALARY.' '.TDB_LANG_ASC."</option>";
                $val .= "<option  value ='salaryD' >".TDB_LANG_SALARY.' '.TDB_LANG_DESC."</option>";;
                $countFilter++;
            }

            $titleHtml = "";
            $filterHtml = "";

            if($countFilter > 0){
                $titleHtml = TDB_LANG_SEARCHRESULTS;
                $filterHtml = $helper->tdb_jb_get_col("","select","sortFilter",$val,"","tdb-jd-sort-by-btn",'','','','','','','','','tdb-jd-sort-filter');
            }

            $arrayHeader= array(
                'langSearchResult' => $titleHtml,
                'nbElemTxt' => $nbElemTxt,
                'sorFilter' => $filterHtml
            );

            $helper->tdb_jb_show_template(TDB_JOB_LIST_HEAD_TPL,$arrayHeader);

            $jsonJobs = $json->data->jobs;
            $i = 1;

            foreach($jsonJobs as $jsonJob) {

                $job = new Job($urlArray, $jsonJob,'', $attributes['api'], $attributes['urlList']);
                $titleJob = $job->get_title();
                $date_published = $job->get_published_date();
                $industry = $job->get_industry();
                $category = $job->get_category();
                $amount = $job->get_amount();
                $formatedAmount = $job->get_formated_amount_no_detail();
                $imageUrl = $job->get_image_url();
                $basis = $job->get_basis();
                $type = $job->get_type();
                $location = $job->get_location();
                $shortDescription = $job->get_short_description();
                $language = $job->get_language();
                $jobLanguage = $job->get_job_language();
                $tags = $job->get_tags();
                $salaryCurrency = $job->get_salary_currency();

                $url = $jobHelper->tdb_get_job_page($attributes['api'], $job->get_id());
                if($url == ''){
                    $url = $job->get_url();
                }

                $summary = $job->get_summary();
                if($summary <> ''){
                    $shortDescription = $summary;
                }

                $urlVideo = $job->get_url_video();
                $video ='';
                if(!empty($urlVideo)){
                    $video ="<iframe  src='$urlVideo'></iframe>";
                }

                $hasImage = $hasVideo = false;
                if (!empty($urlVideo)){
                    $hasVideo = true;
                }
                if (!empty($imageUrl)){
                    $hasImage = true;
                }

                $arrayBody = array(
                    'url' => $url,
                    'imageUrl' => $imageUrl,
                    'title' => $titleJob,
                    'datePublished' => $date_published,
                    'dateFormated' => $date_published,
                    'langPublished' => TDB_LANG_PUBLISHED,
                    'langType' => TDB_LANG_TYPE,
                    'langIndustry' => TDB_LANG_INDUSTRY,
                    'langCategory' => TDB_LANG_CATEGORY,
                    'langrequiredLanguage' => TDB_LANG_REQUIREDLANGUAGE,
                    'langLocation' => TDB_LANG_LOCATION,
                    'langSalary' => TDB_LANG_SALARY,
                    'langMore' => TDB_LANG_READMORE,
                    'langDescription' => TDB_LANG_DESCRIPTION,
                    'langMoreInformation' => TDB_LANG_MOREINFORMATION,
                    'urlApply' => $url,
                    'langApply' => TDB_LANG_APPLYNOW,
                    'type' => $type,
                    'tags' => $tags,
                    'industry' => $industry,
                    'category' => $category,
                    'jobLanguage' => $jobLanguage,
                    'requiredLanguage' => $language,
                    'location' => $location,
                    'salary' => $salaryCurrency ." ".$basis,
                    'amount' => $amount,
                    'formatedAmount' => $formatedAmount,
                    'video' => $video,
                    'description' => $shortDescription,
                    'hasImage' => $hasImage,
                    'hasVideo' => $hasVideo,
                    'i' => $i,
                    'nbElementJson' => $nbElementJson
                );

                $i++;
                $helper->tdb_jb_show_template(TDB_JOB_LIST_TPL,$arrayBody);
            }
        } else {
            $arrayError = array(
                'errorMessage' => TDB_LANG_NOJOBFOUND
            );
            $helper->tdb_jb_show_template(TDB_JOB_LIST_ERROR_TPL,$arrayError);
        }

        if( $nonceCheck == false){
            if($i>1){
                $arrayFooter = array('pagination' => $jobHelper->tdb_jb_get_pagination($nbElementJson,$urlArray,$getParameter));
            } else {
                $arrayFooter = array('pagination' => '');
            }

            $helper->tdb_jb_show_template(TDB_JOB_LIST_FOOT_TPL,$arrayFooter);
        }
    }
}