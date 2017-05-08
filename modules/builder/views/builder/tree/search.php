      <style>
          .search-file {
               flex: 0 0 27px;
               border-bottom:1px solid #ccc;
               background:white;
               position:relative;
          }
          .search-file .icon {
               position:absolute;
               line-height:24px;
               right:0px;
               padding:0px 20px 3px 8px;
               z-index:1;
               cursor:pointer;
               text-align:center;
          }
          .search-file .icon i {
               font-size:13px;
               color:#bbb;
               font-weight:normal;
          }
          .search-file .arrow {
               position:absolute;
               right:0px;
               width:15px;
               font-size:10px;
               line-height:26px;
               height:26px;
               text-align:center;
               color:#ccc;
               cursor:pointer;
               z-index:2;
               font-size:8px;
               border-left:1px dotted #ececeb;
          }
          .search-file .arrow:hover {
               background:#fafafa;
          }
          .search-file .arrow.active {
               background:#fafafa;
               border-left:1px solid #ccc;
               height:27px;
               border-bottom:1px dotted #ccc;
          }
          .search-file .search-file-detail {
               position:absolute;
               z-index:1;
               margin-top:26px;
               left:0px;
               right:0px;
               padding:10px 5px 0px 5px;
               background:#fafafa;
               border-top:1px solid #ccc;
               border-bottom:1px solid #ccc;
          }
          .search-file .search-file-detail-head {
               font-weight:bold;
               font-size:11px;
               text-align:center;
               margin-bottom:5px;
               margin-top:-5px;
          }
          
          .search-file .search-file-detail-row {
               display:flex;
               align-items: center;
               justify-content:space-between;
               font-size:11px;
               border-top:1px solid #ddd;
               margin:0px -5px;
               padding:5px;
          }
          
          
          .search-file .search-file-detail-row .btn {
               font-size:10px;
               font-weight:bold;
          }
          .search-file input.search-text {
               border:0px;
               outline:0px;
               padding:0px 45px 0px 7px;
               font-size:14px;
               width:100%;
               line-height:26px;
               border-radius:0px;
               position:absolute;
               right:30px;
               left:0px;
               bottom:0px;
               top:0px;
               background:transparent;
          }
          
          .search-file input:focus {
               background:#fafafa;
          }

      </style>
      
      <div class="search-file">
          <input type="search" class="search-text" ng-delay="300"
                 ng-change="doSearch()" placeholder="Files" ng-model="search.text">
          <div class="icon" ng-click="resetSearch()">
               <i ng-if="!search.loading && !search.text" class="fa fa-search"></i>
               <b ng-if="!search.loading && !!search.text">&times;</b>
               <i ng-if="search.loading" class="fa fa-refresh fa-spin"></i>
          </div>
          <div class="arrow" ng-class="{active: search.detail.show}"
               ng-click="search.detail.show = !search.detail.show">
               <i class="fa fa-chevron-down" ng-if="!search.detail.show"></i>
               <i class="fa fa-chevron-up" ng-if="search.detail.show"></i>
          </div>
          <div class="search-file-detail" ng-if="search.detail.show">
               <div class="search-file-detail-head">TREE OPTIONS</div>
               <div class="search-file-detail-row">
                    Search&nbsp;Path
                    <input type="text" style="margin-left:5px"
                           ng-keydown="detailPathChanged($event)" ng-model="search.detail.path">
               </div>
               <div class="search-file-detail-row"> 
                    <div ng-click="search.detail.show = false; doSearch()" 
                         class="btn btn-xs btn-default btn-block">
                         OK
                    </div>
                </div>
          </div>
     </div>