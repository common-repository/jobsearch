jQuery(function($) {

    // Check all box for the field who should be visible on the detail page
    $('#checkAllShow').click(function(){

        if(this.checked == true) {
            $('input[name$="Param"]').each(function() {
                this.checked = true;
            });
        } else {
            $('input[name$="Param"]').each(function() {
                this.checked = false;
            });
        }
    });
    // Check all box for the field who should be mandatory on the apply page
    $('#checkAllRequired').click(function(){

        if(this.checked == true) {
            $('input[name$="Required"]').each(function() {
                this.checked = true;
            });
        } else {
            $('input[name$="Required"]').each(function() {
                this.checked = false;
            });
        }
    });
    // Check all box for the field who should be shown on the apply page
    $('#checkAllApply').click(function(){
        if(this.checked == true) {
            $('input[name$="Apply"]').each(function() {
                this.checked = true;
            });
        } else {
            $('input[name$="Apply"]').each(function() {
                this.checked = false;
            });
        }
    });

    $('#tdb-jd-design').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-design', '#tdb-jd-design');
    });
    $('#tdb-jd-link-api').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-link-api', '#tdb-jd-link-api');
    });
    $('#tdb-jd-job').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-job','#tdb-jd-job');
    });
    $('#tdb-jd-search').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-search','#tdb-jd-search');
    });
    $('#tdb-jd-detail-page').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-detail-page','#tdb-jd-detail-page');
    });
    $('#tdb-jd-apply-page').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-apply-page','#tdb-jd-apply-page');
    });
    $('#tdb-jd-colsize-page').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-colsize-page','#tdb-jd-colsize-page');
    });
    $('#tdb-jd-widget-page').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-widget-page','#tdb-jd-widget-page');
    });
    $('#tdb-jd-shortcode-page').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-shortcode-page','#tdb-jd-shortcode-page');
    });
    $('#tdb-jd-social').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-social','#tdb-jd-social');
    });
    $('#tdb-jd-mail').click(function(){
        tdbOpenAdminTab('#tdb-jd-tab-mail','#tdb-jd-mail');
    });

    $(document.body).on('click', '.tdb-tag-button' ,function(){

        var id = $(this).closest('div').attr('id');
        var idLanguage = id.split('_');
        var idTextarea = 'submit_' + idLanguage[2];
        var idTextareaVisual = 'submit_' + idLanguage[2] + '-tmce';
        var idTextareaHtml = 'submit_' + idLanguage[2] + '-html';
        var value = '[' + $(this).text() + ']';
        tdbSetTextToLasttPos(idTextarea, value);
    });

    $(document.body).on('click', '.tdb-jd-template-tablinks' ,function(){
        var id = $(this).attr('id');
        var idLanguage = id.split('-');
        var tab = 'tdb-jd-tab-link-template-'+idLanguage[2];

        tdbOpenAdminLanguageTab('#' + id, '#' + tab);
    });

    $(document.body).on('click', '#addLink' ,function(){
        AddRowLinkField("linkcontainer","apiRow","apiLink","apiKey","apiPage", "apiSearch","DelLink");
    });

    $(document.body).on('click', '#DelLink' ,function(){
        DelRow("apiRow","DelLink");
    });

    //phone and email
    function AddRowLinkField(divContainer, divRow, divCellLink,divCellKey,divCellPage,divCellSearch,DelName)  {
        // get the last DIV which ID starts with ^= "emailrow"
        var $divRow         = $( "div[id^="+ divRow +"]:last" );
        //var $typeInput       = $( "input[id^="+ Inputname +"]:last" );
        var $inputLink       = $( "input[id^="+ divCellLink +"]:last" );
        var $inputKey       = $( "input[id^="+ divCellKey +"]:last" );
        var $selectPage       = $( "select[id^="+ divCellPage +"]:last" );
        var $selectSearch       = $( "select[id^="+ divCellSearch +"]:last" );


        // Read the Number from that DIV's ID (i.e: 3 from "emailrow3")
        // And increment that number by 1
        var numRow          = parseInt( $divRow.prop("id").match(/\d+/g), 10 ) +1;

        $('<div class="tdb-jd-row" id ="'+divRow +numRow+'"></div>').appendTo('#'+divContainer);
        $('<div class="tdb-jd-col-3" id ="' + divCellLink + numRow + '"></div>').appendTo( '#' + divRow + numRow);
        $('<div class="tdb-jd-col-2" id ="' + divCellKey + numRow + '"></div>').appendTo( '#' + divRow + numRow);
        $('<div class="tdb-jd-col-3" id ="' + divCellPage + numRow + '"></div>').appendTo( '#' + divRow + numRow);
        $('<div class="tdb-jd-col-3" id ="' + divCellSearch + numRow + '"></div>').appendTo( '#' + divRow + numRow);
        $inputLink.clone()
            .prop('id', divCellLink+numRow )
            .prop('name', divCellLink+numRow )
            .val( "" )
            .appendTo( "#"+divCellLink+numRow);
        $inputKey.clone()
            .prop('id', divCellKey+numRow )
            .prop('name', divCellKey+numRow )
            .val( "" )
            .appendTo( "#"+divCellKey+numRow);
        $selectPage.clone()
            .prop('id', divCellPage+numRow )
            .prop('name', divCellPage+numRow )
            .val( "" )
            .appendTo( "#"+divCellPage+numRow);
        $selectSearch.clone()
            .prop('id', divCellSearch+numRow )
            .prop('name', divCellSearch+numRow )
            .val( "" )
            .appendTo( "#"+divCellSearch+numRow);

        if(numRow > 1) {
            $("#"+DelName).show();
            $("#"+DelName).show(5000);
            $("#"+DelName).show('slow','swing');
        }
    };

    // Delete row
    function DelRow(divRow,DelName,numRowToDelete = 0) {
        var numRow = 0;

        if( numRowToDelete > 0){
            numRow = numRowToDelete;
        } else {
            var $divRow = $( "div[id^='"+divRow+"']:last" );
            numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 );
        }

        if(numRow > 1) {
            $("#"+divRow+numRow).remove();
            numRow --;
        }

        if(numRow == 1 && numRowToDelete == 0) {
            $("#"+DelName).hide();
            $("#"+DelName).hide(1000);
            $("#"+DelName).hide('slow');
        }
    }

    function tdbOpenAdminTab(tabName,tabName2) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tdb-jd-tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tdb-jd-tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        $(tabName).addClass("active");
        $(tabName2).addClass("active");
        $(tabName).css('display', '');
        $(tabName).css('display', 'block');
    }

    function tdbOpenAdminLanguageTab(tabName,tabName2) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tdb-jd-tabcontent-template");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tdb-jd-template-tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        // Show the current tab, and add an "active" class to the button that opened the tab
        $(tabName).addClass("active");
        $(tabName2).addClass("active");
        $(tabName2).css('display', '');
        $(tabName2).css('display', 'inherit');
    }

    // add tag to the cursor position(for template
    function tdbSetTextToLasttPos(id, text) {
        $( '#' + id ).val($( '#' + id ).val() + ' ' + text);
    }
});