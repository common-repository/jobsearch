<div class ='tdb-jd-container' id = 'resultPost'>
        <h2>{$langApplicationSent}</h2>

        {if $bError == TRUE}
            <!-- The highlighted part -->
            <div class="tdb-jd-row tdb-jd-row-header-detail">
                <div class="tdb-jd-col-12 tdb-jd-submit-error-message">
                    <p class="tdb-jd-custom-p" id="submit-error-msg">{$langLabelError}</p>
                </div>
            </div>

            <div class="tdb-jd-row tdb-jd-row-header-detail">
                <div class="tdb-jd-col-3 tdb-jd-submitp-title">
                    <p class="tdb-jd-custom-p">{$langLabelMessage}</p>
                </div>

                <div class="tdb-jd-col-8 tdb-jd-submit-content">
                    <p class="tdb-jd-custom-p" id ="message">{$langMessage}</p>
                </div>
            </div>
            <!-- message part? -->
            {if $nbFile > 0 }
                <div class="tdb-jd-row tdb-jd-row-header-detail">
                    <div class="tdb-jd-col-3 tdb-jd-submit-title">
                        <p class="tdb-jd-custom-p">{$langLabelMessageAttachment}</p>
                    </div>

                    <div class="tdb-jd-col-8 tdb-jd-col-subject">
                        <p class="tdb-jd-custom-p" id ="message">{$langMessageAttachment}</p>
                    </div>
                </div>
            {/if}

            <!-- email message -->
            {foreach from=$error key=field item=message}
                <div class="tdb-jd-row tdb-jd-row-header-detail">
                    <div class="tdb-jd-col-3 tdb-jd-submit-title">
                        <p class="tdb-jd-custom-p">{$field}</p>
                    </div>

                    <div class="tdb-jd-col-8 tdb-jd-submit-content">
                        <p class="tdb-jd-custom-p" id ="message">{$message}</p>
                    </div>
                </div>
            {/foreach}
        {/if}
    <div>
        <hr id="submit-page-hr">
    </div>
</div>