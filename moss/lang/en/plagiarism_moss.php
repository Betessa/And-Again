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
 *
 * @package   plagiarism_moss
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['studentdisclosuredefault']  ='All files uploaded will be submitted to a plagiarism detection service';
$string['studentdisclosure'] = 'Student Disclosure';
$string['studentdisclosure_help'] = 'This text will be displayed to all students on the file upload page.';
$string['mossexplain'] = 'Moss is a source-code plagiarism checker';
$string['moss'] =  'Moss anti-plagiarism';
$string['graph_link'] =  'Graph Results';
$string['stanford_link'] =  'Plagiarism Results';
$string['timetomeasure'] ='Time to measure';
$string['sentfile']='Files to be sent to moodle';
$string['sent_help']='Please download the submissions that are not in folders and upload the zip, and then unzip the file and delte the zip';
$string['basefile'] = 'Base file (only .rar or .zips are accepted)';
$string['duedate'] = 'Due date';
$string['basefile_help'] = 'Moss normally reports all code that matches in pairs of files. When a base file is supplied, program code that also appears in the base file is not counted in matches. A typical base file will include, for example, the instructor-supplied code for an assignment. You can provide multiple base files here. Base files improve results, but are not usually necessary for obtaining useful information.';
$string['timetomeasure_help'] ='Set the time to measure all submissions to detect plagiarism. If not set, the measure will occur after the activity\'s due time.

The measure will be executed only once against all existing submissions. If you want to measure again, reset the time.';
$string['usemoss'] ='Enable moss';
$string['savedconfigsuccess'] = 'Plagiarism Settings Saved';
$string['language']='Default language';
$string['langascii'] = 'Text';
$string['langmips'] = 'MIPS assembly';
$string['langa8086'] = 'a8086 assembly';
$string['mossuserid'] ='Moss User id';
$string['mossuserid_help'] ='To obtain a Moss account, send a mail message to <a href="mailto:moss@moss.stanford.edu">moss@moss.stanford.edu</a>. The body of the message should be in <strong>PLAIN TEXT</strong>(without any HTML tags) format and appear exactly as follows:

    registeruser
    mail username@domain

    After registration, you will get a reply mail which contains a perl script with one line of code just likes:

    $userid=1234567890;

The number is exactly your moss account.';
