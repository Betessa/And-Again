<?php



require_once('../../config.php');
require_once(__DIR__.'/scan_assignment.php');
require_once(__DIR__.'/moss.php');
global $OUTPUT, $DB, $USER, $CFG;

$cmid = required_param('cmid', PARAM_INT);

$assignment = $DB->get_record('plagiarism_moss', array('cmid' => $cmid));
$userid = "370143826"; // Enter your MOSS userid
$moss = new MOSS($userid);
$moss->setLanguage=$assignment->language;

plagiarism_moss_extract_assignment($assignment);
$moss->addByWildcard($CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'/*/*');
//$fs = get_file_storage();
//$basefile = $fs->get_area_files($context->id, 'plagiarism_moss', 'codeseeding', $assignment->cmid, '', false);
//$basefiles =  plagiarism_moss_extract_file($basefile, plagiarism_moss_get_file_extension($assignment->language),'plagiarism_moss', $user = null, $textfileonly = true);
//$moss->addBaseFile($basefile);

$moss->setCommentString("This is a test");
$website= $moss->send();
$website = substr($website,0,strlen($website)-1);

$readFile=explode('/',$website)[5];
$numberFile=explode('/',$website)[4];
shell_exec('/usr/local/bin/wget --no-clobber --convert-links --random-wait -r -p --level 1 -E -e robots=off -P $CFG->dataroot/temp/plagiarism_moss/'.$cmid. ' '.$website.'');
$content = file_get_contents($CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'.html');
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
$threshold=28;
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

shell_exec('/usr/local/bin/dot -Tsvg /Applications/MAMP/htdocs/moodle38/plagiarism/moss/graph.dot  -o '.$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'/mossGraph.svg');
shell_exec('cp a$CFG->dataroot/temp/HTMLPage1.html '.$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'');
shell_exec('cp $CFG->dataroot/temp/test.php '.$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'');



$string='ln  -s $CFG->dataroot/temp/plagiarism_moss/'.$cmid.'/  /Applications/MAMP/htdocs/moodle38/';
shell_exec($string);

echo '<script type="text/javascript">
    window.open("http://localhost:8888/moodle38/mod/assign/view.php?id=13&rownum=0&useridlistid=5f60786426f07024638823&action=grading");
</script>';
