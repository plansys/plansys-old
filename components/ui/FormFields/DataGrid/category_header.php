<div ng-if="showCategories" class="ngTopPanel categoryStyle">

    <div class="ngHeaderContainer" style="height:{{headerRowHeight }}px !important;">
        <div class="categoryHeaderScroller" style="height:{{headerRowHeight }}px;position:absolute;">  <!-- fixes scrollbar issue -->
            <div class="ngHeaderCell" ng-repeat="cat in categories"  style="left: {{cat.left}}px; width: {{cat.width}}px;height:{{cat.single?headerRowHeight * 2: headerRowHeight}}px;">
                <div class="ngVerticalBar" style="height:100%" ng-class="{ ngVerticalBarVisible: !$last }">&nbsp;</div>
                <div class="ngHeaderText" style="text-align:center;line-height:{{cat.single ? 55 : 25}}px;">{{cat.displayName}} </div>
            </div>
        </div>
    </div>
</div>
<div ng-if="showCategories" style="height:{{headerRowHeight}}px;visibility:hidden;"></div>