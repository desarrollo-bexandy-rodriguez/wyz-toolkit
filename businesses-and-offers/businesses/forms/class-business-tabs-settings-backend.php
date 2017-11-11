<?php

class WyzSettingsBusinessTabsForm_Builder {


    public function __construct() {
    
        $this->settings_page_init();
    }


  
    public function settings_page_init() {

        ?>
<h4><?php echo sprintf( esc_html__( 'You can manage the order of %s tabs that appear in single %s page. Click on any Form Field to start', 'wyzi-business-finder' ), WYZ_BUSINESS_CPT, WYZ_BUSINESS_CPT );?></h4>
        <form method="POST"><input type="submit" value="Reset" style="float: right;" name="business-tabs-reset"/></form>
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
                                <a href="#" ng-click="addFormField('wall', 'Wall', $event, '')" class="button-secondary"><?php esc_html_e('Wall','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('photo', 'Photo', $event, '')" class="button-secondary"><?php esc_html_e('Photo','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('about', 'About', $event, '')" class="button-secondary"><?php esc_html_e('About','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('offers', 'Offers', $event, '')" class="button-secondary"><?php esc_html_e('Offers','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('message', 'Message', $event, '')" class="button-secondary"><?php esc_html_e('Message','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('products', 'Products', $event, '')" class="button-secondary"><?php esc_html_e('Products','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('bookings', 'Bookings', $event, '')" class="button-secondary"><?php esc_html_e('Bookings','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('jobs', 'Jobs', $event, '')" class="button-secondary"><?php esc_html_e('Jobs','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('ratings', 'Ratings', $event, '')" class="button-secondary"><?php esc_html_e('Ratings','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('customs', 'Extra Fields', $event, '')" class="button-secondary"><?php esc_html_e('Extra Fields','wyzi-business-finder'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('additionalContent', 'Additional Content', $event, '')" class="button-secondary"><?php esc_html_e('Additional Content','wyzi-business-finder'); ?></a>
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