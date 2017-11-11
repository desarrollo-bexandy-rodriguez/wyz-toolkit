

var app = angular.module("wyzi_claim_registration", ['ui.sortable']);

app.service('wyzi_claim_registration_service', function () {
    var formJson = [];
    if(wyzi_registration_parameters.form_data !== ''){
        var formJson = wyzi_registration_parameters.form_data;
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

app.controller('postbox_menu',['$scope', 'wyzi_claim_registration_service', function ($scope, wyzi_claim_registration_service) {
    $scope.postboxClass = "";
    var formJson = wyzi_claim_registration_service.getField();
    $scope.addFormField = function (type, label, event) {
        event.preventDefault();
        var jsonLength = formJson.length;
        switch (type) {
            case 'selectbox':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    selecttype: 'radio',
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    required: false,
                    options: [
                        {
                            value: 'option1',
                            label: 'Option 1',
                            selected: false
                        },
                        {
                            value: 'option2',
                            label: 'Option 2',
                            selected: true
                        },
                    ],
                    cssClass: ''
                });
                break;
            case 'email':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    placeholder: '',
                    required: false,
//                    emailValidation: false,
                    cssClass: ''
                });
                break;
            case 'textarea':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    defaultValue: '',
                    limit : '',
                    required: false,
                    cssClass: ''
                });
                break;
            case 'checkbox':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    defaultValue: 'unchecked',
                    required: false,
                    cssClass: ''
                });
                break;
            case 'recaptcha':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    script: '',
                    required: false
                });
                break;
            case 'file':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    fileSize: '',
                    fileType: [
                        {
                            value : 'application/pdf',
                            label : 'PDF',
                            selected : false
                        },
                        {
                            value : 'image/jpeg',
                            label : 'JPEG',
                            selected : false
                        },
                        {
                            value : 'image/png',
                            label : 'PNG',
                            selected : false
                        },
                        {
                            value : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            label : 'DOC',
                            selected : false
                        },
                        {
                            value : 'application/vnd.ms-excel',
                            label : 'xls',
                            selected : false
                        }
                    ],
                    required: false,
                    muliple: false,
                    cssClass: ''
                });
                break;
            case 'separator':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    cssClass: ''
                });
                break;
            default :
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    placeholder: '',
                    required: false,
                    cssClass: ''
                });
                break;
        }

        wyzi_claim_registration_service.setField(formJson);
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
app.controller('postbox_content',['$scope', '$http', 'wyzi_claim_registration_service', function ($scope, $http, wyzi_claim_registration_service) {
    var formJson = wyzi_claim_registration_service.getField();
    $scope.fields = formJson;
    $scope.showSaveSpinner = false;
    $scope.togglePostboxField = function (index) {
        if ($scope.fields[index].hidden) {
            $scope.fields[index].hidden = false;
        } else {
            $scope.fields[index].hidden = true;
        }
    };
    $scope.removeFormField = function (index, event) {
        event.preventDefault();
        formJson.splice(index, 1);
        wyzi_claim_registration_service.setField(formJson);
    };
    $scope.addSelectBoxOption = function (index, event) {
        event.preventDefault();
        var count = $scope.fields[index].options.length + 1;
        $scope.fields[index].options.push({value: 'option' + count, label: 'Option ' + count, selected: false});
    };
    $scope.removeSelectboxOption = function (index, key, event) {
        event.preventDefault();
        $scope.fields[index].options.splice(key, 1);
    };
    $scope.fieldSortableOptions = {
        stop: function (e, ui) {

        }
    };
    $scope.listOnchange = function (parentIndex,index){
        console.log($scope.fields[parentIndex].options);
        angular.forEach($scope.fields[parentIndex].options,function(value,key){
            if(key !== index){
                $scope.fields[parentIndex].options[key].selected = false;
            }
        });
    };
    

   $scope.saveFormData = function () {
        $scope.showSaveSpinner = true;
        
        var data = jQuery.param({
            action: 'wyzi_claim_save_form',
            form_data: JSON.stringify($scope.fields)
        });
        
        //console.log(data);
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        $http.post(ajaxurl,data,config).success(function (data, status, headers, config){
            $scope.showSaveSpinner = false;
        }).error(function (data, status, header, config){
            console.log(data);
            $scope.showSaveSpinner = false;
        });  
    };
}]);


