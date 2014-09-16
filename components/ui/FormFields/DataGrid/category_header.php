<div ng-if="categories.length > 2" class="ngTopPanel categoryStyle">
    <div class="ngHeaderContainer" style="height:{{headerRowHeight}}px;">
        <div class="categoryHeaderScroller" style="height:{{headerRowHeight}}px;position:absolute">  <!-- fixes scrollbar issue -->
            <div class="ngHeaderCell" ng-repeat="cat in categories"  style="left: {{cat.left}}px; width: {{cat.width}}px">
                <div class="ngVerticalBar" style="height:100%" ng-class="{ ngVerticalBarVisible: !$last }">&nbsp;</div>
                <div class="ngHeaderText" style="text-align:center">{{cat.displayName}}</div>
            </div>
        </div>
    </div>
</div>