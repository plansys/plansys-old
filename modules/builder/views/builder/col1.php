<?php $h = 26; ?>
<style type="text/css">
    #col1 .tabs-container {
        height:<?= $h + 1 ?>px;
        overflow-y:hidden;
    }
    #col1 .tabs {
        border-bottom:1px solid #ccc;
        color:#666;
        background: #ffffff; /* Old browsers */
        background: -moz-linear-gradient(top, #ffffff 0%, #fafafa 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffffff), color-stop(100%, #fafafa)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #ffffff 0%, #fafafa 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #ffffff 0%, #fafafa 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top, #ffffff 0%, #fafafa 100%); /* IE10+ */
        background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#fafafa', GradientType=0); /* IE6-9 */
        height:<?= $h + 1 ?>px;
    }

    #col1 .tabs .tab {
        background:#fafafa;
        border:1px solid #ddd;
        border-left:0px;
        border-bottom:0px;
        margin-top:3px;
        min-width:30px;
        padding-left:8px;
        padding-right:8px;
        height:<?= $h - 3 ?>px;
        line-height:<?= $h - 3 ?>px;
        text-align:center;
        float:left;
        cursor:pointer;
    }
    #col1 .tabs .tab span {
        font-size:12px;
        font-weight:bold;
    }
    #col1 .tabs .tab:first-child {
        margin-left:3px;
        border-left:1px solid #ddd;
        border-top-left-radius: 3px;
    }
    #col1 .tabs .tab:last-child {
        border-top-right-radius: 3px;
    }
    #col1 .tabs .tab:hover {
        background:#fff;
    }
    #col1 .tabs .tab.active {
        background:#fff;
        height:31px;
    }
    #col1 .tab-load {
        float:right;
        width:30px;
        height:<?= $h ?>px;
        line-height:<?= $h ?>px;
        text-align:center;
    }

    #col1 .tab-load .fa-spin {
        -webkit-transform-origin: 50% calc(50% - .5px);
        transform-origin: 50% calc(50% - .5px);
        -webkit-animation: fa-spin .5s infinite linear;
        animation: fa-spin .5s infinite linear;
    }
</style>

<div id="col1" ng-controller="Col1">
    <div class="tabs-container">
        <div class="tab-load" ng-if="view.loading">
            <i class="fa fa-circle-o-notch fa-spin "></i>
        </div>
        <div class="tabs">
            <div ng-repeat="v in builder.views"
                 class="tab {{ activeTabClass(v.name)}}" 
                 ng-click="activate(v.name)">

                <i class="{{ v.$meta.icon}}"></i> 
                <span ng-if="view.name == v.name">{{ v.$meta.title}}</span>

            </div>
        </div>
    </div>
    <div class="clearfix"></div>

    <div ng-if="view" ng-show="!view.loading"
         ng-include 
         src="view.url"
         onload="activated()"></div>

</div>
