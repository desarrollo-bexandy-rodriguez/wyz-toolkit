<?php

class WyzSettingsBusinessCustomFieldsForm_Builder {


    public function __construct() {
    
        $this->settings_page_init();
    }


  
    public function settings_page_init() {

        ?>
<h4><?php echo sprintf( esc_html__( 'You can manage %s custom Form Fields that appear on %s registration page. Click on any Form Field to start', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT, WYZ_BUSINESS_CPT );?></h4>
        <form method="POST"><input type="submit" value="Reset" style="float: right;" name="business-custom-fields-reset"/></form>
        <div id="nav-menus-frame" ng-app="wyzBusinessCustomFormFields">
            <div id="menu-settings-column" class="metabox-holder" ng-controller="postbox_menu">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="postboxClass">
                        <button ng-click="togglePostbox()" aria-expanded="false" class="handlediv button-link" type="button"><span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Format', 'wyzi-business-finder' );?></span><span aria-hidden="true" class="toggle-indicator"></span></button>
                        <h3 class="hndl ui-sortable-handle">
                            <span><?php esc_html_e( 'Form Fields', 'wyzi-business-finder' );?></span>
                        </h3>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textbox', 'Text Box', $event)" class="button-secondary"><?php esc_html_e('Textbox','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('email', 'Email', $event)" class="button-secondary"><?php esc_html_e('Email','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('url', 'Url', $event)" class="button-secondary"><?php esc_html_e('Url','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textarea', 'Text Area', $event)" class="button-secondary"><?php esc_html_e('Textarea','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('wysiwyg', 'WYSIWYG', $event)" class="button-secondary"><?php echo __('WYSIWYG','wyzi-business-finder'); ?></a>
                            </p> 


                           
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('selectbox', 'Select Box', $event)" class="button-secondary"><?php echo __('List','wyzi-business-finder'); ?></a>
                            </p><!--
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('checkbox', 'Checkbox', $event)" class="button-secondary"><?php //echo __('Check Box','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('recaptcha', 'Recaptcha', $event)" class="button-secondary"><?php //echo __('Recaptcha','wyzi-business-finder'); ?></a>
                            </p>   --> 
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('file', 'Attachment', $event)" class="button-secondary"><?php echo __('Attachment','wyzi-business-finder'); ?></a>
                            </p> 
                            <!--
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('separator', 'Section', $event)" class="button-secondary"><?php //echo __('Section','wyzi-business-finder'); ?></a>-->


                        </div>
                    </div>
                </div>
            </div>
            <div id="poststuff" ng-controller="postbox_content">
                <div id="post-body">
                    <div id="post-body-content">
                        <div id="wyzi-claim-form">
                            
                            <ul class="meta-box-sortables" ui-sortable="fieldSortableOptions" ng-model="fields">
                                <li ng-repeat="(parentIndex,field) in fields track by $index">
                                    <div class="postbox" ng-class="{'closed' : field.hidden }">
                                        <button aria-expanded="false" ng-click="togglePostboxField($index)" class="handlediv button-link" type="button"><span class="screen-reader-text">Toggle panel: Format</span><span aria-hidden="true" class="toggle-indicator"></span></button>
                                        <h2 class="hndle ui-sortable-handle" ng-dblclick="togglePostboxField($index)"><span>{{field.label}}</span></h2>
                                        <div class="inside">
                                            <div id="post-formats-select">
                                                <div ng-include src="field.partial"></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}