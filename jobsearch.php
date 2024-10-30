<?php
/*
Plugin Name: Tamago-DB Job Board
Description: Job board integrated with the ATS/CRM Tamago-DB
Version: 2.3.0
Author: Tamago-DB
Text Domain: jobsearch
Domain Path: /languages
*/
global $wpdb;

use Jobsearch\Admin\Admin;
use Jobsearch\Admin\AdminList;
use Jobsearch\Helper\Translation;
use Jobsearch\SQL;
use Jobsearch\Helper;
use Jobsearch\GlobalLoader;

/*require all the file we need in the project*/
require_once('helper/helper.php');
require_once('helper/sql.php');
require_once('helper/translation.php');
require_once('frontEnd/search/jobFormSearch.php');
require_once('frontEnd/job/jobDetail.php');
require_once('frontEnd/job/jobList.php');
require_once('frontEnd/job/helper/jobHelper.php');
require_once('admin/helper/adminHelper.php');
require_once('admin/admin.php');
require_once('admin/adminListOption.php');
require_once("helper/smarty/libs/Smarty.class.php");
require_once("helper/smarty/libs/SmartyBC.class.php");
require_once("helper/Job.php");
require_once("helper/migration.php");
require_once("helper/version.php");
require_once("helper/GlobalLoader.php");
require_once("jobFeed.php");

require_once('frontEnd/widget/widgetJobList.php');
require_once('frontEnd/widget/sidebarJobList.php');
require_once('frontEnd/apply/helper/applyHelper.php');
require_once('frontEnd/apply/applyForm.php');
require_once('frontEnd/apply/applyResult.php');

// Every time it needs an SQLl update(new table etc...), we need to increase the TDB_VERSION
define("TDB_VERSION",3.7);
define('TDB_SQL_PREFIX',$wpdb->prefix . 'js_');
define('TDB_TABLE_LAST_UPDATE',TDB_SQL_PREFIX.'lastUpdt');
define('TDB_TABLE_TYPE',TDB_SQL_PREFIX.'type');
define('TDB_TABLE_CATEGORY',TDB_SQL_PREFIX.'category');
define('TDB_TABLE_INDUSTRY',TDB_SQL_PREFIX.'industry');
define('TDB_TABLE_VISA',TDB_SQL_PREFIX.'visa');
define('TDB_TABLE_BASIS',TDB_SQL_PREFIX.'basis');
define('TDB_TABLE_CURRENCY',TDB_SQL_PREFIX.'currency');
define('TDB_TABLE_EDUCATION',TDB_SQL_PREFIX.'education');
define('TDB_TABLE_EXPERIMENT',TDB_SQL_PREFIX.'experiment');
define('TDB_TABLE_COMPANY',TDB_SQL_PREFIX.'company');
define('TDB_TABLE_SKILL',TDB_SQL_PREFIX.'skill');
define('TDB_TABLE_MIGRATION',TDB_SQL_PREFIX.'migration');
define('TDB_TABLE_PARAM',TDB_SQL_PREFIX.'param');
define('TDB_TABLE_LANG_USED',TDB_SQL_PREFIX.'langUsed');
define('TDB_TABLE_APPLY_ATTACHMENT',TDB_SQL_PREFIX.'applyAttachment');
define('TDB_TABLE_APPLY_DETAIL',TDB_SQL_PREFIX.'applyDetail');
define('TDB_TABLE_APPLY',TDB_SQL_PREFIX.'apply');
define('TDB_TABLE_VERSION',TDB_SQL_PREFIX.'version');
define('TDB_TABLE_API',TDB_SQL_PREFIX.'api');
define('TDB_TABLE_TEMPLATE',TDB_SQL_PREFIX.'template');
define("TDB_HTTP_PROTOCOL", 'https');
define("TDB_PREFIX", 'tdb_');
define('TDB_DIR_PATH', plugin_dir_path( __FILE__ ) );

/// jobsearch.php = main page
/// jobsearch work on the following rule, this page call the front page who will be shown on the front end(
/// function initForm() and the page who will be called in the admin panel ( function admin_init())
/// This page generated all the needed content, css, sql install and delete
///
/// The admin panel have to be set to let the plugin work correctly, it has to have the link
///
/// the front end main page have to show a search form and some content

class jobsearch_Plugin
{
    private $wpdb;
    private $globalLoaderObject;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->globalLoaderObject = new GlobalLoader();

        // init all the template file
        $this->tdb_jb_init_var();

        //load button to access to settings on admin
        add_filter( 'plugin_action_links_' .plugin_basename(__FILE__), array($this->globalLoaderObject,"tdb_jb_settings_link") );
    }

    // init template file
    function tdb_jb_init_var(){

        $helper = new Helper();

        //Set up all the template access
        $templateUsed = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'templateUsed', 'sValue', 'sName');

        $repSearch = TDB_DIR_PATH . "templates/search/searchForm1.tpl";
        $repJobList = TDB_DIR_PATH . "templates/job/list/jobListBody1.tpl";
        $repJobListHeader = TDB_DIR_PATH . "templates/job/list/jobListHeader1.tpl";
        $repJobListError = TDB_DIR_PATH . "templates/job/list/jobListError1.tpl";
        $repJobListFooter = TDB_DIR_PATH . "templates/job/list/jobListFooter1.tpl";
        $repJobDetailHeader = TDB_DIR_PATH . "templates/job/detail/jobDetailHeader1.tpl";
        $repJobDetailFooter = TDB_DIR_PATH . "templates/job/detail/jobDetailFooter1.tpl";
        $repJobTopDetail = TDB_DIR_PATH . "templates/job/detail/jobDetailTopBody1.tpl";
        $repJobDetail = TDB_DIR_PATH . "templates/job/detail/jobDetailBody1.tpl";
        $repAdmin = TDB_DIR_PATH . "templates/admin/admin1.tpl";
        $repAdminListFooter = TDB_DIR_PATH . "templates/admin/adminListApplyFooter1.tpl";
        $repAdminListHeader = TDB_DIR_PATH . "templates/admin/adminListApplyHeader1.tpl";
        $repAdminListBody = TDB_DIR_PATH . "templates/admin/adminListApplyBody1.tpl";
        $repJobSpace = TDB_DIR_PATH . "templates/common/jobSpace1.tpl";
        $repWidgetJobListHeader = "";
        $repWidgetSidebarHeader = "";
        $repWidgetSidebarBody = "";
        $repWidgetSidebarFooter = "";
        $repWidgetJobListFooter = "";
        $repWidgetJobListBody = "";
        $repWidgetApplyFromPages = "";
        $repApplyForm = "";
        $repApplyResult = "";

        if($templateUsed > 1) {
            if(file_exists(TDB_DIR_PATH . "templates/search/searchForm".$templateUsed.".tpl")){
                $repSearch = TDB_DIR_PATH . "templates/search/searchForm".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/list/jobListBody".$templateUsed.".tpl")){
                $repJobList = TDB_DIR_PATH . "templates/job/list/jobListBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/list/jobListHeader".$templateUsed.".tpl")){
                $repJobListHeader = TDB_DIR_PATH . "templates/job/list/jobListHeader".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/list/jobListError".$templateUsed.".tpl")){
                $repJobListError = TDB_DIR_PATH . "templates/job/list/jobListError".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/list/jobListFooter".$templateUsed.".tpl")){
                $repJobListFooter = TDB_DIR_PATH . "templates/job/list/jobListFooter".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/detail/jobDetailHeader".$templateUsed.".tpl")){
                $repJobDetailHeader = TDB_DIR_PATH . "templates/job/detail/jobDetailHeader".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/detail/jobDetailFooter".$templateUsed.".tpl")){
                $repJobDetailFooter = TDB_DIR_PATH . "templates/job/detail/jobDetailFooter".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/detail/jobDetailBody".$templateUsed.".tpl")){
                $repJobDetail = TDB_DIR_PATH . "templates/job/detail/jobDetailBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/job/detail/jobDetailTopBody".$templateUsed.".tpl")){
                $repJobTopDetail = TDB_DIR_PATH . "templates/job/detail/jobDetailTopBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/admin/admin".$templateUsed.".tpl")){
                $repAdmin = TDB_DIR_PATH . "templates/admin/admin".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/admin/adminListApplyBody".$templateUsed.".tpl")){
                $repAdminListBody = TDB_DIR_PATH . "templates/admin/adminListApplyBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/admin/adminListApplyHeader".$templateUsed.".tpl")){
                $repAdminListHeader = TDB_DIR_PATH . "templates/admin/adminListApplyHeader".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/admin/adminListApplyFooter".$templateUsed.".tpl")){
                $repAdminListFooter = TDB_DIR_PATH . "templates/admin/adminListApplyFooter".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/common/jobSpace".$templateUsed.".tpl")){
                $repJobSpace = TDB_DIR_PATH . "templates/common/jobSpace".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/widget/widgetJobListBody".$templateUsed.".tpl")){
                $repWidgetJobListBody = TDB_DIR_PATH . "templates/widget/widgetJobListBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/widget/widgetJobListFooter".$templateUsed.".tpl")){
                $repWidgetJobListFooter = TDB_DIR_PATH . "templates/widget/widgetJobListFooter".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/widget/widgetJobListHeader".$templateUsed.".tpl")){
                $repWidgetJobListHeader = TDB_DIR_PATH . "templates/widget/widgetJobListHeader".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/apply/form/jobApplyBody".$templateUsed.".tpl")){
                $repApplyForm = TDB_DIR_PATH . "templates/apply/form/jobApplyBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/apply/result/jobResultBody".$templateUsed.".tpl")){
                $repApplyResult = TDB_DIR_PATH . "templates/apply/result/jobResultBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetHeader.tpl".$templateUsed.".tpl")){
                $repWidgetSidebarHeader = TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetHeader".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetBody".$templateUsed.".tpl")){
                $repWidgetSidebarBody = TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetBody".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetFooter".$templateUsed.".tpl")){
                $repWidgetSidebarFooter = TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetFooter".$templateUsed.".tpl";
            }
            if(file_exists(TDB_DIR_PATH . "templates/widget/widgetApplyFromPages".$templateUsed.".tpl")){
                $repWidgetSidebarFooter = TDB_DIR_PATH . "templates/widget/widgetApplyFromPages".$templateUsed.".tpl";
            }
        }

        do_action('tdb_set_up_customize');

        //customize folder
        if(defined('TDB_SEARCH_CUSTOMIZE_TPL') && TDB_SEARCH_CUSTOMIZE_TPL <> ""){
            $repSearch = TDB_SEARCH_CUSTOMIZE_TPL;
        }
        if(defined('TDB_JOB_LIST_CUSTOMIZE_TPL') && TDB_JOB_LIST_CUSTOMIZE_TPL <> ""){
            $repJobList = TDB_JOB_LIST_CUSTOMIZE_TPL;
        }
        if(defined('TDB_JOB_LIST_CUSTOMIZE_HEAD_TPL') && TDB_JOB_LIST_CUSTOMIZE_HEAD_TPL <> ""){
            $repJobListHeader = TDB_JOB_LIST_CUSTOMIZE_HEAD_TPL;
        }
        if(defined('TDB_JOB_LIST_CUSTOMIZE_ERROR_TPL') && TDB_JOB_LIST_CUSTOMIZE_ERROR_TPL <> ""){
            $repJobListError = TDB_JOB_LIST_CUSTOMIZE_ERROR_TPL;
        }
        if(defined('TDB_JOB_LIST_CUSTOMIZE_FOOT_TPL') && TDB_JOB_LIST_CUSTOMIZE_FOOT_TPL <> ""){
            $repJobListFooter = TDB_JOB_LIST_CUSTOMIZE_FOOT_TPL;
        }
        if(defined('TDB_JOB_DETAIL_CUSTOMIZE_HEAD_TPL') && TDB_JOB_DETAIL_CUSTOMIZE_HEAD_TPL <> ""){
            $repJobDetailHeader = TDB_JOB_DETAIL_CUSTOMIZE_HEAD_TPL;
        }
        if(defined('TDB_WIDGET_CUSTOMIZE_LIST_FOOT_TPL') && TDB_WIDGET_CUSTOMIZE_LIST_FOOT_TPL <> ""){
            $repJobDetailFooter = TDB_WIDGET_CUSTOMIZE_LIST_FOOT_TPL;
        }
        if(defined('TDB_JOB_DETAIL_CUSTOMIZE_TPL') && TDB_JOB_DETAIL_CUSTOMIZE_TPL <> ""){
            $repJobDetail = TDB_JOB_DETAIL_CUSTOMIZE_TPL;
        }
        if(defined('TDB_JOB_DETAIL_CUSTOMIZE_TOP_TPL') && TDB_JOB_DETAIL_CUSTOMIZE_TOP_TPL <> ""){
            $repJobTopDetail = TDB_JOB_DETAIL_CUSTOMIZE_TOP_TPL;
        }
        if(defined('TDB_WIDGET_CUSTOMIZE_LIST_BODY_TPL')){
            $repWidgetJobListBody = TDB_WIDGET_CUSTOMIZE_LIST_BODY_TPL;
        }
        if(defined('TDB_WIDGET_CUSTOMIZE_LIST_FOOT_TPL')){
            $repWidgetJobListFooter = TDB_WIDGET_CUSTOMIZE_LIST_FOOT_TPL;
        }
        if(defined('TDB_WIDGET_CUSTOMIZE_LIST_HEAD_TPL')){
            $repWidgetJobListHeader = TDB_WIDGET_CUSTOMIZE_LIST_HEAD_TPL;
        }
        if(defined('TDB_APPLY_CUSTOMIZE_FORM_TPL')){
            $repApplyForm = TDB_APPLY_CUSTOMIZE_FORM_TPL;
        }
        if(defined('TDB_APPLY_CUSTOMIZE_RESULT_TPL')){
            $repApplyResult = TDB_APPLY_CUSTOMIZE_RESULT_TPL;
        }

        if($repWidgetJobListHeader ==""){
            $repWidgetJobListHeader = TDB_DIR_PATH . "templates/widget/widgetJobListHeader1.tpl";
        }
        if($repWidgetJobListFooter ==""){
            $repWidgetJobListFooter = TDB_DIR_PATH . "templates/widget/widgetJobListFooter1.tpl";
        }
        if($repWidgetJobListBody ==""){
            $repWidgetJobListBody = TDB_DIR_PATH . "templates/widget/widgetJobListBody1.tpl";
        }
        if($repApplyForm ==""){
            $repApplyForm = TDB_DIR_PATH . "templates/apply/form/jobApplyBody1.tpl";
        }
        if($repApplyResult ==""){
            $repApplyResult = TDB_DIR_PATH . "templates/apply/result/jobResultBody1.tpl";
        }
        if($repWidgetSidebarHeader ==""){
            $repWidgetSidebarHeader = TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetHeader1.tpl";
        }
        if($repWidgetSidebarBody ==""){
            $repWidgetSidebarBody = TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetBody1.tpl";
        }
        if($repWidgetSidebarFooter ==""){
            $repWidgetSidebarFooter = TDB_DIR_PATH . "templates/sidebarWidget/sidebarWidgetFooter1.tpl";
        }
        if($repWidgetApplyFromPages ==""){
            $repWidgetApplyFromPages = TDB_DIR_PATH . "templates/widget/widgetApplyFromPages1.tpl";
        }

        define("TDB_SEARCH_TPL", $repSearch);
        define("TDB_JOB_LIST_TPL", $repJobList);
        define("TDB_JOB_LIST_ERROR_TPL", $repJobListError);
        define("TDB_JOB_LIST_HEAD_TPL", $repJobListHeader);
        define("TDB_JOB_LIST_FOOT_TPL", $repJobListFooter);
        define("TDB_JOB_DETAIL_TPL", $repJobDetail);
        define("TDB_JOB_DETAIL_TOP_TPL", $repJobTopDetail);
        define("TDB_JOB_SPACE_TPL", $repJobSpace);
        define("TDB_JOB_DETAIL_HEAD_TPL", $repJobDetailHeader);
        define("TDB_JOB_DETAIL_FOOT_TPL", $repJobDetailFooter);
        define("TDB_ADMIN_TPL", $repAdmin);
        define("TDB_ADMIN_LIST_TPL", $repAdminListBody);
        define("TDB_ADMIN_LIST_HEAD_TPL", $repAdminListHeader);
        define("TDB_ADMIN_LIST_FOOT_TPL", $repAdminListFooter);
        define("TDB_WIDGET_LIST_HEAD_TPL", $repWidgetJobListHeader);
        define("TDB_WIDGET_LIST_FOOT_TPL", $repWidgetJobListFooter);
        define("TDB_WIDGET_LIST_BODY_TPL", $repWidgetJobListBody);
        define("TDB_WIDGET_APPLY_FROM_PAGES_TPL", $repWidgetApplyFromPages);
        define("TDB_APPLY_FORM_TPL", $repApplyForm);
        define("TDB_APPLY_RESULT_TPL", $repApplyResult);
        define("TDB_SIDEBAR_HEAD_TPL", $repWidgetSidebarHeader);
        define("TDB_SIDEBAR_BODY_TPL", $repWidgetSidebarBody);
        define("TDB_SIDEBAR_FOOT_TPL", $repWidgetSidebarFooter);
        define('TDB_NB_JOB_TO_SHOW',$helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'nbPageToShow', 'sValue', 'sName'));
    }

    // Create all table if not exist and default data
    function tdb_jb_jobsearch_create_db() {
        $sql = new SQL();
        if( current_user_can('editor') || current_user_can('administrator') ) {
            // Create Database
            $sql->tdb_jb_create_database();

            // Insert default value if not exist
            $sql->tdb_jb_update_api();
        }
    }
}
$globalLoaderObject = new GlobalLoader();

//Set up the  session on the header
add_action('template_redirect', function() {
    $helper = new Helper();
    $helper->tdb_jb_set_session();
},2);
add_action( 'wp_head',array($globalLoaderObject,'tdb_jb_meta'),0);
add_action( 'widgets_init', array($globalLoaderObject,'tdb_register_widgets') );
// Security
add_filter('http_request_reject_unsafe_urls', '__return_false');

// set up shortcode [jobsearch_form jobLanguage='']
add_shortcode( 'jobsearch_form', array($globalLoaderObject,'tdb_jb_shortcode_global'));
add_shortcode( TDB_PREFIX.'job_board_form', array($globalLoaderObject,'tdb_jb_shortcode_global'));
add_shortcode( 'jobsearch_search_form', array($globalLoaderObject,'tdb_jb_shortcode_search'));
add_shortcode( TDB_PREFIX.'search_form', array($globalLoaderObject,'tdb_jb_shortcode_search'));
add_shortcode( TDB_PREFIX.'list_form', array($globalLoaderObject,'tdb_jb_list_job'));
add_shortcode( 'jobsearch_list_form', array($globalLoaderObject,'tdb_jb_list_job'));
add_shortcode( TDB_PREFIX.'detail', array($globalLoaderObject,'tdb_jb_detail_form'));
add_shortcode( 'jobsearch_detail', array($globalLoaderObject,'tdb_jb_detail_form'));
add_shortcode( 'jobsearch_tag', array($globalLoaderObject,'tdb_jb_shortcode_tag'));
add_shortcode( TDB_PREFIX.'job_board_tag', array($globalLoaderObject,'tdb_jb_shortcode_tag'));
add_shortcode( 'jobsearch_featured', array($globalLoaderObject,'tdb_jb_shortcode_featured'));
add_shortcode( TDB_PREFIX.'job_board_featured', array($globalLoaderObject,'tdb_jb_shortcode_featured'));
add_shortcode( 'jobsearch_category', array($globalLoaderObject,'tdb_jb_shortcode_category'));
add_shortcode( TDB_PREFIX.'job_board_category', array($globalLoaderObject,'tdb_jb_shortcode_category'));
add_shortcode( 'jobsearch_last_job', array($globalLoaderObject,'tdb_jb_shortcode_last_job'));
add_shortcode( TDB_PREFIX.'job_board_last_job', array($globalLoaderObject,'tdb_jb_shortcode_last_job'));
add_shortcode( 'jobsearch_apply', array($globalLoaderObject,'tdb_jb_shortcode_apply'));
add_shortcode( TDB_PREFIX.'job_board_apply', array($globalLoaderObject,'tdb_jb_shortcode_apply'));
add_shortcode( 'jobsearch_apply_btn', array($globalLoaderObject,'tdb_jb_shortcode_apply_from_pages'));
add_shortcode( TDB_PREFIX.'apply_btn', array($globalLoaderObject,'tdb_jb_shortcode_apply_from_pages'));

//*********************************ADMIN FUNCTION****************************//
add_action('admin_menu', 'tdb_jb_setup_menu', 2);
add_action('admin_enqueue_scripts', 'tdb_jb_admin_js_custom_page', 2);

function tdb_jb_admin_css_custom_page() {
    /** Register */
    $helper = new Helper();
    $plugin = basename( plugin_dir_path( __FILE__  ) );
    $templateUsed = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'templateUsed', 'sValue', 'sName');
    $cssLink = $plugin . '/css/jobsearch'.$templateUsed.'.css';
    $cssLinkDefault = $plugin . '/css/admin.css';
    $jsLinkColor = $plugin . '/js/jscolor.js';
    $jsAdmin = $plugin . '/js/admin.js';
    wp_register_style( 'jobsearch-admin-css', plugins_url( $cssLinkDefault ) );
    wp_enqueue_style( 'jobsearch-admin-css', 1 );
    wp_register_style( 'jobsearch-admin-css1', plugins_url( $cssLink ));
    wp_enqueue_style( 'jobsearch-admin-css1');
    wp_register_script( 'jobsearchScriptAdmin',  plugins_url( $jsAdmin),'admin', '1.0',true );
    wp_enqueue_script('jobsearchScriptAdmin');
    wp_register_script( 'jobsearchScriptColor',  plugins_url( $jsLinkColor),'jscolor', '1.0',true );
    wp_enqueue_script('jobsearchScriptColor');
}

function tdb_jb_admin_list_apply(){
    $adminList = new AdminList();
    $globalLoader = new GlobalLoader();

    $plugin = basename( plugin_dir_path( __FILE__  ) );
    $jsAdmin = $plugin . '/js/admin.js';
    if( current_user_can('editor') || current_user_can('administrator') ) {
        wp_register_script('jobsearchScriptAdmin', plugins_url($jsAdmin), 'admin', '1.0', true);
        wp_enqueue_script('jobsearchScriptAdmin');
        $globalLoader->tdb_jb_get_opt_api();

        if (isset($_POST['pushApplication'])) {
            $appId = $_POST['pushApplication'];
            $messages = $adminList->tdb_jb_push_application($appId);

            $helper = new Helper();
            $adminHeaderArray = array("messages" => $messages);
            $helper->tdb_jb_show_template(TDB_ADMIN_LIST_HEAD_TPL, $adminHeaderArray);
        }

        $adminList->tdb_jb_get_list_applications();
    }
}

function tdb_jb_admin_js_custom_page() {
    /** Register */
    $plugin = basename( plugin_dir_path( __FILE__  ) );
    $jsAdmin = $plugin . '/js/admin.js';
    wp_register_script( 'jobsearchScriptAdmin',  plugins_url( $jsAdmin),'admin', '1.0',true );
    wp_enqueue_script('jobsearchScriptAdmin');
}
function tdb_jb_setup_menu() {
    // @todo manage_options to edit_pages
    $adminMainPage = add_menu_page(TDB_LANG_TDB, TDB_LANG_TDBJOBBOARD, 'edit_pages', 'jobsearch', 'tdb_jb_admin_init');
    $adminSubMenuPage = add_submenu_page('jobsearch', TDB_LANG_APPLYHISTORY, TDB_LANG_APPLYHISTORY, 'edit_pages', 'jobsearchsubmenu', 'tdb_jb_admin_list_apply');
    add_action('admin_print_styles-' . $adminMainPage, 'tdb_jb_admin_css_custom_page');
    add_action('admin_print_styles-' . $adminSubMenuPage, 'tdb_jb_admin_css_custom_page');
}

// Admin main page
function tdb_jb_admin_init(){
    $sql = new SQL();
    $admin = new Admin();
    $globalLoader = new GlobalLoader();

    $globalLoader->tdb_jb_get_opt_api();
    $sql->tdb_jb_update_api();
    $admin->tdb_jb_set_adminPage();
}
//*********************************ADMIN END FUNCTION****************************//
function tdb_jb_callback_for_setting_up_scripts() {
    global $plugin_name ;
    $helper = new Helper();

    $plugin = basename( plugin_dir_path( __FILE__  ));


    $plugin_name = $plugin;
    $jsLink = $plugin . '/js/jobsearch.js';
    $templateUsed = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'templateUsed', 'sValue', 'sName');
    $cssLinkDefault = $plugin . '/css/jobsearch.css';
    $cssLink = $plugin . '/css/jobsearch'.$templateUsed.'.css';
    wp_register_style( 'jobsearch', plugins_url( $cssLinkDefault ) );
    wp_enqueue_style( 'jobsearch' );
    if(defined('TDB_CUSTOMIZE_DIR_PATH')){
        $pluginCustomize = TDB_CUSTOMIZE_DIR_PATH . '/css/jobsearch.css';
        wp_register_style( 'jobsearch1', plugins_url( $pluginCustomize ) );
        wp_enqueue_style( 'jobsearch1' );
    } else {
        wp_register_style( 'jobsearch1', plugins_url( $cssLink ) );
        wp_enqueue_style( 'jobsearch1' );
    }
    wp_enqueue_style( 'jobsearch2' );
    wp_register_script( 'jobsearchScript',  plugins_url( $jsLink),'jobsearchScript', '1.0',true );
    wp_enqueue_script('jobsearchScript');
    wp_enqueue_script('jquery');
}

add_action( 'wp_enqueue_scripts', 'tdb_jb_callback_for_setting_up_scripts',2 );
//Delete jquery error message generated by wordpress
add_action( 'wp_default_scripts', function( $scripts ) {
    if ( ! empty( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
    }
} ,2);
/**************************init hook*********************/
add_action( 'init', 'tdb_jb_plugin_name_load_plugin_textdomain');
add_action('core_upgrade_preamble', array($globalLoaderObject,'tdb_jb_translation_updates_list'),2);

add_filter('query_vars', 'tdb_jb_rewrite_add_var');
add_filter( 'get_canonical_url', array($globalLoaderObject,'tdb_jb_canonical_url'), 10, 2 );

$jobsearchObject = new jobsearch_Plugin();
register_activation_hook( __FILE__, array($jobsearchObject,'tdb_jb_jobsearch_create_db') );
register_uninstall_hook(__FILE__, 'tdb_jb_on_uninstall');


/***************************Rewrite url and load plugin textdomain ***************/
//Use current language + rewrite url to be able to get link like www.test/api/jobId/apply
// @todo Be careful, the rewrite url don't work on post, only pages!
function tdb_jb_plugin_name_load_plugin_textdomain() {
    $domain = 'jobsearch';
    $helper = new Helper();
    $translation = new Translation();
    $currentLanguage = $helper->tdb_jb_get_current_language();
    $mofile = WP_CONTENT_DIR .'/plugins/'.basename( dirname( __FILE__ ) ) . '/languages/'.$currentLanguage.'/jobsearch-'.$currentLanguage.'.mo';
    $mowp = WP_CONTENT_DIR . '/languages/plugins/jobsearch-'.$currentLanguage.'.mo';
    $powp = WP_CONTENT_DIR . '/languages/plugins/jobsearch-'.$currentLanguage.'.po';

    tdb_jd_update_translation($mofile,$mowp,$powp);

    load_plugin_textdomain( $domain, TRUE, basename( dirname( __FILE__ ) ) . '/languages/'.$currentLanguage );
    $translation->tdb_jb_define_translation();

    $rewrite = false;
    $rewriteUrl = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'rewriteUrl', 'sValue', 'sName');
    if($rewriteUrl <> ''){
        $rewrite = true;
    }

    if($rewrite == true){
        $apiLinks = $helper->tdb_jb_get_linkApi();
        $apiPages = $helper->tdb_jb_get_jobPage();

        try {
            //get polylang slug for rewrite rule if is set
            if ( function_exists('pll_the_languages') ) {
                $translationsSlugs = pll_the_languages(array('raw'=>1));
            } else {
                $translationsSlugs = null;
            }
        } catch (Exception $e){
            $translationsSlugs = null;
        }


        //get all pages using shortcode
        $pagesWithShortcode = $helper->tdb_jb_get_pages_by_shortcode();

        $listPages = [];

        // add all the page by name
        foreach($pagesWithShortcode as $pageShortcode){
            $listPages[] = $pageShortcode->post_name;
        }

        //add page with api
        if(count($apiPages) > 0){
            foreach($apiLinks as $api => $value){
                $page = get_post( $apiPages[$api] );
                if(isset($page->post_name)){
                    $listPages[] = $page->post_name;
                }
            }
        }

        if(count($listPages) > 0){
            foreach($listPages as $postName){
                add_rewrite_rule(
                    $postName . '/([^/]+)/([^/]+)/?$',
                    'index.php?pagename=' .$postName . '&job-api=$matches[1]&job-id=$matches[2]',
                    'bottom'
                );

                add_rewrite_rule(
                    $postName . '/([^/]+)/([^/]+)/apply?$',
                    'index.php?pagename=' . $postName . '&job-api=$matches[1]&job-id=$matches[2]&job-apply=1',
                    'bottom'
                );

                if(is_array($translationsSlugs)){
                    foreach($translationsSlugs as $translationSlug){
                        $slug = $translationSlug['slug'];

                        add_rewrite_rule(
                            $slug.'/'. $postName . '/([^/]+)/([^/]+)/?$',
                            'index.php?pagename=' . $postName . '&job-api=$matches[1]&job-id=$matches[2]',
                            'bottom'
                        );

                        add_rewrite_rule(
                            $slug.'/'. $postName . '/([^/]+)/([^/]+)/apply?$',
                            'index.php?pagename=' . $postName . '&job-api=$matches[1]&job-id=$matches[2]&job-apply=1',
                            'bottom'
                        );
                    }
                }
            }
            flush_rewrite_rules();
        }
    }
}

//Delete the old translation file in wordpress plugin folder
function tdb_jd_update_translation($mofile,$mowp,$powp){
    if(file_exists($mowp) && file_exists($mofile)){
        $created_time_mowp = filectime($mowp);
        $created_time_mofile = filectime($mofile);

        if($created_time_mowp <> $created_time_mofile){
            if (file_exists($mowp) && !is_writable($mowp)) {
                chmod($mowp, 0777);
            }
            if (file_exists($powp) && !is_writable($powp)) {
                chmod($powp, 0777);
            }
            wp_delete_file($mowp);
            wp_delete_file($powp);
        }
    }
}

function tdb_jb_rewrite_add_var( $vars ) {
    $vars[] = "job-api";
    $vars[] = "job-id";
    $vars[] = "job-apply";
    $vars[] = "job-name";

    return $vars;
}

// Disable auto updates for this plugin, it depends on an API, manually update to make sure it follows the correct API version
function auto_update_specific_plugins ( $update, $item ) {
    // Array of plugin slugs to always auto-update
    $plugins = array (
        'jobsearch',
    );
    if ( in_array( $item->slug, $plugins ) ) {
        // Never auto update plugins in this array
        return false;
    } else {
        // Else, use the normal API response to decide whether to update or not
        return $update;
    }
}
add_filter( 'auto_update_plugin', 'auto_update_specific_plugins', 10, 2 );
