(function (angular, $, _) {

  angular.module('crmFreco').config(function ($routeProvider) {
    $routeProvider.when('/freco/computer', {
      controller: 'CrmFrecoComputeCtrl',
      controllerAs: '$ctrl',
      templateUrl: '~/crmFreco/ComputeCtrl.html',
      reloadOnSearch: false,

      // If you need to look up data when opening the page, list it out
      // under "resolve".
      resolve: {}
    });
  });

  angular.module('crmFreco').controller('CrmFrecoComputeCtrl', function ($scope, crmApi4, crmStatus, crmUiHelp, $route, $location) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('net.kompetenzz.freco');
    var hs = $scope.hs = crmUiHelp({ file: 'CRM/crmFreco/ComputeCtrl' }); // See: templates/CRM/crmFreco/ComputeCtrl.hlp
    var ctrl = this;

    $scope.apiParams = {
      'activityTypeId': $route.current.params.type,
      'activityStatusIds': $route.current.params.stati,
      'groupId': $route.current.params.group,
      'customGroupId': $route.current.params.custom_group,
      'customFieldIds': $route.current.params.fields
    };

    $scope.info = undefined;
    $scope.results = undefined;
    $scope.error = undefined;

    $scope.$watch('apiParams', function () {
      ctrl.syncUI();
    }, true);

    this.getData = function () {
      var params = $scope.apiParams;
      crmApi4('FreCo', 'info', params)
        .then(function(results) {
          $scope.info = results[0];
      });
      return crmApi4('FreCo', 'compute', params)
        .then(function(results) {
          $scope.results = results;
          $scope.groupedResults = {};
          if (!results.length)
            return;
          for (var i = 0; i < results.length; i++) {
            var item = results[i];
            if (!(item.field_id in $scope.groupedResults))
              $scope.groupedResults[item.field_id] = [];
            $scope.groupedResults[item.field_id].push(item);
          }
        }, function(failure) {
          $scope.error = failure;
      });
    };

    this.compute = function () {
      var dP = ctrl.getData();
      return dP;
    };

    this.submit = function () {
      var s = crmStatus(
        { start: ts('Computing...'), success: ts('Ready') },
        ctrl.compute()
      );
      return s;
    };

    this.prerequisitesMet = function() {
      if(!$scope.apiParams.customGroupId)
        return false;
      if ($scope.apiParams.activityTypeId)      
        return $scope.apiParams.activityStatusIds.length > 0;
      return true;
    };

    this.syncUI = function () {
      if (!!$scope.apiParams.activityTypeId)
       $location.search('type', $scope.apiParams.activityTypeId);
       if (!!$scope.apiParams.activityStatusIds)
        $location.search('stati', $scope.apiParams.activityStatusIds);
      if (!!$scope.apiParams.groupId)
        $location.search('group', $scope.apiParams.groupId);
      if (!!$scope.apiParams.customGroupId)
        $location.search('custom_group', $scope.apiParams.customGroupId);
      if (!!$scope.apiParams.customFieldIds)
        $location.search('fields', $scope.apiParams.customFieldIds);
      if (ctrl.prerequisitesMet())
        ctrl.submit();
    };
  });

})(angular, CRM.$, CRM._);
