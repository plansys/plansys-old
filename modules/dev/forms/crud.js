
$scope.progress = 0;
$scope.status = "Ready";
$scope.result = "- CRUD Generator Ready - ";
$scope.error = false;
$scope.generate = function () {
    $scope.formSubmitting = false;
    var needConfirm = $scope.progress == 0 ? confirm('Are You sure? your files will be overwritten') : true;
    if (needConfirm) {
        $scope.formSubmitting = true;
        //step1: Create Model File
        $scope.progress = 5;
        $scope.status = "Creating Model File...";
        $http.get(Yii.app.createUrl('/dev/crudGenerator/createModelFile', $scope.model))
                .success(function (data) {
                    $scope.progress = 10;
                    $scope.result = data + " Created\n\
Generating Model...";

                    $scope.formSubmitting = false;
                    generateModel();
                })
                .error(function (data) {
                    $scope.error = true;
                    $scope.result = data;
                    $scope.formSubmitting = false;
                });

        //step2: Generate Model
        function generateModel() {
            $scope.formSubmitting = true;
            $scope.status = "Generating Model...";
            $http.get(Yii.app.createUrl('/dev/modelGenerator/update', {
                class: "app.models." + $scope.model.model,
                type: "app"
            })).success(function (data) {
                $scope.progress = 20;
                $scope.result = "Model Successfully Generated\n\
Creating Form File ...";

                $scope.formSubmitting = false;
                createFormFile();
            }).error(function (data) {
                $scope.error = true;
                $scope.result = data;
                $scope.formSubmitting = false;
            });
        }


        //step2: Create Form File
        function createFormFile() {
            $scope.formSubmitting = true;
            $scope.status = "Creating Form File...";
            $http.get(Yii.app.createUrl('/dev/crudGenerator/createFormFile', $scope.model))
                    .success(function (data) {
                        $scope.progress = 25;
                        $scope.result = "Form Successfully created\n\
Generating Form ...";

                        $scope.formSubmitting = false;
                        generateForm(0, data);
                    })
                    .error(function (data) {
                        $scope.error = true;
                        $scope.result = data;
                        $scope.formSubmitting = false;
                    });
        }

        //step3: Generate Form
        function generateForm(index, data) {
            $scope.formSubmitting = true;
            console.log(Yii.app.createUrl('/dev/forms/update', {
                class: data[index]
            }));
            $scope.status = "Generating Form " + (index + 1) + " ...";
            $http.get(Yii.app.createUrl('/dev/forms/update', {
                class: data[index]
            })).success(function (res) {
                $scope.progress += 10;
                if (index < data.length - 1) {
                    generateForm(index + 1, data);
                } else {
                    $scope.progress = 80;
                    $scope.result = "Form Successfully Generated\n\
Creating Controller ...";
                    $scope.formSubmitting = false;
                    generateController();
                }
            }).error(function (res) {
                $scope.error = true;
                $scope.result = res;
                $scope.formSubmitting = false;
            });
        }

        //step4: Generate Controllers
        function generateController() {
            $scope.formSubmitting = true;
            $scope.status = "Generating Controller...";
            $http.get(Yii.app.createUrl('/dev/crudGenerator/generateController', $scope.model))
                    .success(function (data) {
                        $scope.progress = 100;
                        $scope.result = data;

                        $scope.formSubmitting = false;
                    })
                    .error(function (data) {
                        $scope.error = true;
                        $scope.result = data;
                        $scope.formSubmitting = false;
                    });
        }
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