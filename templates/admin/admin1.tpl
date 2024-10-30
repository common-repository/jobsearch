<h2>{$langParameter}</h2>
<!--<h3>{$langUpdateParameter}</h3>-->

<!--{$apiTab}-->

<div class="tdb-jd-tab">
    <button class="tdb-jd-tablinks active" id="tdb-jd-link-api" >{$langTabLink}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-job" >{$langTabJob}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-design" >{$langTabDesign}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-search" >{$langTabSearch}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-detail-page" >{$langTabDetail}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-apply-page" >{$langTabApply}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-colsize-page" >{$langColSize}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-widget-page" >{$langTabWidget}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-social" >{$langTabSocial}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-mail" >{$langTabTemplate}</button>
    <button class="tdb-jd-tablinks" id="tdb-jd-shortcode-page" >{$langTabShortcode}</button>
</div>
<br/>

<form method="POST" enctype="multipart/form-data">
    <div class="tdb-jd-container">
        <!-- LINK -->
        <div id="tdb-jd-tab-link-api" class="tdb-jd-tabcontent active" style="display: block;">

            <div class="tdb-jd-container" id="linkcontainer" >
                {$apiLink}
            </div>

            <div class="tdb-jd-container" >
                <div class = "tdb-jd-row">
                    <div class="tdb-jd-col-sm-4-add-del">
                        <a  class="tdb-jd-button-add tdb-jd-button-add-del" id="addLink">{$langAddLink} </a>
                    </div>
                    <div class="tdb-jd-col-sm-1-add-del">
                        <a  class="tdb-jd-button-add-del tdb-jd-button-del" id="DelLink" {$displayLink}>{$langRemove}</a>
                    </div>
                </div>
            </div>

            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- JOB -->
        <div id="tdb-jd-tab-job" class="tdb-jd-tabcontent">
            <h4>{$langOther}</h4>
            <div class="tdb-jd-row">
                {$nbPageToShow}
            </div>
            <div class="tdb-jd-row">
                {$nbJobToShowWidget}
            </div>

            <hr class="tdb-jd-my-4">

            <div class="tdb-jd-row">
                {$shortDescriptionMaxCharacters}
            </div>

            <div class="tdb-jd-row">
                {$descriptionCleanedCheck}
            </div>

            <h3>{$langSortBy}</h3>
            <div class="tdb-jd-row">
                {$getSortByField}
            </div>

            <h3>{$langPreferedContent}</h3>
            <p>{$langPreferedListOption}</p>
            <div class="tdb-jd-row">
                {$favoriteCurrency}
                {$favoriteBasis}
                {$favoriteCertif}
            </div>
            <div class="tdb-jd-row">
                {$searchShowOneCurrency}
                {$searchShowOneBasis}
            </div>
            <h4>{$langCategories}</h4>
            <div class="tdb-jd-row">
                {$displayCategories} {$excludedCategories}
            </div>

            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- DESIGN -->
        <div id="tdb-jd-tab-design" class="tdb-jd-tabcontent">
{*            <h3>{$langContentUpdate}</h3>*}
{*            <div class="tdb-jd-row">*}
{*                {$colorPicker}*}
{*            </div>*}

{*            <h4>{$langButton}</h4>*}
{*            <div class="tdb-jd-row">*}
{*                {$submitBackground}*}
{*                {$submitFont}*}
{*            </div>*}

{*            <div class="tdb-jd-row">*}
{*                {$btnMoreBackground}*}
{*                {$btnMoreFont}*}
{*            </div>*}

{*            <h4>{$langFont}</h4>*}
{*            <div class="tdb-jd-row">*}
{*                {$linkFont}*}
{*            </div>*}

            {if $nbTemplate > 1}
                <h4>{$langTemplate}</h4>

                <div class="tdb-jd-row">
                    {$template}
                </div>
            {/if}

            <h4>{$langDefaultImage}</h4>

            <div class="tdb-jd-row">
                <div class="tdb-jd-col-12">
                    {$defaultImage}
                </div>
            </div>
            <div class="tdb-jd-row">
                <img src = '{$pathDefaultImage}'>
            </div>

            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- SEARCH -->
        <div id="tdb-jd-tab-search" class="tdb-jd-tabcontent">
            <h3>{$langDisplayField}</h3>
            <div class="tdb-jd-row">
                {$displaySearchType}
                {$displaySearchIndustry}
                {$displaySearchCategory}
            </div>
            <h3>{$langHideField}</h3>
            <div class="tdb-jd-row">
                {$hideSalary}
            </div>
            <div class="tdb-jd-row">
                {$hideLocation}
            </div>
            <div class="tdb-jd-row">
                {$hideCurrency}
            </div>
            <div class="tdb-jd-row">
                {$hideBasis}
            </div>
            <div class="tdb-jd-row">
                {$hideMaxWage}
            </div>
            <div class="tdb-jd-row">
                {$hideMinWage}
            </div>
            <div class="tdb-jd-row">
                {$hideLanguage}
            </div>
            <div class="tdb-jd-row">
                {$hideAddLanguage}
            </div>
            <div class="tdb-jd-row">
                {$hideReset}
            </div>
            <br/>
            <div class="tdb-jd-row">
                {$searchVisible}
            </div>
            <h3>{$langTags}</h3>
            <div class="tdb-jd-row">
                {$tagGroup1} {$displayTagGroup1}
            </div>
            <div class="tdb-jd-row">
                {$tagGroup2} {$displayTagGroup2}
            </div>
            <div class="tdb-jd-row">
                {$tagGroup3} {$displayTagGroup3}
            </div>
            <div class="tdb-jd-row">
                {$tagGroup4} {$displayTagGroup4}
            </div>
            <br/>
            <h3>{$langsearchFieldIsMultiple}</h3>
            <div class="tdb-jd-row">
                {$searchFieldIsMultiple}
            </div>

            <h3>{$langRewriteUrl}</h3>
            <div class="tdb-jd-row">
                {$rewriteUrl}
            </div>
            <br/>
            <div class="tdb-jd-row">
                <p>{$langPositionSearchButton}</p>
                {$getpositionButton}
            </div>
            <div class="tdb-jd-row">
                {$searchAdvancedButton}
            </div>
            <div class="tdb-jd-row">
                {$belowLanguageCheckHtml}
            </div>
            <div class="tdb-jd-row">
                {$reverseLanguageSkillCheck}
            </div>
            <h3>{$langOrderFavoriteLanguage}</h3>
            <p>{$langExplainFavoriteLanguage}</p>
            <div class="tdb-jd-row">
                {$favoriteLanguageContent}
            </div>
            <hr class="tdb-jd-my-4">
            <h3>{$langOrderFavoriteLanguageSearch}</h3>
            <div class="tdb-jd-row">
                {$favoriteLanguageSearchContent}
            </div>
            <hr class="tdb-jd-my-4">
            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- DETAIL -->
        <div id="tdb-jd-tab-detail-page" class="tdb-jd-tabcontent">
            <h3>{$langUpdateElemShow}</h3>
            <p>{$langElemShowExplain}</p>
            <div class="tdb-jd-row">
                {$checkAllShow}
            </div>
            {$getShowField}
            <h3>{$langUpdateVideoShow}</h3>
            <p>{$langVideoShowExplain}</p>
            {$getVideoField}
            <h3>{$langTagJobTitle}</h3>
            {$jobTitleTag}
            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- APPLY -->
        <div id="tdb-jd-tab-apply-page" class="tdb-jd-tabcontent">
            <h3>{$langOrderFavoriteCountry}</h3>
            <p>{$langExplainFavoriteNationality}</p>
            <div class="tdb-jd-row">
                {$favoriteCountryContent}
            </div>

            <h3>{$langOrderFavoriteNationality}</h3>
            <p>{$langExplainFavoriteNationality}</p>
            <div class="tdb-jd-row">
                {$favoriteNationalityContent}
            </div>



           <!-- <h3>{$langOrderFavoriteCountryOther}</h3>
            <p>{$langExplainFavoriteNationality}</p>
            <div class="tdb-jd-row">
                {$favoriteNationalityContentOthers}
            </div>

            <hr class="tdb-jd-my-4">
            <br/>-->



            <h3>{$langRequiredField}</h3>
            <p>{$langMandatoryExplain}</p>
            <div class="tdb-jd-row">
                {$checkAllRequired}
            </div>
            <br/>
            <div class="tdb-jd-row">
                {$requiredGivenName}
                {$requiredFamilyName}
            </div>

            {$required}
            <br/>
            <div class="tdb-jd-row">
                {$sourceType}
            </div>
            <hr class="tdb-jd-my-4">
            <h3>{$langApplyField}</h3>
            <p>{$langApplyExplain}</p>
            <div class="tdb-jd-row">
                {$checkAllApply}
            </div>
            {$applyField}
            <hr class="tdb-jd-my-4">
            <h3>{$langAttachmentStorage}</h3>
            <div class="tdb-jd-row">
                {$attachmentStorageLocalEnableCheck}
            </div>
            <p><strong><span style="color:red;">{$langAttention}</span> {$langAttachmentStorageLocalWarning}</strong></p>
            <hr class="tdb-jd-my-4">
            <h3>{$langNationalityCountryVisa}</h3>
            <div class="tdb-jd-row">
                {$autoNationalityCountry}
            </div>
            <h3>{$langPrivacyPolicy}</h3>
            <div class="tdb-jd-row">
                {$privacyPolicyText}
            </div>
            <h3>{$langRecaptchaKey}</h3>
            <p>{$langRecaptchaExplain} <a href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a> </p>
            <div class="tdb-jd-row">
                {$recaptchaKey}
                {$recaptchaSecret}
            </div>
            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Col size -->
        <div id="tdb-jd-tab-colsize-page" class="tdb-jd-tabcontent">
            <h3>{$langColSize}</h3>
            <p>{$langExplainColSize}</p>
            <br/>
            {$colsizefield}
            <br/>
            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- WIDGET -->
        <div id="tdb-jd-tab-widget-page" class="tdb-jd-tabcontent">
            <h3>{$langWidget}</h3>
            <div class="tdb-jd-row">
                {$widgetChoosenMaximumDateJob}
            </div>
            <div class="tdb-jd-row">
                {$widgetChoosenCategory}
            </div>
            <h4>{$langUrlApply}</h4>
            <div class="tdb-jd-row">
                {$urlApplyText}
            </div>

            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>

        <!-- SOCIAL -->
        <div id="tdb-jd-tab-social" class="tdb-jd-tabcontent">
            <h3>{$langTabSocial}</h3>
            <div class="tdb-jd-row">
                <div class="tdb-jd-col-12">
                    {$socialLogo}
                </div>
            </div>
            <div class="tdb-jd-row">
                <img src = '{$pathSocialLogo}'>
            </div>
            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>

        <!-- TEMPLATE -->
        <div id="tdb-jd-tab-mail" class="tdb-jd-tabcontent">
            <h3>{$langTabTemplate}</h3>
            <div class="tdb-jd-row">
                <p style="font-weight: bold">{$langExplainTemplate}</p>
            </div>
            <div class="tdb-jd-row">
                <div class="tdb-jd-col-12">
                    {$templateActivate}
                </div>
            </div>
            <div class="tdb-jd-row">
                {$fromTemplate}
            </div>
            <div class="tdb-jd-row">
                {$ccTemplate}
            </div>
            <div class="tdb-jd-row">
                {$bccTemplate}
            </div>
            <div class="tdb-jd-row">
                <div class="tdb-jd-col-12">
                    {$defaultTemplate}
                </div>
            </div>
            <div class="tdb-jd-container">
                {$otherTemplate}
            </div>

            <div class="tdb-jd-container">
                <div class="tdb-jd-row">
                    <div class="tdb-jd-col-12">
                        <input name="hiddenUpdtParam" type="hidden"  value="hiddenUpdtParam"/>
                        <input name="jobSearchSend" type="submit" class="tdb-jd-btn-admin tdb-jd-btn-primary" value="{$langUpdate}"/>
                    </div>
                </div>
            </div>
        </div>

        <!-- SHORTCODE -->
        <div id="tdb-jd-tab-shortcode-page" class="tdb-jd-tabcontent">
            <h3>{$langShortcode}</h3>
            <div class="tdb-jd-row">
                <div class="tdb-jd-col-12">
                    {$shortCodeContent}
                </div>
            </div>
        </div>
    </div>
    <hr class="tdb-jd-my-4">
    {$nonce}
</form>
