<?php
 global $plugin_name;

 $helper = new \Jobsearch\Helper();
 // job title
$submitBackground = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM,'BtnSubmitBackground','sValue','sName');
$submitFont = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM,'BtnSubmitFont','sValue','sName');
$btnMoreBackground = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'BtnMoreBackground', 'sValue', 'sName');
$btnMoreFont = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM, 'BtnMoreFont', 'sValue', 'sName');
$linkFont = $helper->tdb_jb_get_parameters(TDB_TABLE_PARAM,'linkFont','sValue','sName');
    ?>

<style>

/****************SUBMIT************************/
/* Apply, add language, */
<?php if($helper->tdb_jb_check_valid_colorhex($submitBackground) == true){
?>
    .tdb-jd-apply-btn {
        background-color:<?php echo $submitBackground?>;
    }
    .tdb-jd-button-add {
        background-color:<?php echo $submitBackground?>;
    }

<?php } ?>

<?php if($helper->tdb_jb_check_valid_colorhex($submitFont) == true  ){
    ?>
    .tdb-jd-btn-primary:not(:disabled):not(.disabled).active,
    .tdb-jd-btn-primary:not(:disabled):not(.disabled):active,
    .tdb-jd-show > .tdb-jd-btn-primary.dropdown-toggle {
        color: <?php $submitFont?>;
    }

    .tdb-jd-btn-primary {
        color:<?php echo $submitFont?>;
    }
    .tdb-jd-button-add {
        color:<?php echo $submitFont?>;
    }

<?php } ?>
/****************END SUBMIT************************/
/****************MORE*****************************/
<?php if($helper->tdb_jb_check_valid_colorhex($btnMoreBackground) == true){
    ?>

    .tdb-jd-button-more {
        background-color: <?php echo $btnMoreBackground?>;
    }
    /****************ADMIN************************/
    .tdb-jd-btn-admin {
         background-color: <?php echo $btnMoreBackground?> !important;
    }
    /****************END ADMIN************************/


<?php } ?>

<?php if($helper->tdb_jb_check_valid_colorhex($btnMoreFont) == true){

    ?>
    .tdb-jd-button-more {
        color:<?php echo $btnMoreFont?>;
    }

    /****************ADMIN************************/
    .tdb-jd-btn-admin {
        color: <?php echo $btnMoreFont?>;
    }
    /****************END ADMIN************************/

<?php } ?>

/****************END MORE************************/
/****************Link Title************************/
<?php if($helper->tdb_jb_check_valid_colorhex($linkFont)== true){
    ?>
    h3.tdb-jd-title a {
    color: <?php echo $linkFont?> !important;
}
<?php } ?>
/****************END LINK************************/
</style>