<?php

namespace Jobsearch\Admin;

use Jobsearch\Helper;
use Jobsearch\Helper\Translation;
use Jobsearch\SQL;

class AdminHelper {

    // Update param who have to be shown on list page
    function tdb_jb_updt_param($array) {
        global $wpdb;

        Foreach ($array as $key => $value) {
            $wpdb->delete(TDB_TABLE_PARAM, array('sName' => $key));
            $wpdb->insert(TDB_TABLE_PARAM, array('sName' => $key, 'sValue' => $value));
        }
    }

    // Update param who have to be shown on list page
    function tdb_jb_updt_tpl($array, $type) {
        global $wpdb;

        Foreach ($array as $key => $value) {
            $splitKey = explode('_', $key);
            $preName = $splitKey[0].$type;
            $language = $splitKey[1];

            $count = $wpdb->get_var("SELECT COUNT(*) FROM ". TDB_TABLE_TEMPLATE ." WHERE sName = '$preName' and sLanguage = '$language'");
            if($count == 1) {
                //test if empty, delete the line
                if (strlen(trim($value, ' \n\r\t\v\0\&nbsp;')) == 0){
                    $wpdb->delete(TDB_TABLE_TEMPLATE, array('sName' => $preName, 'sLanguage' =>$language));
                }

                $data = [ 'sValue' => $value ]; // NULL value.
                $format = [ '%s' ];  // Ignored when corresponding data is NULL, set to NULL for readability.
                $where = [ 'sName' => $preName, 'sLanguage' => $language]; // NULL value in WHERE clause.
                $where_format = [ '%s', '%s' ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
                $wpdb->update( TDB_TABLE_TEMPLATE, $data, $where, $format, $where_format );
            }
            else {
                if (strlen(trim($value, ' \n\r\t\v\0\&nbsp;')) != 0){
                    $data = array(
                        'sName' => $preName,
                        'sLanguage' => $language,
                        'sValue' => $value
                    );

                    $format = array(
                        '%s',
                        '%s',
                        '%s'
                    );
                    $wpdb->insert(TDB_TABLE_TEMPLATE, $data, $format);
                }

            }
        }
    }

    // Clear required param, mandatory param or apply param or Filter
    function tdb_jb_clear_param_sql($valueToEmpty) {
        global $wpdb;

        $wpdb->query($wpdb->prepare('UPDATE '.TDB_TABLE_PARAM.' SET sValue = %s WHERE sName LIKE "%'.$valueToEmpty.'%" ',''));
    }

    // Get all the language id used in database
    function tdb_jb_get_table_language_value ($table, $columnName1, $columnName2 ) {
        global $wpdb;

        $result = array();

        $request = "SELECT ".$columnName1 . ", ". $columnName2 ;
        $request .= " FROM " . $table;
        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $result[$ligneResult->$columnName2] = $ligneResult->$columnName1;
        }

        return $result;
    }

    // Update translation on database
    function tdb_jb_updt_database() {
        global $gTypes;
        global $gCategories;
        global $gIndustries;
        global $gLocation;
        global $gLanguages;
        global $glanguageUsedId;

        $helper = new Helper();

        $correspOldNewLanguage = array();

        $idLanguage = 0;
        $idLanguage = $this->tdb_jb_set_new_id_language($gTypes, $idLanguage);
        $idLanguage = $this->tdb_jb_set_new_id_language($gCategories, $idLanguage);
        $idLanguage = $this->tdb_jb_set_new_id_language($gIndustries, $idLanguage);
        $idLanguage = $this->tdb_jb_set_new_id_language($gLocation, $idLanguage);
        $idLanguage = $this->tdb_jb_set_new_id_language($gLanguages, $idLanguage);

        $oldLanguageUsedId = $this->tdb_jb_get_table_language_value(TDB_TABLE_LANG_USED, "nId", "sLanguageName");

        if(is_array($glanguageUsedId) && !empty($glanguageUsedId)){
            // Get the correspondence between old language id and new one before update
            foreach ($glanguageUsedId as $keyNew => $valueNew) {
                $val = $valueNew;
                foreach ($oldLanguageUsedId as $keyOld => $valueOld) {
                    if ($keyOld == $keyNew) {
                        $val = $valueOld;
                    }
                }
                if ($val <> $valueNew) {
                    $correspOldNewLanguage[$val] = $valueNew;
                }
            }
        }

        if(is_array($glanguageUsedId) && count($glanguageUsedId) > 0) {
            $this->tdb_jb_updt_table_language($glanguageUsedId, TDB_TABLE_LANG_USED, "nId", "sLanguageName");
        }
        if(is_array($gTypes) && count($gTypes) > 0) {
            $this->tdb_jb_updt_table_api_val($gTypes, TDB_TABLE_TYPE, "sName", "sTranslate", "nIdLanguage");
        }
        if(is_array($gCategories) && count($gCategories) > 0) {
            $this->tdb_jb_updt_table_api_val($gCategories, TDB_TABLE_CATEGORY, "sName", "sTranslate", "nIdLanguage");
        }
        if(is_array($gIndustries) && count($gIndustries) > 0) {
            $this->tdb_jb_updt_table_api_val($gIndustries, TDB_TABLE_INDUSTRY, "sName", "sTranslate", "nIdLanguage");
        }
        if(is_array($gLanguages) && count($gLanguages) > 0) {
            $this->tdb_jb_updt_table_api_val($gLanguages, TDB_TABLE_SKILL, "sName", "sTranslate", "nIdLanguage");
        }

        if(is_array($correspOldNewLanguage) && count($correspOldNewLanguage) > 0 ){
            $this->tdb_jb_updt_id_language_all_table($correspOldNewLanguage, TDB_TABLE_CURRENCY, "nId", "nIdLanguage");
            $this->tdb_jb_updt_id_language_all_table($correspOldNewLanguage, TDB_TABLE_LAST_UPDATE, "nId", "nIdLanguage");
            $this->tdb_jb_updt_id_language_all_table($correspOldNewLanguage, TDB_TABLE_BASIS, "nId", "nIdLanguage");
            $this->tdb_jb_updt_id_language_all_table($correspOldNewLanguage, TDB_TABLE_VISA, "nId", "nIdLanguage");
        }
    }

    //update id language on all table if something changed
    function tdb_jb_updt_id_language_all_table($array, $tableName, $column1, $column2) {
        global $wpdb;

        $idTable = array();
        if (count($array) <= 0) {
            return "";
        }

        // Get the row with value who changed and get the id
        foreach ($array as $valueOld => $valueNew) {
            if ($valueOld <> $valueNew) {
                $request = "SELECT " . $column1 . ", " . $column2 . " FROM " . $tableName . " WHERE " . $column2 . " = '" . $valueOld . "';";
                //on execute la requete
                $exec = $wpdb->get_results($request);

                foreach ($exec as $ligneResult) {
                    $idTable[$ligneResult->$column1] = $ligneResult->$column2;
                }
            }
        }
        // update all row with the id who changed
        foreach ($idTable as $id => $oldIdLanguage) {
            $data = array($column2 => $array[$oldIdLanguage]);
            $format = array(
                '%d');
            $where = array($column1 => $id);
            $formatWhere = array(
                '%d');
            $wpdb->update($tableName, $data, $where, $format, $formatWhere);
        }
    }

    // Update the language table with new id and value
    function tdb_jb_updt_table_language($array, $tableName, $column1, $column2) {
        global $wpdb;

        $sql = "TRUNCATE TABLE  $tableName;";
        $wpdb->query($sql);

        foreach ($array as $key => $content) {
            $data = array(
                $column1 => $content,
                $column2 => $key);

            $format = array(
                '%d',
                '%s');
            $wpdb->insert($tableName, $data, $format);
        }
    }

    // Update table with api value(type, category, industry, language)
    function tdb_jb_updt_table_api_val($array, $tableName, $column1, $column2, $column3) {
        global $wpdb;
        global $glanguageUsedId;

        $sql = "TRUNCATE TABLE  $tableName;";
        $wpdb->query($sql);

        foreach ($array as $key => $content) {
            foreach ($content as $language => $value) {
                if (isset($glanguageUsedId[$language]))
                {
                    $data = array(
                        $column1 => $key,
                        $column2 => $value,
                        $column3 => $glanguageUsedId[$language]);
                    $format = array(
                        '%s',
                        '%s',
                        '%d');
                    $wpdb->insert($tableName, $data, $format);
                }
            }
        }
    }

    // Set up language on (type,category,location, language)
    function tdb_jb_set_new_id_language($array, $idLanguage) {
        global $glanguageUsedId;

        if(is_array($array) && !empty($array)){
            foreach ($array as $key => $content) {
                foreach ($content as $language => $value) {
                    if (!empty($language) && !isset($glanguageUsedId[$language])) {
                        $idLanguage++;
                        $glanguageUsedId[$language] = $idLanguage;
                    }
                }
            }
        }
        return $idLanguage;
    }

    // Return html with all required field in checkbox
    function tdb_jb_get_required_field($array) {
        $translation = new Translation();
        $helper = new Helper();
        $i = 0;
        $columSize = 2;
        $html = "";
        $labelRequired["gender"] = TDB_LANG_GENDER;
        $labelRequired["email"] = TDB_LANG_EMAIL;
        $labelRequired["emailType"] = TDB_LANG_TYPEEMAIL;
        $labelRequired["phone"] = TDB_LANG_PHONENUMBER;
        $labelRequired["phoneType"] = TDB_LANG_TYPEPHONE;
        $labelRequired["nationality"] = TDB_LANG_NATIONALITY;
        $labelRequired["birthdate"] = TDB_LANG_BIRTHDATE;
        $labelRequired["postal"] = TDB_LANG_POSTALCODE;
        $labelRequired["region"] = TDB_LANG_REGION;
        $labelRequired["country"] = TDB_LANG_COUNTRY;
        $labelRequired["city"] = TDB_LANG_CITY;
        $labelRequired["street"] = TDB_LANG_STREET;
        $labelRequired["language"] = TDB_LANG_LANGUAGE;
        $labelRequired["languageAbility"] = TDB_LANG_LANGUAGEABILITY;
        $labelRequired["languageCertification"] = TDB_LANG_LANGUAGECERTIF;
        $labelRequired["languageScore"] = TDB_LANG_CERTIFSCORE;
        $labelRequired["desiredWage"] = TDB_LANG_DESIREDWAGE;
        $labelRequired["currency"] = TDB_LANG_CURRENCY;
        $labelRequired["basis"] = TDB_LANG_BASIS;
        $labelRequired["currentSalary"] = TDB_LANG_CURRENTSALARY;
        $labelRequired["currentSalaryCurrency"] = TDB_LANG_CURRENTCURRENCY;
        $labelRequired["currentSalaryBasis"] = TDB_LANG_CURRENTBASIS;
        $labelRequired["currentSalaryBonus"] = TDB_LANG_CURRENTSALARYBONUS;
        $labelRequired["currentSalaryBonusCurrency"] = TDB_LANG_BONUSCURRENCY;
        $labelRequired["currentEmploymentDepartment"] = TDB_LANG_CURRENTEMPLOYMENTDEPARTMENT;
        $labelRequired["currentEmploymentPosition"] = TDB_LANG_CURRENTEMPLOYMENTPOSITION;
        $labelRequired["currentEmploymentCompany"] = TDB_LANG_CURRENTEMPLOYMENTCOMPANY;
        $labelRequired["employementType"] = TDB_LANG_EMPLOYEMENTTYPE;
        $labelRequired["desiredIndustry"] = TDB_LANG_INDUSTRY;
        $labelRequired["desiredLocation"] = TDB_LANG_DESIREDLOCATION;
        $labelRequired["noticedPeriod"] = TDB_LANG_NOTICED_PERIOD;
        $labelRequired["desiredJobCategory"] = TDB_LANG_DESIREDCATEGORY;
        $labelRequired["certification"] = TDB_LANG_CERTIF;
        $labelRequired["nearestStation"] = TDB_LANG_NEARESTSTATION;
        $labelRequired["referrer"] = TDB_LANG_REFFERER;
        $labelRequired["source"] = TDB_LANG_SOURCE;
        $labelRequired["sourceType"] = TDB_LANG_SOURCETYPE;
        $labelRequired["visaCountry"] = TDB_LANG_VISACOUNTRY;
        $labelRequired["visaType"] = TDB_LANG_VISATYPE;
        $labelRequired["url"] = TDB_LANG_URL;
        $labelRequired["linkedin"] = TDB_LANG_LINKEDIN;
        $labelRequired["recaptcha"] = '';
        $labelRequired["facebook"] = TDB_LANG_FACEBOOK;
        $labelRequired["attachment"] = TDB_LANG_ATTACHMENT;

        $html .= '<div class="tdb-jd-row" >';

        foreach ($array as $key => $content) {
            if ($i>11) {
                $i = 0;
                $html .= "</div>";
                $html .= '<div class="tdb-jd-row" >';
            }
            $html .= $helper->tdb_jb_get_col($columSize, "checkbox", $key."Required", $content,$labelRequired[$key], "tdb-jd-input-text tdb-jd-input");
            $i+= $columSize;
        }
        $html .= "</div>";

        return $html ;
    }

    // Return html with all required field in checkbox
    function tdb_jb_get_colsized_field($array) {
        $translation = new Translation();
        $helper = new Helper();
        $i = 0;
        $columSize = 2;
        $html = "";

        $labelRequired["familyName"] = TDB_LANG_FAMILYNAME;
        $labelRequired["givenName"]  = TDB_LANG_GIVENNAME;
        $labelRequired["birthYear"] = TDB_LANG_YEARS;
        $labelRequired["birthMonth"] = TDB_LANG_MONTH;
        $labelRequired["birthDay"] = TDB_LANG_DAY;
        $labelRequired["gender"] = TDB_LANG_GENDER;
        $labelRequired["addressPostal"] = TDB_LANG_POSTALCODE;
        $labelRequired["addressCountry"] = TDB_LANG_COUNTRY;
        $labelRequired["addressRegion"] = TDB_LANG_REGION;
        $labelRequired["addressCity"] = TDB_LANG_CITY;
        $labelRequired["addressExtended"] = TDB_LANG_EXTENDED;
        $labelRequired["addressStreet"] = TDB_LANG_STREET;
        $labelRequired["nearestStation"] = TDB_LANG_NEARESTSTATION;
        $labelRequired["email"] = TDB_LANG_EMAIL;
        $labelRequired["emailType"] = TDB_LANG_TYPEEMAIL;
        $labelRequired["phone"] = TDB_LANG_PHONE;
        $labelRequired["phoneType"] = TDB_LANG_TYPEPHONE;
        $labelRequired["addEmail"] = TDB_LANG_ADDEMAIL;
        $labelRequired["addPhone"] = TDB_LANG_ADDPHONE;
        $labelRequired["delEmail"] = TDB_LANG_DELEMAIL;
        $labelRequired["delPhone"] = TDB_LANG_DELPHONE;
        $labelRequired["nationality"] = TDB_LANG_NATIONALITY;
        $labelRequired["visa"] = TDB_LANG_VISA;
        $labelRequired["visaCountry"] = TDB_LANG_VISACOUNTRY;
        $labelRequired["certifAbility"] = TDB_LANG_LANGUAGESCERTIFABILITY;
        $labelRequired["languageCertification"] = TDB_LANG_LANGUAGESCERTIF;
        $labelRequired["levelCertification"] = TDB_LANG_SCORE;
        $labelRequired["certification"] = TDB_LANG_CERTIFADMIN;
        $labelRequired["certificationText"] = TDB_LANG_CERTIFTEXT;
        $labelRequired["currentCompany"] = TDB_LANG_EMPLOYMENTCOMPANY;
        $labelRequired["currentPosition"] = TDB_LANG_EMPLOYMENTPOSITION;
        $labelRequired["currentDepartment"] = TDB_LANG_EMPLOYMENTDEPARTMENT;
        $labelRequired["currentSalaryAmount"] = TDB_LANG_CURRENTSALARY;
        $labelRequired["currentSalaryCurrency"] = TDB_TABLE_CURRENCY;
        $labelRequired["currentSalaryBasis"] = TDB_TABLE_BASIS;
        $labelRequired["bonusSalaryAmount"] = TDB_LANG_CURRENTSALARYBONUS;
        $labelRequired["bonusSalaryCurrency"] = TDB_TABLE_CURRENCY;
        $labelRequired["bonusSalaryBasis"] = TDB_TABLE_BASIS;
        $labelRequired["desiredSalaryAmount"] = TDB_LANG_DESIREDWAGE;
        $labelRequired["desiredSalaryCurrency"] = TDB_TABLE_CURRENCY;
        $labelRequired["desiredSalaryBasis"] = TDB_TABLE_BASIS;
        $labelRequired["desiredEmployment"] = TDB_LANG_EMPLOYEMENT;
        $labelRequired["desiredLocation"] = TDB_LANG_DESIREDLOCATION;
        $labelRequired["desiredIndustry"] = TDB_LANG_DESIREDINDUSTRY;
        $labelRequired["desiredCategory"] = TDB_LANG_DESIREDCATEGORY;
        $labelRequired["findUs"] = TDB_LANG_REFFERERHOWDIDYOUEAR;
        $labelRequired["noticedPeriod"] = TDB_LANG_NOTICED_PERIOD;
        $labelRequired["facebook"] = TDB_LANG_FACEBOOK;
        $labelRequired["linkedin"] = TDB_LANG_LINKEDIN;
        $labelRequired["urlRegister"] = TDB_LANG_URL;
        $labelRequired["resumeRegister"] = TDB_LANG_RESUME;
        $labelRequired["privacyPolicyLabel"] = TDB_LANG_PRIVACYPOLICYTEXT;
        $labelRequired["urlApplyLabel"] = TDB_LANG_URL_APPLY_TEXT;
        $labelRequired["privacyPolicyCheck"] = TDB_LANG_PRIVACYPOLICYCHECK;

        $html .= '<div class="tdb-jd-row" >';

        foreach ($array as $key => $content) {
            if ($i>11) {
                $i = 0;
                $html .= "</div>";
                $html .= '<div class="tdb-jd-row" >';
            }
            $html .= $helper->tdb_jb_get_col($columSize, "number", $key."RegisterColSize", $content,$labelRequired[$key], "tdb-jd-input-text tdb-jd-input");
            $i+= $columSize;
        }
        $html .= "</div>";

        return $html ;
    }

    //Return html with all field have to be shown in detail page in checkbox
    function tdb_jb_get_show_field($array, $paramEndName ='Param') {
        $translation = new Translation();
        $helper = new Helper();
        $i = 0;
        $columSize = 2;
        $html = "";

        $label["location"] = TDB_LANG_LOCATION;
        $label["amount"] = TDB_LANG_WAGEAMOUNT;
        $label["currency"] = TDB_LANG_WAGECURRENCY;
        $label["maxAmount"] = TDB_LANG_MAXWAGEAMOUNT;
        $label["maxCurrency"] = TDB_LANG_MAXWAGECURRENCY;
        $label["negotiable"] = TDB_LANG_WAGENEGOTIABLE;
        $label["basis"] = TDB_LANG_WAGEBASIS;
        $label["maxBasis"] = TDB_LANG_MAXWAGEBASIS;
        $label["type"] = TDB_LANG_EMPLOYEMENTTYPE;
        $label["type_detail"] = TDB_LANG_TYPEDETAIL;
        $label["requirements"] = TDB_LANG_JOBREQUIREMENT;
        $label["education_level"] = TDB_LANG_EDUCATIONLVL;
        $label["language"] = TDB_LANG_LANGUAGEREQUIRED;
        $label["currentSalary"] = TDB_LANG_CURRENTSALARY;
        $label["currentSalaryBonus"] = TDB_LANG_CURRENTSALARYBONUS;
        $label["currentEmploymentDepartment"] = TDB_LANG_CURRENTEMPLOYMENTDEPARTMENT;
        $label["currentEmploymentPosition"] = TDB_LANG_CURRENTEMPLOYMENTPOSITION;
        $label["currentEmploymentCompany"] = TDB_LANG_CURRENTEMPLOYMENTCOMPANY;
        $label["holidays"] = TDB_LANG_HOLIDAY;
        $label["conditions"] = TDB_LANG_CONDITION;
        $label["selling_points"] = TDB_LANG_SELLINGPOINTS;
        $label["established_year"] = TDB_LANG_ESTABLISHEDYEARS;
        $label["working_hours"] = TDB_LANG_WORKINGHOURS;
        $label["probationPeriod"] = TDB_LANG_PROBATIONPERIOD;
        $label["reason_for_hiring_details"] = TDB_LANG_REASONHIRINGDETAILS;
        $label["category"] = TDB_LANG_CATEGORY;
        $label["industry"] = TDB_LANG_INDUSTRY;
        $label["wage_details"] = TDB_LANG_WAGEDETAIL;
        $label["required_visas"] = TDB_LANG_REQUIREDVISA;
        $label["benefits"] = TDB_LANG_BENEFITS;
        $label["description"] = TDB_LANG_DESCRIPTION;
        $label["video"] = TDB_LANG_VIDEO;
        $label["summary"] = TDB_LANG_SUMMARY;
        $label["defaultImageCheck"] = TDB_LANG_DEFAULTIMGCHECK;

        $html.= '<div class="tdb-jd-row" >';

        foreach ($array as $key => $content) {
            if ($i>11) {
                $i = 0;
                $html.= "</div>";
                $html.= '<div class="tdb-jd-row" >';
            }
            $html.= $helper->tdb_jb_get_col(2, "checkbox", $key.$paramEndName, $content, $label[$key], "tdb-jd-input-text tdb-jd-input");
            $i+= $columSize;
        }
        $html.= "</div>";

        return $html ;
    }

    //Return html with all field have to be shown in detail page in checkbox
    function tdb_jb_get_sort_by_field($array) {
        $translation = new Translation();
        $helper = new Helper();
        $i = 0;
        $columSize = 2;
        $html = "";

        $label["title"] = TDB_LANG_TITLE;
        $label["date"] = TDB_LANG_DATE;
        $label["salary"] = TDB_LANG_SALARY;

        $html.= '<div class="tdb-jd-row" >';

        foreach ($array as $key => $content) {
            if ($i>11) {
                $i = 0;
                $html.= "</div>";
                $html.= '<div class="tdb-jd-row" >';
            }
            $html.= $helper->tdb_jb_get_col(2, "checkbox", $key."SortBy", $content, $label[$key], "tdb-jd-input-text tdb-jd-input");
            $i+= $columSize;
        }
        $html.= "</div>";

        return $html ;
    }

    //Return html field with all field have to be shown in apply form in checkbox
    function tdb_jb_get_apply_field($array) {
        $translation = new Translation();
        $helper = new Helper();
        $i = 0;
        $columSize = 2;
        $html = "";

        $label["gender"] = TDB_LANG_GENDER;
        $label["emails"] = TDB_LANG_EMAILS;
        $label["emailsType"] = TDB_LANG_TYPEEMAIL;
        $label["phoneNumbers"] = TDB_LANG_PHONENUMBER;
        $label["phoneType"] = TDB_LANG_PHONENUMBERTYPE;
        $label["address"] = TDB_LANG_ADDRESS;
        $label["birthdate"] = TDB_LANG_BIRTHDATE;
        $label["nationality"] = TDB_LANG_NATIONALITY;
        $label["languages"] = TDB_LANG_LANGUAGES;
        $label["languageCertifications"] = TDB_LANG_LANGUAGESCERTIF;
        $label["currentSalary"] = TDB_LANG_CURRENTSALARY;
        $label["currentSalaryBonus"] = TDB_LANG_CURRENTSALARYBONUS;
        $label["currentEmploymentDepartment"] = TDB_LANG_CURRENTEMPLOYMENTDEPARTMENT;
        $label["currentEmploymentPosition"] = TDB_LANG_CURRENTEMPLOYMENTPOSITION;
        $label["currentEmploymentCompany"] = TDB_LANG_CURRENTEMPLOYMENTCOMPANY;
        $label["desiredWage"] = TDB_LANG_DESIREDWAGE;
        $label["desiredEmploymentTypes"] = TDB_LANG_DESIREDEMPLOYEMENTTYPE;
        $label["desiredJobCategory"] = TDB_LANG_DESIREDCATEGORY;
        $label["desiredIndustry"] = TDB_LANG_DESIREDINDUSTRY;
        $label["desiredLocation"] = TDB_LANG_DESIREDLOCATION;
        $label["neareststation"] = TDB_LANG_NEARESTSTATION;
        $label["certification"] = TDB_LANG_CERTIF;
        $label["referrer"] = TDB_LANG_REFFERER;
        $label["visa"] = TDB_LANG_VISA;
        $label["noticedPeriod"] = TDB_LANG_NOTICED_PERIOD;
        $label["facebook"] = TDB_LANG_FACEBOOK;
        $label["linkedin"] = TDB_LANG_LINKEDIN;
        $label["recaptcha"] = TDB_LANG_RECAPTCHA;
        $label["noticePeriod"] = TDB_LANG_NOTICED_PERIOD;
        $label["url"] = TDB_LANG_URL;
        $label["attachment"] = TDB_LANG_ATTACHMENT;

        $html.= '<div class="tdb-jd-row" >';

        foreach ($array as $key => $content) {
            if ($i>11) {
                $i = 0;
                $html.= "</div>";
                $html.= '<div class="tdb-jd-row" >';
            }
            $html.= $helper->tdb_jb_get_col(2, "checkbox", $key."Apply", $content, $label[$key], "tdb-jd-input-text tdb-jd-input");
            $i+= $columSize;
        }
        $html.= "</div>";

        return $html ;
    }

    // Return the number of apply done
    function tdb_jb_total_row($table) {
        global $wpdb;
        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        return $rowcount;
    }

    // Get applications from database
    function tdb_jb_get_applications($nbRow = 10,$offset = 0) {
        global $wpdb;
        $applyAr = [];

        $request = "SELECT nId, nIdApi, nIdJob, sName, sDate, sTimezone, sJson FROM ".TDB_TABLE_APPLY
            . " ORDER BY nId DESC LIMIT $nbRow OFFSET $offset;";

        $exec = $wpdb->get_results($request);

        foreach ($exec as $result) {
            $applyAr[$result->nId]["id"] = $result->nId;
            $applyAr[$result->nId]["idApi"] = $result->nIdApi;
            $applyAr[$result->nId]["idJob"] = $result->nIdJob;
            $applyAr[$result->nId]["name"] = $result->sName;
            $applyAr[$result->nId]["date"] = $result->sDate;
            $applyAr[$result->nId]["timezone"] = $result->sTimezone;
            $applyAr[$result->nId]["json"] = $result->sJson;
        }
        return $applyAr;
    }

    // Get single application from the database
    function tdb_jb_get_application($id) {
        global $wpdb;
        $applyAr = [];

        $request = "SELECT nId, nIdApi, nIdJob, sName, sDate, sTimezone, sJson FROM ".TDB_TABLE_APPLY . " WHERE nId = $id;";

        $exec = $wpdb->get_results($request);

        foreach ($exec as $result) {
            $applyAr[$result->nId]["id"] = $result->nId;
            $applyAr[$result->nId]["idApi"] = $result->nIdApi;
            $applyAr[$result->nId]["idJob"] = $result->nIdJob;
            $applyAr[$result->nId]["name"] = $result->sName;
            $applyAr[$result->nId]["date"] = $result->sDate;
            $applyAr[$result->nId]["timezone"] = $result->sTimezone;
            $applyAr[$result->nId]["json"] = $result->sJson;
        }
        return $applyAr;
    }

    // Get attachments from database
    function tdb_jb_get_attachments() {
        global $wpdb;
        $applyAAr = [];
        $countAAr = [];

        $request = "SELECT nId, nIdApply, sName, sFile FROM ".TDB_TABLE_APPLY_ATTACHMENT. ";";

        $exec = $wpdb->get_results($request);

        foreach ($exec as $result) {
            if(isset($countAAr[$result->nIdApply])){
                $countAAr[$result->nIdApply] ++;
            } else {
                $countAAr[$result->nIdApply] = 1;
            }
            $applyAAr[$result->nIdApply][$countAAr[$result->nIdApply]]["name"] = $result->sName;
            $applyAAr[$result->nIdApply][$countAAr[$result->nIdApply]]["file"] = $result->sFile;
        }
        return $applyAAr;
    }

    // Get the attachments for an application
    function tdb_jb_get_attachment_for_application($id) {
        global $wpdb;
        $applyAAr = [];
        $countAAr = [];

        $request = "SELECT nId, nIdApply, sName, sFile FROM ".TDB_TABLE_APPLY_ATTACHMENT. " WHERE nIdApply = $id;";

        $exec = $wpdb->get_results($request);

        foreach ($exec as $result) {
            if(isset($countAAr[$result->nIdApply])){
                $countAAr[$result->nIdApply] ++;
            } else {
                $countAAr[$result->nIdApply] = 1;
            }
            $applyAAr[] = [
                "name" => $result->sName,
                "file" => $result->sFile
            ];
        }

        return $applyAAr;
    }

    //Set up pagination in the application history page
    function tdb_jb_get_pagination_admin($totalApply,$offset,$NbJobToShow,$urlStart) {
        $translation = new Translation();
        $nbLinkToShow = 6;
        $nbLinkToShowBefore = 0;
        $nbLinkToShowAfter = 0;
        $nDiffPageToShowBefore = 0;
        $nDiffPageToShowAfter = 0;

        $helper = new Helper();

        $html = "" ;

        if($offset > 0){
            $current = $offset / $NbJobToShow + 1;
        } else {
            $current = 1;
        }

        $html.= "<div id ='pagination'>";
        $html.= "<div class ='tdb-jd-row'>";
        $html.= "<div class ='tdb-jd-col-11'>";
        $html.= '<ul class="tdb-jd-pagination">';
        //nb of job
        $nb = $totalApply;
        // nb page to show
        $nbPage = ceil($nb / $NbJobToShow);

        /*Calculate number of page to show before and after */
        if($current == $nbPage) {
            $nbLinkToShowBefore = $nbLinkToShow;
        }
        if($current == 1) {
            $nbLinkToShowAfter = $nbLinkToShow;
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

        /* Show first page link if the current page isn t the first */
        if($current !== 1) {
            $newOffset = 0 ;
            $html.= '<li><a href="'.$urlStart.'&offset='.$newOffset.'" class="tdb-jd-first-page">'.TDB_LANG_FIRST.'</a></li>';
        }
        /* Show the previous link if it have  */
        if($current !== 1) {
            $newOffset = ($current - 1) * $NbJobToShow - $NbJobToShow ;
            $html.= '<li><a href="'.$urlStart.'&offset='.$newOffset.'"  class="tdb-jd-previous-page">'.TDB_LANG_PREVIOUS.'</a></li>';
        }

        /* Show all the previous link before the current page */
        for($i = ($current - $nbLinkToShowBefore) ; $i < $current ; $i++) {
            if($i > 0 ) {
                $newOffset = $i * $NbJobToShow - $NbJobToShow ;
                $html.= '<li><a href="'.$urlStart.'&offset='.$newOffset.'"  class="tdb-jd-page">'.$i.'</a></li>';
            }
        }

        /* show the link of the c urrent page */
        $html.= '<li><a class="active">'.$current.'</a></li>';

        /* show the followed link */
        $nb = $current + 1;
        for($i = ($current + 1) ; $i <= $nbPage ; $i++) {
            if($nb <= $nbPage && $nb <= ($current + $nbLinkToShowAfter)) {
                $newOffset = $i * $NbJobToShow - $NbJobToShow ;
                $html.= '<li><a href="'.$urlStart.'&offset='.$newOffset.'"  class="tdb-jd-page">'.$i.'</a></li>';
                $nb++;
            }
        }

        /* show the next link if the current page isn t the last */
        if($current < $nbPage ) {
            $newOffset = ($current + 1) * $NbJobToShow - $NbJobToShow ;
            $html.= '<li><a href="'.$urlStart.'&offset='.$newOffset.'"  class="tdb-jd-next-page">'.TDB_LANG_NEXT.'</a></li>';
        }

        /* Show last page link if the current page isn t the first */
        if($current < $nbPage ) {
            $newOffset = $nbPage * $NbJobToShow - $NbJobToShow ;
            $html.= '<li><a href="'.$urlStart.'&offset='.$newOffset.'"  class="tdb-jd-last-page">'.TDB_LANG_LAST.'</a></li>';
        }

        $option = '';
        // Listbox to chose the page
        for ($i=1;$i<=$nbPage;$i++){
            $newOffset = $i * $NbJobToShow - $NbJobToShow ;

            if ($i == $current){
                $option .= "<option selected= 'true' >$i</option>";
            } else {
                $option .= "<option value ='".get_admin_url()."$urlStart&offset=$newOffset' >$i</option>";
            }
        }

        $html.= '</ul>';
        $html.= "</div>";
        $html.= "<div class ='tdb-jd-col-1' id='applyPage' >";
        $html.= $helper->tdb_jb_get_col("","select","pageJob",$option);
        $html.= "</div>";
        $html.= "</div>";

        return $html;
    }


    // Format all content for the application history page
    function tdb_jb_format_content($id){
        global $wpdb;
        $translation = new Translation();

        $translatArray["job"] = TDB_LANG_JOB1;
        $translatArray["name"] = TDB_LANG_NAME;
        $translatArray["birthdate"] = TDB_LANG_BIRTHDATE;
        $translatArray["gender"] = TDB_LANG_GENDER;
        $translatArray["emails"] = TDB_LANG_EMAIL;
        $translatArray["phoneNumbers"] = TDB_LANG_PHONENUMBER;
        $translatArray["address"] = TDB_LANG_ADDRESS;
        $translatArray["nationality"] = TDB_LANG_NATIONALITY;
        $translatArray["nearestStation"] = TDB_LANG_NEARESTSTATION;
        $translatArray["employments"] = TDB_LANG_EMPLOYEMENT;
        $translatArray["languages"] = TDB_LANG_LANGUAGES;
        $translatArray["languageCertifications"] = TDB_LANG_LANGUAGECERTIF;
        $translatArray["certification"] = TDB_LANG_CERTIF;
        $translatArray["desiredWage"] = TDB_LANG_DESIREDWAGE;
        $translatArray["visas"] = TDB_LANG_VISAS;
        $translatArray["desiredEmploymentTypes"] = TDB_LANG_DESIREDEMPLOYEMENT;
        $translatArray["desiredJobCategory"] = TDB_LANG_DESIREDCATEGORY;
        $translatArray["desiredIndustry"] = TDB_LANG_DESIREDINDUSTRY;
        $translatArray["desiredIndustries"] = TDB_LANG_DESIREDINDUSTRY;
        $translatArray["desiredLocation"] = TDB_LANG_DESIREDLOCATION;
        $translatArray["desiredLocations"] = TDB_LANG_DESIREDLOCATION;
        $translatArray["referrer"] = TDB_LANG_REFFERER;
        $translatArray["noticePeriod"] = TDB_LANG_NOTICED_PERIOD;
        $translatArray["url"] = TDB_LANG_URL;
        $translatArray["facebook"] = TDB_LANG_FACEBOOK;
        $translatArray["linkedin"] = TDB_LANG_LINKEDIN;
        $translatArray["recaptcha"] = TDB_LANG_RECAPTCHA;
        $translatArray["sourceType"] = TDB_LANG_SOURCETYPE;
        $translatArray["tagGroup1"] = TDB_LANG_TAGGROUP;
        $translatArray["tagGroup2"] = TDB_LANG_TAGGROUP;
        $translatArray["tagGroup3"] = TDB_LANG_TAGGROUP;
        $translatArray["tagGroup4"] = TDB_LANG_TAGGROUP;
        $translatArray["jobTitleTag"] = TDB_LANG_TAGJOBTITLE;
        $translatArray["privacyPolicy"] = TDB_LANG_PRIVACYPOLICY;
        $translatArray["recaptchaKey"] = TDB_LANG_RECAPTCHAKEY;
        $translatArray["recaptchaSecret"] = TDB_LANG_RECAPTCHASECRET;

        $translatTitle["socialMedia"] = TDB_LANG_SOCIALMEDIA;
        $translatTitle["employment"] = TDB_LANG_EMPLOYEMENT;
        $translatTitle["salary"] = TDB_LANG_SALARY;
        $translatTitle["visa"] = TDB_LANG_VISA;
        $translatTitle["skills"] = TDB_LANG_SKILLCERTIF;
        $translatTitle["contact"] = TDB_LANG_CONTACTDETAIL;
        $translatTitle["personal"] = TDB_LANG_PERSONALINFO;
        $translatTitle["source"] = TDB_LANG_SOURCE;
        $translatTitle["job"] = TDB_LANG_JOB;
        $translatTitle["fromTemplate"] = TDB_LANG_FROMTEMPLATE;
        $translatTitle["ccTemplate"] = TDB_LANG_CCTEMPLATE;
        $translatTitle["bccTemplate"] = TDB_LANG_BCCTEMPLATE;

        $contentArray["socialMedia"] = "";
        $contentArray["employment"] = "";
        $contentArray["salary"] = "";
        $contentArray["visa"] = "";
        $contentArray["skills"] = "";
        $contentArray["contact"] = "";
        $contentArray["personal"] = "";
        $contentArray["source"] = "";
        $contentArray["job"] = "";

        $bResult = false;

        $request = "SELECT 1stlvl, 2ndlvl, 3ndlvl1, 3ndlvl2, val, val1, val2 FROM ".TDB_TABLE_APPLY_DETAIL." WHERE nIdApply = $id";

        $exec = $wpdb->get_results($request);

        foreach ($exec as $result) {
            $bResult = true;
            $val = $translatArray[$result->{'1stlvl'}] . " : ";
            switch ($result->{'1stlvl'}) {
                case 'employments':
                    $val .= $result->val1 . "<br/>".$result->{'3ndlvl2'}. ": ".$result->val2;
                    break;
                case 'emails':
                case 'phoneNumbers':
                case 'languages':
                case 'languageCertifications':
                case 'visas':
                    $val .= $result->val1 . "<br/>".$result->{'3ndlvl2'}. ": ".$result->val2;
                    break;
                default:
                    $val .= $result->val;
                    break;
            }

            switch ($result->{'1stlvl'}) {
                case 'name':
                case 'birthdate':
                case 'gender':
                    $contentArray['personal'] .= "<br/>".$val;
                    break;
                case 'emails':
                case 'phoneNumbers':
                case 'address':
                case 'nationality':
                case 'nearestStation':
                    $contentArray["contact"] .= "<br/>".$val;
                    break;
                case 'languages':
                case 'languageCertifications':
                case 'certification':
                    $contentArray["skills"] .= "<br/>".$val;
                    break;
                case 'desiredWage':
                    $contentArray["salary"] .= "<br/>".$val;
                    break;
                case 'visas':
                    $contentArray["visa"]  .= "<br/>".$val;
                    break;
                case 'employment' :
                case 'employments' :
                case 'desiredEmploymentTypes':
                case 'desiredJobCategory':
                case 'desiredIndustry':
                case 'desiredIndustries':
                case 'noticePeriod':
                case 'desiredLocation':
                case 'desiredLocations':
                    $contentArray["employment"] .= "<br/>".$val;
                    break;
                case 'url':
                case 'facebook':
                case 'linkedin':
                    $contentArray["socialMedia"] .= "<br/>".$val;
                    break;
                case 'referrer':
                case 'sourceType':
                case 'privacyPolicy':
                    $contentArray["source"] .= "<br/>".$val;
                    break;
                default:
            }
        }

        $request = "SELECT sjson FROM ".TDB_TABLE_APPLY." WHERE nId = $id";
        $exec = $wpdb->get_results($request);
        $json = $exec[0]->sjson;

        $html = "";
        if ($bResult == true){
            $countCol = 0;
            $html = "";
            foreach($contentArray as $key => $value){
                if($value <> ""){
                    if($countCol == 4){
                        $html .= '</div>';
                        $countCol = 0 ;
                    }

                    if($countCol == 0){
                        $html .= '<div class="tdb-jd-row tdb-jd-admin-border tdb-jd-admin-header-color">';
                        $countCol ++ ;
                    }

                    $tmpVal = substr($value,5);
                    $html .= '<div class="tdb-jd-col-3 ">';
                    $html .= '<h4>'.$translatTitle[$key].'</h4>';
                    $html .= $tmpVal;
                    $html .= '</div>';
                }
            }
            $html .= '<div class="tdb-jd-col-12"><h4>JSON</h4>'.$json.'</div>';
            $html .= "</div>";
        }
        return $html;
    }

    function tdb_jb_mime_content_type($ext) {

        $mime_types = array(
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml'
        );

        if (array_key_exists($ext, $mime_types)) {
            return true;
        }
        else{
            return false;
        }
    }

    function tdb_jb_upload_image($fileName) {
        $socialLogo = '';
        /*Update file logo if it has*/
        if(isset($_FILES[$fileName]['name']) && $_FILES[$fileName]['name'] <> ''){
            $helper = new Helper();

            $logo = $_FILES[$fileName];
            $maxsize    = 3097152;
            $isValid = true;
            $errormsg = '';
            $ext = strtolower(array_pop(explode('.',$logo['name'])));
            if (!$this->tdb_jb_mime_content_type($ext)) {
                $isValid = false;
                $errormsg .= TDB_LANG_ERRORUPLOADEXTENSION;
            }

            if(($logo['size'] >= $maxsize) || ($logo["size"] == 0)) {
                $isValid = false;
                if($errormsg <> ''){
                    $errormsg .= ', ';
                }
                $errormsg .= TDB_LANG_ERRORUPLOADSIZE . '3 mo';

            }
            if ($isValid) {
                // Use the wordpress function to upload
                // test_upload_pdf corresponds to the position in the $_FILES array
                // 0 means the content is not associated with any other posts
                $uploadedId = media_handle_upload($fileName, 0);

                // Error checking using WP functions
                if(is_wp_error($uploadedId)){
                    $errormsg .= "Error uploading file: " . $uploadedId->get_error_message();
                }else{
                    if($helper->tdb_jb_validate_data($uploadedId,'text')){
                        return $helper->tdb_jb_sanitize($uploadedId,'text');
                    }
                }
            }

            if($errormsg <> ''){
                echo $errormsg;
            }
        }
        return false;
    }

    function tdb_jb_update_param($helper, $paramName, $sanitizeType = '', $dataType ='') {
        if(isset($_POST[$paramName])){
            if($helper->tdb_jb_validate_data($_POST[$paramName], $dataType)){
                return $helper->tdb_jb_sanitize($_POST[$paramName], $sanitizeType);
            }
        }

        return '';
    }

    function tdb_jb_update_css_param($helper, $paramName, $sanitizeType = '', $dataType ='') {
        if(isset($_POST[$paramName])){
            if($helper->tdb_jb_validate_data($_POST[$paramName], $dataType)){
                return '#' . $helper->tdb_jb_sanitize($_POST[$paramName], $sanitizeType);
            }
        }

        return '';
    }

    function tdb_jb_update_clean_param($helper, $paramName, $sanitizeType = '', $dataType ='') {
        if(isset($_POST[$paramName])){
            if($helper->tdb_jb_validate_data($_POST[$paramName], $dataType)){
                $tmpVar =  $helper->tdb_jb_sanitize(str_replace('"',"'",$_POST[$paramName]), $sanitizeType);
                return str_replace('\\',"",$tmpVar);
            }
        }

        return '';
    }

    function tdb_jb_update_multiple_textarea_param(Helper $helper, $paramName, $sanitizeType = '', $defaultUpper = true, $dataType ='') {
        $paramString = '';
        if(isset($_POST[$paramName])){
            if($helper->tdb_jb_validate_data($_POST[$paramName], $dataType)){
                $content = $helper->tdb_jb_sanitize(str_replace(".", "", $_POST[$paramName]), $sanitizeType);
                $content = str_replace(",","",$content);
                $content = str_replace(";","",$content);
                $content = str_replace(":","",$content);
                $content = str_replace(" ","",$content);

                $content = explode("\n",$content);


                $i = 0;
                foreach($content as $key=>$value){
                    if($value <> ""){
                        $i++;
                        $paramString .= ";".$i.":".$value;
                    }
                }

                $paramString = substr($paramString,1);
                $paramString = preg_replace('/\s+/', '', $paramString);
                if($defaultUpper == true){
                    $paramString = strtoupper($paramString);
                } else {
                    $paramString = strtolower($paramString);
                }

            }
        }

        return $paramString;
    }

    function tdb_jb_update_category_param(Helper $helper, $paramName, $dataType ='') {
        $paramString = '';
        if(isset($_POST[$paramName])){
            if($helper->tdb_jb_validate_data($_POST[$paramName], $dataType)){
                $i = 0;
                foreach($_POST[$paramName] as $key){
                    if($key <> ""){
                        $i++;
                        $paramString .= ";".$i.":".$key;
                    }
                }
            }
        }
        return $paramString;
    }

    // return a list of option for select with the array data we send
    function tdb_jb_get_opt_tag_select($array, $defaultValue = "") {
        $helper = new Helper();
        $valueTmp = "";
        $selectedContent = 'selected="true"';
        $val =[];
        $html = "<option value=''></option>";
        $acceptedLanguage = $helper->tdb_jb_get_current_language();
        $count = 1;

        if(is_array($array) && !empty($array)){
            foreach ($array as $key => $content) {
                $bIsSelected = false ;
                $bInserted = false ;
                foreach ($content as  $languageKey => $languageValue) {
                    $langageSubstr = substr($languageKey,0,2);
                    if($langageSubstr == $acceptedLanguage) {
                        if($defaultValue == $key){
                            $selected = $selectedContent;
                        } else {
                            $selected = '';
                        }
                        $val[$key] = "<option value='$key' $selected>$languageValue</option>";

                        $bInserted = true;
                    }

                    if($bInserted == false){
                        if($defaultValue == $key){
                            $selected = $selectedContent;
                        } else {
                            $selected = '';
                        }

                        if(isset($content["en"])){
                            $val[$key] =  "<option value='$key' $selected>".$content["en"]."</option>";
                        }
                        elseif (isset($content["ja"])){
                            $val[$key] =  "<option value='$key' $selected>".$content["ja"]."</option>";
                        } else {
                            if(isset($content["zh"]))
                                $val[$key]=  "<option value='$key' $selected>".$content["zh"]."</option>";
                        }
                    }
                }
            }
        }


        if(is_array($val) && !empty($val)){
            foreach($val as $key => $value){
                $html .= $value;
            }
        }

        return $html;
    }

    // return a list of option for select with the array data we send
    function tdb_jb_get_opt_select_template($array,$selectedValue = "") {
        $selectedContent = 'selected="true"';

        $val = "";
        foreach ($array as $key => $value) {
            $bIsSelected = false;
            if ($selectedValue == $key) {
                $val .= "<option value='$key' $selectedContent>$value</option>";
                $bIsSelected = true;
            }

            if($bIsSelected == false){
                $val .=  "<option value='$key' >$value</option>";
            }
        }

        return $val;
    }

    // Get parameter value from the database wordpress, need to set the table, content name and column name
    function tdb_jb_get_api_data($table, $columnNameId, $columnNameName) {
        global $wpdb;
        $api = [];

        $request = "SELECT '$columnNameId' ,'$columnNameName' FROM $table ;" ;
        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $api[$ligneResult->$columnNameId] =   $ligneResult->$columnNameName;
        }
        return $api;
    }

    function tdb_jb_get_SearchApi() {
        global $wpdb;

        $apiKeyArray = [];
        $request = "SELECT sValue, nIdApi FROM " . TDB_TABLE_PARAM ." WHERE sName ='apiSearch' ORDER BY nIdApi;" ;

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $apiKeyArray[$ligneResult->nIdApi] = $ligneResult->sValue;
        }
        return $apiKeyArray;
    }

    function tdb_jb_get_KeyApi() {
        global $wpdb;

        $apiKeyArray = [];
        $request = "SELECT sValue, nIdApi FROM " . TDB_TABLE_PARAM ." WHERE sName ='apiKey' ORDER BY nIdApi;" ;

        $exec = $wpdb->get_results($request);

        foreach ($exec as $ligneResult) {
            $apiKeyArray[$ligneResult->nIdApi] = $ligneResult->sValue;
        }
        return $apiKeyArray;
    }

    function tdb_remove_link() {
        global $wpdb;

        $request = "DELETE FROM " . TDB_TABLE_PARAM ." WHERE sName ='Link' OR sName ='apiKey' OR sName ='apiPage' OR sName ='apiSearch'" ;

        $wpdb->get_results($request);
    }

    function tdb_insert_link($api, $link, $key, $page, $search) {
        $sql = new SQL();
        $sql->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'Link',"sValue" => $link,'nIdApi' => $api));
        $sql->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'apiKey',"sValue" => $key,'nIdApi' => $api));
        $sql->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'apiPage',"sValue" => $page,'nIdApi' => $api));
        $sql->tdb_jb_insert_line(TDB_TABLE_PARAM, array('sName' => 'apiSearch',"sValue" => $search,'nIdApi' => $api));
    }

    function tdb_jd_generate_api_field($count, $apiLinks, $apiKeys, $apiJobPages, $apiJobSearchs) {
        $helper = new Helper();

        $html = "";
        $html .= "<div class='tdb-jd-row' id ='apiRow$count'>";
        $html .= $helper->tdb_jb_get_col(3, "text", "apiLink$count", $apiLinks, TDB_LANG_APILINK, "tdb-jd-input-text tdb-jd-input","","","","","","","",TDB_LANG_COMPANYURL);
        $html .= $helper->tdb_jb_get_col(2, "text", "apiKey$count", $apiKeys, TDB_LANG_APIKEY, "tdb-jd-input-text tdb-jd-input");
        $html .= $helper->tdb_jb_get_col(3, "select", "apiPage$count",$this->tdb_jb_get_opt_pages($apiJobPages),TDB_LANG_JOBPAGE,"tdb-jd-custom-select tdb-jd-input");
        $html .= $helper->tdb_jb_get_col(3, "select", "apiSearch$count",$this->tdb_jb_get_opt_pages($apiJobSearchs),TDB_LANG_JOBSEARCH,"tdb-jd-custom-select tdb-jd-input");
        $html .= "<div class='tdb-jd-col-1 tdb-jd-api-counter' >$count</div>";
        $html .= "</div>";

        return $html;
    }

    function tdb_jd_generate_template_field($typeTemplate, $language = 'default', $value='', $valueSubject = '') {
        $helper = new Helper();
        $settings  = array('media_buttons' => false,
            'textarea_rows' => 15,
            'textarea_name' => $typeTemplate."_".$language,
            'editor_height' => 425);
        $default_content=html_entity_decode($value);
        $default_content=stripslashes($default_content);
        $html = '';
        $html .= "<div class='tdb-jd-row' id ='row_subject_".$typeTemplate."_".$language."'>";
        $html .= $helper->tdb_jb_get_col(8, "text", "submit_template_subject_$language", $valueSubject, TDB_LANG_SUBJECT, "tdb-jd-input-text");
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row' id ='row_".$typeTemplate."_".$language."'>";
        $html .= "<div class='tdb-jd-col-8' >";
        $html .= $this->tdb_jd_bc_get_wp_editor( $default_content, $typeTemplate."_".$language, $settings  );
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='tdb-jd-row'>";
        $html .= "<div class='tdb-jd-col-8 ' id ='tag_selector_".$language."' >";
        $html .= $this->tdb_generate_shortcode_tpl_button('row_'.$typeTemplate.'_'.$language);
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }

    /* generate the tag button to put shortcode inside the textarea for mailing */
    function tdb_generate_shortcode_tpl_button($elemId) {
        $html = "";
        $html .= '<a id="tag-generator-list" class="tdb-jb-unselectable" unselectable="on">';
        $html .= '<a class="thickbox button tdb-tag-button" title="Template-tag Generator: job-url">job-url</a>';
        $html .= '<a class="thickbox button tdb-tag-button" title="Template-tag Generator: given-name">given-name</a>';
        $html .= '<a class="thickbox button tdb-tag-button" title="Template-tag Generator: family-name">family-name</a>';
        $html .= '</span>';
        return $html;
    }

    function tdb_jd_bc_get_wp_editor($content, $editor_id, $options = array() ) {
        ob_start();

        wp_editor( $content, $editor_id, $options );

        $temp = ob_get_clean();
        $temp .= \_WP_Editors::enqueue_scripts();
        //$temp .= print_footer_scripts();
        $temp .= \_WP_Editors::editor_js();

        return $temp;
    }

    // return a list of option for select with the array data we send
    function tdb_jb_get_opt_pages($value = '') {
        $html = "<option value = '' ></option>";

        $pages = get_pages();
        foreach ( $pages as $page ) {
            if($page->ID == $value){
                $selected = 'selected="true"';
            } else {
                $selected = '';
            }
            //$html .= '<option '. $selected.' value="' . get_page_link( $page->ID ) . '">';
            $html .= '<option '. $selected.' value="' . $page->ID . '">';
            $html .= $page->post_title;
            $html .= '</option>';
        }

        return $html;
    }
}
