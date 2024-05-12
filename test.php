<?php

/**
 * Test
 *
 * Sample plugin
 *
 * @version 1.0
 * @author
 * @url
 */
class test extends rcube_plugin
{
    public $task = 'settings';

    public function init()
    {

        //adding the settings link
        $this->add_hook('settings_actions', array($this, 'settings_actions'));

        //display template page
        $this->register_action('test', array($this, 'init_html'));


    }


    public function init_html()
    {
        $rcmail = rcmail::get_instance();

        $rcmail->output->add_handler('test.form1', array($this, 'settingsform'));
        $rcmail->output->set_pagetitle($this->gettext('Test plugin'));
        $this->api->output->add_handler('form1', [$this, 'settingsform']);
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

    public function settingsform()
    {
        $rcmail = rcmail::get_instance();
        $config = $this->getConfig();

        $name = new html_inputfield(['name' => 'name', 'id' => 'name', 'size' => 50, 'class' => 'form-control']);

        $table = new html_table([ 'cols' => 2, 'class' => 'propform' ]);

        $field_id = 'enabled';
        $checkbox_activate = new html_checkbox([ 'name' => $field_id, 'id' => $field_id, 'type' => 'checkbox' ]);
        $table->add('title', html::label($field_id, rcube::Q($this->gettext($field_id))));
        $table->add(null, $checkbox_activate->show($config[$field_id] == true ? false : true));

        $field_id = 'name';
        $table->add('title', html::label($field_id, rcube::Q($this->gettext($field_id))));
        $table->add(null, $name->show(!empty($config[$field_id]) ? $config[$field] : null));

        $rcmail->output->add_gui_object('webauthnform', 'twofactor_webauthn-form');
        $form = $rcmail->output->form_tag([
          'id' => 'form1',
          'name' => 'form1',
          'method' => 'post',
          'action' => './?_task=settings&_action=plugin.test_save',
        ], $table->show());

        return $form;
    }

    private function getConfig()
    {
        $rcmail = rcmail::get_instance();
        $prefs = $rcmail->user->get_prefs();
        $config = $prefs['test'] ?? [];
        if (!isset($config['enabled'])) {
            $config['enabled'] = false;
        }
        return $config;
    }

}
