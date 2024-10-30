jQuery(function($) {

    //***********Advanced search************************//
    $(document.body).on('click', '#advancedSearchBtn' ,function(){
        addRemoveAdvancedSearchClass('#tdb-advanced-search', 'tdb-jd-hidden');
    });
    //***************************************************//

    //********** Add row in the apply form**************//

    $(document.body).on('click', '#addEmail' ,function(){
        AddRowEmailApply();
    });
    $(document.body).on('click', '#addPhone' ,function(){
        AddRowPhoneApply();
    });
    $(document.body).on('click', '#addLanguageSkill' ,function(){
        AddRowLanguageSkill();
    });
    // Language form
    $(document.body).on('click', '#addLanguageSkillForm' ,function(){
        AddRowLanguageForm();
    });
    $(document.body).on('click', '#addLanguageScore' ,function(){
        AddRowCertifField();
    });
    $(document.body).on('click', '#DelLanguageSkill' ,function(){
        DelRow("languageSkillrow","DelLanguageSkill");
    });
    $(document.body).on('click', '#DelEmail' ,function(){
        DelRow("emailrow","DelEmail");
    });
    $(document.body).on('click', '#DelPhone' ,function(){
        DelRow("phonerow","DelPhone");
    });
    $(document.body).on('click', '#DelLanguageScore' ,function(){
        DelRow("languageScorerow","DelLanguageScore");
    });
    $(document.body).on('click', '#DelLanguageSkillForm1' ,function(){
        DelRow("languageSkillRow","DelLanguageSkillForm",1);
    });
    $(document.body).on('click', '#DelLanguageSkillForm2' ,function(){
        DelRow("languageSkillRow","DelLanguageSkillForm",2);
    });
    $(document.body).on('click', '#DelLanguageSkillForm3' ,function(){
        DelRow("languageSkillRow","DelLanguageSkillForm",3);
    });
    $(document.body).on('click', '#DelLanguageSkillForm4' ,function(){
        DelRow("languageSkillRow","DelLanguageSkillForm",4);
    });
    $(document.body).on('click', '#DelLanguageSkillForm5' ,function(){
        DelRow("languageSkillRow","DelLanguageSkillForm",5);
    });

    //***********************************************************//

    // Load new url when change the sort filter (new api request)
    $('#sortFilter').change(function(){
        var str = $(this).find('option:selected').val();

        var url = window.location.href;     // Returns full URL

        var sortField = "";
        var sortOrder = "";

        switch(str) {
            case 'titleA':
                sortField = "title";
                sortOrder = "asc";
                break;
            case 'dateA':
                sortField = "date";
                sortOrder = "asc";
                break;
            case 'salaryA':
                sortField = "salary";
                sortOrder = "asc";
                break;
            case 'titleD':
                sortField = "title";
                sortOrder = "desc";
                break;
            case 'dateD':
                sortField = "date";
                sortOrder = "desc";
                break;
            case 'salaryD':
                sortField = "salary";
                sortOrder = "desc";
                break;
            default:
        }

        url = url.replace("?sortField=date","");
        url = url.replace("?sortField=title","");
        url = url.replace("?sortField=salary","");
        url = url.replace("&sortField=date","");
        url = url.replace("&sortField=title","");
        url = url.replace("&sortField=salary","");
        url = url.replace("&sortOrder=desc","");
        url = url.replace("&sortOrder=asc","");

        method = getMethodLink(url) ;

        linkFinal = url + method + "sortField=" + sortField + "&sortOrder=" + sortOrder;
        window.location.href = linkFinal;
    });

    //************On search form, make blank the place older if not choose, normal font if some value
    $("#wageBasis").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#location").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#currency").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $('[name="language1[max]"]').change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $('[name="language1[language]"]' ).change(function () {
        addRemoveClassEmpty(this)
    }).change();

    $(document.body).on('change', '#language2' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', 'select[name^="language2[max]"]' ,function(){
        addRemoveClassEmpty(this)
    });

    $(document.body).on('change', '#language3' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', 'select[name^="language3[max]"]' ,function(){
        addRemoveClassEmpty(this)
    });

    $(document.body).on('change', '#language4' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', 'select[name^="language4[max]"]' ,function(){
        addRemoveClassEmpty(this)
    });

    $(document.body).on('change', '#language5' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', 'select[name^="language5[max]"]' ,function(){
        addRemoveClassEmpty(this)
    });

    //************On Apply form, make blank the place older if not choose, normal font if some value
    $("#nationality").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#country").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#visaCountry").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#visaType").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#basis").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#desiredindustry").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#desiredjobcategory").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#desiredlocation").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#typeemail1").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $(document.body).on('change', '#typeemail2' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typeemail3' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typeemail4' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typeemail5' ,function(){
        addRemoveClassEmpty(this)
    });

    $("#typephone1").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $(document.body).on('change', '#typephone2' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typephone3' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typephone4' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typephone5' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typephone6' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typephone7' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#typephone8' ,function(){
        addRemoveClassEmpty(this)
    });

    $("#languagesCode1").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#languagesability1").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $(document.body).on('change', '#languagesCode2' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#languagesCode3' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#languagesability2' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#languagesability3' ,function(){
        addRemoveClassEmpty(this)
    });

    $("#languagecertifications1").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $("#score1").change(function () {
        addRemoveClassEmpty(this)
    }).change();
    $(document.body).on('change', '#languagecertifications2' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#languagecertifications3' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#score2' ,function(){
        addRemoveClassEmpty(this)
    });
    $(document.body).on('change', '#score3' ,function(){
        addRemoveClassEmpty(this)
    });

    function addRemoveAdvancedSearchClass(elm, className){
        if($(elm).hasClass(className)) {
            $(elm).removeClass(className);
            $('<input type="hidden" name="advancedSearch"  id="advancedSearch" value ="advancedSearch"/>').appendTo(elm);
        } else {
            $(elm).addClass(className);
            $("#advancedSearch").remove();
        }
    }

    function addRemoveClassEmpty(elm){
        if($(elm).prop('selectedIndex') == "0") {
            //$(elm).addClass("tdb-jd-empty");
        } else {
            //$(elm).removeClass("tdb-jd-empty");
        }
    }

    //************************************************************************//

    // Reset all get data on the search form
    $(document.body).on('click', '#jobSearchReset' ,function(){
        var uri = window.location.toString();
        if (uri.indexOf("?") > 0) {
            var clean_uri = uri.substring(0, uri.indexOf("?"));
        } else {
            var clean_uri = uri;
        }

        window.location.href = clean_uri;
    });

    // Change page if select another page on the selectbox pagination
    $("#pageJob").change(function () {
        if($(this).val() !== "") {
            window.location.href = $(this).val();
        }
    });

    // Test number of file sended and size >3file or >6mo let an error message and don t submit the form
    $("#rendered-form").on('submit', function(e){
        var files = $('#attachments')[0].files;
        var totalSize = 0;
        var nbFile = files.length;
        if(nbFile > 3){
            alert("You have exceeded the number maximum of file to upload size.(3 files)");
            return false;
        }
        for (var i = 0; i < files.length; i++) {
            // calculate total size of all files
            totalSize += (files[i].length / 1024 / 1024).toFixed(4);
        }
        if(totalSize > 6){
            alert("You have exceeded the maximum file upload size.(6mo)");
            return false;
        }
    });

    // Delete row
    function DelRow(divRow,DelName,numRowToDelet = 0) {
        var numRow = 0;

        if( numRowToDelet > 0){
            numRow = numRowToDelet;
        } else {
            var $divRow = $( "div[id^='"+divRow+"']:last" );
            numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 );
        }

        if(numRow > 1) {
            $("#"+divRow+numRow).remove();
            numRow --;
        }

        if(numRow == 1 && numRowToDelet == 0) {
            $("#"+DelName).hide();
            $("#"+DelName).hide(1000);
            $("#"+DelName).hide('slow');
        }
    }

    //Language
    function AddRowLanguageSkill()  {
        var limit = 3;
        var row = "languageSkillrow";
        // get the last DIV which ID starts with ^= "emailrow"
        var $divRow = $( "div[id^="+ row +"]:last" );

        // Read the Number from that DIV's ID (i.e: 3 from "emailrow3")
        // And increment that number by 1
        var numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 );
        var $selectCode = $( "select[id^='languagesCode1']" );

        numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 ) +1;
        var cssSkillType = $( "#skill-type-1").attr('class');
        var cssSkillAbility = $( "#skill-ability-1").attr('class');

        if(numRow <= limit)  {

            var labelLanguage = $("label[for='languagesCode1']").text().replace("*", "");
            var labelAbility = $("label[for='languagesability1']").text().replace("*", "");
            var optionCode = '';
            var optionAbility = '';
            $("#languagesCode1 option").each(function()
            {
                var id = $(this).val();
                if(id == null){
                    id = '0';
                }
                optionCode += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
            });

            $("#languagesability1 option").each(function()
            {
                var id = $(this).val();
                if(id == null){
                    //id = '0';
                }
                optionAbility += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
            });

            var newRowHtml = "<div class='tdb-jd-row' id='languageSkillrow" + numRow + "'>";
            newRowHtml += "<div class='"+cssSkillType+"' name='languageSkillText" + numRow + "' id='languageSkillText" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' name='languageSkillTextlanguageSkillTypeContainer" + numRow + "' id='languageSkillTextlanguageSkillTypeContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row' name='languageSkillTextlanguageSkillSubRowLabel" + numRow + "' id='languageSkillTextlanguageSkillSubRowLabel" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='languageSkillTextlanguageSkillSubColLabel" + numRow + "' id='languageSkillTextlanguageSkillSubColLabel" + numRow + "'>";
            newRowHtml += "<label class='tdb-jd-label' for='languagesCode" + numRow + "'>";
            newRowHtml += labelLanguage;
            newRowHtml += "</label>";
            newRowHtml += "<label for='languagesCode" + numRow + "' class='tdb-jd-label'>";
            newRowHtml += "</label>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-row' id='languageSkillTextlanguageSkillSubRowContent" + numRow + "' name='languageSkillTextlanguageSkillSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' id='languageSkillTextlanguageSkillSubColContent" + numRow + "' name='languageSkillTextlanguageSkillSubColContent" + numRow + "'>";
            newRowHtml += "<select id='languagesCode" + numRow + "' name='languagesCode" + numRow + "' class='tdb-jd-form-control tdb-jd-input '>";
            newRowHtml += optionCode;
            newRowHtml += "</select>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='"+cssSkillAbility+"' id='languageSkillSkillCellType" + numRow + "' name='languageSkillSkillCellType" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' id='languageSkillTypelanguageSkillTypeContainer" + numRow + "' name='languageSkillTypelanguageSkillTypeContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row' name='languageSkillTypelanguageSkillSubRowLabel" + numRow + "' id='languageSkillTypelanguageSkillSubRowLabel" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='languageSkillTypelanguageSkillSubColLabel" + numRow + "' id='languageSkillTypelanguageSkillSubColLabel" + numRow + "'>";
            newRowHtml += "<label class='tdb-jd-label' for='languagesability" + numRow + "'>";
            newRowHtml += labelAbility;
            newRowHtml += "</label>";
            newRowHtml += "<label for='languagesability" + numRow + "' class='tdb-jd-label'>";
            newRowHtml += "</label>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-row' name='languageSkillTypelanguageSkillSubRowContent" + numRow + "' id='languageSkillTypelanguageSkillSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' name='languageSkillTypelanguageSkillSubColContent" + numRow + "' id='languageSkillTypelanguageSkillSubColContent" + numRow + "'>";
            newRowHtml += "<select id='languagesability" + numRow + "' name='languagesability" + numRow + "' class='tdb-jd-form-control tdb-jd-input '>";
            newRowHtml += optionAbility;
            newRowHtml += "</select>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";

            $(newRowHtml).appendTo('#languageSkillContainer');

            if ($selectCode.hasClass("select2-hidden-accessible")){
                $('#languagesability' + numRow).select2();
                $('#languagesCode' + numRow).select2();
                $('#languagesability' + numRow).empty();
                $('#languagesCode' + numRow).empty();

                $("#languagesCode1 option").each(function()
                {
                    var id = $(this).val();
                    var val = $(this).text();
                    if(id == null){
                        id = '0';
                        val = ' ';
                    }

                    var newOption = new Option(val, id, false, false);
                    // Append it to the select
                    $('#languagesCode' + numRow).append(newOption).trigger('change');
                });

                $("#languagesability1 option").each(function()
                {
                    var id = $(this).val();
                    var val = $(this).text();
                    if(id == null){
                        id = '0';
                        val = ' ';
                    }

                    var newOption = new Option(val, id, false, false);
                    // Append it to the select
                    $('#languagesability' + numRow).append(newOption).trigger('change');
                });
            }

            if(numRow > 1) {
                $("#DelLanguageSkill").show();
                $("#DelLanguageSkill").show(5000);
                $("#DelLanguageSkill").show('slow','swing');
            }
        }
    };

    //certification
    function AddRowCertifField()  {
        var row = "languageScorerow";

        var limit = 5;

        // get the last DIV which ID starts with ^= "emailrow"
        var $divRow = $( "div[id^="+ row +"]:last" );

        // Read the Number from that DIV's ID (i.e: 3 from "emailrow3")
        // And increment that number by 1
        var numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 );

        var $selectCertification = $( "select[name~='languagecertifications" + numRow + "']" );

        numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 ) +1;
        var cssScoreText = $( "#score-text-1").attr('class');
        var cssScore = $( "#score-score-1").attr('class');

        if(numRow <= limit)  {
            var labelLangageCertif = $("label[for='languagecertifications1']").text().replace("*", "");
            var labelLevelClass = $("label[for='score1']").text().replace("*", "");
            var optionCertif = '';

            $("#languagecertifications1 option").each(function()
            {
                var id = $(this).val();
                if(id == null){
                    id = '0';
                }
                optionCertif += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
            });

            var newRowHtml = "<div class='tdb-jd-row' id='languageScorerow" + numRow + "'>";
            newRowHtml += " <div class='"+cssScoreText+"' id='languageScoreSkillCellType" + numRow + "' name='languageScoreSkillCellType" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' id='languageScoreTypelanguageScoreTypeContainer" + numRow + "' name='languageScoreTypelanguageScoreTypeContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row' name='languageScoreTypelanguageScoreSubRowLabel" + numRow + "' id='languageScoreTypelanguageScoreSubRowLabel" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='languageScoreTypelanguageScoreSubColLabel" + numRow + "' id='languageScoreTypelanguageScoreSubColLabel" + numRow + "'>";
            newRowHtml += "<label class='tdb-jd-label' for='languagecertifications" + numRow + "'>";
            newRowHtml += labelLangageCertif;
            newRowHtml += "</label>";
            newRowHtml += "<label for='languagecertifications" + numRow + "' class='tdb-jd-label'>";
            newRowHtml += "</label>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-row' name='languageScoreTypelanguageScoreSubRowContent" + numRow + "' id='languageScoreTypelanguageScoreSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' name='languageScoreTypelanguageScoreSubColContent" + numRow + "' id='languageScoreTypelanguageScoreSubColContent" + numRow + "'>";
            newRowHtml += "<select id='languagecertifications" + numRow + "' name='languagecertifications" + numRow + "' class='tdb-jd-form-control tdb-jd-input '>";
            newRowHtml += optionCertif;
            newRowHtml += "</select>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='"+cssScore+"' name='languageScoreText" + numRow + "' id='languageScoreText" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' name='languageScoreTextlanguageScoreTypeContainer" + numRow + "' id='languageScoreTextlanguageScoreTypeContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row' name='languageScoreTextlanguageScoreSubRowLabel" + numRow + "' id='languageScoreTextlanguageScoreSubRowLabel" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='languageScoreTextlanguageScoreSubColLabel" + numRow + "' id='languageScoreTextlanguageScoreSubColLabel" + numRow + "'>";
            newRowHtml += "<label class='tdb-jd-label' for='score" + numRow + "'>";
            newRowHtml += labelLevelClass;
            newRowHtml += "</label>";
            newRowHtml += "<label for='score" + numRow + "' class='tdb-jd-label'>";
            newRowHtml += "</label>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-row' id='languageScoreTextlanguageScoreSubRowContent" + numRow + "' name='languageScoreTextlanguageScoreSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' id='languageScoreTextlanguageScoreSubColContent" + numRow + "' name='languageScoreTextlanguageScoreSubColContent" + numRow + "'>";
            newRowHtml += "<input type='text' id='score" + numRow + "' name='score" + numRow + "' class='tdb-jd-form-control tdb-jd-input ' value=''>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";

            $(newRowHtml).appendTo('#languageScoreContainer');

            if ($selectCertification.hasClass("select2-hidden-accessible")){
                $('#languagecertifications' + numRow).select2();
                $('#languagecertifications' + numRow).empty();

                $("#languagecertifications1 option").each(function()
                {
                    var id = $(this).val();
                    var val = $(this).text();
                    if(id == null){
                        id = '0';
                        val = ' ';
                    }

                    var newOption = new Option(val, id, false, false);
                    // Append it to the select
                    $('#languagecertifications' + numRow).append(newOption).trigger('change');
                });
            }

            if(numRow > 1) {
                $("#DelLanguageScore").show();
                $("#DelLanguageScore").show(5000);
                $("#DelLanguageScore").show('slow','swing');
            }
        }
    };

    // Language skill search form
    function AddRowLanguageForm() {
        var container = "languageSkillContainer";

        var row = "languageSkillRow";
        var name = "language";
        var skillmax = "[max]";  //select

        var Limit = 5;

        // get the last DIV which ID starts with ^= "emailrow"
        var $divRow = $( "div[id^="+ row +"]:last" );

        // Read the Number from that DIV's ID (i.e: 3 from "emailrow3")
        // And increment that number by 1
        var numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 );
        var $selectSkillMax = $( "select[name~='" + name + numRow + skillmax + "']" );

        numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 ) +1;

        if(numRow <= Limit)  {
            var labelLanguage = $("label[for='language1']").text().replace("*", "");
            var labelAndBelow = $("label[for='language1[min]']").text().replace("*", "");
            var languageOption = '';
            var skillMaxOption = '';
            $("#language1 option").each(function()
            {
                var id = $(this).val();
                if(id == null){
                    id = '0';
                }
                languageOption += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
            });

            $("#languageSkillMax1 option").each(function()
            {
                var id = $(this).val();
                if(id == null){
                    //id = '0';
                }

                skillMaxOption += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
            });

            var newRowHtml = "<div class='tdb-jd-row' id='languageSkillRow" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-3 tdb-jd-search-column'>";
            newRowHtml += "<div class='tdb-jd-container' id='languageCountrylanguageContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row tdb-jd-row-content-search' id='languageCountrylanguageSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' id='languageCountrylanguageSubColContent" + numRow + "'>";
            newRowHtml += "<select id='language" + numRow + "' name='language" + numRow + "[language]' class='tdb-jd-custom-select tdb-jd-input  '>";
            newRowHtml += languageOption;
            newRowHtml += "</select>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-col-3' id='languageSkillCellMin" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' id='languageSkilllanguageContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row tdb-jd-row-content-search' id='languageSkilllanguageSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' id='languageSkilllanguageSubColContent" + numRow + "'>";
            newRowHtml += "<select id='languageSkillMax" + numRow + "' name='language" + numRow + "[max]' class='tdb-jd-custom-select tdb-jd-input '>";
            newRowHtml += skillMaxOption;
            newRowHtml += "</select>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-col-3 tdb-jd-below' id='languageSkillCellMax" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' id='languageBelowlanguageContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row tdb-jd-row-label-search'>";
            newRowHtml += "<div class='tdb-jd-row tdb-jd-row-content-search' id='languageBelowlanguageSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-below' id='languageBelowlanguageSubColContent" + numRow + "'>";
            newRowHtml += "<input type='checkbox' name='language" + numRow + "[min]' checked='' value='' class='tdb-jd-emptypes ' id='languageSkillMin" + numRow + "'>";
            newRowHtml += "<label for='language" + numRow + "[min]' class='tdb-jd-emptypes'>";
            newRowHtml += labelAndBelow;
            newRowHtml += "</label>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";

            $(newRowHtml).appendTo('#languageSkillContainer');

            if ($selectSkillMax.hasClass("select2-hidden-accessible")){
                $('#languageSkillMax' + numRow).select2();
                $('#language' + numRow).select2();
                $('#languageSkillMax' + numRow).empty();
                $('#language' + numRow).empty();

                $("#language1 option").each(function()
                {
                    var id = $(this).val();
                    if(id == null){
                        id = '0';
                    }
                    languageOption += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
                });

                $("#languageSkillMax1 option").each(function()
                {
                    var id = $(this).val();
                    var val = $(this).text();
                    if(id == null){
                        id = '0';
                        val = ' ';
                    }

                    var newOption = new Option(val, id, false, false);
                    // Append it to the select
                    $('#languageSkillMax' + numRow).append(newOption).trigger('change');
                });

                $("#language1 option").each(function()
                {
                    var id = $(this).val();
                    var val = $(this).text();
                    if(id == null){
                        id = '0';
                        val = ' ';
                    }

                    var newOption = new Option(val, id, false, false);
                    // Append it to the select
                    $('#language' + numRow).append(newOption).trigger('change');
                });
            }
        }
    };

    // Email search form
    function AddRowEmailApply() {
        var row = "emailrow";
        var Limit = 5;
        // get the last DIV which ID starts with ^= "emailrow"
        var $divRow = $( "div[id^="+ row +"]:last" );

        // Read the Number from that DIV's ID (i.e: 3 from "emailrow3")
        // And increment that number by 1
        var numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 );
        numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 ) +1;

        var labelEmailType = $("label[for='typeemail1']").text().replace("*", "");
        var labelEmail = $("label[for='emails1']").text().replace("*", "");
        var emailOption = '';

        var $selectTypeEmail = $( "select[name~='typeemail1']" );

        var cssEmailText = $( "#email-text-1").attr('class');
        var cssEmailType = $( "#email-type-1").attr('class');

        $("#typeemail1 option").each(function()
        {
            var id = $(this).val();
            if(id == null){
                id = '0';
            }
            emailOption += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
        });

        if(numRow <= Limit) {
            var newRowHtml = "<div class='tdb-jd-row' id='emailrow" + numRow + "'>";
            newRowHtml += "<div class='" +cssEmailText + "' name='emailText" + numRow + "' id='emailText" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' name='emailTextemailTypeContainer" + numRow + "' id='emailTextemailTypeContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row' name='emailTextemailSubRowLabel" + numRow + "' id='emailTextemailSubRowLabel" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='emailTextemailSubColLabel" + numRow + "' id='emailTextemailSubColLabel" + numRow + "'>";
            newRowHtml += "<label class='tdb-jd-label ' for='emails" + numRow + "'>";
            newRowHtml += labelEmail;
            newRowHtml += "</label>";
            newRowHtml += "<label for='emails" + numRow + "' class='tdb-jd-label'>";
            newRowHtml += "</label>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-row' id='emailTextemailSubRowContent" + numRow + "' name='emailTextemailSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' id='emailTextemailSubColContent" + numRow + "' name='emailTextemailSubColContent" + numRow + "'>";
            newRowHtml += "<input type='email' id='emails" + numRow + "' name='emails" + numRow + "' class='tdb-jd-form-control tdb-jd-input ' value='' required='required' aria-required='true'>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            if (typeof cssEmailType !== "undefined") {
                newRowHtml += "<div class='"+cssEmailType+"' id='emailSkillCellType" + numRow + "' name='emailSkillCellType" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-container' id='emailTypeemailTypeContainer" + numRow + "' name='emailTypeemailTypeContainer" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-row' name='emailTypeemailSubRowLabel" + numRow + "' id='emailTypeemailSubRowLabel" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='emailTypeemailSubColLabel" + numRow + "' id='emailTypeemailSubColLabel" + numRow + "'>";
                newRowHtml += "<label class='tdb-jd-label tdb-jd-type-email' for='typeemail" + numRow + "'>";
                newRowHtml += labelEmailType;
                newRowHtml += "</label>";
                newRowHtml += "<label for='typeemail" + numRow + "' class='tdb-jd-label'>";
                newRowHtml += "</label>";
                newRowHtml += "</div>";
                newRowHtml += "</div>";
                newRowHtml += "</div>";
                newRowHtml += "<div class='tdb-jd-row' name='emailTypeemailSubRowContent" + numRow + "' id='emailTypeemailSubRowContent" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-col-12' name='emailTypeemailSubColContent" + numRow + "' id='emailTypeemailSubColContent" + numRow + "'>";
                newRowHtml += "<select id='typeemail" + numRow + "' name='typeemail" + numRow + "' class='tdb-jd-form-control tdb-jd-input tdb-jd-type-email '>";
                newRowHtml += emailOption;
                newRowHtml += "</select>";
                newRowHtml += "</div>";
                newRowHtml += "</div>";
                newRowHtml += "</div>";
            }

            newRowHtml += "</div>";
            newRowHtml += "</div>";

            $(newRowHtml).appendTo('#emailcontainer');

            if ($selectTypeEmail.hasClass("select2-hidden-accessible")){
                $('#typeemail' + numRow).select2();
                $('#typeemail' + numRow).empty();

                $("#typeemail1 option").each(function()
                {
                    var id = $(this).val();
                    var val = $(this).text();
                    if(id == null){
                        id = '0';
                        val = ' ';
                    }

                    var newOption = new Option(val, id, false, false);
                    // Append it to the select
                    $('#typeemail' + numRow).append(newOption).trigger('change');
                });
            }

            if(numRow > 1) {
                $("#DelEmail").show();
                $("#DelEmail").show(5000);
                $("#DelEmail").show('slow','swing');
            }
        }
    };

    // Phone search form
    function AddRowPhoneApply() {
        var row = "phonerow";
        var Limit = 8;
        // get the last DIV which ID starts with ^= "emailrow"
        var $divRow = $( "div[id^="+ row +"]:last" );

        // Read the Number from that DIV's ID (i.e: 3 from "emailrow3")
        // And increment that number by 1
        var numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 );
        numRow = parseInt( $divRow.prop("id").match(/\d+/g), 10 ) +1;

        var labelPhoneType = $("label[for='typephone1']").text().replace("*", "");
        var labelPhone = $("label[for='phonenumbers1']").text().replace("*", "");
        var phoneOption = '';

        var $selectTypePhone = $( "select[name~='typephone1']" );

        var cssPhoneText = $( "#phone-text-1").attr('class');
        var cssPhoneType = $( "#phone-type-1").attr('class');

        $("#typephone1 option").each(function()
        {
            var id = $(this).val();
            if(id == null){
                id = '0';
            }
            phoneOption += "<option value='"+ $(this).val() + "' id='"+ id + "'>"+ $(this).text() + "</option>";
        });

        if(numRow <= Limit) {
            var newRowHtml = "<div class='tdb-jd-row' id='phonerow" + numRow + "'>";
            newRowHtml += "<div class='"+cssPhoneText+"' name='phoneText" + numRow + "' id='phoneText" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-container' name='phoneTextphoneTypeContainer" + numRow + "' id='phoneTextphoneTypeContainer" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-row' name='phoneTextphoneSubRowLabel" + numRow + "' id='phoneTextphoneSubRowLabel" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='phoneTextphoneSubColLabel" + numRow + "' id='phoneTextphoneSubColLabel" + numRow + "'>";
            newRowHtml += "<label class='tdb-jd-label' for='phonenumbers" + numRow + "'>";
            newRowHtml += labelPhone;
            newRowHtml += "</label>";
            newRowHtml += "<label for='phonenumbers" + numRow + "' class='tdb-jd-label'>";
            newRowHtml += "</label>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "<div class='tdb-jd-row' id='phoneTextphoneSubRowContent" + numRow + "' name='phoneTextphoneSubRowContent" + numRow + "'>";
            newRowHtml += "<div class='tdb-jd-col-12' id='phoneTextphoneSubColContent" + numRow + "' name='phoneTextphoneSubColContent" + numRow + "'>";
            newRowHtml += "<input type='tel' id='phonenumbers" + numRow + "' name='phonenumbers" + numRow + "' class='tdb-jd-form-control tdb-jd-input ' value=''>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            newRowHtml += "</div>";
            if (typeof cssPhoneType !== "undefined") {
                newRowHtml += "<div class='"+cssPhoneType+"' id='phoneSkillCellType" + numRow + "' name='phoneSkillCellType" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-container' id='phoneTypephoneTypeContainer" + numRow + "' name='phoneTypephoneTypeContainer" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-row' name='phoneTypephoneSubRowLabel" + numRow + "' id='phoneTypephoneSubRowLabel" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-col-12 tdb-jd-sublabel tdb-jd-col-label-search' name='phoneTypephoneSubColLabel" + numRow + "' id='phoneTypephoneSubColLabel" + numRow + "'>";
                newRowHtml += "<label class='tdb-jd-label tdb-jd-type-phone' for='typephone" + numRow + "'>";
                newRowHtml += labelPhoneType;
                newRowHtml += "</label>";
                newRowHtml += "<label for='typephone" + numRow + "' class='tdb-jd-label'>";
                newRowHtml += "</label>";
                newRowHtml += "</div>";
                newRowHtml += "</div>";
                newRowHtml += "<div class='tdb-jd-row' name='phoneTypephoneSubRowContent" + numRow + "' id='phoneTypephoneSubRowContent" + numRow + "'>";
                newRowHtml += "<div class='tdb-jd-col-12' name='phoneTypephoneSubColContent" + numRow + "' id='phoneTypephoneSubColContent" + numRow + "'>";
                newRowHtml += "<select id='typephone" + numRow + "' name='typephone" + numRow + "' class='tdb-jd-form-control tdb-jd-input tdb-jd-type-phone '>";
                newRowHtml += phoneOption;
                newRowHtml += "</select>";
                newRowHtml += "</div>";
                newRowHtml += "</div>";
                newRowHtml += "</div>";
            }


            newRowHtml += "</div>";
            newRowHtml += "</div>";

            $(newRowHtml).appendTo('#phonecontainer');

            if ($selectTypePhone.hasClass("select2-hidden-accessible")){
                $('#typephone' + numRow).select2();
                $('#typephone' + numRow).empty();

                $("#typephone1 option").each(function()
                {
                    var id = $(this).val();
                    var val = $(this).text();
                    if(id == null){
                        id = '0';
                        val = ' ';
                    }

                    var newOption = new Option(val, id, false, false);
                    // Append it to the select
                    $('#typephone' + numRow).append(newOption).trigger('change');
                });
            }

            if(numRow > 1) {
                $("#DelPhone").show();
                $("#DelPhone").show(5000);
                $("#DelPhone").show('slow','swing');
            }
        }
    };

    // Get every data about the current page( on the init of the plugin) to know every information about the url
// who has to be used
    function getMethodLink(url) {
        var has_string;
        var method = "?";
        has_string= url.indexOf("?");

        if (has_string !== -1){
            method = "&";
        }

        return method;
    }
});
