<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * lib.php - Contains Plagiarism plugin specific functions called by Modules.
 *
 * @since 2.0
 * @package    plagiarism_moss
 * @subpackage plagiarism
 * @copyright  2010 Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//get global class
global $CFG;
require_once($CFG->dirroot.'/plagiarism/moss/utils.php');
require_once($CFG->dirroot.'/plagiarism/lib.php');
require_once(__DIR__.'/scan_assignment.php');
require_once(__DIR__.'/moss.php');





///// Turnitin Class ////////////////////////////////////////////////////
class plagiarism_plugin_moss extends plagiarism_plugin {
  private $filemanageroption;

  public function __construct() {
      $this->filemanageroption = array('subdir' => 0, 'maxbytes' => 20 * 1024 * 1024, 'maxfiles' => 50,
          'accepted_type' => array('*'));
  }




     /**
     * hook to allow plagiarism specific information to be displayed beside a submission
     * @param array  $linkarraycontains all relevant information for the plugin to generate a link
     * @return string
     *
     */
    public function get_links($linkarray) {
        //$userid, $file, $cmid, $course, $module
        $cmid = $linkarray['cmid'];
        $userid = $linkarray['userid'];
        $file = $linkarray['file'];

        //add link/information about this file to $output

        //return $output;
    }

    /* hook to save plagiarism specific settings on a module settings page
     * @param object $data - data from an mform submission.
    */
    public function save_form_elements($data) {
      global $DB;

      $cmid = $data->coursemodule;
      $context = context_module::instance($cmid);
      if (!$this->is_plugin_enabled($cmid)) {
          return;
      }

      if (!empty($data->usemoss)) { // The plugin is enabled for this assignment.
          $setting = $DB->get_record('plagiarism_moss', array('cmid' => $cmid));
          $new = false;
          if (!$setting) {
              $new = true;
              $setting = new stdClass();
              $setting->cmid = $cmid;
          }

          $setting->language = $data->language;
          $setting->notification_text = $data->notification_text;
          $setting->duedate= $data->senddate;

          if ($new) {
              $setting->id = $DB->insert_record('plagiarism_moss', $setting);
          } else {
              $DB->update_record('plagiarism_moss', $setting);
          }
          file_postupdate_standard_filemanager($data, 'code', $this->filemanageroption,
              $context, 'plagiarism_moss', 'codeseeding', $setting->id);
            }
    }


    /**
     * hook to add plagiarism specific settings to a module settings page
     * @param object $mform  - Moodle form
     * @param object $context - current context
     */
    public function get_form_elements_module($mform, $context,$modulename = '') {
      global $DB;

      $cmid = optional_param('update', 0, PARAM_INT);
      // When creating an assignment, cmid does not exist, but course id is provided via "course" param.
      $courseid = optional_param('course', 0, PARAM_INT);

      if (!$this->is_plugin_enabled($cmid, $courseid)) {
          return;
      }

      $plagiarismconfig = null;
      $assignmentcontext = null;

      if ($cmid) {
          $plagiarismconfig = $DB->get_record('plagiarism_moss', array('cmid' => $cmid));
          $assignmentcontext = context_module::instance($cmid);
      }

     $mform->addElement('header', 'mossdesc', get_string('moss', 'plagiarism_moss'));



     $enablechecking = array();
     $enablechecking[] = &$mform->createElement('radio', 'usemoss', '', get_string('disable'), 0);
     $enablechecking[] = &$mform->createElement('radio', 'usemoss', '', get_string('enable'), 1);
     $mform->addGroup($enablechecking, 'usemoss',
         get_string('usemoss', 'plagiarism_moss'), array(' '), false);



     $mform->addElement('date_time_selector','senddate',get_string('duedate', 'plagiarism_moss'),
         null,array('optional' => true, 'step' => 5));
     $mform->disabledIf('senddate', 'usemoss', 'eq', 0);



     $langs = array(
          'cc'     => 'C++',
          'c'      => 'C',
          'csharp' =>'C#',
          'java'   => 'Java',
          'javascript'      => 'Javascript',
          'matlab' => 'Matlab',
          'python' => 'Python');
     $mform->addElement('select','language' ,get_string('language', 'plagiarism_moss'), $langs);
     $mform->disabledIf('language', 'usemoss', 'eq', 0);

     $mform->addElement('textarea', 'notification_text', get_string('studentdisclosure', 'plagiarism_moss'),
         'wrap="virtual" rows="4" cols="50"');
     $mform->disabledIf('notification_text', 'usemoss', 'eq', 0);
     $mform->addHelpButton('notification_text','studentdisclosure', 'plagiarism_moss');

     $this->setup_code_seeding_filemanager($mform, $plagiarismconfig, $assignmentcontext);

     if ($plagiarismconfig) { // Update mode, populate the form with current values.
         $mform->setDefault('usemoss', 1);
         $mform->setDefault('language', $plagiarismconfig->language);
         $mform->setDefault('senddate', $plagiarismconfig->duedate);
         $mform->setDefault('notification_text', $plagiarismconfig->notification_text);
     }
     if (empty($plagiarismconfig->notification_text)) {
         $mform->setDefault('notification_text',  get_string('studentdisclosuredefault', 'plagiarism_moss'));
     }


    }

    /**
     * hook to allow a disclosure to be printed notifying users what will happen with their submission
     * @param int $cmid - course module id
     * @return string
     */
    public function print_disclosure($cmid) {
        global $OUTPUT,$DB;
        $setting = $DB->get_record('plagiarism_moss', array('cmid' => $cmid));

        if (!$this->is_plugin_enabled($cmid)) {
            return '';
        }

        if (!$setting) { // Plagiarism scanning turned off.
            return '';
        }

        $context = context_module::instance($cmid);

         $content = '';
         $content = format_text($setting->notification_text, FORMAT_MOODLE);



        if ($content) {
            return $OUTPUT->box_start('generalbox boxaligncenter', 'plagiarism_info')
                .$content
                .$OUTPUT->box_end();
         } else {
            return '';
        }
    }

    /**
     * hook to allow status of submitted files to be updated - called on grading/report pages.
     *
     * @param object $course - full Course object
     * @param object $cm - full cm object
     */
    public function update_status($course, $cm) {
        //called at top of submissions/grading pages - allows printing of admin style links or updating status
        global $OUTPUT, $DB, $USER, $CFG, $PAGE;

        $cmid = $cm->id;
        $context = context_module::instance($cmid);
        $assignment = $DB->get_record('plagiarism_moss', array('cmid' => $cmid));


        $resultlink='no data';
        $resultlink1='no data';
        if (!$this->is_plugin_enabled($cmid)) {
            return '';
        }

        if (!$assignment) { // Plagiarism scanning turned off.
            return '';
        }
        if (!is_dir('/Applications/MAMP/data/moodle38/temp/plagiarism_moss')){
          mkdir('/Applications/MAMP/data/moodle38/temp/plagiarism_moss/');
        }

        if($this->is_plugin_enabled($cmid) && !is_dir('/Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/moss.stanford.edu/')){
          $this->send_to_moss($assignment,$cm);
          $setting1 = $DB->get_record('plagiarism_moss_result', array('cmid' => $cmid));
          $website1=$setting1->resultlink;
          $readFile=explode('/',$website1)[5];
          $numberFile=explode('/',$website1)[4];
          $resultlink1=$CFG->wwwroot.'/plagiarism/moss/test.php?cmid='.$cmid.'';
           //$resultlink1=$CFG->wwwroot.'/'.$cmid.'/HTMLPage1.html';
          $resultlink=$CFG->wwwroot.'/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'.html';

        }

        else{
          $setting1 = $DB->get_record('plagiarism_moss_result', array('cmid' => $cmid));
          $website1=$setting1->resultlink;
          $readFile=explode('/',$website1)[5];
          $numberFile=explode('/',$website1)[4];
          //$resultlink1=$CFG->wwwroot.'/plagiarism/moss/HTMLPage1.html';
          $resultlink1=$CFG->wwwroot.'/plagiarism/moss/test.php?cmid='.$cmid.'';
          $resultlink=$CFG->wwwroot.'/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'.html';
          //$content1=file_get_contents('/Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'.html');
        }

            // Write the rescan button if the user has the capability to do so.
        $buttonlabel = get_string('rescanning', 'plagiarism_moss');

        $buttonattr = array('type' => 'submit',
                    'id' => 'plagiarism_moss_scan',
                    'value' => $buttonlabel);

        $scanbutton = html_writer::empty_tag('input', $buttonattr);

        $content1 = html_writer::tag('form', $scanbutton, array('method' => 'post',
                 'action'=>"$CFG->wwwroot/plagiarism/moss/sendToMoss.php?cmid=$cmid"));


        $content1.='<input type="text" id="threshold" name="Threshold"><br>';
        //$content1.='<input type="submit" value="Submit"><br>';*/



        $content1 .= '<a target="_blank" href='.$resultlink.'>'.get_string('stanford_link', 'plagiarism_moss').'</a> <br>';


        $content1 .= '<a target="_blank" href='.$resultlink1.'>'.get_string('graph_link', 'plagiarism_moss').'</a> <br>';



        if ($content1) {

          return $OUTPUT->box_start('generalbox boxaligncenter', 'plagiarism_info')
                .$content1
                .$OUTPUT->box_end();
         } else {
            return '';
        }


    }

    /**
     * called by admin/cron.php
     *
     */
    public function cron() {
        //do any scheduled task stuff
    }
    public function generateGraph($cmid){
          global $OUTPUT, $DB, $USER, $CFG;
          $myfile = fopen('graph.dot', "w");
          $txt = "digraph D {\n";
          fwrite($myfile, $txt);
          $rs = $DB->get_record('plagiarism_moss_result', array('cmid' => $cmid));
          $count=$DB->count_records('plagiarism_moss_result', array('cmid' => $cmid));
          $id=$rs->id;
          $website1=$rs->resultlink;
          $readFile=explode('/',$website1)[5];
          $numberFile=explode('/',$website1)[4];

          for($i=0;$i<$count;++$i){
          $records = $DB->get_record('plagiarism_moss_result', array('id' => $id));
          $match='match'.$i.'.html';
          $std1=$records->student1_name;
          $std2=$records->student2_name;
          $s1Similar=$records->similarity1;
          $s2Similar=$records->similarity2;
          $threshold=50;
          if ($s1Similar<$threshold){
            $s1Similar='';
          }
          if ($s2Similar<$threshold){
            $s2Similar='';
          }
          $Similar=intval($s1Similar).'/'.intval($s2Similar).' ';

          if($s1Similar!=''||$s2Similar!=''){
          $txt = $std1.'->'.$std2.' [dir=none, label="'.$Similar.'",edgeURL="'.$CFG->wwwroot.'/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'/'.$match.'"];'."\n";
          fwrite($myfile, $txt);
          }

          $id=$id+1;
        }

          $txt = "}\n";
          fwrite($myfile, $txt);
          fclose($myfile);

          shell_exec('/usr/local/bin/dot -Tsvg /Applications/MAMP/htdocs/moodle38/mod/assign/graph.dot  -o /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/mossGraph.svg');
          shell_exec('cp /Applications/MAMP/data/moodle38/temp/HTMLPage1.html /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'');
          shell_exec('cp /Applications/MAMP/data/moodle38/temp/test.php /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'');

        }


    public function send_to_moss($assignment,$cm){
      global $OUTPUT, $DB, $USER, $CFG;
      $cmid = $cm->id;
      $userid = "370143826"; // Enter your MOSS userid
      $moss = new MOSS($userid);
      $moss->setLanguage=$assignment->language;

      plagiarism_moss_extract_assignment($assignment);
      $moss->addByWildcard('/Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/*/*');
      //$fs = get_file_storage();
      //$basefile = $fs->get_area_files($context->id, 'plagiarism_moss', 'codeseeding', $assignment->cmid, '', false);
      //$basefiles =  plagiarism_moss_extract_file($basefile, plagiarism_moss_get_file_extension($assignment->language),'plagiarism_moss', $user = null, $textfileonly = true);
      //$moss->addBaseFile($basefile);

      $moss->setCommentString("This is a test");
      $website= $moss->send();
      $website = substr($website,0,strlen($website)-1);

      $readFile=explode('/',$website)[5];
      $numberFile=explode('/',$website)[4];
      shell_exec('/usr/local/bin/wget --no-clobber --convert-links --random-wait -r -p --level 1 -E -e robots=off -P /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid. ' '.$website.'');
      $content = file_get_contents('/Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'.html');
      $content1=$content;


      //shell_exec('open /Applications/MAMP/htdocs/moodle38/plagiarism/moss/result.png');
      //Split data out of table
      $arr = explode('<TABLE>',$content);
      $arr = explode('</TABLE>',$arr[1]);

      //Get needed data out of array
      $content = $arr[0];

      //Split by newlines
      $lineSplitData = preg_split ('/$\R?^/m', $content);
      $DB->delete_records('plagiarism_moss_result', array('cmid' => $cmid));
      for ($i = 2; $i < count($lineSplitData); $i+=3)
      {
          //Get first position for link
          $pos = strpos($lineSplitData[$i], '<A HREF="')+9;

          //Get second position for link
          $pos2 = strpos($lineSplitData[$i], '">', $pos);

          //Cut from position for length pos2-pos
          $link = substr($lineSplitData[$i], $pos,$pos2-$pos);


          //TODO something with link
          $line1 = explode('/', $lineSplitData[$i]);


          $student1=explode('_',$line1[9])[0];
          $student1name=explode('_',$line1[9])[1];

          $line2 = explode('/', $lineSplitData[$i+1]);

          $student2=explode('_',$line2[9])[0];
          $student2name=explode('_',$line2[9])[1];

          $line1=explode('%', $lineSplitData[$i]);

          $sim1 = substr($line1[0], -2);

          $line2=explode('%', $lineSplitData[$i+1]);
          $sim2 = substr($line2[0], -2);

          $setting = $DB->get_record('plagiarism_moss_result', array('cmid' => $cmid));
          $new = false;
          if (!$setting) {
              $new = true;
              $setting = new stdClass();
              $setting->cmid = $cmid;
          }
          $setting->resultlink=$website;
          $setting->student1_id=$student1;
          $setting->student2_id =$student2;
          $setting->student1_name=$student1name;
          $setting->student2_name =$student2name;

          $setting->similarity1 = $sim1;
          $setting->similarity2 = $sim2;



          $setting->id = $DB->insert_record('plagiarism_moss_result', $setting);


      }
      $this->generateGraph($cmid);
      $string='ln  -s /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/  /Applications/MAMP/htdocs/moodle38/';
      shell_exec($string);
      return $content1;
    }

    private function setup_code_seeding_filemanager($mform, $plagiarismconfig, $assignmentcontext) {

      $mform->addElement('filemanager', 'code_filemanager', get_string('basefile', 'plagiarism_moss'),
          null, $this->filemanageroption);
      $mform->addHelpButton('code_filemanager', 'basefile', 'plagiarism_moss');
      $data = new stdClass();
      file_prepare_standard_filemanager($data, 'code', $this->filemanageroption,
      $assignmentcontext, 'plagiarism_moss', 'codeseeding',
      ($plagiarismconfig) ? $plagiarismconfig->id : null);
      $mform->setDefault('code_filemanager', $data->code_filemanager);
      }


    public function is_plugin_enabled($cmid, $courseid=null) {
        return get_config('plagiarism')->moss_use;
    }

}

function moss_event_file_uploaded($eventdata) {
    $result = true;
        //a file has been uploaded - submit this to the plagiarism prevention service.

    return $result;
}
function moss_event_files_done($eventdata) {
    $result = true;
        //mainly used by assignment finalize - used if you want to handle "submit for marking" events
        //a file has been uploaded/finalised - submit this to the plagiarism prevention service.

    return $result;
}

function moss_event_mod_created($eventdata) {
    $result = true;
        //a moss module has been created - this is a generic event that is called for all module types
        //make sure you check the type of module before handling if needed.

    return $result;
}

function moss_event_mod_updated($eventdata) {
    $result = true;
        //a module has been updated - this is a generic event that is called for all module types
        //make sure you check the type of module before handling if needed.

    return $result;
}

function moss_event_mod_deleted($eventdata) {
    $result = true;
        //a module has been deleted - this is a generic event that is called for all module types
        //make sure you check the type of module before handling if needed.

    return $result;
}
