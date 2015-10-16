$scope.procManPopUp = function(){	
	/*window.activeItem = item;*/
	PopupCenter(Yii.app.createUrl('/dev/processManager/createCommand'),"Create New Command","400","500");	
}