<div class="tdb-jd-job tdb-jd-col-3">
    <!--Title job -->
    <div class="tdb-jd-row tdb-jd-row-header-detail">
        <div  class="tdb-jd-col-12">
            <h3 class ="tdb-jd-title tdb-jd-col-12 tdb-jd-col-title"><a class='tdb-jd-link-title' href="{$url}">{$title}</a></h3>
        </div>
    </div>

    <div class="tdb-jd-row-header-detail">
        <div>
            <img src="{$imageUrl}" alt="Job Result Image" id="joblist-img">
        </div>

        <div  class="tdb-jd-col-subject">
            <!--Location -->
            {if !empty($location)}
                <p class="tdb-jd-custom-p">{$location}</p>
            {/if}
            <!--Type -->
            {if !empty($type)}
                <p class ="tdb-jd-custom-p">{$type}</p>
            {/if}
            <!--Job Language -->
            {if !empty($jobLanguage)}
                <p class="tdb-jd-custom-p" >{$jobLanguage}</p>
            {/if}
            <!--Description -->
            <p class="tdb-jd-custom-p">{$description}
                <br/><a class='tdb-jd-link-more' href="{$url}"> {$langMore} </a>
            </p>
            <small>{$langPublished}: &nbsp; {$dateFormated}</small>
        </div>
        <!--Separator -->
        {if $i != $nbElementJson }
            <hr class="tdb-jd-my-4">
        {/if}
    </div>
    <div class="tdb-jd-row tdb-jd-row-header-detail">
        <div class="tdb-jd-more-detail"><a class='tdb-jd-link-more' href="{$url}"> {$langMoreInformation} </a></div>
    </div>
</div>