<?php

/**
 * Fancy Emoticons
 *
 * Sample plugin to replace emoticons in plain text message body with real icons
 *
 * @version 1.0
 * @author Thomas Bruederli
 * @url http://roundcube.net/plugins/fancy_emoticons
 */
class test extends rcube_plugin
{
    public $task = 'settings';

    public function init()
    {


        //adding the settings link
        $this->add_hook('settings_actions', array($this, 'settings_actions'));

        //display template page
        $this->register_action('test', array($this, 'start'));


    }


    public function start()
    {
        $rcmail = rcmail::get_instance();

        $rcmail->output->set_pagetitle($this->gettext('Test plugin'));
        $rcmail->output->send('test.settingspage');
    }

    public function settings_actions($args)
    {
        $args['actions'][] = [
          'action' => 'plugin.test',
          'type' => 'link',
          'label' => 'test.config',
          'title' => 'test.config'
        ];
        return $args;
    }



}
