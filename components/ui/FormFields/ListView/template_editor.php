<div class="form-group form-group-sm {{ field.layout == 'Vertical' ? 'form-vertical' : ''}}">
    <div class="col-sm-{{field.fieldWidth}}">
        <div class="list-view-item" style='margin-bottom:-1px;'>
            <div style="float:right;margin-top:7px;">
                <div  class="list-view-item-remove btn btn-xs">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div class='list-view-item-container' style="opacity:1;">
                <div style="line-height:35px;text-align:center;font-size:12px;color:#999;">
                    <i class="fa fa-list"></i> List View: {{ field.name }}
                </div>
            </div>
        </div>
        <div class="btn list-view-add btn-default btn-sm"><i class="fa fa-nm fa-plus"></i> <b>Add</b></div>
    </div>
</div>