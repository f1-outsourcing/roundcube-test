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

    private $rc;
    private $ui_initialized = false;

    public function init()
    {

        $this->rc = rcube::get_instance();

        if ($this->rc->task == 'settings') {
            //adding the settings link
            $this->add_hook('settings_actions', array($this, 'settings_actions'));

            //display template page
            $this->register_action('plugin.test', array($this, 'init_html'));
            //$this->init_html();

            //save form values
            $this->register_action('plugin.test.save', array($this, 'settingsformsave'));
        }

    }


    public function init_html()
    {

        if (!empty($this->ui_initialized)) {
            return;
        }

        $this->ui_initialized = true;

        $rcmail = rcmail::get_instance();

        $rcmail->output->add_handler('test.form1', array($this, 'settingsform'));
        $rcmail->output->set_pagetitle($this->gettext('Test plugin'));
        $this->api->output->add_handler('form1', [$this, 'settingsform']);
        //$this->include_script('test.js');
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

    public function settingsformsave()
    {

        $enabled = rcube_utils::get_input_value('enabled', rcube_utils::INPUT_POST);
        $name = rcube_utils::get_input_value('name', rcube_utils::INPUT_POST);

        $config = $this->getConfig();

        if ($config['enabled'] != $enabled) {
            $config['enabled'] = $enabled;
        }

        if ($config['name'] != $name) {
            $config['name'] = $name;
        }

        $this->saveConfig($config);

        $this->init_html();
    }

    public function settingsform()
    {

        $rcmail = rcmail::get_instance();
        $config = $this->getConfig();

        $table = new html_table([ 'cols' => 2, 'class' => 'propform' ]);

        $field_id = 'enabled';
        $enabled = new html_checkbox([ 'name' => $field_id, 'id' => $field_id, 'type' => 'checkbox' ]);
        $table->add('title', html::label($field_id, rcube::Q($this->gettext($field_id))));
        $table->add(null, $enabled->show($config[$field_id] == true ? false : true));

        $field_id = 'name';
        $name = new html_inputfield(['name' => 'name', 'id' => 'name', 'size' => 50, 'class' => 'form-control']);
        $table->add('title', html::label($field_id, rcube::Q($this->gettext($field_id))));
        $table->add(null, $name->show(!empty($config[$field_id]) ? $config[$field_id] : null));

        //adding input button so we do not need javascript
        $inputbutton = new html_inputfield(['type' => 'submit', 'class' => 'btn btn-primary submit']);

        $form = $rcmail->output->form_tag([
          'id' => 'form1',
          'name' => 'form1',
          'method' => 'post',
          'action' => './?_task=settings&_action=plugin.test.save',
        ], $table->show() . $inputbutton->show('Save'));

        $formcontent = html::div([ 'class' => 'boxcontent formcontent' ], $form);

        $box = html::div([ 'class' => 'box formcontainer scroller' ], $formcontent);

        return $box;
    }

    private function saveConfig($config)
    {

        $rcmail = rcmail::get_instance();
        $prefs = $rcmail->user->get_prefs();
        $prefs['test'] = $config;
        $rcmail->user->save_prefs($prefs);

    }


    private function getConfig()
    {

        $rcmail = rcmail::get_instance();
        $prefs = $rcmail->user->get_prefs();
        $config = $prefs['test'] ?? [];

        return $config;

    }

}
