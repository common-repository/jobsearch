<div class="tdb-jd-job">
    <!--Title job -->
    <div class="tdb-jd-row tdb-jd-row-header-detail tdb-jd-row-title-featured">
        <div class="tdb-jd-col-9 tdb-jd-col-title tdb-jd-col-title-featured">
            <h3 class="tdb-jd-title tdb-jd-title-featured"><a href="{$url}" class='tdb-jd-widget-link-title tdb-jd-widget-link-title-featured'>{$title}</a></h3>
        </div>
        <div class="tdb-jd-col-2 tdb-jd-date-published-main tdb-jd-date-published-main-featured tdb-jd-col-title">
            <span class="tdb-jd-date-published-second tdb-jd-date-published-second-featured">{$langPublished}: &nbsp; {$dateFormated}</span>
        </div>
    </div>
    <div class="tdb-jd-row tdb-jd-row-featured ">
        <div  class="tdb-jd-col-8 tdb-jd-col-title-content tdb-jd-col-title-content-featured ">
            <p class="tdb-jd-custom-p tdb-jd-custom-p-featured">{$description}
                <br/><a href="{$url}" class='tdb-jd-link-more tdb-jd-link-more-featured'> {$langMore} </a>
            </p>
        </div>
        {if urlVideo != ''}
            <div  class="tdb-jd-col-4 tdb-jd-col-title-content tdb-jd-list-video tdb-jd-list-video-featured ">
                {$video}
            </div>
        {/if}
    </div>

    <!--Separator -->
    {if $i != $nbElementJson }
        <hr class="tdb-jd-my-4">
    {/if}
</div>