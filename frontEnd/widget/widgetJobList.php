<?php
namespace Jobsearch\Widget;

use Jobsearch\Helper;
use Jobsearch\Helper\Translation;
use Jobsearch\Job\JobHelper;
use Jobsearch\Job;

class WidgetJobList{
    function tdb_jb_widget_apply_from_pages($urlApply){
        $helper = new Helper();

        $pageTitle = get_the_title();
        if(strpos($urlApply,'?') == true) {
            $pageTitleUrl = "&tdb-page-title=".$pageTitle;
        }
        else {
            $pageTitleUrl = "?tdb-page-title=".$pageTitle;
        }

        $url = $urlApply . $pageTitleUrl;
        $bodyArray = array(
            "applyTitle"=> TDB_LANG_APPLY_A_JOB,
            "urlApply"=> $url
        );
        $helper->tdb_jb_show_template(TDB_WIDGET_APPLY_FROM_PAGES_TPL,$bodyArray);

    }

    function tdb_jb_widget_jobs_featured($attributes, $urlArray){
        $helper = new Helper();
        $jobHelper = new JobHelper();
        $langWidget = TDB_LANG_FEATURED_JOBS;

        $nbJobToShowWidget = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'nbJobToShowWidget', 'sValue', 'sName');
        if($nbJobToShowWidget > 0 ){
            $nbJobToShow = $nbJobToShowWidget;
        } else {
            $nbJobToShow = 10;
        }

        $getParameter = [];
        $getParameter["keyword"] = '';
        $getParameter["offset"] = 0;
        $getParameter["sortOrder"] = 0;
        $getParameter["company"] = $attributes['companyId'];
        $getParameter["limit"] = $nbJobToShow;

        if($attributes['companyId'] <> ''){
            $linkApi  = $helper->tdb_jb_get_api_link('LinkSearch', $attributes['api']);
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'company', $getParameter["company"]);
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'featured', true);
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'offset',$getParameter["offset"]);
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'limit',$getParameter["limit"]);
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

            //$widgetHeadArray = array('langWidgetTitle' => $langWidget);
            //$helper->tdb_jb_show_template(TDB_WIDGET_LIST_HEAD_TPL,$widgetHeadArray);

            $i = 1;
            if($nbElementJson > 0) {
                $jsonJob = $json->data->jobs;

                foreach($jsonJob as $jobs) {
                    $job = new Job($urlArray, $jobs, $attributes['url']);
                    $titleJob = $job->get_title();
                    $date_published = $job->get_published_date();
                    //$date_formated = $jobHelper->tdb_jb_format_date($date_published);
                    $industry = $job->get_industry();
                    $category = $job->get_category();
                    $amount = $job->get_amount();
                    $formated_amount = $job->get_formated_amount_no_detail();
                    $imageUrl = $job->get_image_url();
                    $basis = $job->get_basis();
                    //$max_basis = $job->get_max_basis();
                    $type = $job->get_type();
                    $location = $job->get_location();
                    $short_description = $job->get_short_description();
                    $language = $job->get_language();
                    $job_language = $job->get_job_language();
                    $tags = $job->get_tags();
                    $salary_currency = $job->get_salary_currency();
                    $url = $job->get_url_widget();
                    if($url == ''){
                        $url = $job->get_url();
                    }
                    $summary = $job->get_summary();
                    if($summary <> ''){
                        $short_description = $summary;
                    }

                    $url_video = $job->get_url_video();
                    $video ='';
                    if(!empty($url_video)){
                        $video ="<iframe  src='$url_video'></iframe>";
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
                        'jobLanguage' => $job_language,
                        'requiredLanguage' => $language,
                        'location' => $location,
                        'salary' => $salary_currency ." ".$basis,
                        'amount' => $amount,
                        'formatedAmount' => $formated_amount,
                        'video' => $video,
                        'description' => $short_description,
                        'i' => $i,
                        'nbElementJson' => $nbElementJson,
                        'jobLink' => $attributes['url'],
                        'jobTitle' => $titleJob,
                        'jobImage' => $job->get_image_url(),
                        'jobDescription' => $short_description,
                        'jobDate' => $date_published
                    );

                    $i++;
                    $helper->tdb_jb_show_template(TDB_WIDGET_LIST_BODY_TPL,$arrayBody);
                }
            } else {
                $arrayError = array(
                    'errorMessage' => TDB_LANG_NOJOBFOUND
                );
                $helper->tdb_jb_show_template(TDB_JOB_LIST_ERROR_TPL,$arrayError);
            }

            $widgetFootArray = array();
            $helper->tdb_jb_show_template(TDB_WIDGET_LIST_FOOT_TPL,$widgetFootArray);
        }

    }

    //function tdb_jb_widget_job_list($widgetTitle = "",$url = "",$title = "",$tag = "", $api=1){
    function tdb_jb_widget_job_list($widgetTitle = "", $attributes){
        global $gCategories;
        $helper = new Helper();
        $jobHelper = new JobHelper();

        $nbJobToShowWidget = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'nbJobToShowWidget', 'sValue', 'sName');
        if($nbJobToShowWidget > 0 ){
            $nbJobToShow = $nbJobToShowWidget;
        } else {
            $nbJobToShow = 10;
        }

        $getParameter = [];
        $getParameter["keyword"] = '';
        $getParameter["offset"] = 0;
        $getParameter["sortOrder"] = 0;
        $getParameter["limit"] = $nbJobToShow;

        $job=[];
        $job['title']='';
        $job['id']='';
        $job['image']='';
        $job['description']='';
        $job['date']='';

        $langWidget = $attributes['title'];

        $linkApi  = $helper->tdb_jb_get_api_link('LinkSearch', $attributes['api']);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'keyword', $getParameter["keyword"]);

        if($widgetTitle == 'jobsearch_last_job'){
            $langWidget = TDB_LANG_LATESTJOB;
            $maximumDateParam = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'widgetChoosenMaximumDateJob', 'sValue', 'sName');
            if($maximumDateParam <> ''){

                $getParameter["sinceDaysAgo"] = $maximumDateParam;
                $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'sinceDaysAgo', $getParameter["sinceDaysAgo"]);
            }
        }

        if($widgetTitle == 'jobsearch_category'){
            $category = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'widgetChoosenCategory', 'sValue', 'sName');
            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'category[]',$category);

            if($langWidget == ''){
                if(isset($gCategories[$category][$helper->tdb_jb_get_current_language()])){
                    $categoryTranslate = $gCategories[$category][$helper->tdb_jb_get_current_language()];
                } else {
                    $categoryTranslate = $category;
                }

                $langWidget = $categoryTranslate . " " . TDB_LANG_JOBS;
            }
        }

        if($widgetTitle == 'jobsearch_tag'){

            $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi, 'tag', $attributes['tag']);
        }

        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'offset',$getParameter["offset"]);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'limit',$getParameter["limit"]);
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

        $widgetHeadArray = array('langWidgetTitle' => $langWidget);
        $helper->tdb_jb_show_template(TDB_WIDGET_LIST_HEAD_TPL,$widgetHeadArray);

        if($nbElementJson > 0) {
            $jsonJob = $json->data->jobs;

            foreach($jsonJob as $jobs) {
                $job = new Job("", $jobs, $attributes['url']);
                $titleJob = $job->get_title();
                $image =  $job->get_image_url();
                //$short_description = $job->get_short_description();
                $short_description = '';
                $urlJob =  $job->get_url_widget();
                $date_published = $job->get_published_date();

                $widgetBodyArray = array('jobLink' => $urlJob,
                    'jobTitle' => $titleJob,
                    'jobImage' => $image,
                    'jobDescription' => $short_description,
                    'jobDate' => $date_published);

                $helper->tdb_jb_show_template(TDB_WIDGET_LIST_BODY_TPL,$widgetBodyArray);
            }
        }
        $widgetFootArray = array();

        $helper->tdb_jb_show_template(TDB_WIDGET_LIST_FOOT_TPL,$widgetFootArray);
    }
}
