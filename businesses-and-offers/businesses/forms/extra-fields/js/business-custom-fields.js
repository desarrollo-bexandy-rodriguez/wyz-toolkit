

var app = angular.module("wyzBusinessCustomFormFields", ['ui.sortable']);

app.service('wyzBusinessCustomFormFieldsService', function () {
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

app.controller('postbox_menu',['$scope', 'wyzBusinessCustomFormFieldsService', function ($scope, wyzBusinessCustomFormFieldsService) {
    $scope.postboxClass = "";
    var formJson = wyzBusinessCustomFormFieldsService.getField();
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
                    visible: true,
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
                    visible: true,
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
                    visible: true,
                    cssClass: ''
                });
                break;
            case 'wysiwyg':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    required: false,
                    visible: true,
                    mediaupload: true,
                    cssClass: ''
                });
                break;
            /*case 'checkbox':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    defaultValue: 'unchecked',
                    required: false,
                    visible: true,
                    cssClass: ''
                });
                break;*/
            /*case 'recaptcha':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    script: '',
                    required: false
                });
                break;*/
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
                            value : ['application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/msword'],
                            label : 'DOC',
                            selected : false
                        }, 
                        {
                            value : 'application/vnd.ms-excel',
                            label : 'xls',
                            selected : false
                        },
                        {
                            value : 'application/zip',
                            label : 'zip',
                            selected : false
                        }
                    ],
                    required: false,
                    muliple: false,
                    cssClass: ''
                });
                break;
           /* case 'separator':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    cssClass: ''
                });
                break;*/
            default :
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: wyzi_registration_parameters.partials + type + '.html',
                    placeholder: '',
                    required: false,
                    visible: true,
                    cssClass: ''
                });
                break;
        }

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
        wyzBusinessCustomFormFieldsService.setField(formJson);
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
        //console.log($scope.fields[parentIndex].options);
        angular.forEach($scope.fields[parentIndex].options,function(value,key){
            if(key !== index){
                $scope.fields[parentIndex].options[key].selected = false;
            }
        });
    };
    

   $scope.saveFormData = function () {
        $scope.showSaveSpinner = true;
        var data = jQuery.param({
            action: 'wyzi_business_custom_fields_save_form',
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


