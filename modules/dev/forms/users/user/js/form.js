$scope.updateRole = function () {

    if ($scope.userRoles && $scope.userRoles.value.length > 0) {
        var roles = [];
        for (i in $scope.userRoles.value) {
            var c = $scope.userRoles.value[i];
            if (c.role_id != null && roles.indexOf(c.role_id) < 0) {
                roles.push(c.role_id);
            } else {
                $scope.userRoles.value.splice(i, 1);
                $scope.userRoles.updateListView();
            }
        }

        for (i in $scope.userRoles.value) {
            var c = $scope.userRoles.value[i];
            c.is_default_role = 'No';
        }
        $scope.userRoles.value[0].is_default_role = 'Yes';
    } else {
        $scope.userRoles.value.push({
            id: "",
            role_id: "1",
            user_id: "",
            is_default_role: 'Yes'
        });
    }
}

switch ($scope.module) {
    case 'dev':
        if (!$scope.isNewRecord) {
            $scope.form.title = 'User Detail: ' + $scope.model.username;
        } else {
            $scope.form.title = 'New User';
        }
        break;
    default:
        $scope.form.title = "Edit Profile";
        break;
}

$timeout(function () {
    if (!$scope.isNewRecord) {
        $("[name='DevUserForm[changePassword]']").val('a');
        $("[name='DevUserForm[changePassword]']").val('');
    }
    $scope.updateRole();
}, 0);

if ($scope.params.u && $scope.params.f && $scope.isNewRecord) {
    $scope.model.username = $scope.params.u;
    $scope.model.fullname = $scope.params.f;
    $scope.model.useLdap = true;
}

$scope.backUrl = $scope.params.ldap ? 'ldap' : 'index';
