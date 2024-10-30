<?php


namespace Jobsearch;

use Jobsearch\Job\jobDetail;
use Jobsearch\Job\jobList;
use Jobsearch\Apply\ApplyResult;
use Jobsearch\Apply\ApplyForm;
use Jobsearch\Widget\WidgetJobList;

class globalLoader
{
    // if need some specific code on uninstall, need to be there
    function tdb_jb_on_uninstall(){
        if( current_user_can('editor') || current_user_can('administrator') ) {
            if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
                return;
            }
        }
    }

    //Add setting button on the plugin page
    function tdb_jb_settings_link($links) {
        $settings_link = '<a href="admin.php?page=jobsearch">'.TDB_LANG_SETTINGS.'</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    function tdb_jb_shortcode_global($atts) {
        $attributes = $this->tdb_jb_get_attribute($atts);
        ob_start();

        $this->tdb_jb_init_global($attributes);
        return ob_get_clean();
    }

    //Launch apply form with short code  [jobsearch_form jobLanguage='ja'] or [jobsearch_form]
    function tdb_jb_init_global($attributes) {
        require_once ('translation.php');
        $helper = new Helper();

        $jobFormSearch = new jobFormSearch();
        $jobList = new jobList();
        $jobDetail = new jobDetail();
        $jobFeed = new jobFeed();
        $sql = new SQL();

        $sql->tdb_jb_update_api();

        // Get different url(home, parameters, clean url etc)
        $urlArray = $helper->tdb_jb_get_page_link();
        $applyResult = new ApplyResult();
        $applyForm = new ApplyForm();

        //check which page will be loaded
        $page = '';
        //xml feed
        if($attributes['type'] == 'xml'){
            $jobFeed->indeed_xml($attributes['api'], $urlArray);
        } else {
            // job form
            if($attributes['type'] != 'apply' && $attributes['jobId'] == ''){
                if( $attributes['searchHidden'] != true){
                    $this->tdb_jb_get_opt_api($attributes['api'], "search");
                    $jobFormSearch->tdb_jb_show($attributes, $urlArray);
                }
            }
            // list job
            if(isset($_POST["send"] ) || ($attributes['jobId'] == '' && $attributes['type'] <> 'apply' && $attributes['urlSearch'] == '')){
                $this->tdb_jb_get_opt_api($attributes['api'], "search");
                $jobList->tdb_jb_show($attributes, $urlArray);
                $helper->tdb_jb_set_session();
                $_SESSION["searchUrl"] = $urlArray["home"].$urlArray["get"];
            }
            // register
            if($attributes['jobId'] == '' && $attributes['type'] == "apply"){
                $this->tdb_jb_get_opt_api($attributes['api']);
            }
            // apply result
            if (isset($_POST["hidden-id"] )) {
                $applyResult->tdb_jb_get_result_apply($attributes, $urlArray);
                $helper->tdb_jb_return_home_page();
            } else {
                // form apply
                if ($attributes['jobId'] != '' && $attributes['type'] == 'apply') {
                    $this->tdb_jb_get_opt_api($attributes['api']);
                    $applyForm->tdb_jb_apply_form($attributes, $urlArray);
                }
                //Detail job
                if ($attributes['jobId'] != '' && $attributes['type'] <> 'apply') {
                    $jobDetail->tdb_jb_show($attributes, $urlArray);
                }
            }
        }
    }

    function tdb_jb_shortcode_search($atts) {
        ob_start();

        $attributes = $this->tdb_jb_get_attribute($atts);

        $this->tdb_jb_search_form($attributes);
        return ob_get_clean();
    }

    function tdb_jb_search_form($attributes){
        // Get the css generating code
        require_once ('translation.php');

        $helper = new Helper();

        $jobFormSearch = new jobFormSearch();
        $sql = new SQL();
        $sql->tdb_jb_update_api();

        // search form
        if ($attributes['type'] <> 'apply' && $attributes['jobId']  == '') {
            // show the search only if search hidden is not set to true

            if((isset($_GET['searchHidden']) && $_GET['searchHidden'] == 'true') == false){
                $this->tdb_jb_get_opt_api($attributes['api'] , "search");
                $urlArray = $helper->tdb_jb_get_page_link();
                $jobFormSearch->tdb_jb_show($attributes, $urlArray);
            }
        }
    }

    function tdb_jb_list_job($atts) {
        ob_start();
        $attributes = $this->tdb_jb_get_attribute($atts);
        $this->tdb_jb_list_form($attributes);
        return ob_get_clean();
    }

    function tdb_jb_list_form($attributes){
        // Get the css generating code
        require_once ('translation.php');

        $helper = new Helper();
        $jobList = new jobList();
        $jobDetail = new jobDetail();
        $jobFeed = new jobFeed();

        $sql = new SQL();
        $sql->tdb_jb_update_api();

        $applyResult = new ApplyResult();
        $applyForm = new ApplyForm();


        if($attributes['type'] == 'xml'){
            $urlArray = $helper->tdb_jb_get_page_link();
            $jobFeed->indeed_xml($attributes['api'], $urlArray);
        } else {
            //get the option exept if job detail
            if($attributes['type'] <> 'apply' && $attributes['jobId'] == ''){
                $this->tdb_jb_get_opt_api($attributes['api'], "search");
            }

            if($attributes['jobId'] != '' && $attributes['type'] == 'apply'){
                // Get all the option set in tamago
                $this->tdb_jb_get_opt_api($attributes['api']);
            }

            //getopt_available_api();
            // Get the link of the page to make link
            $urlArray = $helper->tdb_jb_get_page_link();

            //List job
            if (isset($_POST["send"] ) || $attributes['jobId'] == '' && $attributes['type'] <> "apply" && $attributes['urlSearch'] == '') {
                $jobList->tdb_jb_show($attributes, $urlArray);
                $helper->tdb_jb_set_session();
                $_SESSION["searchUrl"] = $urlArray["home"].$urlArray["get"];
            }

            //result apply
            if (isset($_POST["hidden-id"] )) {
                $applyResult->tdb_jb_get_result_apply($attributes, $urlArray);
                $helper->tdb_jb_return_home_page();
            }
            else {
                // form apply
                if ($attributes['jobId'] != '' && $attributes['type'] == "apply") {
                    $applyForm->tdb_jb_apply_form($attributes, $urlArray);
                }

                //Detail job
                if ($attributes['jobId'] != '' && $attributes['type'] <> "apply") {
                    $jobDetail->tdb_jb_show($attributes, $urlArray);
                }
            }
        }
    }

    /* detail of job if set the job detail page on the admin panel link api */
    function tdb_jb_detail_form(){
        // Get the css generating code
        require_once ('translation.php');

        $helper = new Helper();
        $jobDetail = new jobDetail();

        $sql = new SQL();
        $sql->tdb_jb_update_api();

        $attributes = $this->tdb_jb_get_attribute();

        //getopt_available_api();
        // Get the link of the page to make link
        $urlArray = $helper->tdb_jb_get_page_link();

        //Detail job
        if (isset($jobId) && $jobId != '') {
            $applyResult = new ApplyResult();
            $applyForm = new ApplyForm();

            if(isset($_POST["hidden-id"])){
                $applyResult->tdb_jb_get_result_apply($attributes, $urlArray);
                $helper->tdb_jb_return_home_page();
            } else {
                if ($attributes['apply'] == 1) {
                    $applyForm->tdb_jb_apply_form($attributes, $urlArray);
                } else {
                    $jobDetail->tdb_jb_show($attributes, $urlArray);
                }
            }
        }
    }

    //opengraph function
    function tdb_jb_meta() {
        $api = 1;
        $helper = new Helper();
        $meta = '';

        $attributes = $this->tdb_jb_get_attribute();

        if ($attributes['jobId'] != ''  && !isset($_GET["type"])) {
            $meta = $helper->tdb_jd_generate_meta_follow();
            $linkApi  = $helper->tdb_jb_get_api_link('LinkDetailJob', $attributes['api']);

            $linkGet = $linkApi."/".$attributes['jobId'];

            // Security check, have to have an id to start request
            if ( $attributes['jobId'] > 0) {
                // Get the link of the page to make link
                $urlArray = $helper->tdb_jb_get_page_link();
                // send to the api
                $section = $helper->tdb_jb_get_content($linkGet, $api);

                $json = json_decode($section);

                $status = 0;

                if(isset($json->status)){
                    $status = $json->status;
                }

                if ($status == "200" && isset($json->data)) {
                    $jsonJob = $json->data;
                    $job = new Job($urlArray,$jsonJob,'', $api);

                    $meta .= $helper->tdb_jd_open_graph_generate($job);
                }
            }
        } else {
            $meta .= $helper->tdb_jd_open_graph_generate();
        }

        echo $meta;
    }


    /****** pro version (apply form + plugin) **********/

    function tdb_register_widgets() {
        register_widget( 'Jobsearch\Widget\sidebarJobList' );
    }
//Launch apply form with short code  [jobsearch_apply]
    function tdb_jb_launch_apply_form($attributes){
        if (!class_exists('Jobsearch\Helper')) {
            return ;
        }
        $sql = new SQL();
        $helper = new Helper();
        $applyResult = new ApplyResult();
        $applyForm = new ApplyForm();

        // Get all option form api(language,industry,...)
        $sql->tdb_jb_update_api();
        $this->tdb_jb_get_opt_api($attributes['api']);
        //Get all the link ( with get data, without)
        $urlArray = $helper->tdb_jb_get_page_link();

        // Result apply
        if (isset($_POST["familyname"] )) {
            $applyResult->tdb_jb_get_result_apply($attributes, $urlArray);
        } else {
            // Apply form
            $applyForm->tdb_jb_apply_form($attributes, $urlArray);
        }
    }

// set up shortcode [jobsearch_apply]
    function tdb_jb_shortcode_apply($atts) {
        $attributes = $this->tdb_jb_get_attribute($atts);
        $this->tdb_jb_get_opt_api($attributes['api']);

        ob_start();
        $this->tdb_jb_launch_apply_form($attributes);
        return ob_get_clean();
    }

    /* launch the widget who show only latest jobs */
    function tdb_jb_shortcode_last_job($atts) {
        $attributes = $this->tdb_jb_get_attribute($atts);


        $widgetJob = new WidgetJobList();

        ob_start();
        $widgetJob->tdb_jb_widget_job_list('jobsearch_last_job', $attributes);
        return ob_get_clean();
    }

    /* init code for the widget who show only latest jobs */
    function tdb_jb_shortcode_apply_from_pages($atts) {
        $helper = new Helper();

        $attributes = $this->tdb_jb_get_attribute($atts);
        $this->tdb_jb_get_opt_api($attributes['api']);

        $widgetJob = new WidgetJobList();
        if($attributes["url"] != ''){
            $url = $attributes["url"];
        } else {
            $url = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'urlApplyText', 'sValue', 'sName');
        }
        ob_start();
        $widgetJob->tdb_jb_widget_apply_from_pages($url);

        return ob_get_clean();
    }

    /* init code for the widget who show jobs by category */
    function tdb_jb_shortcode_category($atts) {
        $widgetJob = new WidgetJobList();
        $attributes = $this->tdb_jb_get_attribute($atts);

        ob_start();
        $widgetJob->tdb_jb_widget_job_list('jobsearch_category', $attributes);
        return ob_get_clean();
    }

    /* init code for the widget who show featured jobs  */
    function tdb_jb_shortcode_featured($atts) {
        $helper = new Helper();
        $widgetJob = new WidgetJobList();

        $attributes = $this->tdb_jb_get_attribute($atts);
        $urlArray = $helper->tdb_jb_get_page_link();

        ob_start();
        $widgetJob->tdb_jb_widget_jobs_featured($attributes, $urlArray);
        return ob_get_clean();
    }


    /* init code for the widget who show jobs by tag group */
    function tdb_jb_shortcode_tag($atts) {
        $widgetJob = new WidgetJobList();
        $attributes = $this->tdb_jb_get_attribute($atts);

        ob_start();
        $widgetJob->tdb_jb_widget_job_list('jobsearch_tag', $attributes);
        return ob_get_clean();
    }
    /****************Stylesheet and js hook************/

    /* function to update translation on wordpress plugin update */
    function tdb_jb_translation_updates_list() {
        $translation_updates = wp_get_translation_updates();
        if ( empty($translation_updates) ) { return; }

        echo "<h4>Available translations</h4><pre>";
        echo esc_html( print_r($translation_updates, true) );
        echo "</pre>";
    }

    //Generate correctcanonical url for job pages
    function tdb_jb_canonical_url( $canonical_url, $post ) {
        global $wp;
        $attributes = $this->tdb_jb_get_attribute();

        if($attributes['apply'] != ''){
            $applyTxt = '/apply';
        } else {
            $applyTxt = '';
        }

        $canonical_url =  home_url($wp->request);
        /*if(isset($jobId)){
            $canonical_url .= "/".$attributes['api']."/".$attributes['jobId'];
        }*/

        return $canonical_url;
    }

    /* get the parameter on the shortcode */
    function tdb_jb_get_attribute($atts = []){
        $helper = new Helper();
        $attributes['jobLanguage'] = '';
        $attributes['urlSearch'] = '';
        $attributes['urlList'] = '';
        $attributes['link'] = '';
        $attributes['api'] = '';
        $attributes['companyId'] = '';
        $attributes['featured'] = '';
        $attributes['jobId'] = '';
        $attributes['apply'] = '';
        $attributes['send'] = '';
        $attributes['searchHidden'] = false;
        $attributes['title'] = '';
        $attributes['url'] = '';
        $attributes['type'] = '';
        $attributes['redirect-link'] = '';
        $attributes['tag'] = '';

        if(isset($atts['jobLanguage'])){
            $attributes['jobLanguage'] = $atts['jobLanguage'];
        }
        if(isset($atts['url'])){
            $attributes['urlSearch'] = $atts['url'];
        }
        if(isset($atts['urllist'])){
            $attributes['urlList'] = $atts['urllist'];
        }
        if(isset($atts['joblanguage'])){
            $attributes['jobLanguage'] = $atts['joblanguage'];
        }
        if(isset($atts['url'])){
            $attributes['url'] = $atts['url'];
        }
        if(isset($atts["title"])){
            $attributes['title'] = $atts['title'];
        }
        if(isset($atts["redirect-link"])){
            $attributes['redirect-link'] = $atts["redirect-link"];
        }
        if(isset($atts["tag"])){
            $attributes['tag'] = $atts["tag"];
        }

        $apiQuery = get_query_var( 'job-api' );

        $api = 1;
        if($apiQuery != ''){
            $api = $apiQuery;
        } else {
            if(isset($atts['api'])){
                $api = abs($atts['api']);
            }
        }

        $link = $helper->tdb_jb_get_api_url($api);
        if(!$link){
            $api = 1;
        }
        $attributes['link'] = $link;
        $attributes['api'] = $api;

        if(isset($atts['company'])){
            $attributes['companyId'] = $atts['company'];
        }
        if(isset($atts['featured'])){
            $attributes = $atts['featured'];
        }

        if(get_query_var( 'job-id' ) != ''){
            $attributes['jobId'] = get_query_var( 'job-id' );
        } else {
            if(isset($_GET["tdb-id-job"])){
                $attributes['jobId'] = $helper->tdb_jb_sanitize($_GET["tdb-id-job"],'text');
            }
        }

        $attributes['apply'] = get_query_var( 'job-apply' );

        if($attributes['apply'] != '') {
            $attributes['type'] = 'apply';
        } else {
            if(isset($_GET["type"])) {
                if($helper->tdb_jb_validate_data($_GET['type'])){
                    $attributes['type'] = $helper->tdb_jb_sanitize($_GET["type"],'text');
                }
            }
        }

        if(isset($_GET["send"])) {
            $attributes['send'] = true;
        }

        if(isset($_GET['searchHidden']) && $_GET['searchHidden'] == 'true') {
            $attributes['searchHidden'] = true;
        }

        return $attributes;
    }

    // get option for select box from the api
    function tdb_jb_get_opt_api($api = 1, $type = "") {
        $helper = new Helper();
        //get local language
        global $gTypes;
        global $gCategories;
        global $gCategoriesFiltered;
        global $gIndustries;
        global $gLocation;
        global $gLanguages;
        global $gLanguagesAvailable;
        global $gJobLanguages;
        global $gSource;
        global $gVisaType;
        global $gTags;
        global $gGroupTags;

        $acceptedLanguage = $helper->tdb_jb_get_current_language();

        // search option
        if($type == ""){
            $linkApi  = $helper->tdb_jb_get_api_link('LinkOption', $api);
        } else {
            $linkApi  = $helper->tdb_jb_get_api_link('LinkSearchOption', $api);
        }

        $userApi = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM,'apiKey','sValue','sName');
        $passApi = "";
        $autUser = base64_encode("$userApi:$passApi");
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
            "http" => ['header' => "Authorization: Basic $autUser",
                "protocol_version" => 1.1]
        );
        if (filter_var($linkApi, FILTER_VALIDATE_URL) == TRUE) {
            $section = $helper->tdb_jb_get_content($linkApi, $api);
            $json = json_decode($section);
        }

        // get all option with language, if no translation set array with the local language key ""
        if(isset($json->data)) {
            $jsonJob = $json->data;

            if (isset($jsonJob->types)) {
                $jsonType = $jsonJob->types;
            }

            if (isset($jsonJob->categories)) {
                $jsonCategories = $jsonJob->categories;
            }

            if (isset($jsonJob->industries)) {
                $jsonIndustries = $jsonJob->industries;
            }

            if (isset($jsonJob->locations)) {
                $jsonLocation = $jsonJob->locations;
            }

            if (isset($jsonJob->language_abilities)) {
                $jsonlanguages = $jsonJob->language_abilities;
            }

            if (isset($jsonJob->jobLanguages)) {
                $jsonJobLanguage = $jsonJob->jobLanguages;
            }

            if (isset($jsonJob->source_types)) {
                $jsonSource = $jsonJob->source_types;
            }

            if (isset($jsonJob->visa_types)) {
                $jsonVisa = $jsonJob->visa_types;
            }

            if (isset($jsonJob->tags)) {
                $jsonTags = $jsonJob->tags;
            }

            if (isset($jsonJob->tag_groups)) {
                $jsonTagGroup = $jsonJob->tag_groups;
            }
        }

        if(isset($jsonSource)){
            $gSource = $this->tdb_jb_get_array_api($jsonSource,$acceptedLanguage) ;
        }
        if(isset($jsonVisa)){
            $gVisaType = $this->tdb_jb_get_array_api($jsonVisa,$acceptedLanguage) ;
        }
        if(isset($jsonType)){
            $gTypes = $this->tdb_jb_get_array_api($jsonType,$acceptedLanguage) ;
        }
        if(isset($jsonLocation)){
            $gLocation = $this->tdb_jb_get_array_api($jsonLocation,$acceptedLanguage) ;
        }
        if(isset($jsonCategories)){
            $excludedCategoriesContent = $helper->tdb_jb_get_array_category('excludedCategories');
            $excludedCategories = $helper->tdb_jb_set_content_category($excludedCategoriesContent);
            $displayCategoriesContent = $helper->tdb_jb_get_array_category('displayCategories');
            $displayCategories = $helper->tdb_jb_set_content_category($displayCategoriesContent);

            $gCategories = $this->tdb_jb_get_array_api($jsonCategories,$acceptedLanguage) ;
            $gCategoriesFiltered = $this->tdb_jb_get_array_api_categories($jsonCategories,$acceptedLanguage,$excludedCategories,$displayCategories) ;
        }

        if(isset($jsonIndustries)){
            $gIndustries = $this->tdb_jb_get_array_api($jsonIndustries,$acceptedLanguage) ;
        }
        if(isset($jsonJobLanguage)){
            foreach ($jsonJobLanguage as $key => $value) {
                $gJobLanguages[substr($value,0,2)] = substr($value,0,2);
            }
        }
        if(isset($jsonlanguages)){
            $gLanguages = $this->tdb_jb_get_array_api($jsonlanguages,$acceptedLanguage) ;
            foreach($gLanguages as $key => $value){
                foreach($value as $language => $val){
                    $gLanguagesAvailable[$language] = $language;
                }
            }
        }

        if(isset($jsonTagGroup)){
            $gGroupTags = $this->tdb_jb_get_array_api($jsonTagGroup,$acceptedLanguage) ;
        }

        if(isset($jsonTags)){
            $groupTags = $this->tdb_jb_get_array_tags_api($jsonTags,$acceptedLanguage) ;
            foreach($groupTags as $key => $value){
                if(isset($value['translation'])){
                    foreach($value as $groupTag => $val){
                        if($groupTag == 'translation'){
                            $gGroupTags[$key][] = $val;
                        }

                        if($groupTag == 'value'){
                            foreach($val as $tagNameKey => $tagNameValue){
                                $gTags[$key][key($tagNameValue)] = current($tagNameValue);
                            }
                        }
                    }
                } else {
                    $gGroupTags[$key][] = $key;
                    if(isset($value['value'])){
                        foreach($value['value'] as $tagNameKey => $tagNameValue){
                            $gTags[$key][key($tagNameValue)] = current($tagNameValue);
                        }
                    }
                }
            }
        }
    }

    // get array of the option sended by list option in the api
    function tdb_jb_get_array_api($json,$acceptedLanguage) {
        $array = array();
        foreach ($json as $key => $value) {
            if(isset($value->numerical_value)) {
                $keyFinal = $value->numerical_value;
            }
            else {
                $keyFinal = $key;
            }
            if(isset($value->translations)) {
                foreach ($value->translations as $keyTranslation => $valueTranslation) {
                    $array[$keyFinal][$keyTranslation] = $valueTranslation;
                }
            }
            else {
                $array[$keyFinal][$acceptedLanguage] = $key;
            }
        }
        return $array;
    }
// get array of the option sended by list option in the api
    function tdb_jb_get_array_api_categories($json,$acceptedLanguage,$excludedCategories = [], $displayCategories = []) {
        $array = array();
        foreach ($json as $key => $value) {
            $bExcluded = false;

            if(isset($value->numerical_value)) {
                $keyFinal = $value->numerical_value;
            }
            else {
                $keyFinal = $key;
            }

            foreach ($excludedCategories as $keyExcluded){
                if($keyFinal == $keyExcluded || $key == $keyExcluded){
                    $bExcluded = true;
                }
            }

            if($bExcluded == false) {
                $bDisplay = true;
                if(count($displayCategories)> 0){
                    $bDisplay = false;
                    foreach ($displayCategories as $keyDisplay){
                        if($keyFinal == $keyDisplay || $key == $keyDisplay){
                            $bDisplay = true;
                        }
                    }
                }

                if($bDisplay == true){
                    if(isset($value->translations)) {
                        foreach ($value->translations as $keyTranslation => $valueTranslation) {
                            $array[$keyFinal][$keyTranslation] = $valueTranslation;
                        }
                    }
                    else {
                        $array[$keyFinal][$acceptedLanguage] = $key;
                    }
                }
            }

        }
        return $array;
    }

    // get array of the option sended by list option in the api
    function tdb_jb_get_array_tags_api($json,$acceptedLanguage) {
        $array = array();
        foreach ($json as $key => $value) {
            if(isset($value->numerical_value)) {
                $keyFinal = $value->numerical_value;
            }
            else {
                $keyFinal = $key;
            }
            if(isset($value->translations)) {
                foreach ($value->translations as $keyTranslation => $valueTranslation) {
                    $array[$keyFinal]['translation'][$keyTranslation] = $valueTranslation;
                }
            }
            if(isset($value->value)) {
                foreach ($value->value as $keyValue => $tagName) {
                    $array[$keyFinal]['value'][$keyValue] = $tagName;
                }
            }
            else {
                $array[$keyFinal][$acceptedLanguage] = $key;
            }
        }
        return $array;
    }
}
