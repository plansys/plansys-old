<span ng-if="field.renderInEditor == 'Yes'" dynamic="field.value"></span>
<span ng-if="!field.renderInEditor || field.renderInEditor == 'No'" 
      style="white-space:pre-wrap;font-family:'Courier', Monaco, monospace;
      color:#999;font-size:12px;word-wrap: break-word;">{{ field.value }}</span>