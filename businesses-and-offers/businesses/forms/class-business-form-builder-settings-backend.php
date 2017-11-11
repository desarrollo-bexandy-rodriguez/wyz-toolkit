<?php

class WyzSettingsBusinessForm_Builder {


    public function __construct() {
    
        $this->settings_page_init();
    }


  
    public function settings_page_init() {

        ?>
<h4><?php echo sprintf( esc_html__( 'You can manage the order of %s form fields that appear in %s registration page. Click on any Form Field to start', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT, WYZ_BUSINESS_CPT );?></h4>
        <form method="POST"><input type="submit" value="Reset" style="float: right;" name="form-builder-reset"/></form>
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
                                <a href="#" ng-click="addFormField('separator', 'New Tab', $event)" class="button-secondary"><?php esc_html_e('New Tab','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('name', 'Name', $event)" class="button-secondary"><?php echo sprintf( __( '%s Name', 'wyzi-business-finder' ),  WYZ_BUSINESS_CPT ); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('logo', 'Logo', $event)" class="button-secondary"><?php esc_html_e('Logo','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('logoBg', 'Logo Background', $event)" class="button-secondary"><?php esc_html_e('Logo Background','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('desc', 'Description', $event)" class="button-secondary"><?php esc_html_e('Description','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('about', 'About', $event)" class="button-secondary"><?php esc_html_e('About','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('slogan', 'Slogan', $event)" class="button-secondary"><?php esc_html_e('Slogan','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('category', 'Categories', $event)" class="button-secondary"><?php esc_html_e('Categories','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('categoryIcon', 'Category Icon', $event)" class="button-secondary"><?php esc_html_e('Category Icon','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('time', 'Open/Close Times', $event)" class="button-secondary"><?php esc_html_e('Open/Close Times','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('bldg', 'Building', $event)" class="button-secondary"><?php esc_html_e('Building','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('street', 'Street', $event)" class="button-secondary"><?php esc_html_e('Street','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('city', 'City', $event)" class="button-secondary"><?php esc_html_e('City','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('location', 'Location', $event)" class="button-secondary"><?php esc_html_e('Location','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('addAddress', 'Additional Address', $event)" class="button-secondary"><?php esc_html_e('Additional Address','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('map', 'Map', $event)" class="button-secondary"><?php esc_html_e('Map','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('phone1', 'Phone 1', $event)" class="button-secondary"><?php esc_html_e('Phone 1','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('phone2', 'Phone 2', $event)" class="button-secondary"><?php esc_html_e('Phone 2','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('email1', 'Email 1', $event)" class="button-secondary"><?php esc_html_e('Email 1','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('email2', 'Email 2', $event)" class="button-secondary"><?php esc_html_e('Email 2','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('website', 'Website', $event)" class="button-secondary"><?php esc_html_e('Website','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('fb', 'Facebook', $event)" class="button-secondary"><?php esc_html_e('Facebook','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('twitter', 'Twitter', $event)" class="button-secondary"><?php esc_html_e('Twitter','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('gplus', 'Google Plus', $event)" class="button-secondary"><?php esc_html_e('Google Plus','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('linkedin', 'Linkedin', $event)" class="button-secondary"><?php esc_html_e('Linkedin','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('youtube', 'Youtube', $event)" class="button-secondary"><?php esc_html_e('Youtube','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('insta', 'Instagram', $event)" class="button-secondary"><?php esc_html_e('Instagram','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('flicker', 'Flicker', $event)" class="button-secondary"><?php esc_html_e('Flicker','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('pinterest', 'Pinterest', $event)" class="button-secondary"><?php esc_html_e('Pinterest','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('comments', 'Post Comments', $event)" class="button-secondary"><?php esc_html_e('Post Comments','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('custom', 'Custom Fields', $event)" class="button-secondary"><?php esc_html_e('Custom Fields','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('tags', 'Tags', $event)" class="button-secondary"><?php esc_html_e('Tags','wyzi-business-finder'); ?></a>
                            </p>

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