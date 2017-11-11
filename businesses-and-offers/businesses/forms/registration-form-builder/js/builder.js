

var app = angular.module("wyzBusinessCustomFormFields", ['ui.sortable']);


app.service('wyzBusinessCustomFormFieldsService', function () {
    var formJson = [];
    if(wyziBuilderData.form_data !== ''){
        var formJson = wyziBuilderData.form_data;
    }
    return {
        getField: function () {
            return formJson;
        },
        setField: function (value) {
            formJson = value;
        }
    };
});

var added;
app.controller('postbox_menu',['$scope', 'wyzBusinessCustomFormFieldsService', function ($scope, wyzBusinessCustomFormFieldsService) {
    $scope.postboxClass = "";
    var formJson = wyzBusinessCustomFormFieldsService.getField();
    $scope.addFormField = function (type, label, event) {
        if(type!='separator'&&added[type])
            return;
        added[type]=true;
        event.preventDefault();
        var jsonLength = formJson.length;
        if(type=='separator')
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                required: true,
                //active: false,
                partial: wyziBuilderData.partials  + 'separator.html',
                cssClass: ''
            });
        else if(type=='category')
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                required: true,
                separateCategories: false,
                parentSelectable: false,
                //active: false,
                partial: wyziBuilderData.partials  + 'category-element.html',
                cssClass: ''
            });
        else
            formJson.push({
                id: jsonLength,
                type: type,
                label: label,
                required: true,
                //active: false,
                partial: wyziBuilderData.partials  + 'form-element.html',
                cssClass: ''
            });

        wyzBusinessCustomFormFieldsService.setField(formJson);
    };
    $scope.togglePostbox = function () {
        if ($scope.postboxClass === "") {
            $scope.postboxClass = "closed";
        } else {
            $scope.postboxClass = "";
        }
    };
}]);
/*
 * angular controller for form fields
 */
app.controller('postbox_content',['$scope', '$http', 'wyzBusinessCustomFormFieldsService', function ($scope, $http, wyzBusinessCustomFormFieldsService) {
    var formJson = wyzBusinessCustomFormFieldsService.getField();
    added = { name: false ,logo: false ,logoBg: false ,desc: false ,about: false, slogan: false ,
              category: false ,categoryIcon: false, time: false, bldg: false, street: false, city: false,
              location: false,addAddress: false,map: false, phone1: false,phone2: false,email1: false,email2:false,
              website: false,fb: false,twitter: false,gplus: false,linkedin: false,youtube: false,insta: false,
              flicker: false,pinterest: false,comments: false,tags: false, custom:false };
    $scope.fields = formJson;
    $scope.showSaveSpinner = false;
    $scope.togglePostboxField = function (index) {
        if ($scope.fields[index].hidden) {
            $scope.fields[index].hidden = false;
        } else {
            $scope.fields[index].hidden = true;
        }
    };
    $scope.addToAdded = function(type){
        added[type]=true;
        return true;
    };
    $scope.removeFormField = function (type,index, event) {
        event.preventDefault();
        added[type]=false;
        formJson.splice(index, 1);
        wyzBusinessCustomFormFieldsService.setField(formJson);
    };
    $scope.fieldSortableOptions = {
        stop: function (e, ui) {

        }
    };
    $scope.listOnchange = function (index){
        angular.forEach($scope.fields,function(value,key){
            //console.log(key+" "+index);
            if(key != index){
                $scope.fields[key].active=false;
            }
        });
        $scope.fields[index].active=true;
    };
    

   $scope.saveFormData = function () {
        $scope.showSaveSpinner = true;
        var data = jQuery.param({
            action: 'wyzi_business_form_builder_save_form',
            form_data: JSON.stringify($scope.fields)
        });
        
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        $http.post(ajaxurl,data,config).success(function (data, status, headers, config){
            $scope.showSaveSpinner = false;
        }).error(function (data, status, header, config){
            //console.log(data);
            $scope.showSaveSpinner = false;
        });  
    };
}]);


