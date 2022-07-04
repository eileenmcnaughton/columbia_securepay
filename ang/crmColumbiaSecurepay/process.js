(function(angular, $, _) {

  // The controller uses *injection*. This default injects a few things:
  //   $scope -- This is the set of variables shared between JS and HTML.
  //   crmApi, crmStatus, crmUiHelp -- These are services provided by civicrm-core.
  //   myContact -- The current contact, defined above in config().
  angular.module('crmColumbiaSecurepay').controller('CrmColumbiaSecurepayctrl',  function($scope, dialogService, crmApi4) {
    var ts = $scope.ts = CRM.ts('columbia_securepay'),
      model = $scope.model,
      ctrl = this;

    this.entityTitle = model.ids.length === 1 ? model.entityInfo.title : model.entityInfo.title_plural;

    this.cancel = function() {
      dialogService.cancel('crmSearchTask');
    };

    this.process = function() {
      $('.ui-dialog-titlebar button').hide();
      ctrl.run = {
        select: ['id'],
        chain: {process: ['Securepay', 'process', {id : '$id'}]}
      };
    };

    this.onSuccess = function() {
      CRM.alert(ts('Successfully updated %1 %2.', {1: model.ids.length, 2: ctrl.entityTitle}), ts('Notifications processed'), 'success');
      dialogService.close('crmSearchTask');
    };

    this.onError = function() {
      CRM.alert(ts('An error occurred while attempting to update %1 %2.', {1: model.ids.length, 2: ctrl.entityTitle}), ts('Error'), 'error');
      dialogService.close('crmSearchTask');
    };

  });

})(angular, CRM.$, CRM._);
