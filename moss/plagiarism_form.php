<?php

require_once($CFG->dirroot.'/lib/formslib.php');

class plagiarism_setup_form extends moodleform {

/// Define the form
    function definition () {
        global $CFG;

        $mform =& $this->_form;
        $choices = array('No','Yes');
        $mform->addElement('html', get_string('mossexplain', 'plagiarism_moss'));
        $mform->addElement('checkbox', 'moss_use', get_string('usemoss', 'plagiarism_moss'));

  

        $this->add_action_buttons(true);
    }
}
