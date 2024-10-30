<?php
namespace Jobsearch\Job;

use Jobsearch\Helper;
use Jobsearch\Helper\Translation;

class JobHelper{
    // Add get value to the link ( used to implement the link who will be send to the api)
    function tdb_jb_updt_link_to_send($linkApi,$nameGet,$valueGet) {
        $helper = new Helper();

        // if value empty or if it s not keyword or language minimum we don t add it in the get
        if ($valueGet == '' && $nameGet <> 'keyword' && stripos('min',$nameGet)  == false ) {
            return $linkApi;
        }

        $value = $helper->tdb_jb_clean_opt_value($valueGet);

        if(is_array($value) == false) {
            if (stristr($linkApi, '?') == FALSE) {
                $linkApi .= '?' . $nameGet . '=' . $value;
            } else {
                $linkApi .= '&' . $nameGet . '=' . $value;
            }
        }
        return $linkApi;
    }
// Clean  paragraph flag
    function tdb_jb_get_clean_txt($text) {
        $textChanged = str_replace("<p>", "",$text) ;
        $textChanged = str_replace("</p>", "",$textChanged) ;
        $textChanged = str_replace("<u>", "",$textChanged) ;
        $textChanged = str_replace("</u>", "",$textChanged) ;
        return $textChanged;
    }

    function tdb_jb_get_clean_short_description_txt($text) {
        $textChanged = str_replace("<p>", "",$text) ;
        $textChanged = str_replace("</p>", "",$textChanged) ;
        $textChanged = str_replace("<u>", "",$textChanged) ;
        $textChanged = str_replace("</u>", "",$textChanged) ;
        $textChanged = str_replace("<strong>", "",$textChanged) ;
        $textChanged = str_replace("</strong>", "",$textChanged) ;
        $textChanged = str_replace("<a>", "",$textChanged) ;
        $textChanged = str_replace("</a>", "",$textChanged) ;
        $textChanged = str_replace("<li>", "<br>",$textChanged) ;
        $textChanged = str_replace("</li>", "",$textChanged) ;
        $textChanged = str_replace("<ul>", "",$textChanged) ;
        $textChanged = str_replace("</ul>", "",$textChanged) ;
        return $textChanged;
    }

    // Clean text from HTML, escape special characters and clean linebreaks
    function tdb_jb_get_clean_txt_full($text) {

        // Remove HTML & Remove line breaks
        $cleanText = strip_tags($text);
        $cleanText = trim(preg_replace('/\s+/', ' ', $cleanText));
        $cleanText = htmlspecialchars($cleanText, ENT_QUOTES |  ENT_HTML5);

        return $cleanText;
    }

    // Return WordPress Translation
    function tdb_jb_get_translation($table,$value){
        global $wpdb;

        $helper = new Helper();
        // Get Language used
        $idLangage = $helper->tdb_jb_get_id_language($helper->tdb_jb_get_current_language());

        if($idLangage <= 0 || $idLangage == ""){
            $idLangage = 1 ;
        }

        $columnName1 = "sTranslate";

        $request = "SELECT ".$columnName1 ;

        $request .= " FROM " . $table;
        $request .= " WHERE  nIdLanguage = " . $idLangage;
        $request .= " AND  sName = '" . $value ."'";

        $exec = $wpdb->get_results($request);

        $i = 0;

        foreach ($exec as $ligneResult) {
            return $ligneResult->$columnName1;
        }

        return $value;
    }

// Return a html row , col and content for job detail (left title, right content)
// $name = Title
// $value = Content
// $sizeColLeft = width of the title
// $classRow = class of the row if need
// $classCol1 =
// $classCol2 =
    function tdb_jb_get_html_row($name,$value,$sizeColLeft = 6,$classRow = "tdb-jd-row", $classCol1= "", $classCol2 = "")
    {
        $idContent = "";
        $sizeColRight = 12 - $sizeColLeft;

        $html = "";

        if($classCol1 <> ""){
            $classLeft = $classCol1;
        } else {
            $classLeft = "tdb-jd-col-$sizeColLeft tdb-jd-col-title";
        }
        if($classCol2 <> ""){
            $classRight = $classCol2;
        } else {
            $classRight = "tdb-jd-col-$sizeColRight tdb-jd-col-subject";
        }
        $html .=  "<div class='$classRow' >";
        $html .=  "<div class='$classLeft' > <p>$name</p></div>";

        if(is_array($value)){
            foreach ($value as $result){
                $valTmp = $result;
            }
        }
        else{
            $valTmp = $value;
        }

        $html .=  "<div class='$classRight'>$valTmp</div>";
        $html .=  "</div>";

        Return $html;
    }

    // Get an excerpt from the description, 250 characters by default
    function tdb_jb_get_description_short($jsonDescription, $maxLength = 250) {
        $endDescription = "";
        if(strlen($jsonDescription) > $maxLength) {
            $endDescription = "...";
        }

        return mb_substr($jsonDescription, 0, $maxLength) . $endDescription;
    }

    // Format the salary, make space and decimals
    function tdb_jb_format_salary($salary) {
        if(trim($salary) <> "" ) {
            return number_format($salary);
        }

        return "";
    }

// function format amount (salary Currency)
    function tdb_jd_format_salary_currency($currency = '',$salary = ''){
        $jobHelper = new JobHelper();

        $currencyTmp = '';
        if($salary <> ''){
            $currencyTmp = $jobHelper->tdb_jb_format_salary($salary)." ".$currency;
        }

        switch(strtoupper($currency)){
            case 'JPY':
                $currencyTmp = '&yen; ' . $jobHelper->tdb_jb_format_salary($salary);
                break;
            case 'EUR':
                $currencyTmp = $jobHelper->tdb_jb_format_salary($salary) . ' &euro;';
                break;
            case 'USD':
                $currencyTmp = '&#36; ' . $jobHelper->tdb_jb_format_salary($salary);
                break;
            Default:
        }

        return $currencyTmp;
    }

    // Return link with parameter sent for updating the pagination button
    function tdb_jb_get_link($offset = 0, $url = [], $parameters = null) {
        $jobHelper = new JobHelper();

        $link = $url['home'];
        foreach($parameters as $key => $value) {
            if ($key <> "offset" && $key <> "category" && $key <> "industry" && (strpos($key,"language") < 0 || strpos($key,"language") == "" || strpos($key,"language") == false || strpos($key,"language") === false)){
                $link = $jobHelper->tdb_jb_updt_link_to_send($link,$key, $value);
            }
            if($key == "category" || $key == "industry"){
                foreach ( $parameters[$key] as $value2) {
                    $link = $jobHelper->tdb_jb_updt_link_to_send($link,$key.'[]', $value2);
                }
            }

            if(strpos($key,"language") >= 0){
                if(is_array($parameters[$key]) && !empty($parameters[$key])){
                    foreach ( $parameters[$key] as $language) {
                        if(is_array($language) && !empty($language)){
                            foreach ($language as $key2 => $array) {
                                $link = $jobHelper->tdb_jb_updt_link_to_send($link,'language'.$key2.'[language]',$array["language"]);
                                if(isset($array["min"])){
                                    $valMin = $array["min"];
                                    if($valMin ==""){
                                        $valMin = "0";
                                    }
                                    $link = $jobHelper->tdb_jb_updt_link_to_send($link,'language'.$key2.'[min]',$valMin);
                                }

                                if(isset($array["max"])){
                                    $link = $jobHelper->tdb_jb_updt_link_to_send($link,'language'.$key2.'[max]',$array["max"]);
                                }
                            }
                        }
                    }
                }
            }
        }
        $link = $jobHelper->tdb_jb_updt_link_to_send($link,'offset', $offset);
        return $link;
    }

    // Pagination for job list
    function tdb_jb_get_pagination($totaljob,$arrayUrl,$parameter) {
        $translation = new Translation();
        $helper = new Helper();
        $jobHelper = new JobHelper();
        $nbLinkToShow = 6;
        $nbLinkToShowMobile = 5;
        $nbLinkToShowBefore = 0;
        $nbLinkToShowAfter = 0;
        $nbLinkToShowBeforeMobile = 0;
        $nbLinkToShowAfterMobile = 0;
        $nDiffPageToShowBefore = 0;
        $nDiffPageToShowAfter = 0;
        $nDiffPageToShowBeforeMobile = 0;
        $nDiffPageToShowAfterMobile = 0;
        $nDiffTotal = 0;
        $offset = 0;

        $html = "" ;
        $html_first_page = "" ;
        $html_page = "" ;
        $html_next_page = "" ;

        $bFirstElem = true;
        $bLastElem = true;
        $bSecondLastElem = true;
        $bSecondFirstElem = true;

        if(isset($parameter["offset"])){
            $offset = $parameter["offset"];
        }

        if($offset > 0){
            $current = $offset / TDB_NB_JOB_TO_SHOW + 1;
        } else {
            $current = 1;
        }
        $html.= "<div id ='pagination'>";
        $html.= "<div class ='tdb-jd-row'>";
        $html.= "<div class ='tdb-jd-col-11'>";
        $html.= '<ul class="tdb-jd-pagination">';

        //nb of job
        $nb = $totaljob;
        // nb page to show
        $nbPage = ceil($nb / TDB_NB_JOB_TO_SHOW);

        /*Calculate number of page to show before and after */
        if($current == $nbPage) {
            $nbLinkToShowBefore = $nbLinkToShow;
        }
        if($current == 1) {
            $nbLinkToShowAfter = $nbLinkToShow;
        }
        if($current == $nbPage) {
            $nbLinkToShowBeforeMobile = $nbLinkToShowMobile;
        }
        if($current == 1) {
            $nbLinkToShowAfterMobile = $nbLinkToShowMobile;
        }

        if($nbLinkToShowBefore == 0 &&  $nbLinkToShowAfter == 0) {
            for($i=0;$i<$current;$i++) {
                $nDiffPageToShowBefore ++ ;
            }
            for($i=$current;$i<$nbPage;$i++) {
                $nDiffPageToShowAfter ++ ;
            }

            if($nDiffPageToShowBefore < ($nbLinkToShow/2)){
                $nbLinkToShowBefore = $nDiffPageToShowBefore ;
                $nbLinkToShowAfter = ($nbLinkToShow/2) + (($nbLinkToShow/2) - $nbLinkToShowBefore) ;
            }

            if($nDiffPageToShowAfter < ($nbLinkToShow/2)){
                $nbLinkToShowAfter = $nDiffPageToShowAfter ;
                $nbLinkToShowBefore = ($nbLinkToShow/2) + (($nbLinkToShow/2) - $nbLinkToShowAfter) ;
            }
            if(!($nDiffPageToShowAfter < ($nbLinkToShow/2)) && !($nDiffPageToShowBefore < ($nbLinkToShow/2))){
                $nbLinkToShowAfter = ($nbLinkToShow/2);
                $nbLinkToShowBefore = ($nbLinkToShow/2);
            }
        }

        if($nbLinkToShowBeforeMobile == 0 &&  $nbLinkToShowAfterMobile == 0) {
            for($i=0;$i<$current;$i++) {
                $nDiffPageToShowBeforeMobile ++ ;
            }
            for($i=$current;$i<$nbPage;$i++) {
                $nDiffPageToShowAfterMobile ++ ;
            }

            if($nDiffPageToShowBeforeMobile < ($nbLinkToShowMobile/2)){
                $nbLinkToShowBeforeMobile = $nDiffPageToShowBeforeMobile ;
                $nbLinkToShowAfterMobile = ($nbLinkToShowMobile/2) + (($nbLinkToShowMobile/2) - $nbLinkToShowBeforeMobile) ;
            }

            if($nDiffPageToShowAfterMobile < ($nbLinkToShowMobile/2)){
                $nbLinkToShowAfterMobile = $nDiffPageToShowAfterMobile ;
                $nbLinkToShowBeforeMobile = ($nbLinkToShowMobile/2) + (($nbLinkToShowMobile/2) - $nbLinkToShowAfterMobile) ;
            }
            if(!($nDiffPageToShowAfterMobile < ($nbLinkToShowMobile/2)) && !($nDiffPageToShowBeforeMobile < ($nbLinkToShowMobile/2))){
                $nbLinkToShowAfterMobile = ($nbLinkToShowMobile/2);
                $nbLinkToShowBeforeMobile = ($nbLinkToShowMobile/2);
            }
        }

        /* Show first page link if the current page isn t the first */
        if($current !== 1) {
            $newOffset = 0 ;
            $html_first_page.= '<li class="tdb-jd-li-first-page"><a href="'.$jobHelper->tdb_jb_get_link($newOffset,$arrayUrl,$parameter).'" class="tdb-jd-first-page"><<</a></li>';
        }
        /* Show the previous link if it have  */
        if($current !== 1) {
            $newOffset = ($current - 1) * TDB_NB_JOB_TO_SHOW - TDB_NB_JOB_TO_SHOW ;
            $html_first_page.= '<li class="tdb-jd-li-previous-page"><a href="'.$jobHelper->tdb_jb_get_link($newOffset,$arrayUrl,$parameter).'" class="tdb-jd-previous-page"><</a></li>';
        }
       // $var_resol=$this->tdb_jb_resol();

        /* Show all the previous link before the current page */
        $countPages = 0 ;
        for($i = ($current - $nbLinkToShowBefore) ; $i < $current ; $i++) {

            if($i > 0 ) {
                $countPages ++;
                $newOffset = $i * TDB_NB_JOB_TO_SHOW - TDB_NB_JOB_TO_SHOW ;

                $cssTmp = "tdb-jd-li-page";

                if($i <($current - $nbLinkToShowBeforeMobile) || ( (($nbLinkToShowBeforeMobile ) == 5 || ($nbLinkToShowBeforeMobile ) == 4 || ($nbLinkToShowBeforeMobile ) == 3 || ($nbLinkToShowBeforeMobile ) == 1) && $i <($current - ($nbLinkToShowBeforeMobile-1)) && $i > 2) ){
                    $cssTmp .= " tdb-jd-li-page-first-element";
                }

                $html_page.= '<li class="'.$cssTmp.'"><a href="'.$jobHelper->tdb_jb_get_link($newOffset,$arrayUrl,$parameter).'" class="tdb-jd-page">'.$i.'</a></li>';
            }
        }
        // check current
        /* show the link of the current page */
        $html_page.= '<li class="tdb-jd-li-active-page"><a href="'.$jobHelper->tdb_jb_get_link($offset,$arrayUrl,$parameter).'" class="active">'.$current.'</a></li>';

        /* show the followed link */

        $nb = $current + 1;

        for($i = ($current + 1) ; $i <= $nbPage ; $i++) {
            $countPages ++;
            if($nb <= $nbPage && $nb <= ($current + $nbLinkToShowAfter)) {
                $newOffset = $i * TDB_NB_JOB_TO_SHOW - TDB_NB_JOB_TO_SHOW ;

                $cssTmp = "tdb-jd-li-page";

                if($i >($current + $nbLinkToShowAfterMobile) || ($i >=($current + $nbLinkToShowAfterMobile) && $current ==1) ){
                    $cssTmp .= " tdb-jd-li-page-last-element";
                }

                $html_page.= '<li class="'.$cssTmp.'"><a href="'.$jobHelper->tdb_jb_get_link($newOffset,$arrayUrl,$parameter).'" class="tdb-jd-page">'.$i.'</a></li>';
                $nb++;
            }
        }

        /* show the next link if the current page isn t the last */
        if($current < $nbPage ) {
            $newOffset = ($current + 1) * TDB_NB_JOB_TO_SHOW - TDB_NB_JOB_TO_SHOW ;
            $html_next_page.= '<li class="tdb-jd-li-next-page"><a href="'.$jobHelper->tdb_jb_get_link($newOffset,$arrayUrl,$parameter).'" class="tdb-jd-next-page">></a></li>';
        }

        /* Show last page link if the current page isn t the first */
        if($current < $nbPage ) {
            $newOffset = $nbPage * TDB_NB_JOB_TO_SHOW - TDB_NB_JOB_TO_SHOW ;
            $html_next_page.= '<li class="tdb-jd-li-next-last-page"><a href="'.$jobHelper->tdb_jb_get_link($newOffset,$arrayUrl,$parameter).'" class="tdb-jd-last-page">>></a></li>';
        }

        /****/
        if($html_first_page <> ''){
            $html_first_page = "<li class ='tdb-jd-col2 tdb-jd-page-number tdb-jd-first-elem'>" . $html_first_page . "</li>";
            //   $html_first_page = '';
        }
        if($html_page <> ''){
            $html_page = "<li class ='tdb-jd-col8 tdb-jd-page-number tdb-jd-search-display tdb-jd-second-elem'>" . $html_page . "</li>";
            // $html_page = '';
        }
        if($html_next_page <> ''){
            $html_next_page = "<li class ='tdb-jd-col2 tdb-jd-page-number tdb-jd-third-elem'>" . $html_next_page . "</li>";
            //   $html_next_page = '';
        }

        $html.=
             $html_first_page
            . $html_page
            . $html_next_page
            . '</ul>';

        $option = '';
        // Listbox to chose the page
        for ($i=1;$i<=$nbPage;$i++){
            $newOffset = $i * TDB_NB_JOB_TO_SHOW - TDB_NB_JOB_TO_SHOW ;

            if ($i == $current){
                $option .= "<option selected= 'true' >$i</option>";
            } else {
                $option .= "<option value ='".$jobHelper->tdb_jb_get_link($newOffset,$arrayUrl,$parameter)."' >$i</option>";
            }
        }
        $html.= "</div>"; // tdb-jd-col-11
        $html.= "<div class ='tdb-jd-col-1 tdb-jd-row-widget tdb-jd-select-pagination' >";
        $html.= $helper->tdb_jb_get_col("","select","pageJob",$option,"","","","","","","","","","","tdb-jd-col-6-widget tdb-jd-page-job ");
        $html.= "</div>";
        $html.= "</div>"; //tdb-jd-row
        $html.= "</div>"; //tdb-jd-pagination

        return $html;
    }
    // Return link with parameter sended for update the pagination button
    function tdb_jb_google_job($title,$fields) {
        $jobHelper = new JobHelper();

        $sep = ": ";
        $sepEnd = ", ";
        $sepContent = '"';
        $script = "";

        if(isset($title) && $title <> ''){
            $script .= $sepContent."title".$sepContent.$sep.$sepContent.$title.$sepContent.$sepEnd;
        }
        if(isset($fields["publish_date"]) && $fields["publish_date"] <> ''){
            $script .= $sepContent."datePosted".$sepContent.$sep.$sepContent.$fields["publish_date"].$sepContent.$sepEnd;
        }
        if(isset($fields["expiration_date"]) && $fields["expiration_date"] <> ''){
            $script .= $sepContent."validThrough".$sepContent.$sep.$sepContent.$fields["expiration_date"].$sepContent.$sepEnd;
        }
        if(isset($fields["education_level"]) && $fields["education_level"] <> ''){
            $education_lvl = "";
            foreach($fields["education_level"] as $title => $content) {
                if($education_lvl <> ""){
                    $education_lvl .= " ";
                }
                $education_lvl .= $content;
            }
            $script .= $sepContent."educationRequirements".$sepContent.$sep.$sepContent.$education_lvl.$sepContent.$sepEnd;
        }
        if(isset($fields["type"]) && $fields["type"] <> ''){
            $type = "";
            foreach($fields["type"] as $title => $content) {
                if($type <> ""){
                    $type .= " ";
                }
                $type .= $content;
            }
            $script .= $sepContent."employmentType".$sepContent.$sep.$sepContent.$type.$sepContent.$sepEnd;
        }
        if(isset($fields["industry"]) && $fields["industry"] <> ''){
            $industry = "";
            foreach($fields["industry"] as $title => $content) {
                if($industry <> ""){
                    $industry .= " ";
                }
                $industry .= $content;
            }
            $script .= $sepContent."industry".$sepContent.$sep.$sepContent.$industry.$sepContent.$sepEnd;
        }
        if(isset($fields["Benefits"]) && $fields["Benefits"] <> ''){
            $script .= $sepContent."jobBenefits".$sepContent.$sep.$sepContent.$fields["Benefits"].$sepContent.$sepEnd;
        }
        if(isset($fields["category"]) && $fields["category"] <> ''){
            $category = "";
            foreach($fields["category"] as $title => $content) {
                if($category <> ""){
                    $category .= " ";
                }
                $category .= $content;
            }
            $script .= $sepContent."occupationalCategory".$sepContent.$sep.$sepContent.$category.$sepContent.$sepEnd;
        }
        if(isset($fields["requirements"]) && $fields["requirements"] <> ''){
            $script .= $sepContent."skills".$sepContent.$sep.$sepContent.$fields["requirements"].$sepContent.$sepEnd;
        }
        $currency = "";
        if(isset($fields["currency"]) && $fields["currency"] <> ''){
            $currency = $fields["currency"];
            $script .= $sepContent."salaryCurrency".$sepContent.$sep.$sepContent.$fields["currency"].$sepContent.$sepEnd;
        }
        if(isset($fields["working_hours"]) && $fields["working_hours"] <> ''){
            $script .= $sepContent."workHours".$sepContent.$sep.$sepContent.$fields["working_hours"].$sepContent.$sepEnd;
        }
        if(isset($fields["image"]) && $fields["image"] <> ''){
            $script .= $sepContent."image".$sepContent.$sep.$sepContent.$fields["image"].$sepContent.$sepEnd;
        }
        $qualification = "";
        if(isset($fields["education_level"]) && $fields["education_level"] <> ''){
            $education = "";
            foreach($fields["education_level"] as $title => $content) {
                if($education <> ""){
                    $education .= " ";
                }
                $education .= $content;
            }

            $qualification .= $education ;
        }
        if(isset($fields["required_languages"]) && $fields["required_languages"] <> ''){
            if ($qualification <> ''){
                $qualification = "<br/>";
            }
            $qualification .= $fields["required_languages"] ;
        }

        if($qualification <> ""){
            $script .= $sepContent."qualifications".$sepContent.$sep.$sepContent.$qualification.$sepContent.$sepEnd;
        }
        $amount = "";
        if (isset( $fields["amount"]) && $fields["amount"] <> ''){
            $amount = $fields["amount"];
        }

        if ($amount <> "" || $currency <> ""){
            $script .= $sepContent."baseSalary".$sepContent.$sep." {";
            $script .= $sepContent."@type".$sepContent.$sep.$sepContent."MonetaryAmount".$sepContent.$sepEnd;
            if ($currency <> ""){
                $script .= $sepContent."currency".$sepContent.$sep.$sepContent.$currency.$sepContent;
            }
            if ($amount <> ""){
                if($currency <> ""){
                    $script .= $sepEnd;
                }
                $script .= $sepContent."value".$sepContent.$sep.$amount;
            }
            $script .= '},';
        }

        $script .= '"jobLocation": {';
        $script .= '"@type": "Place",';
        $script .= '"address": {';
        $script .= '"@type": "PostalAddress",';
        $script .= '"streetAddress": "",';
        $script .= '"addressLocality": "'.(isset($fields["city"])?$fields["city"]:'').'",';
        $script .= '"addressRegion": "'.(isset($fields["region"])?$fields["region"]:'').'",';
        $script .= '"postalCode": "'.(isset($fields["postal_code"])?$fields["postal_code"]:'').'",';
        $script .= '"addressCountry": "'.(isset($fields["country"])?$fields["country"]:'').'"';
        $script .= '},';

        if (isset( $fields["location"]) && $fields["location"] <> ''){
            $script .= '"jobLocation": {';
            $script .= '"@type": "Place",';
            $script .= '"address": {';
            $script .= '"@type": "PostalAddress",';
            $script .= '"addressLocality": "'.$fields["location"].'"';
            $script .= '}';
        }
        $script .= '},';

        if (isset( $fields["description"]) && $fields["description"] <> ''){
            $script .= '"description": "'.$fields["description"];
            if(isset( $fields["visas"]) && $fields["visas"] <> ''){
                $script .= '<br/>.'.$fields["visas"];
            }
            $script .= $sepContent.$sepEnd;
        }

        // company name
        $companyName = get_bloginfo('name');
        $companyLink = get_bloginfo('wpurl');
        $companyLogo = get_header_image();

        $script .= $sepContent."hiringOrganization".$sepContent.$sep."{";
        $script .= '"@type": "Organization",';
        $script .= $sepContent."name".$sepContent.$sep.$sepContent.$companyName.$sepContent.$sepEnd;
        $script .= $sepContent."sameAs".$sepContent.$sep.$sepContent.$companyLink.$sepContent;
        if($companyLogo <> ""){
            $script .= $sepEnd.$sepContent."logo".$sepContent.$sep.$sepContent.$companyLogo.$sepContent;
        }
        $script.="}";

        $scriptGoogle = '<script type="application/ld+json">{';
        $scriptGoogle .= '"@context": "http://schema.org",';
        $scriptGoogle .= '"@type": "JobPosting",';
        $scriptGoogle .= $script;
        $scriptGoogle .= '}</script>';

        return $scriptGoogle;

    }

    function tdb_jb_resol()
    {
        $resol='<script type="text/javascript">
                       document.write(""+screen.width+"*"+screen.height+"");
        </script>';
        return $resol;
    }

    // Return the full name of the country with the code transmitted, if no result, return the code
    function tdb_jb_get_country_name($countryCode) {
        $translation = new Translation();
        $helper = new Helper();
        $language = $helper->tdb_jb_get_current_language();

        $codes = $translation->tdb_jb_iso6391($language);

        foreach ($codes as $key => $value) {
            if(strtoupper($countryCode) == strtoupper($key)){
                return $value;
            }
        }
        return $countryCode;
    }

    //get one json value with all his parameter and find if it have translation, send back the translated value or the key
    function tdb_jb_get_json_value($json, $bNeedArray = false) {
        $helper = new Helper();
        $acceptedLanguage = $helper->tdb_jb_get_current_language();
        $language = substr($acceptedLanguage, 0, 2);
        $bIsTranslated = false;
        $returnArray = [];

        if(is_string($json) || is_int($json) || is_bool($json) || is_null($json) || is_double($json) || is_float($json)) {
            return $json;
        }

        foreach ($json as $key => $content) {
            $bIsTranslated = false;

            if(is_array($content) || is_object($content)){
                foreach ($content as $languageValue => $translation) {
                    if ($languageValue == $language) {
                        if($bNeedArray == true){
                            return $translation;
                        }
                        if(!empty($translation)){
                            $returnArray[$key] = $translation;
                            $bIsTranslated = true;
                        }
                    }
                }
            }

            if ($bIsTranslated == false) {
                if($bNeedArray == true){
                    return $key;
                }
                $returnArray[$key] = $key;
            }
        }

        if(empty($returnArray)){
            return $json;
        } else {
            return $returnArray;
        }
    }

    // Format the date Years month day
    function tdb_jb_format_date($date) {
        $dateFormat = (int)$date;
        return date('Y/m/d', $dateFormat);
    }

    function tdb_get_job_page($api = '', $jobId = ''){
        $helper = new Helper();
        $url = '';

        if($jobId == ''){
            $id = get_query_var( 'job-id' );
        } else {
            $id = $jobId;
        }

        $job_page = $helper->tdb_jb_get_job_page_api($api);
        if($job_page != ''){
            $page = get_permalink( $job_page );
            if(isset($page) && $page != ''){
                $url = $page . "$api/$id/";
            }
        }

        if($url == ''){
            $rewriteUrl = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'rewriteUrl', 'sValue', 'sName');
            if($rewriteUrl <> ''){
                $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'];
                $uri = $_SERVER['REQUEST_URI'];
                $url = $protocol.$host.$uri;
                $urlTmp = $protocol.$helper->tdb_jb_get_between($url,$protocol,"?");
                $urlTmp = $protocol.$helper->tdb_jb_get_between($urlTmp,$protocol,"$api/$id/");
                $url = $urlTmp . "$api/$id/";
            }
        }

        return $url;
    }

    function tdb_jd_parse_video($url){
        $finalUrl = '';
        if (strpos($url, 'youtube') !== false) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
            $youtubeId = '/embed/' . $match[1] .'?autoplay=0';
            $finalUrl = 'https://www.youtube.com'.$youtubeId;
        }
        if (strpos($url, 'vimeo') !== false) {
            if(preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $url, $match)) {
                $vimeoId = '/'.$match[5] .'?autoplay=0&loop=1&autopause=1&autopause=0';
                $finalUrl = 'https://player.vimeo.com/video' . $vimeoId;
            }
        }
        return $finalUrl;
    }
}
