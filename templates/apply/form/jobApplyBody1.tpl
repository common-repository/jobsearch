<!--<script src="https://www.linkedin.com/autofill/js/autofill.js" type="text/javascript" async></script>
<script type="IN/Form2" data-form="rendered-form"
        data-field-firstname="familyname"
        data-field-lastname="givenname"
        data-field-phone="phonenumbers1"
        data-field-email="emails1"
        data-field-company="currentEmploymentCompany"
        data-field-title="currentEmploymentPosition"
        data-field-city="city"
        data-field-country="country"
        data-field-zip="postal">

</script> -->

{if recaptcha == true }
    <script src="https://www.google.com/recaptcha/api.js"></script>
{/if}

<div class="tdb-jd-row">
    <h2 class="tdb-jd-apply-header" id="tdb-title-form">{$langApplicationForm}</h2>
    {if $id != ""}
        <a href="{$urlJob}" class="tdb-jd-apply-form-return-job"> {$langReturnJob}</a>
        &nbsp;&nbsp; <!-- the 'Return to Job Details' button is not working: 'Error, Page not found' -->
        <a href="{$urlHome}" class="tdb-jd-apply-form-return-search"> {$langReturnSearch}</a>
    {/if}
</div>

<div class="tdb-jd-appform tdb-jd-container">
    <form Method ="POST"  id="rendered-form" xmlns="http://www.w3.org/1999/xhtml" enctype="multipart/form-data">
        <input type="text" name="tdbname"  id="tdbname" style="display: none;">
        <input type="hidden" name="hidden-search-url" id="hidden-search-url" value ={$searchUrl}>
        {if $id <= 0 && $postIdJob <= 0}
            <input type="hidden" name="hidden-id" id="hidden-id" value ="">
        {else}
            {if $id <= 0}
                <input type="hidden" name="hidden-id" id="hidden-id" value ="{$postIdJob}">
            {else}
                <input type="hidden" name="hidden-id" id="hidden-id" value ="{$id}">
            {/if}
        {/if}

        {if $api > 0}
            <input type="hidden" name="hidden-api" id="hidden-api" value ="{$api}">
        {/if}

        {if $sourceDetail != ""}
            <input type="hidden" name="hidden-source-detail" id="hidden-source-detail" value ="{$sourceDetail}">
        {/if}

        <hr class="tdb-jd-my-4">
        <!-- Personal data bloc -->
        <div class="tdb-jd-show-id-form" id="personalData" >
            <h3 id="tdb-title-contact">{$langContact}</h3>
        </div>
        <div class="tdb-jd-show-id-form1" id="personalDataBloc" >
            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-{$colSize['familyName']} tdb-jd-form-group" id="tdb-col-familyname" >
                        <label for="familyname" class="tdb-jd-label">{$langFamilyName}
                            <span class="tdb-jd-fb-required">*</span>
                        </label>
                        <input type="text" class="tdb-jd-form-control tdb-jd-label" name="familyname" value="{$postFamilyname}" id="familyname" required="required" aria-required="true">
                    </div>
                    <div class="tdb-jd-col-{$colSize['givenName']} tdb-jd-form-group"  id="tdb-col-givenname">
                        <label for="givenname" class="tdb-jd-label ">{$langGivenName}
                            <span class="tdb-jd-fb-required">*</span>
                        </label>
                        <input type="text" class="tdb-jd-form-control tdb-jd-label" name="givenname" id="givenname" value="{$postGivenname}" required="required" aria-required="true">
                    </div>
                    {if $applyGender != ""}
                        <div class="tdb-jd-col-{$colSize['gender']} tdb-jd-col-gender" id="tdb-col-label-gender">
                            <div class="tdb-jd-container tdb-jd-container-gender">
                                <div class="tdb-jd-row tdb-jd-row-gender" id="tdb-row-label-gender">
                                    <label for="gender" class="tdb-jd-label tdb-jd-label-gender">{$langGender}
                                        {$requiredSpanGender}
                                    </label>

                                </div>

                                <div class="tdb-jd-row tdb-jd-row-gender" id="tdb-col-check-gender">
                                    <div class="tdb-jd-col-1 tdb-jd-form-check tdb-jd-form-check-gender" id="tdb-col-gender-m1">
                                        <label class="tdb-jd-form-check-label tdb-jd-form-check-label-gender" for="gender-0">{$langGenderM}</label>
                                    </div>
                                    <div class="tdb-jd-col-1 tdb-jd-form-check tdb-jd-form-check-gender tdb-jd-form-check-gender-m" id="tdb-col-gender-m2">
                                        {if $postGender =="m"}
                                            <input class="tdb-jd-form-check-input tdb-jd-form-check-input-gender" name="gender" id="gender-0" value="m" checked type="radio"  {$requiredContentGender}>
                                        {else}
                                            <input class="tdb-jd-form-check-input tdb-jd-form-check-input-gender" name="gender" id="gender-0" value="m" type="radio"  {$requiredContentGender}>
                                        {/if}
                                    </div>
                                    <div class="tdb-jd-col-1 tdb-jd-form-check tdb-jd-form-check-gender" id="tdb-col-gender-f1">
                                        <label class="tdb-jd-form-check-label tdb-jd-form-check-label-gender" for="gender-1">{$langGenderF}</label>
                                    </div>
                                    <div class="tdb-jd-col-1 tdb-jd-form-check tdb-jd-form-check-gender" id="tdb-col-gender-f2">
                                        {if $postGender =="f"}
                                            <input class="tdb-jd-form-check-input tdb-jd-form-check-input-gender" name="gender" id="gender-1" value="f" checked type="radio">
                                        {else}
                                            <input class="tdb-jd-form-check-input tdb-jd-form-check-input-gender" name="gender" id="gender-1" value="f" type="radio">
                                        {/if}

                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
                {if $applyBirthdate != ""}
                    {$birthday}
                {/if}

                <!-- Adress bloc -->
                {if $applyAddress != ""}
                    <hr class="tdb-jd-my-4">
                    <div class="tdb-jd-show-id-form1" id="personalDataAdressBloc"  >
                        <div class="tdb-jd-container">

                            <div class="tdb-jd-row" id="tdb-col-address1">
                                {$postal}
                                {$country}
                            </div>
                            <div class="tdb-jd-row" id="tdb-col-address2">
                                {$region}{$city}
                            </div>
                            <div class="tdb-jd-row" id="tdb-col-address3">
                                {$street} {$extended}
                                {if $applyNeareststation != ""}
                                    {$nearestStation}
                                {/if}
                            </div>
                        </div>
                    </div>
                    <hr class="tdb-jd-my-4">
                {/if}

                {if $applyEmails != ""}
                    <div class="tdb-jd-container" id="emailcontainer">
                        {$email}
                    </div>

                    <div class="tdb-jd-container" >
                        <div class = "tdb-jd-row">
                            <div class="tdb-jd-col-sm-4-add-del" id="tdb-col-add-del-mail1">
                                <a  class="tdb-jd-button-add tdb-jd-button-add-del" id="addEmail">{$langAddEmail} </a>
                            </div>
                            <div class="tdb-jd-col-sm-1-add-del" id="tdb-col-add-del-mail2">
                                <a  class="tdb-jd-button-add-del tdb-jd-button-del" id="DelEmail" {$displayEmail}>{$langRemove}</a>
                            </div>
                        </div>
                    </div>
                {/if}
                {if $applyPhoneNumbers != ""}
                    <div class="tdb-jd-container" id="phonecontainer">
                        {$telephone}
                    </div>

                    <div class="tdb-jd-container" >
                        <div class = "tdb-jd-row">
                            <div class="tdb-jd-col-sm-4-add-del" id="tdb-col-add-del-phone1">
                                <a  class="tdb-jd-button-add tdb-jd-button-add-del" id="addPhone">{$langAddPhone}</a>
                            </div>
                            <div class="tdb-jd-col-sm-1-add-del" id="tdb-col-add-del-phone2">
                                <a  class="tdb-jd-button-add-del tdb-jd-button-del" id="DelPhone" {$displayPhone}>{$langRemove}</a>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>

        {if $applyNationality != ""}
            <div class="tdb-jd-row">
                {$nationality}
            </div>
        {/if}
        {if $applyVisa == true}
            <div class="tdb-jd-row">
                {$visaType} {$visaCountry}
            </div>
        {/if}
        {if $nbSkill > 0}
            <hr class="tdb-jd-my-4">
            <!-- Language bloc -->
            <div class="tdb-jd-show-id-form" id="LanguageData" >
                <h3 id="tdb-title-language">{$langLanguage}</h3>
            </div>
            <div class="tdb-jd-show-id-form1" id="LanguageDataBloc"  >
                {if $applyLanguages != ""}
                    <div class="tdb-jd-container" id="languageSkillContainer">
                        {$languageAbility}
                    </div>

                    <div class="tdb-jd-container" >
                        <div class = "tdb-jd-row">
                            <div class="tdb-jd-col-sm-4-add-del" id="tdb-col-add-del-skill1">
                                <a  class="tdb-jd-button-add tdb-jd-button-add-del" id="addLanguageSkill">{$langLanguageSkill}</a>
                            </div>
                            <div class="tdb-jd-col-sm-1-add-del" id="tdb-col-add-del-skill2">
                                <a  class="tdb-jd-button-add-del tdb-jd-button-del" id="DelLanguageSkill" {$displayLanguage}>{$langRemove}</a>
                            </div>
                        </div>
                    </div>
                {/if}
                {if $applyLanguageCertifications != ""}
                    <div class="tdb-jd-show-id-form1" id="LanguageScoreBloc"  >
                        <div class="tdb-jd-container" id="languageScoreContainer">
                            {$languageCertification}
                        </div>
                    </div>
                    <div class="tdb-jd-container" >
                        <div class = "tdb-jd-row">
                            <div class="tdb-jd-col-sm-4-add-del" id="tdb-col-add-del-certif1">
                                <a  class="tdb-jd-button-add tdb-jd-button-add-del" id="addLanguageScore">{$langAddCertif}</a>
                            </div>
                            <div class="tdb-jd-col-sm-1-add-del" id="tdb-col-add-del-certif2">
                                <a  class="tdb-jd-button-add-del tdb-jd-button-del" id="DelLanguageScore" {$displayCertification}>{$langRemove}</a>
                            </div>
                        </div>
                        <div class = "tdb-jd-row">
                            {$certification}
                        </div>
                    </div>
                {/if}
            </div>
        {/if}

        {if $applyCurrentSalaryBonus != "" || $applyCurrentSalary != "" || $applyCurrentEmploymentCompany != ""|| $applyCurrentEmploymentPosition != ""|| $applyCurrentEmploymentDepartment != ""}
            <hr class="tdb-jd-my-4">
            <!-- employment bloc -->
            <div class="tdb-jd-show-id-form" id="CurrentEmploymentData" >
                <h3 id="tdb-title-employment">{$langEmployment}</h3>
            </div>
            <div class="tdb-jd-show-id-form1" id="CurrentEmploymentDataBloc"  >
                <div class="tdb-jd-container">
                    <div class="tdb-jd-row">
                        {if $applyCurrentEmploymentCompany != ""}
                            {$currentEmploymentCompany}
                        {/if}
                        {if $applyCurrentEmploymentPosition != ""}
                            {$currentEmploymentPosition}
                        {/if}
                        {if $applyCurrentEmploymentDepartment != ""}
                            {$currentEmploymentDepartment}
                        {/if}
                    </div>


                    {if $applyCurrentSalary != ""}
                        <div class="tdb-jd-row">
                            {$currentSalary}
                            {$currentSalaryCurrency}
                            {$currentSalaryBasis}
                        </div>
                    {/if}
                    {if $applyCurrentSalaryBonus != ""}
                        <div class="tdb-jd-row">
                            {$currentSalaryBonus}
                            {$currentSalaryBonusCurrency}
                        </div>
                    {/if}
                </div>
            </div>
        {/if}

        {if $applyDesiredWage != ""}
            <hr class="tdb-jd-my-4">
            <!-- Salary bloc -->
            <div class="tdb-jd-show-id-form" id="SalaryData" >
                <h3 id="tdb-title-salary">{$langSalary}</h3>
            </div>
            <div class="tdb-jd-show-id-form1" id="SalaryDataBloc"  >
                <div class="tdb-jd-container">
                    <div class="tdb-jd-row">
                        {$desiredWage}
                        {$currency}
                        {$basis}
                    </div>
                </div>
            </div>
        {/if}
        {if $nbEmployement > 0  && ($applyDesiredEmploymentTypes != "" || $applyDesiredIndustry != "" || $applyDesiredJobCategory != "" || $applyDesiredLocation != "")}
            <hr class="tdb-jd-my-4">
            <!-- Employement bloc -->
            <div class="tdb-jd-show-id-form" id="EmploymentData">
                <h3 id="tdb-title-employement">{$langEmployement}</h3>
            </div>
            <div class="tdb-jd-show-id-form1" id="EmploymentDataBloc" >
                <div class="tdb-jd-container">
                    {if $applyDesiredEmploymentTypes != ""}
                        <div class="tdb-jd-row">
                            <label class="tdb-jd-emptypes">
                                {$requiredSpanEmployementType}
                            </label>
                        </div>
                        {$checkBoxEmployement}
                        <br/>
                    {/if}

                    {if $applyDesiredIndustry != ""}
                        <div class="tdb-jd-show-id-form" id="IndustryData">
                            <h4 id="tdb-title-industry">{$langIndustry}</h4>
                        </div>
                        <div class="tdb-jd-row tdb-jd-box">
                            <label class="tdb-jd-desired-ind">
                                {$requiredSpanIndustry}
                            </label>
                        </div>
                        {$checkBoxIndustry}
                        <br/>
                    {/if}
                    {if $applyDesiredJobCategory != ""}
                        {if $bCategory != FALSE }
                            <div class="tdb-jd-show-id-form" id="JobCategoryData">
                                <h4 id="tdb-title-category">{$langCategory}</h4>
                            </div>
                            <div class="tdb-jd-row tdb-jd-box">
                                <label class="tdb-jd-desired-cat">
                                    {$requiredSpanCategory}
                                </label>
                            </div>
                            {$checkBoxCategory}
                            <br/>
                        {/if}
                    {/if}
                    {if $applyDesiredLocation != ""}
                        <div class="tdb-jd-show-id-form" id="DesiredLocationData">
                            <h4 id="tdb-title-location">{$langLocation}</h4>
                        </div>
                        <div class="tdb-jd-row tdb-jd-box">
                            <label class="tdb-jd-desired-loc">
                                {$requiredSpanLocation}
                            </label>
                        </div>
                        {$checkBoxLocation}
                        <br/>
                    {/if}
                </div>
            </div>
        {/if}
        {if $applyReferrer != ""}
            <hr class="tdb-jd-my-4">
            <!-- Other bloc -->
            <div class="tdb-jd-show-id-form1" id="ExtraDataBloc" >
                <div class="tdb-jd-container">
                    <div class="tdb-jd-row">
                        {$referrer}
                    </div>
                    {if $applyNoticePeriod != ""}
                        <div class="tdb-jd-row">
                            {$noticePeriod}
                        </div>
                    {/if}
                </div>
            </div>
        {/if}

        {if $nbSocial > 0}
            <hr class="tdb-jd-my-4">
            <!-- Employement bloc -->
            <div class="tdb-jd-show-id-form" id="SocialData">
                <h3 id="tdb-title-media">{$langSocialMedia}</h3>
            </div>
            <div class="tdb-jd-show-id-form1" id="SocialDataBloc" >
                <div class="tdb-jd-container">

                    {if $applyFacebook != ""}
                        <div class="tdb-jd-row">
                            {$facebookUrl}
                        </div>
                    {/if}
                    {if $applyLinkedin != ""}
                        <div class="tdb-jd-row">
                            {$linkedinUrl}
                        </div>
                    {/if}
                    {if $applyUrl != ""}
                        <div class="tdb-jd-row">
                            {$linkUrl}
                        </div>
                    {/if}
                </div>
            </div>
        {/if}

        {if $applyAttachment != ""}
            <!-- File bloc -->
            <hr class="tdb-jd-my-4">
            <div class="tdb-jd-show-id-form" id="FileData" >
                <h3 id="tdb-title-file">{$langFile}</h3>
            </div>
            <div class="tdb-jd-show-id-form1" id="FileDataBloc" >
                <div class="tdb-jd-container">
                    <div class="tdb-jd-row">
                        <div class="tdb-jd-col-4" id="tdb-col-file">
                            <input class ="tdb-jd-form-control tdb-jd-label" type="file"  name="attachments[]"  id="attachments"
                                   accept=".doc,.docx,.pdf,.xls,.xlsx"
                                   multiple  {$requiredAttachment}>
                        </div>
                    </div>
                    <div class="tdb-jd-row">
                        <div class="tdb-jd-col-{$colSize['resumeRegister']}" id="tdb-col-attachment">
                            <label for="attachments" class="tdb-jd-label tdb-jd-label-attachment">{$langUpload}
                                {$requiredSpanAttachment}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        {if $privacyPolicyRequired == true}
            <div class="tdb-jd-row tdb-jd-privacy-policy">
                {$checkboxPolicy}
                <div class="tdb-jd-col-{$colSize['privacyPolicyLabel']} tdb-jd-privacy-policy-label" id="tdb-col-policy"> <label for="privacyPolicy">{$labelPolicy} {$classRequired} </label>
                </div>
            </div>
        {/if}

        {if $recaptcha == true }
            <br/>
            <div class="row tdb-jb-row-recaptcha">
                <div class="g-recaptcha brochure__form__captcha tdb-jb-recaptcha" data-sitekey="{$recaptchaKey}"></div>
            </div>
        {/if}

        {$nonce}
        <input type="text" name="tdbinput"  id="tdbinput" style="display: none;">

        <br/>
        <div class="tdb-jd-row">
            <button type="submit" class="tdb-jd-apply-btn" name="button-submit"  id="button-submit">{$langSubmit}</button>
        </div>

    </form>
</div>
