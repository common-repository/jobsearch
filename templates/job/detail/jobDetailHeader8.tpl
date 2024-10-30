<section id="job-detail">
<div class="tdb-jd-container-parent tdb-jd-jumbotron tdb-jd-detail-header-video">
    {if $hasImage == true || $hasVideo == true}
        <div class="tdb-jd-row tdb-jd-row-header-video">
            <div  class="tdb-jd-col-6 tdb-jd-col-title-content tdb-jd-col-title-image tdb-jd-detail-image">
                {if $hasImage == true}
                    <img class="tdb-jd-image-header" src="{$imageUrl}" alt="Job Result Image" id="jobresult-img">
                {/if}
            </div>
            <div  class="tdb-jd-col-6 tdb-jd-col-title-content tdb-jd-col-title-video ">
                {if $hasVideo == true}
                    {$video}
                {/if}
            </div>
        <br/>
    {/if}
    <div  class="tdb-jd-row tdb-jd-button-detail">
        {if isset($jobTitleTag)}
            <{$jobTitleTag} class="tdb-jd-title-detail">{$title}</{$jobTitleTag}>
        {else}
            <h2 class="tdb-jd-title">{$title}</h2>
        {/if}
        <a class='tdb-jd-button-back tdb-jd-custom-p-type' href='{$urlHome}'>{$langReturnSearch}</a>
        {if $is_pro_active == TRUE}
            <a class='tdb-jd-button-more tdb-jd-custom-p-type' href='{$urlApply}'>{$langApply}</a>
        {/if}
    </div>
    <div class="tdb-jd-row tdb-jd-published" >
        <span class="tdb-jd-date-published">  {$langPublished}: &nbsp; {$dateFormated}</span>
    </div>
</div>

<div class="tdb-jd-container tdb-jd-job-detail">
    <div class="tdb-jd-jumbotron-des tdb-jd-jumbo-detail" id="tableBottom">