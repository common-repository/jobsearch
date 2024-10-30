<section id="job-detail">
<div class="tdb-jd-container-parent tdb-jd-jumbotron tdb-jd-job-detail-container-header">
    <div  class="tdb-jd-row tdb-jd-row-title-detail">
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
    <div class="tdb-jd-row" >
        <span class="tdb-jd-date-published">  {$langPublished}: &nbsp; {$dateFormated}</span>
    </div>
</div>

<div class="tdb-jd-container tdb-jd-job-detail">
    <div class="tdb-jd-jumbotron-des" id="tableBottom">