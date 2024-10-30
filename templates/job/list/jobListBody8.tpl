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
    <div class="tdb-jd-row tdb-jd-list-content">
        <div  class="tdb-jd-col-9 tdb-jd-col-list-content ">
            <p class="tdb-jd-custom-p">{$description}
                <br/><a href="{$url}" class='tdb-jd-link-more'> {$langMore} </a>
            </p>
        </div>
        {if $hasImage === true}
            <div  class="tdb-jd-col-3 tdb-jd-list-image">
                <img src="{$imageUrl}" alt="Job Result Image" id="joblist-img">
            </div>
        {/if}
    </div>

    <!--Separator -->
    {if $i != $nbElementJson }
        <hr class="tdb-jd-my-4">
    {/if}
</div>