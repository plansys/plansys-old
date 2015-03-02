$scope.changeEnableAudit = function(){
    if(!!$scope.model.auditEnable){
        $scope.model.auditTrack = "delete,login,save,view";
    }else{
        $scope.model.auditTrack = null;
    }
    
}