<div ui-layout options="{flow: 'column'}" class='dashboard'>
    <div id='col1' class="container-full">
        
        <div class="dashboard-mode" style="display:none;position:absolute;right:0px;font-size:11px;font-weight:bold;color:#999;padding:5px;">
            <i class="fa fa-desktop"></i> Dashboard Mode
        </div>

        <div class="form-builder-saving dashboard-saving" style="display:none;z-index:101;opacity:.6">
            <span> 
                <i class="fa fa-refresh fa-spin"></i>
                Saving... 
            </span>
        </div>
        <div id="must-reload">
            <h3>Source file has changed</h3>
            <div class="btn btn-success" onclick="location.reload()"><i class="fa fa-refresh"></i> Refresh Form</div>
        </div>
        <div class="container-fluid" style="padding-bottom:600px;">
            <?= @$col1['content'] ?>
        </div>
    </div>
</div>