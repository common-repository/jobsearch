<div class="tdb-jd-job">
    <!--Title job -->
    <div class="tdb-jd-row tdb-jd-row-header-detail">
        <div  class="tdb-jd-col-12">
            <h3 class ="tdb-jd-title tdb-jd-col-12 tdb-jd-col-title"><a class='tdb-jd-link-title' href="{$url}">{$title}</a>
                <span class="tdb-jd-date-published">  {$langPublished}: &nbsp; {$dateFormated}</span>
            </h3>
        </div>
    </div>


    <div class="tdb-jd-row-header-detail tdb-jd-row tdb-jd-4-content">
        <div  class="tdb-jd-col-3 tdb-jd-col-subject" id="job-detail-leftside">
            <p>
            <!-- Category -->
            {if !empty($category)}
                {$category}
            {/if}
                <br>
            <!--Industry -->
            {if !empty($industry)}
                {$industry}
            {/if}
                <br>
            <!--Type -->
            {if !empty($type)}
                {$type}
            {/if}
            </p>

        </div>

        <div  class="tdb-jd-col-subject tdb-jd-col-6">
            <!--Description -->
            <p class="tdb-jd-custom-p">{$description}
                <br/><a class='tdb-jd-link-more' href="{$url}"> {$langMore} </a>
            </p>
        </div>

        <div  class="tdb-jd-col-subject tdb-jd-col-3 tdb-jd-leftside-tags">
            <p>
            <!--Location -->
            {if !empty($location)}
                {$location}
            {/if}
            <br>
            <!--Job Language -->
            {if !empty($jobLanguage)}
                {$jobLanguage}
            {/if}
            <br>
            <!-- Salary -->
            {if !empty($salary)}
                {$salary}
            {/if}
            </p>
        </div>

        <!--Separator -->
        {if $i != $nbElementJson }
            <hr class="tdb-jd-my-4">
        {/if}
    </div>

    <div class="tdb-jd-row">
        <div class="tdb-jd-more-detail"><a class='tdb-jd-link-more' href="{$url}"> {$langMoreInformation} </a>
        </div>
        <div>
            <a class="tdb-jd-button-more" href='{$urlApply}'>{$langApply}</a>
        </div>
    </div>
</div>