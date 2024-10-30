<?php
namespace Jobsearch\Widget;

use Jobsearch\Helper;
use Jobsearch\Helper\Translation;
use Jobsearch\Job\JobHelper;
use Jobsearch\Job;

class sidebarJobList extends \WP_Widget
{
    public function __construct() {
        // actual widget processes
        parent::__construct(
            'tdb_widget', // Base ID
            'Jobs list', // Name
            array( 'description' => __( 'list of jobs', 'jobsearch' ), ) // Args
        );
    }

    public function widget( $args, $instance ) {
        // outputs the content of the widget
        $title = '';
        $company = '';
        $featured = '';
        $urlJob = '';
        $api = 1;
        $nbJobs = 5;

        $helper = new Helper();
        $jobHelper = new JobHelper();
        $urlArray = $helper->tdb_jb_get_page_link();
        //getting all value
        if ( isset( $instance[ 'tdb-title' ] ) ) {
            $title = $instance[ 'tdb-title' ];
        }
        if ( isset( $instance[ 'tdb-company' ] ) ) {
            $company = $instance[ 'tdb-company' ];
        }
        if ( isset( $instance[ 'tdb-featured' ] ) ) {
            $featured = $instance[ 'tdb-featured' ];
        }
        if ( isset( $instance[ 'tdb-url' ] ) ) {
            $urlJob = $instance[ 'tdb-url' ];
        }
        if ( isset( $instance[ 'tdb-api' ] ) && $api > 0 ) {
            $api = $instance[ 'tdb-api' ];
        }
        if ( isset( $instance[ 'tdb-nb-jobs' ] ) ) {
            $nbJobs = $instance[ 'tdb-nb-jobs' ];
        }

        $linkApi  = $helper->tdb_jb_get_api_link('LinkSearch', $api);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'keyword', '');
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'company', $company);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'featured', $featured);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'limit',$nbJobs);
        $linkApi = $jobHelper->tdb_jb_updt_link_to_send($linkApi,'sortOrder', 0);

        $section = $helper->tdb_jb_get_content($linkApi, $api);

        // decode json
        $json = json_decode($section);
        $nbElementJson = 0;

        if(isset($json->data->count)){
            $nbElementJson =$json->data->count;
        }

        if($nbElementJson > 0) {
            $i = 1;
            $jsonJob = $json->data->jobs;

            $arrayHeader= array('title' => $title);

            $helper->tdb_jb_show_template(TDB_SIDEBAR_HEAD_TPL,$arrayHeader);

            foreach($jsonJob as $jobs) {

                $job = new Job($urlArray, $jobs, $urlJob);

                $titleJob = $job->get_title();
                $date_published = $job->get_published_date();
                $date_formated = $helper->tdb_jb_format_date($date_published);
                $industry = $job->get_industry();
                $category = $job->get_category();
                $amount = $job->get_amount();
                $formated_amount = $job->get_formated_amount_no_detail();
                $imageUrl = $job->get_image_url();
                $basis = $job->get_basis();
                $max_basis = $job->get_max_basis();
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
                    'nbElementJson' => $nbElementJson
                );

                $i++;
                $helper->tdb_jb_show_template(TDB_SIDEBAR_BODY_TPL,$arrayBody);
            }
        }
    }

    public function form( $instance ) {
        // outputs the options form in the admin
        $title = '';
        $company = '';
        $featured = '';
        $url = '';
        $api = 1;
        $nbJobs = 5;
        //getting all value
        if ( isset( $instance[ 'tdb-title' ] ) ) {
            $title = $instance[ 'tdb-title' ];
        }
        if ( isset( $instance[ 'tdb-company' ] ) ) {
            $company = $instance[ 'tdb-company' ];
        }
        if ( isset( $instance[ 'tdb-featured' ] ) ) {
            $featured = $instance[ 'tdb-featured' ];
        }
        if ( isset( $instance[ 'tdb-url' ] ) ) {
            $url = $instance[ 'tdb-url' ];
        }
        if ( isset( $instance[ 'tdb-api' ] ) ) {
            $api = $instance[ 'tdb-api' ];
        }
        if ( isset( $instance[ 'tdb-nb-jobs' ] ) ) {
            $nbJobs = $instance[ 'tdb-nb-jobs' ];
        }

        $this->tdbConstructWidgetForm('tdb-title', 'text', 'Title', $title);
        $this->tdbConstructWidgetForm('tdb-api', 'number', 'Api ID(default : 1)', $api);
        $this->tdbConstructWidgetForm('tdb-url', 'text', '*Url of jobs page', $url);
        $this->tdbConstructWidgetForm('tdb-company', 'number', 'Company ID', $company);
        $this->tdbConstructWidgetForm('tdb-nb-jobs', 'number', 'number of jobs to show', $nbJobs);
        $this->tdbConstructWidgetForm('tdb-featured', 'select', 'jobs are featured', $featured, '', ['', 'true', 'false']);
    }

    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        $instance = array();
        $instance['tdb-title'] = ( !empty( $new_instance['tdb-title'] ) ) ? strip_tags( $new_instance['tdb-title'] ) : '';
        $instance['tdb-company'] = ( !empty( $new_instance['tdb-company'] ) ) ? strip_tags( $new_instance['tdb-company'] ) : '';
        $instance['tdb-featured'] = ( !empty( $new_instance['tdb-featured'] ) ) ? strip_tags( $new_instance['tdb-featured'] ) : '';
        $instance['tdb-url'] = ( !empty( $new_instance['tdb-url'] ) ) ? strip_tags( $new_instance['tdb-url'] ) : '';
        $instance['tdb-api'] = ( !empty( $new_instance['tdb-api'] ) ) ? strip_tags( $new_instance['tdb-api'] ) : '';
        $instance['tdb-nb-jobs'] = ( !empty( $new_instance['tdb-nb-jobs'] ) ) ? strip_tags( $new_instance['tdb-nb-jobs'] ) : '';

        return $instance;

    }

    private function tdbConstructWidgetForm($id, $type, $title, $value, $explainText = '', $selectOption =[]){
        $outputHtml = '<label for="'.$this->get_field_name( $id ).'">'. $title.'</label>';
        switch($type){
            case 'text':
                $outputHtml.= '<input class="widefat" id="'.$this->get_field_id( $id ) .'" name="'.$this->get_field_name( $id ).'" type="text" value="'.esc_attr( $value ).'" />';
                break;
            case 'number':
                $outputHtml.= '<input class="widefat" id="'.$this->get_field_id( $id ) .'" name="'.$this->get_field_name( $id ).'" type="number" value="'.esc_attr( $value ).'" />';
                break;
            case 'select':
                $options ='';
                foreach($selectOption as $option){
                    $selected = '';
                    if($value == $option){
                        $selected = 'selected';
                    }
                    $options .= '<option value="'.$option.'" '.$selected.'>'.$option.'</option>';
                }

                $outputHtml.= '<select id="'.$this->get_field_id( $id ) .'" name="'.$this->get_field_name( $id ).'" >';
                $outputHtml.= $options;
                $outputHtml.= '</select>';
                break;
            default:
        }
        if(!empty($explainText)){
            $outputHtml = '<label for="'.$this->get_field_name( $id ).'">'. $explainText.'</label>';
        }
        echo $outputHtml;
    }
}