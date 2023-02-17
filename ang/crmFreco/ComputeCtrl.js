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
      'customGroupId': $route.current.params.group,
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
      return !!$scope.apiParams.activityTypeId 
        && $scope.apiParams.activityStatusIds.length
        && !!$scope.apiParams.customGroupId;
    };

    this.syncUI = function () {
      $location.search('type', $scope.apiParams.activityTypeId);
      $location.search('stati', $scope.apiParams.activityStatusIds);
      $location.search('group', $scope.apiParams.customGroupId);
      $location.search('fields', $scope.apiParams.customFieldIds);
      if (ctrl.prerequisitesMet())
        ctrl.submit();
    };
  });

})(angular, CRM.$, CRM._);
