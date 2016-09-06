<div ng-controller="Col2">
    <pre>{{ col2 | json }}</pre>
    {{ col2.view.loading}}

    <div ng-include
         src="col2.view.url"
         onload="builder.colActivated(2)"></div>
</div>