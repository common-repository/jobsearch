<div class="tdb-jd-job">
    <!--Title job -->
    <div class="tdb-jd-row tdb-jd-row-header-detail tdb-jd-row-widget">
        <!-- Image -->
        <div class="tdb-jd-col-2 tdb-jd-col-3-widget">
            <img class='tdb-jd-img' src="{$imageUrl}" alt="Job Result Image" id="joblist-img">
        </div>

        <div  class="tdb-jd-col-10 tdb-jd-col-subject tdb-jd-col-9-widget">
            <div class="tdb-jd-row">
                <div class="tdb-jd-col-6">
                    <span class="tdb-jd-date-widget">  {$langPublished}: &nbsp; {$dateFormated}</span>
                </div>
               <!-- {if $tags != ''}
                    <div class="tdb-jd-col-6">
                        <span class="tdb-jd-tags">  {$tags}</span>
                    </div>
                {/if} -->
            </div>
            <div class="tdb-jd-row tdb-jd-row-widget">
                <div class="tdb-jd-col-12 tdb-jd-col-12-widget">
                    <a class='tdb-jd-link-title' href="{$url}">{$title} {if $amount != ""}
                           / {$salary}
                        {/if} {if !empty($location)}
                           / {$location}
                        {/if}</a><br/>
                    <a class='tdb-jd-link-more tdb-jd-link-bottom' href="{$url}"> {$langMore} </a>
                </div>
            </div>
        </div>

    </div>
</div>