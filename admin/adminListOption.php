<?php
/**
 * Created by PhpStorm.
 * User: joan
 * Date: 18/11/14
 * Time: 14:09
 */

namespace Jobsearch\Admin;

use Jobsearch\Apply\ApplyHelper;
use Jobsearch\Helper;
use Jobsearch\Helper\Translation;

class AdminList {

    // Show the application list
    function tdb_jb_get_list_applications(){
        $helper = new Helper();
        $adminHelper = new AdminHelper();
        $translation = new Translation();
        $pagination = "";
        $nbApplyToShow = 10;    // nb apply to show
        // get the ofset for the pagination
        if(!isset($_GET["offset"])){
            $offset = 0;
        } else {
            $offset = $helper->tdb_jb_sanitize($_GET["offset"],'text');
        }
        $applyArray = $adminHelper->tdb_jb_get_applications($nbApplyToShow,$offset);
        $attachmentArray = $adminHelper->tdb_jb_get_attachments();

        $totalRow = $adminHelper->tdb_jb_total_row(TDB_TABLE_APPLY);

        foreach($applyArray as $content){
            $id = "";
            $idApi = "";
            $idJob = "";
            $name = "";
            $nameAttachment = "";
            $date = "";
            $json = "";
            $timezone = "";
            $link = "";
            // Get the application data
            foreach($content as $key => $value) {
                switch ($key){
                    case "id":
                        $id = $value;
                        break;
                    case "idApi":
                        $idApi = $value;
                        break;
                    case "idJob":
                        $idJob = $value;
                        break;
                    case "name":
                        $name = $value;
                        break;
                    case "date":
                        $date = $value;
                        break;
                    case "timezone":
                        $timezone = $value;
                        break;
                    case "json":
                        $json = $value;
                        break;
                }
            }

            //Get attachment data
            if(isset($attachmentArray[$id])){
                foreach($attachmentArray[$id] as $keyA => $valueA) {
                    $nameAttachment = "";
                    $file = "";
                    foreach($valueA as $keyB => $valueB){
                        switch ($keyB){
                            case "name":
                                $nameAttachment = $valueB;
                                break;
                            case "file":
                                $file = $valueB;
                                break;
                        }
                    }
                    if($nameAttachment <> "" && $file <> ""){
                        if($link <> ""){
                            $link .= "<br/>";
                        }

                        $downloadFile =  content_url() ."/uploads/jobsearch/". $file ;
                        $link.= "<a href='$downloadFile' class='' download>$nameAttachment</a>";
                    }
                }
            }

            $detail = $adminHelper->tdb_jb_format_content($id);

            $urlstart = "admin.php?".$_SERVER["QUERY_STRING"];
            // Pagination
            $pagination = $adminHelper->tdb_jb_get_pagination_admin($totalRow,$offset,$nbApplyToShow,$urlstart);

            $adminBodyArray = array(
                "langId" => TDB_LANG_WORDPRESSID,
                "langIdApi" =>  TDB_LANG_IDAPI,
                "langIdJob" =>  TDB_LANG_IDJOB,
                "langName" =>  TDB_LANG_NAME,
                "langDownload" =>  TDB_LANG_DOWNLOADJSON,
                "langDate" =>  TDB_LANG_APPLYDATE,
                "langJson" =>  TDB_LANG_DATASENDED,
                "langFile" =>  TDB_LANG_FILE,
                "id" => $id,
                "idApi" => $idApi,
                "idJob" => $idJob,
                "name" => $name,
                "date" => $date,
                "json" => $json,
                "detail" => $detail,
                "link" => $link,
                "langPushApplication" => TDB_LANG_PUSH_APPLICATION,
                "langPushConfirm" => TDB_LANG_PUSH_CONFIRM
            );
            $helper->tdb_jb_show_template(TDB_ADMIN_LIST_TPL, $adminBodyArray);
        }
        $adminFooterArray = array("pagination" => $pagination);
        $helper->tdb_jb_show_template(TDB_ADMIN_LIST_FOOT_TPL, $adminFooterArray);
    }

    // Push application to Tamago-DB
    function tdb_jb_push_application($id)
    {
        $helper = new Helper();
        $adminHelper = new AdminHelper();
        $applyArray = $adminHelper->tdb_jb_get_application($id);
        $attachmentArray = $adminHelper->tdb_jb_get_attachment_for_application($id);

        $json = $applyArray[$id]['json'];
        $api = $applyArray[$id]['idApi'];
        $linkApi  = $helper->tdb_jb_get_api_link('LinkCreate', $api);
        $linkFile  = $helper->tdb_jb_get_api_link('LinkFile', $api);

        $applyHelper = new ApplyHelper();

        $requests_response = $applyHelper->tdb_jb_wp_post_apply($linkApi, $json, $api);
        $jsonResponse = json_decode($requests_response["body"], true);

        if (isset($jsonResponse["status"])) {
            $status = $jsonResponse["status"];
        }
        if (isset($jsonResponse["message"])) {
            $message = $jsonResponse["message"];
        }
        // get the result
        if (!empty($status)) {
            switch ($status) {
                // error
                case "400":
                    $bError = true;
                    foreach ($jsonResponse["data"] as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $errorMessage) {
                                if (is_array($errorMessage)) {
                                    foreach ($errorMessage as $errorMessage2) {
                                        if (is_array($errorMessage2)) {
                                            foreach ($errorMessage2 as $errorMessage3) {
                                                $errorField[$key] = $errorMessage3;
                                            }
                                        } else {
                                            $errorField[$key] = $errorMessage2;
                                        }
                                    }
                                } else {
                                    $errorField[$key] = $errorMessage;
                                }
                            }
                        } else {
                            $errorField[$key] = $value;
                        }
                    }
                    break;
                // success
                case "201":
                    if (isset($jsonResponse["data"]["id"])) {
                        $id = $jsonResponse["data"]["id"];
                    }
                    $message = TDB_LANG_PUSH_APPLICATION_SUCCESS;
                    //attachment part
                    $messageAttachment = "";
                    // in case the application is successful and an ID is sent back
                    if ($id > 0) {
                        $linkFile .= "/" . $id . "/attachment";

                        //Get attachment data
                        if (!empty($attachmentArray)) {
                            $attachmentsUrlArray = [];
                            foreach ($attachmentArray as $att) {
                                $nameAttachment = $att['name'];
                                $file = $att['file'];
                                if ($nameAttachment <> "" && $file <> "") {
                                    $fileDir = content_url() . "/uploads/jobsearch/" . $file;
                                    $fileData = base64_encode(file_get_contents($fileDir));
                                    $attachmentsUrlArray[$id] = array('name' => $nameAttachment, 'file' => $fileData);
                                }
                            }

                            // Send attachments
                            $fileUrlArray = array('attachments' => $attachmentsUrlArray);
                            $responseAttachement = $applyHelper->tdb_jb_curl_send_attachement($linkFile, $fileUrlArray, $api);
                            $jsonResponseAttachment = json_decode($responseAttachement, true);
                            $attachmentStatus = $jsonResponseAttachment['status'];
                            switch ($attachmentStatus) {
                                // Case sent
                                case "201":
                                    //$messageAttachment = TDB_LANG_THANKATTACHMENT;
                                    break;
                                // Case error
                                default:
                                    $bError = true;
                                    if (isset ($jsonResponseAttachment['message'])) {
                                        $messageAttachment = $jsonResponseAttachment['message'];
                                    } else {
                                        $messageAttachment = TDB_LANG_ANERRORATTACHMENT;
                                    }
                            }
                        }
                    }
                    break;
                default:
                    if (isset($jsonResponse["message"]) && $jsonResponse["message"] != '') {
                        $message = $jsonResponse['message'];
                    } else {
                        $message = TDB_LANG_ANERROROCCURED;
                    }
                    break;

            }
        }

        return [$message, $messageAttachment];
    }
}
