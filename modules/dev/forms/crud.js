
$scope.progress = 0;
$scope.status = "Ready";
$scope.result = ""
$scope.generate = function () {
    var needConfirm = $scope.progress == 0 ? confirm('Are You sure? your files will be overwritten') : true;
    if (needConfirm) {
        //step1: Create Model File
        $scope.progress = 5;
        $scope.status = "Creating Model File...";
        $http.get(Yii.app.createUrl('/dev/crudGenerator/createModelFile', $scope.model))
                .success(function (data) {
                    $scope.progress = 10;
                    $scope.result = data + " Created\n\
Generating Model...";

                    generateModel();
                });

        //step2: Generate Model
        function generateModel() {
            $scope.status = "Generating Model...";
            $http.get(Yii.app.createUrl('/dev/modelGenerator/update', {
                class: "app.models." + $scope.model.model,
                type: "app"
            })).success(function (data) {
                $scope.progress = 20;
                $scope.result = "Model Successfully Generated\n\
Creating Form File ...";

                createFormFile();
            });
        }


        //step2: Create Form File
        function createFormFile() {
            $scope.status = "Generating Form...";
            $http.get(Yii.app.createUrl('/dev/crudGenerator/createFormFile', $scope.model))
                    .success(function (data) {
                        $scope.progress = 25;
                        $scope.result = data;

                        generateForm();
                    });
        }

        //step3: Generate Controllers
    }
}


$("form").submit(function (e) {
    e.preventDefault();
    $scope.generate();
});

$scope.snakeToCamel = function (s) {
    var a = s.replace(/(\_\w)/g, function (m) {
        return m[1].toUpperCase();
    });

    a = a[0].toUpperCase() + a.substr(1);
    console.log(a);
    return a;
}