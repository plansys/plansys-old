<?php

class NfyModule extends CWebModule {

    public $defaultController = 'queue';

    /**
     * @var string Name of user model class.
     */
    public $userClass = 'User';

    /**
     * @var string if not null a sound will be played along with displaying a notification
     */
    public $soundUrl;

    /**
     * @var integer how many milliseconds to wait for new messages on the server side;
     * zero or null disables long polling
     */
    public $longPolling = 1000;

    /**
     * @var integer how many times can messages be polled in a single action call
     */
    public $maxPollCount = 30;

    /**
     * @var array list of queue application components that will be displayed in the index action of the default controller.
     */
    public $queues = array();

    /**
     * @inheritdoc
     */
    public function getVersion() {
        return '0.9';
    }

    public function init() {
        // import the module-level controllers and forms
        $this->setImport(array(
            'application.modules.nfy.controllers.*',
            'application.modules.nfy.forms.*',
            'application.modules.nfy.components.*',
            'application.modules.nfy.models.*'
        )); 
    }

}