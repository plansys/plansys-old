$scope.changeEnableAudit = function(){
    console.log('abc');
    if(!!$scope.model.auditEnable){
        $scope.model.auditTrack = "delete,login,save,view";
    }else{
        $scope.model.auditTrack = null;
    }
}
