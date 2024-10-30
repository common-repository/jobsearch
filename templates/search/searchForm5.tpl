{if isset($urlSearch) && $urlSearch != ''}
    <form action='{$urlSearch}' class="tdb-jd-search-form" method="GET" >
        <input type="hidden" name="urlSearch"  value ="urlSearch"/>
{else}
    <form class="tdb-jd-search-form" method="GET" >
{/if}

{if isset($searchHidden) &&  $searchHidden != ''}
    <input type="hidden" name="searchHidden"  value ="true"/>
{else}
    <input type="hidden" name="searchHidden"  value ="false"/>
{/if}
    <div class = "tdb-jd-container">
        <div class = "tdb-jd-row">
            <div class = "tdb-jd-col-8" id="keywordplaceholder">
                <div class = "tdb-jd-row">
                    {$keyword}
                    {if $nbLocation != ""}
                        {if $searchHideLocation == ''}
                            {$location}
                        {/if}
                    {/if}
                </div>
            </div>
            {if $searchPosition == 0 }
                <div class = "tdb-jd-col-4 tdb-jd-search-top" id="searchreset">
                    {if $searchAdvancedButton == 1}
                        <input id="advancedSearchBtn" name="advancedSearchBtn" type="button" class="tdb-jd-advanced-btn tdb-jd-advanced-btn-top" value="{$langAdvanced}"/>
                    {/if}
                    {if $searchHideReset == ''}
                        <input id ='jobSearchReset' name="jobSearchReset" type="reset" class="tdb-jd-reset-button" value="{$langReset}"/>
                    {/if}
                    <input id="jobSearchSend" name="jobSearchSend" type="submit" class="tdb-jd-search-btn tdb-jd-search-btn-top" value="{$langSearch}"/>
                </div>
            {/if}
        </div>
        <div class ='{$containerHiddenCss}' id ='tdb-advanced-search'>
            {if $isMultipleFieldEmpty == false}
                <div id="kindJobContainer" class = "tdb-jd-container" id="jobsContainer">
                    <div class ="tdb-jd-row" id="KindJob" >
                        {if $nbType > 0}
                            {$type}
                        {/if}
                        {if $nbIndustry > 0}
                            {$industry}
                        {/if}
                        {if $nbCategory > 0}
                            {$category}
                        {/if}
                    </div>
                </div>
            {/if}

            <!--tags -->
            <div class = "tdb-jd-container" id="tagsContainer">
                <div class = "tdb-jd-row" id="Tags" >
                    {$tags1}
                    {$tags2}
                    {$tags3}
                    {$tags4}
                </div>
            </div>
            <!-- end tags -->

            {if $searchHideSalary == ''}
                <div class="tdb-jd-container" id="salaryContainer">
                    <div class = "tdb-jd-row" id="salaryRow">
                        <div class="tdb-jd-col-4 tdb-jd-type"><label for="salary" class="tdb-jd-label">{$langSalary}</label></div>
                    </div>
                    <div class = "tdb-jd-row" id="currencyRow">
                        {if $searchHideMinWage == ''}
                            {$wageFrom}
                        {/if}
                        {if $searchHideMaxWage == ''}
                            {$wageTo}
                        {/if}
                        {if $searchHideCurrency == ''}
                            {$currency}
                        {/if}
                        {if $searchHideBasis == ''}
                            {$wageBasis}
                        {/if}
                    </div>
                </div>
            {/if}

            {if $searchHideLanguage == ''}
                <div class="tdb-jd-container" id="languageSkillContainer">
                    {$language}
                </div>


                {if $searchHideAddLanguage== ''}
                    <div class="tdb-jd-container" id="languageAddSkillContainer" >
                        <div class = "tdb-jd-row tdb-jd-row-add-search">
                            <!-- empty col to align add with remove -->
                            <a  class="tdb-jd-button-add tdb-jd-button-add-del" id="addLanguageSkillForm">{$langAdd}</a>
                        </div>
                    </div>
                {/if}
            {/if}
        </div>
        {$nonce}
        <input type="hidden" name="send"  value ="send"/>
        {if $searchPosition == 1 }
            <hr>
            <div class = "tdb-jd-row">
                <div class = "tdb-jd-col-12 tdb-jd-search-bottom" id="searchreset">
                    {if $searchAdvancedButton == 1}
                        <input id="advancedSearchBtn" name="advancedSearchBtn" type="button" class="tdb-jd-advanced-btn" value="{$langAdvanced}"/>
                    {/if}
                    <input id ='jobSearchReset' name="jobSearchReset" type="reset" class="tdb-jd-reset-button" value="{$langReset}"/>
                    <input id="jobSearchSend" name="jobSearchSend" type="submit" class="tdb-jd-search-btn" value="{$langSearch}"/>
                </div>
            </div>
        {/if}
    </div>
</form>