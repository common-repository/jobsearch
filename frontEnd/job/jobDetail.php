<?php
namespace Jobsearch\Job;

use Jobsearch\Helper;
use Jobsearch\Job;

// show detail from 1 job

class jobDetail{
    function tdb_jb_show($attributes, $urlArray) {
        $jobHelper = new JobHelper();
        $helper = new Helper();

        // Security check, have to have an id to start request
        if ($attributes['jobId'] > 0) {
            $jobTitleTag = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'jobTitleTag', 'sValue', 'sName');
            $templateUsed = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'templateUsed', 'sValue', 'sName');
            $linkApi  = $helper->tdb_jb_get_api_link('LinkDetailJob', $attributes['api']);

            $linkGet = $linkApi."/".$attributes['jobId'];

            // send to the api
            $section = $helper->tdb_jb_get_content($linkGet, $attributes['api']);

            $json = json_decode($section);

            $hasUrlRewritting = false;
            $url = $jobHelper->tdb_get_job_page($attributes['api'], $attributes['jobId']);
            if($url == ''){
                $urlApply = $urlArray["url"].$urlArray["method"]."type=apply";
                if($attributes['api'] > 1){
                    $urlApply .= $urlArray["method"];
                }
            } else {
                $hasUrlRewritting = true;
            }

            if(isset($_SESSION["searchUrl"]) && $_SESSION["searchUrl"] <> ""){
                $urlHome = $_SESSION["searchUrl"];
            } else {
                $urlTmp = $helper->tdb_get_search_url();
                if($urlTmp == ''){
                    $urlHome = $urlArray["home"];
                } else {
                    $urlHome = $urlTmp;
                }
            }

            $status = 0;

            if(isset($json->status)){
                $status = $json->status;
            }

            if ($status <> "200" || !isset($json->data)) {
                $urlArray = $helper->tdb_jb_get_page_link();
                $helper->tdb_jb_redirect($urlHome);
            }

            $jsonJob = $json->data;
            $job = new Job($urlArray, $jsonJob, '', $attributes['api']);

            if ($hasUrlRewritting){
                $urlApply = $url . 'apply';
            }

            $titleJob = $job->get_title();
            $tags = "";
            $googleArray = $job->get_google_job();
            $contentField = $job->get_array_list();
            $datePublished = $job->get_published_date();
            $imageUrl = $job->get_image_url();
            $shortDescription = $job->get_summary();
            if($shortDescription == ''){
                $shortDescription = $job->get_short_description();
            }

            $urlVideo = $job->get_url_video();
            $video = '';
            if(!empty($urlVideo)){
                $video ="<iframe frameborder='0' allowfullscreen class='tdb-jd-frame-video' src='$urlVideo'></iframe>";
            }

            $hasImage = $hasVideo = false;

            if (!empty($urlVideo)){
                $hasVideo = true;
            }
            if (!empty($imageUrl)){
                $hasImage = true;
            }

            $headerArray =array(
                'title' => $titleJob,
                'jobTitleTag' => $jobTitleTag,
                'langReturnSearch' => TDB_LANG_RETURNTOSEARCH,
                'langPublished' => TDB_LANG_PUBLISHED,
                'urlApply' => $urlApply,
                'langApply' => TDB_LANG_APPLYNOW,
                'is_pro_active' => true,
                'urlHome' => $urlHome,
                'short_description' => $shortDescription,
                'video' => $video,
                'imageUrl' => $imageUrl,
                'tags' => $tags,
                'hasImage' => $hasImage,
                'hasVideo' => $hasVideo,
                'dateFormated' => $datePublished);

            $helper->tdb_jb_show_template(TDB_JOB_DETAIL_HEAD_TPL,$headerArray);

            $classRow = "tdb-jd-row tdb-jd-row-header-detail";
            $classLeft = "tdb-jd-col-3 tdb-jd-col-title-content";
            $classRight = "tdb-jd-col-9 tdb-jd-col-subject";

            //other template (Template 2 use the data differently)
            if($templateUsed <> '2'){
                if (count($contentField) > 0) {
                    foreach($contentField as $title => $content) {
                        if(isset($content["value"]) && $content["value"] <> '' ){
                            $bodyArray = array('rowHtml' =>$jobHelper->tdb_jb_get_html_row($content["translate"], $content["value"],  3,$classRow,$classLeft,$classRight));
                            $helper->tdb_jb_show_template(TDB_JOB_DETAIL_TPL,$bodyArray);
                        }
                    }
                }
            } else {
                //special template 2
                if (count($contentField) > 0) {
                    //change the order of the different value and separate value
                    $tpl_2_field = $job->get_array_list_tpl_2();

                    $classRow = "tdb-jd-row tdb-jd-row-header-detail";
                    $classLeft = "tdb-jd-col-3 tdb-jd-col-title-content";
                    $classRight = "tdb-jd-col-9 tdb-jd-col-subject";

                    foreach($tpl_2_field as $title => $content) {
                        if(isset($content["value"]) && $content["value"] <> '' ){
                            $bodyArray = array('rowHtml' =>$jobHelper->tdb_jb_get_html_row($content["translate"], $content["value"],  3,$classRow,$classLeft,$classRight));
                            $helper->tdb_jb_show_template(TDB_JOB_DETAIL_TPL,$bodyArray);
                        }
                    }
                }
            }
        }

        $googleScript = $jobHelper->tdb_jb_google_job($titleJob,$googleArray);
        //$indeedScript = $jobHelper->tdb_jb_indeed_job($titleJob,$googleArray,$id);

        $footArray = array(
            'urlApply' => $urlApply,
            'is_pro_active' => true,
            'googleJob' => $googleScript,
            'langApply' => TDB_LANG_APPLYNOW
        );

        $helper->tdb_jb_show_template(TDB_JOB_DETAIL_FOOT_TPL,$footArray);
    }
}
