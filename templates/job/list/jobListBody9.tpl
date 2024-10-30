<div class="tdb-jd-job">
    <!--Title job -->
    <div class="tdb-jd-row tdb-jd-row-header-detail">
        <div class="tdb-jd-col-10 tdb-jd-col-title">
            <h3 class="tdb-jd-title "><a href="{$url}" class='tdb-jd-link-title'>{$title}</a></h3>
        </div>
        <div class="tdb-jd-col-2 tdb-jd-date-published-main tdb-jd-col-title">
            <span class="tdb-jd-date-published-second">{$langPublished}: &nbsp; {$dateFormated}</span>
        </div>
    </div>
    <!--Type -->
    {if !empty($type)}
        <div class="tdb-jd-row tdb-jd-row-header-detail">
            <div  class="tdb-jd-col-3 tdb-jd-col-title-content ">
                <p class ="tdb-jd-custom-p" >{$langType}</p>
            </div>

            <div  class="tdb-jd-col-9 tdb-jd-col-subject">
                <p class ="tdb-jd-custom-p">{$type}</p>
            </div>
        </div>
    {/if}
    <!--Location -->
    {if !empty($location)}
        <div class="tdb-jd-row tdb-jd-row-header-detail">
            <div  class="tdb-jd-col-3 tdb-jd-col-title-content">
                <p class ="tdb-jd-custom-p">{$langLocation}</p>
            </div>
            <div class="tdb-jd-col-9 tdb-jd-col-subject">
                <p class="tdb-jd-custom-p">{$location}</p>
            </div>
        </div>
    {/if}
    <!--Industry -->
    {if !empty($industry)}
        <div class="tdb-jd-row tdb-jd-row-header-detail">
            <div  class="tdb-jd-col-3 tdb-jd-col-title-content">
                <p class ="tdb-jd-custom-p"  >{$langIndustry}</p>
            </div>
            <div class="tdb-jd-col-9 tdb-jd-col-subject">
                <p class="tdb-jd-custom-p" >{$industry}</p>
            </div>
        </div>
    {/if}
    <!--Category -->
    {if !empty($category)}
        <div class="tdb-jd-row tdb-jd-row-header-detail">
            <div  class="tdb-jd-col-3 tdb-jd-col-title-content">
                <p class ="tdb-jd-custom-p" >{$langCategory}</p>
            </div>
            <div class="tdb-jd-col-9 tdb-jd-col-subject">
                <p class="tdb-jd-custom-p" >{$category}</p>
            </div>
        </div>
    {/if}

    <!--Job Language -->
    {if !empty($requiredLanguage)}
        <div class="tdb-jd-row tdb-jd-row-header-detail">
            <div  class="tdb-jd-col-3 tdb-jd-col-title-content">
                <p class ="tdb-jd-custom-p">{$langrequiredLanguage}</p>
            </div>
            <div class="tdb-jd-col-9 tdb-jd-col-subject">
                <p class="tdb-jd-custom-p" >{$requiredLanguage}</p>
            </div>
        </div>
    {/if}

    <!--Amount -->
    {if $amount != ""}
        <div class="tdb-jd-row tdb-jd-row-header-detail">
            <div class="tdb-jd-col-3 tdb-jd-col-title-content">
                <p class="tdb-jd-custom-p">{$langSalary}</p>
            </div>

            <div class="tdb-jd-col-9 tdb-jd-col-subject">
                <p class="tdb-jd-custom-p">{$formatedAmount}</p>
            </div>

            <div class="tdb-jd-salary-div" style="display: none">
                <p>{$amount}</p>
            </div>
        </div>
    {/if}
    <!--Description -->
    <div class="tdb-jd-row tdb-jd-row-header-detail">
        <div class="tdb-jd-col-3 tdb-jd-col-title-content">
            <p class="tdb-jd-custom-p">{$langDescription}</p>
        </div>
        <div  class="tdb-jd-col-9 tdb-jd-col-subject">
            <p class="tdb-jd-custom-p">{$description}
                <br/><a href="{$url}" class='tdb-jd-link-more tdb-jd-link-description'> {$langMore} </a>
            </p>
        </div>
    </div>

    <div class="tdb-jd-row tdb-jd-row-header-detail tdb-jd-more-detail-bottom-row">
        <div class="tdb-jd-more-detail tdb-jd-more-detail-bottom-link">
            <a href="{$url}" class='tdb-jd-link-more tdb-jd-link-bottom'> {$langMoreInformation} </a>
        </div>
    </div>
</div>