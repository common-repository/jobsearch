<?php
namespace Jobsearch;

use Jobsearch\Job\JobHelper;

class jobFeed{
    function indeed_xml($api, $urlArray){

        $idUrlArray = [];
        // *********** get all job id
        $helper = new Helper();
        $jobHelper = new JobHelper();
        $linkApi  = $helper->tdb_jb_get_api_link('LinkSearch', $api);

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

        // send to the api
        $section = $helper->tdb_jb_get_content($linkApi, $api);

        // decode json
        $json = json_decode($section);

        if(isset($json->data->count)){
            $nbElementJson =$json->data->count;
        }
        else {
            $nbElementJson = 0;
        }

        if($nbElementJson > 0 ) {
            $jsonJob = $json->data->jobs;

            foreach ($jsonJob as $key => $value) {
                if(isset($value->{'id'})) {
                    $idUrl = "tdb-id-job=".$value->{'id'};
                    $id = $value->{'id'};
                    $url = $urlArray["cleanUrl"];
                    if(strpos($url,$idUrl)==FALSE ){
                        $url .= $urlArray["method"].$idUrl;
                    }
                    $idUrlArray[$id] = $url;
                }
            }
        }

        // *********** get all job detail
        $countJob = 1;
        foreach($idUrlArray as $id => $url){
            $titleJob = "";
            $type = "";
            $country  = "";
            $city  = "";
            $postal_code  = "";
            $visa  = "";
            $visaDetail  = "";
            $googleArray = [];

            $idUrl = "tdb-id-job=".$id;
            $url = $urlArray["cleanUrl"];
            if(strpos($url,$idUrl)==FALSE ){
                $url .= $urlArray["method"].$idUrl;
            }
            $linkApi  = $helper->tdb_jb_get_api_link('LinkDetailJob', $api);
            $linkGet = $linkApi."/".$id;
            $status = "";

            if (filter_var($linkGet, FILTER_VALIDATE_URL) == TRUE) {
                $section = $helper->tdb_jb_get_content($linkGet, $api);
                $json = json_decode($section);
            }


            if(isset($json->status)){
                $status = $json->status;
            }

            if ($status == "200" || isset($json->data)) {
                $jsonJob = $json->data;
            }

            /////////////////////////////////
            //Title
            if(isset($jsonJob->{'title'})) {
                $titleJob = $jsonJob->{'title'};
            }

            // Type
            if(isset($jsonJob->{'type '}) && !empty($param["type"])) {
                $type = $jobHelper->tdb_jb_get_json_value($jsonJob->{'type '},true);
            }

            if(isset($jsonJob->{'type_detail'}) && !empty($param["type_detail"])) {
                if($type <> ""){
                    $type .= "<br/>";
                }
                $type .= $jobHelper->tdb_jb_get_json_value($jsonJob->{'type_detail'});
            }

            if($type <> ""){
                $googleArray["type"] = array("value" => $type,"translate" => TDB_LANG_TYPE);
            }

            // Industry
            if(isset($jsonJob->{'industry'}) && !empty($param["industry"])) {
                $googleArray["industry"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'industry'});
            }

            // Category
            if(isset($jsonJob->{'category'}) && !empty($param["category"])) {
                $googleArray["category"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'category'});
            }

            //Salary
            $salary = "";
            if(isset($jsonJob->{'wage'}->{'amount'}) && !empty($param["amount"])) {
                $googleArray["amount"] = $salary;
            }

            $currency = "";
            if(isset($jsonJob->{'wage'}->{'currency'}) && !empty($param["currency"])) {
                $googleArray["currency"] = $currency;
            }

            if(isset($jsonJob->{'wage'}->{'basis'}) && !empty($param["basis"])) {
                $googleArray["basis"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'wage'}->{'basis'},true);
            }

            // Date
            if(isset($jsonJob->{'publish_date'})){
                $date = $jobHelper->tdb_jb_format_date($jobHelper->tdb_jb_get_json_value($jsonJob->{'publish_date'}));
                $googleArray["publish_date"] = $date;
            }
            if(isset($jsonJob->{'expiration_date'})){
                $googleArray["expiration_date"] = $jobHelper->tdb_jb_format_date($jobHelper->tdb_jb_get_json_value($jsonJob->{'expiration_date'}));
            }

            if(isset($jsonJob->{'image'})) {
                $imageTmp = $jsonJob->{'image'};
                $imageUrl = $jobHelper->tdb_jb_get_json_value($imageTmp->{'url'});
                $googleArray["image"] = $imageUrl;
            }

            //Education
            if(isset($jsonJob->{'education_level'} )  && !empty($param["education_level"])) {
                $googleArray["education_level"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'education_level'});
            }

            //Locale
            if(isset($jsonJob->{'required_languages'})  && !empty($param["language"])) {
                $languageTmp = $jsonJob->{'required_languages'};

                $languageTxtTmp = "";
                foreach($languageTmp as $count => $content){
                    if ($languageTxtTmp <> ""){
                        $languageTxtTmp .= "<br/>";
                    }
                    foreach($content as $key => $value) {
                        switch($key){
                            case "locale":
                                $languageTxtTmp .= $jobHelper->tdb_jb_get_country_name($value);
                                break;
                            case "ability":
                                $languageTxtTmp .= ": ". $jobHelper->tdb_jb_get_json_value($value,true);
                                break;
                            default:
                        }
                    }
                }

                if($languageTxtTmp <> ""){
                    $googleArray["required_language"] = $languageTxtTmp;
                }
            }

            //Holiday
            if(isset($jsonJob->{'holidays'} ) && !empty($param["holidays"]))  {
                $googleArray["holidays"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'holidays'});
            }
            //Conditions
            if(isset($jsonJob->{'conditions'}) && !empty($param["conditions"])) {
                $googleArray["conditions"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'conditions'});
            }
            //selling points
            if(isset($jsonJob->{'company'}->{'selling_points'}) && !empty($param["selling_points"])) {
                $googleArray["selling_points"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'company'}->{'selling_points'});
            }

            //Address
            if(isset($jsonJob->{'address'})  && !empty($param["address"])) {
                $address = $jsonJob->{'address'};

                if(isset($address->country)) {
                    $country = $jobHelper->tdb_jb_get_json_value($address->country);
                    $googleArray["country"] = $address->country;
                }
                if(isset($address->street)) { $street = $jobHelper->tdb_jb_get_json_value($address->street);}
                if(isset($address->postal_code)) {
                    $postal_code = $jobHelper->tdb_jb_get_json_value($address->postal_code);
                    $googleArray["postal_code"] = $address->postal_code;
                }
                if(isset($address->extended)) { $extended = $jobHelper->tdb_jb_get_json_value($address->extended);}
                if(isset($address->city)) {
                    $city = $jobHelper->tdb_jb_get_json_value($address->city);
                    $googleArray["city"] = $address->city;
                }

                $adressFinal = $street ." ". $extended . "<br/>" . $postal_code . " " . $city . " " . $country;

                $googleArray["address"] = $adressFinal;
            }

            //Working hours
            if(isset($jsonJob->{'working_hours'} ) && !empty($param["working_hours"])) {
                $googleArray["working_hours"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'working_hours'});
            }

            //Hiring detail
            if(isset($jsonJob->{'reason_for_hiring_details'}) && !empty($param["reason_for_hiring_details"])) {
                $googleArray["reason_for_hiring_details"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'reason_for_hiring_details'});
            }

            // Location
            if(isset($jsonJob->{'location'}) && !empty($param["location"])) {
                $googleArray["location"] = $jobHelper->tdb_jb_get_json_value($jsonJob->{'location'},true);
            }

            //Required visa
            if((isset($jsonJob->{'visa'}) || isset($jsonJob->{'visa_details'})) || !empty($param["required_visas"])) {
                if(isset($jsonJob->{'visa'}) && $jsonJob->{'visa'} == true ){
                    $visa = $jobHelper->tdb_jb_get_json_value($jsonJob->{'visa'},true);
                }

                if(isset($jsonJob->{'visa_details'})){
                    $visaDetail =  $jsonJob->{'visa_details'};
                }

                if($visaDetail <> ""){
                    if($visa <> ""){
                        $visa .= '<br/>' . $visaDetail;
                    } else {
                        $visa = $visaDetail;
                    }
                }

                if($visa <> "" ){
                    $googleArray["visas"] = $visa;
                }
            }
            //Description
            if(isset($jsonJob->{'description'} )) {
                $googleArray["description"] = $jobHelper->tdb_jb_get_clean_txt($jobHelper->tdb_jb_get_json_value($jsonJob->{'description'}));
            }

            //Description
            if(isset($jsonJob->{'benefits'}) && !empty($param["benefits"])) {
                $googleArray["benefits"] = $jobHelper->tdb_jb_get_clean_txt($jobHelper->tdb_jb_get_json_value($jsonJob->{'benefits'}));
            }
            //Requirement
            if(isset($jsonJob->{'requirements'} )  && !empty($param["requirements"])) {
                $tmpRequirement = str_replace("</p>","",$jsonJob->{'requirements'} );
                $tmpRequirement = str_replace("<p>","",$tmpRequirement );
                $tmpRequirement = str_replace("<div>","",$tmpRequirement );
                $tmpRequirement = str_replace("</div>","",$tmpRequirement );
                $tmpRequirement = str_replace(" ","",$tmpRequirement );
                if($tmpRequirement <> ""){
                    $tmpRequirement = str_replace("</p>","",$jobHelper->tdb_jb_get_json_value($jsonJob->{'requirements'} ));
                    $tmpRequirement = str_replace("<p>","",$tmpRequirement );
                    $tmpRequirement = str_replace("<div>","",$tmpRequirement );
                    $tmpRequirement = str_replace("</div>","",$tmpRequirement );
                    $googleArray["requirements"] = $tmpRequirement;
                }
            }

            // *********** generate xml
            $indeedScript[$countJob] = $this->tdb_jb_indeed_job($titleJob,$googleArray,$id,$url);
            $countJob++;
        }

        // company name
        $companyName = get_bloginfo('name');
        $companyLink = get_bloginfo('wpurl');
        $companyLogo = get_header_image();

        $xml =  "<?xml version='1.0' encoding='utf-8'?><source>";
        $xml .=  "<publisher>$companyName</publisher>";
        $xml .=  "<publisherurl>$companyLink</publisherurl>";
        $xml .=  "<lastBuildDate>".date("D, d M Y H:i:s e")."</lastBuildDate>";

        foreach($indeedScript as $key => $value){
            $xml .=  $value;
        }
//tdb jb redirect to $urlArray["indeed"] after save
        $xml .= "</source>";
        $this->tdb_jb_save_xml_file_to_wordpress($xml);
        $helper->tdb_jb_redirect($urlArray["indeed"]);
        //echo $xml;

    }

    // Return link with parameter sended for update the pagination button
    function tdb_jb_indeed_job($title,$fields,$jobId = "",$url = "") {
        $jobHelper = new JobHelper();

        $sep = ": ";
        $sepEnd = ", ";
        $sepContent = '"';
        $script = '';

        $companyName = get_bloginfo('name');
        $referenceNumber = str_replace(' ', '', $companyName . $jobId);
        //$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


        $script .= '<job>';

        if(isset($title) && $title <> ''){
            $script .= "<title>".$this->tdb_jb_cdata_content($title)."</title>";
        }
        if(isset($fields["publish_date"]) && $fields["publish_date"] <> ''){
            $script .= "<date>".$this->tdb_jb_cdata_content($fields["publish_date"])."</date>";
        }
        $script .= "<referencenumber>".$this->tdb_jb_cdata_content($referenceNumber)."</referencenumber>";
        $script .= "<url>".$this->tdb_jb_cdata_content($url)."</url>";
        //$script .= "<company></company>";
        $script .= "<sourcename>".$this->tdb_jb_cdata_content($companyName)."</sourcename>";
        if (isset( $fields["city"]) && $fields["city"] <> ''){
            $script .= "<city>".$this->tdb_jb_cdata_content($fields["city"])."</city>";
        }
        if (isset( $fields["country"]) && $fields["country"] <> ''){
            $script .= "<country>".$this->tdb_jb_cdata_content($fields["country"])."</country>";
        }
        if (isset( $fields["postal_code"]) && $fields["postal_code"] <> ''){
            $script .= "<postalcode>".$this->tdb_jb_cdata_content($fields["postal_code"])."</postalcode>";
        }
        if (isset( $fields["description"]) && $fields["description"] <> ''){
            // benefits
            //requirement
            //have to put an header on the correct language
            $content = $fields["description"];
            if(isset( $fields["visas"]) && $fields["visas"] <> ''){
                $content .= '<br/>.'.$fields["visas"].'"';
            }
            $script .= "<description>".$this->tdb_jb_cdata_content($content)."</description>";
        }
        if (isset( $fields["city"]) && $fields["city"] <> ''){
            $script .= "<city>".$this->tdb_jb_cdata_content($fields["city"])."</city>";
        }

        $amount = "";
        if (isset( $fields["amount"]) && $fields["amount"] <> ''){
            $amount = $fields["amount"];
        }

        $currency = "";
        if(isset($fields["currency"]) && $fields["currency"] <> ''){
            $currency = $fields["currency"];
        }
        $content = "";
        if ($amount <> "" || $currency <> ""){
            if ($currency <> ""){
                $content = $currency;
            }
            if ($amount <> ""){
                if($currency <> ""){
                    $content .= $sepEnd;
                }

                $content .= $amount;
            }
            $script .= "<salary>".$this->tdb_jb_cdata_content($content)."</salary>";
        }

        if(isset($fields["education_level"]) && $fields["education_level"] <> ''){
            $education_lvl = "";
            foreach($fields["education_level"] as $title => $content) {
                if($education_lvl <> ""){
                    $education_lvl .= " ";
                }
                $education_lvl .= $content;
            }
            $script .= "<education_level>".$this->tdb_jb_cdata_content($education_lvl)."</education_level>";
        }

        if(isset($fields["type"]) && $fields["type"] <> ''){
            $type = "";
            foreach($fields["type"] as $title => $content) {
                if($type <> ""){
                    $type .= " ";
                }
                $type .= $content;
            }
            $script .= "<jobtype>".$this->tdb_jb_cdata_content($type)."</jobtype>";
        }

        if(isset($fields["category"]) && $fields["category"] <> ''){
            $category = "";
            foreach($fields["category"] as $title => $content) {
                if($category <> ""){
                    $category .= " ";
                }
                $category .= $content;
            }
            $script .= "<category>".$this->tdb_jb_cdata_content($category)."</category>";
        }

        if(isset($fields["requirements"]) && $fields["requirements"] <> ''){
            $script .= "<experience>".$this->tdb_jb_cdata_content($fields["requirements"])."</experience>";
        }

        $script .= '</job>';

        return $script;
    }

    function tdb_jb_cdata_content($content){
        $cdataOpen = '<![CDATA[';
        $cdataClosed = ']]>';

        return $cdataOpen .$content.$cdataClosed;
    }

    // copy wp to wordpress repository
    function tdb_jb_save_xml_file_to_wordpress($xml){
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $upload_dir = wp_upload_dir();

        /*$user_dirname_main = $upload_dir['basedir'] . '/' . "jobsearch";
        $upload_dir = $user_dirname_main . '/' . 'xml';

        if(!file_exists($user_dirname_main)){
            wp_mkdir_p($user_dirname_main);
        }

        if(!file_exists($upload_dir)){
            wp_mkdir_p($upload_dir);
        }*/

        $file_name = 'indeed_export.xml';
        //$filepath = $upload_dir . '/' . $file_name;
        $filepath = get_home_path() . '/' . $file_name;

        if(file_exists($filepath)){
            wp_delete_file($filepath);
        }

        $handle = fopen($filepath, 'w') or die('Cannot open file:  '.$filepath); //implicitly creates file
        fwrite($handle, $xml);
        fclose($handle);

        chmod($filepath, 0744);

        return $filepath;

    }

    //
    function tdb_jb_xml_reader($xmlPath){

        $xmlReader = simplexml_load_file($xmlPath);

        if(isset($xmlReader->publisher)){
            echo utf8_decode($xmlReader->publisher).'<br>';
        }
        if(isset($xmlReader->publisherurl)){
            echo utf8_decode($xmlReader->publisherurl).'<br>';
        }
        if(isset($xmlReader->lastBuildDate)){
            echo utf8_decode($xmlReader->lastBuildDate).'<br>';
        }

        foreach($xmlReader->job as $job) {
            if(isset($job->title)){
                echo utf8_decode($job->title).'<br>';
            }
            if(isset($job->date)){
                echo utf8_decode($job->date).'<br>';
            }
            if(isset($job->referencenumber)){
                echo utf8_decode($job->referencenumber).'<br>';
            }
            if(isset($job->url)){
                echo utf8_decode($job->url).'<br>';
            }
            if(isset($job->sourcename)){
                echo utf8_decode($job->sourcename).'<br>';
            }
            if(isset($job->city)){
                echo utf8_decode($job->city).'<br>';
            }
            if(isset($job->country)){
                echo utf8_decode($job->country).'<br>';
            }
            if(isset($job->postalcode)){
                echo utf8_decode($job->postalcode).'<br>';
            }
            if(isset($job->description)) {
                echo utf8_decode($job->description) . '<br>';
            }
            if(isset($job->city)) {
                echo utf8_decode($job->city) . '<br>';
            }
            if(isset($job->salary)){
                echo utf8_decode($job->salary).'<br>';
            }
            if(isset($job->education_level)){
                echo utf8_decode($job->education_level).'<br>';
            }
            if(isset($job->jobtype)){
                echo utf8_decode($job->jobtype).'<br>';
            }
            if(isset($job->category)){
                echo utf8_decode($job->category).'<br>';
            }
            if(isset($job->experience)){
                echo utf8_decode($job->experience).'<br>';
            }
            // echo($xml);
        }
    }
}