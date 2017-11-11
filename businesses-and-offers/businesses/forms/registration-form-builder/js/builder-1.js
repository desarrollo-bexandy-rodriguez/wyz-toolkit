

var builderApp = angular.module("wyzBusinessFormFieldsBulder", ['ui.sortable']);


builderApp.service('wyzBusinessFormFieldsBulderService', function () {
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

var bAdded;
builderApp.controller('builder_postbox_menu',['$bscope', 'wyzBusinessFormFieldsBulderService', function ($bscope, wyzBusinessFormFieldsBulderService) {
    $bscope.postboxClass = "";
    var formJson = wyzBusinessFormFieldsBulderService.getField();
    $bscope.addBuilderFormField = function (type, label, event) {
        if(bAdded[type])
            return;
        bAdded[type]=true;
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

        wyzBusinessFormFieldsBulderService.setField(formJson);
    };
    $bscope.toggleBuilderPostbox = function () {
        if ($bscope.postboxClass === "") {
            $bscope.postboxClass = "closed";
        } else {
            $bscope.postboxClass = "";
        }
    };
}]);



builderApp.controller('builder_postbox_content',['$bscope', '$http', 'wyzBusinessFormFieldsBulderService', function ($bscope, $http, wyzBusinessFormFieldsBulderService) {
    var formJson = wyzBusinessFormFieldsBulderService.getField();
    bAdded = { name: false ,logo: false ,logoBg: false ,desc: false ,about: false, slogan: false ,
              category: false ,categoryIcon: false, time: false, bldg: false, street: false, city: false,
              location: false,addAddress: false,map: false, phone1: false,phone2: false,email1: false,email2:false,
              website: false,fb: false,twitter: false,gplus: false,linkedin: false,youtube: false,insta: false,
              flicker: false,pinterest: false,comments: false,tags: false };
    $bscope.builderFields = formJson;
    $bscope.showBuilderSaveSpinner = false;
    $bscope.toggleBuilderPostboxField = function (bindex) {
        if ($bscope.builderFields[bindex].hidden) {
            $bscope.builderFields[bindex].hidden = false;
        } else {
            $bscope.builderFields[bindex].hidden = true;
        }
    };
    $bscope.addToAdded = function(type){
        bAdded[type]=true;
        return true;
    };
    $bscope.removeFormField = function (type,bindex, event) {
        event.preventDefault();
        bAdded[type]=false;
        formJson.splice(bindex, 1);
        wyzBusinessFormFieldsBulderService.setField(formJson);
    };
    $bscope.fieldBuilderSortableOptions = {
        stop: function (e, ui) {

        }
    };
    $bscope.listOnchange = function (bindex){
        angular.forEach($bscope.builderFields,function(value,key){
            //console.log(key+" "+bindex);
            if(key != bindex){
                $bscope.builderFields[key].active=false;
            }
        });
        $bscope.builderFields[bindex].active=true;
    };
    

   $bscope.saveBuilderFormData = function () {
        $bscope.showBuilderSaveSpinner = true;
        var data = jQuery.param({
            action: 'wyzi_business_tabs_save_form',
            form_data: JSON.stringify($bscope.builderFields)
        });
        
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        $http.post(ajaxurl,data,config).success(function (data, status, headers, config){
            $bscope.showBuilderSaveSpinner = false;
        }).error(function (data, status, header, config){
            //console.log(data);
            $bscope.showBuilderSaveSpinner = false;
        });  
    };
}]);


