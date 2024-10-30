<?php

namespace Jobsearch;

use Jobsearch\Job\JobHelper;

class Job
{
    private $id;
    private $idUrl;
    private $url;
    private $agency;
    private $published_date;
    private $expiration_date;
    private $industry;
    private $category;
    private $amount;
    private $max_amount;
    private $formatedAmount;
    private $formatedAmountNoDetail;
    private $salary_currency;
    private $imageUrl;
    private $currency;
    private $max_currency;
    private $basis;
    private $max_basis;
    private $negotiable;
    private $wage_details;
    private $type;
    private $location;
    private $description;
    private $short_description;
    private $description_clean;
    private $short_description_clean;
    private $language;
    private $joblanguage;
    private $tags;
    private $title;
    private $email;
    private $education;
    private $holidays;
    private $condition;
    private $selling_points;
    private $address;
    private $working_hours;
    private $reason_hiring;
    private $benefit;
    private $requirements;
    private $visa;
    private $summary;
    private $url_video;
    private $url_widget;
    private $google_job;

    //get allow option
    public function __construct($urlArray = "",$xml = "", $urlWidget = "", $api = 1, $urlList = '') {
        $this->set_values();
        if($xml <> '') {
            $this->tdb_jb_init_job($urlArray, $xml, $urlWidget, $api, $urlList);
        }
    }

    private function tdb_jb_init_job($urlArray,$xml,$urlWidget, $api = 1, $urlList = ''){

        $helper = new Helper();
        $jobHelper = new JobHelper();
        $param = $helper->tdb_jb_get_list_parameters();
        $paramVideo = $helper->tdb_jb_get_list_video_parameters();
        $this->set_agency();
        if($xml <> "") {
            // id id url and url
            if(isset($xml->{'id'})) {
                $this->set_id($xml->{'id'});
                $this->set_id_url($xml->{'id'});
                if(isset($urlArray) && !empty($urlArray)){
                    $this->set_url($urlArray,$xml->{'id'}, $api, $urlList);
                }

                $this->set_url_widget($urlWidget,$xml->{'id'},$urlArray, $api);
            }

            // industry
            if(isset($xml->{'industry'}) && !empty($param["industry"])) {
                $this->set_industry($jobHelper->tdb_jb_get_json_value($xml->{'industry'}));
            }
            // category
            if(isset($xml->{'category'}) && !empty($param["category"])) {
                $this->set_category($jobHelper->tdb_jb_get_json_value($xml->{'category'}));
            }
            // amount
            if(isset($xml->{'wage'}->{'amount'})  && !empty($param["amount"])) {
                $this->set_amount($jobHelper->tdb_jb_get_json_value($xml->{'wage'}->{'amount'}));
            }
            // max amount
            if(isset($xml->{'maximum_wage'}->{'amount'})  && !empty($param["maxAmount"])) {
                $this->set_max_amount($jobHelper->tdb_jb_get_json_value($xml->{'maximum_wage'}->{'amount'}));
            }
            // image url
            if(isset($xml->{'image'})) {
                $imageTmp = $xml->{'image'};
                $this->set_image_url($jobHelper->tdb_jb_get_json_value($imageTmp->{'url'}));
            }
            // currency
            if(isset($xml->{'wage'}->{'currency'}) && !empty($param["currency"])) {
                $this->set_currency($jobHelper->tdb_jb_get_json_value($xml->{'wage'}->{'currency'},true));
            }
            // maximum_wage currency
            if(isset($xml->{'maximum_wage'}->{'currency'}) && !empty($param["maxCurrency"])) {
                $this->set_max_currency($jobHelper->tdb_jb_get_json_value($xml->{'maximum_wage'}->{'currency'},true));
            }
            // basis
            if(isset($xml->{'wage'}->{'basis'}) && !empty($param["basis"])) {
                $this->set_basis($jobHelper->tdb_jb_get_json_value($xml->{'wage'}->{'basis'},true));
            }
            // maximum_wage basis
            if(isset($xml->{'maximum_wage'}->{'basis'}) && !empty($param["maxBasis"])) {
                $this->set_max_basis($jobHelper->tdb_jb_get_json_value($xml->{'maximum_wage'}->{'basis'},true));
            }
            // wage_detail
            if(isset($xml->{'wage_details'}) && !empty($param["wage_details"])) {
                $this->set_wage_detail($jobHelper->tdb_jb_get_json_value($xml->{'wage_details'}));
            }
            // negotiable
            if(isset($xml->{'wage'}->{'negotiable'}) && !empty($param["negotiable"])) {
                $this->set_negotiable($jobHelper->tdb_jb_get_json_value($xml->{'wage'}->{'negotiable'}));
            }
            // type
            if((isset($xml->{'type '})  || isset($xml->{'type'}))  && !empty($param["type"])) {
                $type = $jobHelper->tdb_jb_get_json_value($xml->{'type '},true);
                $typeDetail = "";
                if($type == ""){
                    $type = $jobHelper->tdb_jb_get_json_value($xml->{'type'},true);
                }

                if(isset($xml->{'type_detail'})  && !empty($param["type_detail"])) {
                    $typeDetail .= $jobHelper->tdb_jb_get_json_value($xml->{'type_detail'});
                }
                $this->set_type($type, $typeDetail);
            }
            // location
            if(isset($xml->{'location'}) && !empty($param["location"])) {
                $this->set_location($jobHelper->tdb_jb_get_json_value($xml->{'location'},true));
            }
            // description

            if(isset($xml->{'description'}) && !empty($param["description"])) {
                $this->set_description($jobHelper->tdb_jb_get_json_value($xml->{'description'}));
            }
            //date
            if(isset($xml->{'publish_date'})) {
                $this->set_published_date($jobHelper->tdb_jb_format_date($jobHelper->tdb_jb_get_json_value($xml->{'publish_date'})));
            }
            if(isset($xml->{'expiration_date'})) {
                $this->set_expiration_date($jobHelper->tdb_jb_format_date($jobHelper->tdb_jb_get_json_value($xml->{'expiration_date'})));
            }
            // language
            if(isset($xml->{'required_languages'}) && !empty($param["language"])) {
                $this->set_language($xml->{'required_languages'});
            }
            // email
            if(isset($xml->{'email'}) && !empty($param["email"])) {
                $this->set_email($jobHelper->tdb_jb_get_json_value($xml->{'email'}));
            }
            // tags
            if(isset($xml->{'tags'})) {
                $this->set_tags($xml->{'tags'});
            }
            // title
            if(isset($xml->{'title'})) {
                $this->set_title($xml->{'title'});
            }
            // education
            if(isset($xml->{'education_level'})  && !empty($param["education_level"])) {
                $this->set_education($jobHelper->tdb_jb_get_json_value($xml->{'education_level'}));
            }
            // holiday
            if(isset($xml->{'holidays'} ) && !empty($param["holidays"]))  {
                $this->set_holidays($jobHelper->tdb_jb_get_json_value($xml->{'holidays'}));
            }
            // condition
            if(isset($xml->{'conditions'}) && !empty($param["conditions"])) {
                $this->set_condition($jobHelper->tdb_jb_get_json_value($xml->{'conditions'}));
            }
            // selling points
            if(isset($xml->{'company'}->{'selling_points'}) && !empty($param["selling_points"])) {
                $this->set_selling_points($jobHelper->tdb_jb_get_json_value($xml->{'company'}->{'selling_points'}));
            }
            // address
            if(isset($xml->{'address'}) && !empty($param["address"])) {
                $this->set_address($xml->{'address'});
            }
            // working hours
            if(isset($xml->{'working_hours'}) && !empty($param["working_hours"])) {
                $this->set_working_hours($jobHelper->tdb_jb_get_json_value($xml->{'working_hours'}));
            }
            // hiring detail
            if(isset($xml->{'reason_for_hiring_details'}) && !empty($param["reason_for_hiring_details"])) {
                $this->set_reason_hiring($jobHelper->tdb_jb_get_json_value($xml->{'reason_for_hiring_details'}));
            }
            // location
            if(isset($xml->{'location'}) && !empty($param["location"])) {
                $this->set_location($jobHelper->tdb_jb_get_json_value($xml->{'location'},true));
            }
            // benefit
            if(isset($xml->{'benefits'}) && !empty($param["benefits"])) {
                $this->set_benefit($jobHelper->tdb_jb_get_json_value($xml->{'benefits'}));
            }
            // requirement
            if(isset($xml->{'requirements'}) && !empty($param["requirements"])) {
                $this->set_requirements($xml->{'requirements'});
            }
            // summary
            if(isset($xml->{'summary'}) && !empty($paramVideo["summary"])) {
                $this->set_summary($xml->{'summary'});
            }
            // url_video
            if(isset($xml->{'url_video'}) && !empty($paramVideo["video"])) {
                $this->set_url_video($xml->{'url_video'});
            }

            // Visa
            if((isset($xml->{'visa'}) || isset($xml->{'visa_details'})) && !empty($param["required_visas"])) {
                $visa_detail = "";
                $visa = "";

                if(isset($xml->{'visa_details'})){
                    $visa_detail = $xml->{'visa_details'};
                }

                if(isset($xml->{'visa'})){
                   $visa = $jobHelper->tdb_jb_get_json_value($xml->{'visa'},true);
                }

                $this->set_visa($visa,$visa_detail);
            }
            // formated amount
            $this->set_formated_amount();
            $this->set_salary_currency();
        }
    }

    private function set_values() {
        $this->id = '';
        $this->idUrl = '';
        $this->url = '';
        $this->published_date = '';
        $this->expiration_date = '';
        $this->education = '';
        $this->email = '';
        $this->industry = '';
        $this->category = '';
        $this->amount = '';
        $this->max_amount = '';
        $this->max_currency = '';
        $this->max_basis = '';
        $this->formatedAmount = '';
        $this->wage_details = '';
        $this->negotiable = '';
        $this->imageUrl = '';
        $this->currency = '';
        $this->basis = '';
        $this->type = '';
        $this->location = '';
        $this->description = '';
        $this->short_description = '';
        $this->language = '';
        $this->joblanguage = '';
        $this->tags = '';
        $this->title = '';
        $this->salary_currency = '';
        $this->holidays = '';
        $this->condition = '';
        $this->selling_points = '';
        $this->address = '';
        $this->working_hours = '';
        $this->reason_hiring = '';
        $this->benefit = '';
        $this->requirements = '';
        $this->visa = '';
        $this->url_widget = '';
        $this->google_job = [];
        $this->url_video = '';
        $this->summary = '';
    }

    private function set_id($id = "") {
        $this->id = $id;
    }
    private function set_id_url($id = "") {
        $this->idUrl = "tdb-id-job=$id";
    }
    private function set_url($urlArray, $id, $api = 1, $urlList ='') {
        $id = "tdb-id-job=$id";

        if($urlList == ''){
            $this->url = $urlArray["cleanUrl"];
        } else {
            $this->url = $urlList;
        }

        if(strpos($this->url,$id)===FALSE ){
            $this->url .= $urlArray["method"].$id;
        }

        if($api != 1){
            $apiUrl = "&tdb-id-api=$api";
            if(strpos($this->url,$apiUrl)===FALSE ){
                $this->url .= $apiUrl;
            }
        }
    }

    private function set_url_widget($url, $id, $urlArray =[], $api = 1) {
        $idJob = "tdb-id-job=".$id;
        $method = '?';
        if(isset($urlArray["method"])){
            if ($urlArray["method"] == "&"){
                $method = '&';
            }
        }

        if(strpos($url,'?p=') !== false || strpos($url,'?page_id=') !==false){
            $method = '&';
        }

        if(isset($urlArray["cleanUrl"])){
            $this->url_widget = $urlArray["cleanUrl"];

            if(strpos($url,$idJob)===FALSE ){
                $this->url_widget .= $url . $method . $idJob;
            } else {
                $this->url_widget .= $url . $method . $idJob;
            }
        } else {
            $this->url_widget = $url . $method . $idJob;
        }



        if($api != 1){
            $apiUrl = "&tdb-id-api=$api";
            if(strpos($this->url_widget,$apiUrl)===FALSE ){
                $this->url_widget .= $apiUrl;
            }
        }
    }
    private function set_industry($industry) {
        if(is_array($industry)){
            $first_key = key($industry);
            $this->industry = $industry[$first_key];
            $this->google_job["industry"] = $industry;
        }
    }
    private function set_category($category) {
        if(is_array($category)){
            $first_key = key($category);
            $this->category = $category[$first_key];
            $this->google_job["category"] = $category;
        }
    }
    private function set_image_url($imageUrl) {
        $this->imageUrl = $imageUrl;



        $imageUrl = str_replace(' ', '', $imageUrl);
        $imageUrl = str_replace('\\', '', $imageUrl);

        if($imageUrl == ""){
            $helper = new Helper();
            $defaultImageChecked = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'defaultImageCheckVideo', 'sValue', 'sName');
            if($defaultImageChecked <> ''){
                $defaultImage = $helper->tdb_jb_get_wp_image_url($helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'defaultImage', 'sValue', 'sName'));
                if(empty($defaultImage)){
                    $this->imageUrl = 'https://images.pexels.com/photos/1323587/pexels-photo-1323587.jpeg?cs=srgb&dl=art-blocks-colorful-1323587.jpg&fm=jpg';
                } else {
                    $this->imageUrl = $defaultImage;
                }
            }

        } else {
            $this->google_job["image"] = $this->imageUrl;
        }
    }
    private function set_type($type, $typeDetail) {
        $typeTmp = $type;

        if($typeDetail <> ""){
            if($typeTmp <> ""){
                $typeTmp .= "<br/>";
            }
            $typeTmp .= $typeDetail;
        }
        $this->type = $typeTmp;
        $this->google_job["type"] = $this->get_array_type();
    }
    private function set_location($location) {
        $this->location = $location;
        $this->google_job["location"] = $this->location;
    }
    private function set_description($description) {
        $jobHelper = new JobHelper();
        $helper = new Helper();

        $shortDescriptionMaxCharacters = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'shortDescriptionMaxCharacters', 'sValue', 'sName');
        $descriptionCleanedCheck = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'descriptionCleanedCheck', 'sValue', 'sName');

        if($shortDescriptionMaxCharacters ==''){
            $shortDescriptionMaxCharacters = null;
        }

        if($descriptionCleanedCheck){
            $this->description = $jobHelper->tdb_jb_get_clean_txt($description);
        } else {
            $this->description = $description;
        }

        $this->short_description = $jobHelper->tdb_jb_get_description_short($jobHelper->tdb_jb_get_clean_short_description_txt($description), $shortDescriptionMaxCharacters);

        $this->description_clean = $jobHelper->tdb_jb_get_clean_txt_full($description);
        $this->google_job["description"] = $this->description_clean;
        $this->short_description_clean = $jobHelper->tdb_jb_get_description_short($this->description_clean, $shortDescriptionMaxCharacters);
    }
    private function set_published_date($date) {
        $this->published_date = $date;
        $this->google_job["publish_date"] = $this->published_date;
    }
    private function set_expiration_date($date) {
        $this->expiration_date = $date;
        $this->google_job["expiration_date"] = $this->expiration_date;
    }
    private function set_currency($currency) {
        $jobHelper = new JobHelper();
        $this->currency = $jobHelper->tdb_jb_get_translation(TDB_TABLE_CURRENCY,$currency);
        $this->google_job["currency"] = $currency; // Use ISO code for Google
    }
    private function set_max_currency($currency) {
        $jobHelper = new JobHelper();
        $this->max_currency = $jobHelper->tdb_jb_get_translation(TDB_TABLE_CURRENCY,$currency);
    }
    private function set_basis($basis) {
        $this->basis = $basis;
        $this->google_job["basis"] = $this->basis;
    }
    private function set_max_basis($basis) {
        $this->max_basis = $basis;
    }
    private function set_negotiable($nego) {
        if($nego == true){
            $this->negotiable = TDB_LANG_NEGOTIABLE;
        }
    }
    private function set_wage_detail($wageDetail) {
        if($wageDetail <> ""){
            $this->wage_details = "<br/>".$wageDetail;
        }
    }
    private function set_formated_amount() {
        $jobHelper = new JobHelper();
        $formatedAmount = '';
        $formatedMaxAmount = '';
        $basis = '';

        $salary = $jobHelper->tdb_jd_format_salary_currency($this->currency,$this->amount);
        /*if($this->max_amount <> '' && ($this->max_basis <> $this->basis)){
            $basis = $this->basis;
        }*/
        $basis = $this->basis;

        $formatedAmount = $salary . " " . $basis;
        if($this->max_amount <> ''){
            $maxSalary = ' ~ ' . $jobHelper->tdb_jd_format_salary_currency($this->max_currency,$this->max_amount);
            $maxBasis = " ". $this->max_basis;
            $formatedMaxAmount = $maxSalary . " " . $maxBasis;
        }
        $detailPart = " ".$this->negotiable;
        $detailPart .= $this->wage_details;

        $finalAmount = $formatedAmount . $formatedMaxAmount .$detailPart;
        $finalAmountNoDetail = $formatedAmount . $formatedMaxAmount;
        $this->formatedAmountNoDetail = trim($finalAmountNoDetail);
        $this->formatedAmount = trim($finalAmount);
    }
    private function set_salary_currency() {
        $jobHelper = new JobHelper();

        $formatedAmount = $jobHelper->tdb_jd_format_salary_currency($this->currency,$this->amount);
        $this->salary_currency = trim($formatedAmount);
    }
    private function set_language($language) {
        $helper = new Helper();
        $jobHelper = new JobHelper();
        $languageTxtTmp = "";

        foreach($language as $count => $content){
            if ($languageTxtTmp <> ""){
                $languageTxtTmp .= "<br/>";
            }
            foreach($content as $key => $val) {
                switch($key){
                    case "locale":
                        $languageTxtTmp .= $jobHelper->tdb_jb_get_country_name($val);
                        break;
                    case "ability":
                        $languageTxtTmp .= ": ". $jobHelper->tdb_jb_get_json_value($val,true);
                        break;
                    default:
                }
            }
        }
        $this->language = $languageTxtTmp;
        $this->google_job["required_language"] = $this->language;
    }
    private function set_tags($tags) {
        $tagsTxtTmp = "";
        $countTag = 0;
        foreach($tags as $count => $content){
            foreach($content as $key => $val) {
                if ($countTag == 3){
                    $tagsTxtTmp = $tagsTxtTmp . " ...";
                } else {
                    if ($countTag < 3){
                        if($tagsTxtTmp <> ''){
                            $tagsTxtTmp .= ' - ';
                        }
                        $tagsTxtTmp .= $val;
                    }
                }
                $countTag ++;
            }
        }
        $this->tags = $tagsTxtTmp;
    }
    private function set_title($title) {
        $this->title = $title;
    }
    private function set_email($email) {
        $this->email = $email;
    }
    private function set_education($education) {
        $this->education = $education;
        $this->google_job["education_level"] = $this->education;
    }
    private function set_amount($amount) {
        $this->amount = $amount;
        $this->google_job["amount"] = $this->amount;
    }
    private function set_max_amount($amount) {
        $this->max_amount = $amount;
    }
    private function set_condition($condition) {
        $this->condition = $condition;
        $this->google_job["conditions"] = $this->condition;
    }
    private function set_selling_points($selling_points) {
        $this->selling_points = $selling_points;
        $this->google_job["selling_points"] = $this->selling_points;
    }
    private function set_address($address) {
        $helper = new Helper();
        $jobHelper = new JobHelper();

        if(isset($address->country)) {
            $country = $jobHelper->tdb_jb_get_json_value($address->country);
            $this->google_job["country"] = $country;
        }
        if(isset($address->street)) {
            $street = $jobHelper->tdb_jb_get_json_value($address->street);
        }
        if(isset($address->postal_code)) {
            $postal_code = $jobHelper->tdb_jb_get_json_value($address->postal_code);
            $this->google_job["postal_code"] = $postal_code;
        }
        if(isset($address->extended)) {
            $extended = $jobHelper->tdb_jb_get_json_value($address->extended);
        }
        if(isset($address->city)) {
            $city = $jobHelper->tdb_jb_get_json_value($address->city);
            $this->google_job["city"] = $city;
        }

        $this->address = $street ." ". $extended . "<br/>" . $postal_code . " " . $city . " " . $country;
        $this->google_job["address"] = $this->address;
    }
    private function set_working_hours($working_hours) {
        $this->working_hours = $working_hours;
        $this->google_job["working_hours"] = $this->working_hours;
    }
    private function set_reason_hiring($reason_hiring) {
        $this->reason_hiring = $reason_hiring;
        $this->google_job["reason_for_hiring_details"] = $this->reason_hiring;
    }
    private function set_benefit($benefit) {
        $jobHelper = new JobHelper();
        $this->benefit = $jobHelper->tdb_jb_get_clean_txt($benefit);
        $this->google_job["benefits"] = $this->benefit;
    }

    private function set_requirements($requirements) {
        $this->requirements = $requirements;
        $this->google_job["requirements"] = $this->requirements;
    }

    private function set_visa($visa = "", $visaDetail = "") {
        $visaTmp = "";
        if($visa <> ""){
            $visaTmp = $visa;
        }
        if($visaDetail <> ""){
            if($visaTmp <> ""){
                $visaTmp .= '<br/>' . $visaDetail;
            } else {
                $visaTmp = $visaDetail;
            }
        }
        $this->visa = $visaTmp;
        $this->google_job["visas"] = $this->visa;
    }
    private function set_holidays($holidays) {
        $this->holidays = $holidays;
        $this->google_job["holidays"] = $this->holidays;
    }
    private function set_summary($summary) {
        $jobHelper = new JobHelper();
        $this->summary = $jobHelper->tdb_jb_get_clean_txt($summary);
    }
    private function set_url_video($urlVideo) {
        $jobHelper = new JobHelper();

        $this->url_video = $jobHelper->tdb_jd_parse_video($urlVideo);
    }
    private function set_agency() {
        $this->agency = get_bloginfo('name');
       // $this->google_job["hiringOrganization"] = $this->agency;
    }
    public function get_agency() {
        return $this->agency;
    }
    public function get_summary() {
        return $this->summary;
    }
    public function get_url_video() {
        return $this->url_video;
    }
    public function get_id() {
        return $this->id;
    }
    public function get_id_url() {
        return $this->idUrl;
    }
    public function get_url() {
        return $this->url;
    }
    public function get_url_widget() {
        return $this->url_widget;
    }
    public function get_education() {
        return $this->education;
    }
    public function get_published_date() {
        return $this->published_date;
    }
    public function get_industry() {
        return $this->industry;
    }
    public function get_email() {
        return $this->email;
    }
    public function get_reason_hiring() {
        return $this->reason_hiring;
    }
    public function get_category() {
        return $this->category;
    }
    public function get_amount() {
        return $this->amount;
    }
    public function get_max_amount() {
        return $this->max_amount;
    }
    public function get_formated_amount() {
        return $this->formatedAmount;
    }
    public function get_formated_amount_no_detail() {
        return $this->formatedAmountNoDetail;
    }
    public function get_image_url() {
        return $this->imageUrl;
    }
    public function get_currency() {
        return $this->currency;
    }
    public function get_max_currency() {
        return $this->currency;
    }
    public function get_basis() {
        return $this->max_currency;
    }
    public function get_max_basis() {
        return $this->max_basis;
    }
    public function get_salary_currency() {
        return $this->salary_currency;
    }
    public function get_type() {
        return $this->type;
    }
    public function get_location() {
        return $this->location;
    }
    public function get_description() {
        return $this->description;
    }
    public function get_description_clean() {
        return $this->description_clean;
    }
    public function get_short_description() {
        return $this->short_description;
    }
    public function get_short_description_clean() {
        return $this->short_description_clean;
    }
    public function get_language() {
        return $this->language;
    }
    public function get_job_language() {
        return $this->joblanguage;
    }
    public function get_tags() {
        return $this->tags;
    }
    public function get_title() {
        return $this->title;
    }
    public function get_benefit() {
        return $this->benefit;
    }

    public function get_array_email() {
        return array("value" => $this->email,"translate" => TDB_LANG_EMAIL);
    }
    public function get_array_industry() {
        return array("value" => $this->industry,"translate" => TDB_LANG_INDUSTRY);
    }
    public function get_array_category() {
        return array("value" => $this->category,"translate" => TDB_LANG_CATEGORY);
    }
    public function get_array_salary() {
        return array("value" => $this->formatedAmount,"translate" => TDB_LANG_SALARY);
    }
    public function get_array_type() {
        return array("value" => $this->type,"translate" => TDB_LANG_TYPE);
    }
    public function get_array_education() {
        return array("value" => $this->education, "translate" => TDB_LANG_EDUCATION);
    }
    public function get_array_holidays() {
        return array("value" => $this->holidays, "translate" => TDB_LANG_HOLIDAY);
    }
    public function get_array_required_language() {
        return array("value" => $this->language, "translate" => TDB_LANG_REQUIREDLANGUAGE);
    }
    public function get_array_condition() {
        return array("value" => $this->condition, "translate" => TDB_LANG_CONDITION);
    }
    public function get_array_selling_points() {
        return array("value" => $this->selling_points, "translate" => TDB_LANG_SELLINGPOINTS);
    }
    public function get_array_address() {
        return array("value" => $this->address, "translate" => TDB_LANG_ADDRESS);
    }
    public function get_array_working_hours() {
        return array("value" => $this->working_hours, "translate" => TDB_LANG_WORKINGHOURS);
    }
    public function get_array_location() {
        return array("value" => $this->location, "translate" => TDB_LANG_LOCATION);
    }
    public function get_array_visa() {
        return array("value" => $this->visa, "translate" => TDB_LANG_VISA);
    }
    public function get_array_description() {
        return array("value" => $this->description, "translate" => TDB_LANG_DESCRIPTION);
    }
    public function get_array_benefits() {
        return array("value" => $this->benefit, "translate" => TDB_LANG_BENEFITS);
    }
    public function get_array_requirements() {
        return array("value" => $this->requirements, "translate" => TDB_LANG_REQUIREMENT);
    }
    public function get_array_reason_hiring() {
        return array("value" => $this->reason_hiring, "translate" => TDB_LANG_REASONHIRING);
    }
    public function get_google_job(){
        return $this->google_job;
    }

    public function get_array_list(){
        $array= [];
        $array["type"] = $this->get_array_type();
        $array["industry"] = $this->get_array_industry();
        $array["category"] = $this->get_array_category();
        $array["salary"] = $this->get_array_salary();
        $array["email"] = $this->get_array_email();
        $array["education_level"] = $this->get_array_education();
        $array["required_language"] = $this->get_array_required_language();
        $array["holidays"] = $this->get_array_holidays();
        $array["conditions"] = $this->get_array_condition();
        $array["selling_points"] = $this->get_array_selling_points();
        $array["address"] = $this->get_array_address();
        $array["working_hours"] = $this->get_array_working_hours();
        $array["reason_for_hiring_details"] = $this->get_array_reason_hiring();
        $array["location"] = $this->get_array_location();
        $array["visas"] = $this->get_array_visa();
        $array["description"] = $this->get_array_description();
        $array["benefits"] = $this->get_array_benefits();
        $array["requirements"] = $this->get_array_requirements();

        foreach($array as $key => $value){
            if(empty($value)){
                unset($array[$key]);
            }
        }
        return $array;
    }

    public function get_array_list_tpl_2(){
        $array= [];
        $array["description"] = $this->get_array_description();
        $array["requirements"] = $this->get_array_requirements();
        $array["location"] = $this->get_array_location();
        $array["working_hours"] = $this->get_array_working_hours();
        $array["holidays"] = $this->get_array_holidays();
        $array["type"] = $this->get_array_type();
        $array["salary"] = $this->get_array_salary();
        $array["benefits"] = $this->get_array_benefits();
        $array["industry"] = $this->get_array_industry();
        $array["category"] = $this->get_array_category();
        $array["email"] = $this->get_array_email();
        $array["education_level"] = $this->get_array_education();
        $array["required_language"] = $this->get_array_required_language();
        $array["conditions"] = $this->get_array_condition();
        $array["reason_for_hiring_details"] = $this->get_array_reason_hiring();
        $array["selling_points"] = $this->get_array_selling_points();
        $array["visas"] = $this->get_array_visa();

        foreach($array as $key => $value){
            if(empty($value)){
                unset($array[$key]);
            }
        }
        return $array;
    }
}
