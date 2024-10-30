<div class="tdb-jd-container">
    <div class="tdb-jd-row tdb-jd-admin-border tdb-jd-admin-header-color">
        <div class="tdb-jd-col-2 ">
            {$langId}&nbsp;:&nbsp;{$id}
        </div>
        <div class="tdb-jd-col-2">
            {$langIdApi}&nbsp;:&nbsp;{$idApi}
        </div>
        <div class="tdb-jd-col-2">
            {$langIdJob}&nbsp;:&nbsp;{$idJob}
        </div>
        <div class="tdb-jd-col-3">
            {$langName}&nbsp;:&nbsp;{$name}
        </div>
        <div class="tdb-jd-col-3">
            {$langDate}&nbsp;:&nbsp;{$date}
        </div>
    </div>

    {$detail}
    {if $link != ""}
        <div class="tdb-jd-row tdb-jd-admin-border tdb-jd-admin-header-color" style="padding-bottom: 10px;">
            <div class="tdb-jd-col-7 ">
                {$langFile}<br/>{$link}
            </div>
        </div>
    {/if}
    <div class="tdb-jd-row tdb-jd-admin-border tdb-jd-admin-header-color" style="padding: 10px 0 10px 0">
        <div class="tdb-jd-col-7">
            <form action="?page=jobsearchsubmenu" method="post">
                <input type="hidden" name="pushApplication" value="{$id}" />
                <button type="submit" onclick="return confirm('{$langPushConfirm}');">{$langPushApplication}</button>
            </form>
        </div>
    </div>
</div>
