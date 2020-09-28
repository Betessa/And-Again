
<?php



require_once('../../config.php');
require_once(__DIR__.'/scan_assignment.php');
require_once(__DIR__.'/moss.php');
global $OUTPUT, $DB, $USER, $CFG;


require_login();
$cmid = required_param('cmid', PARAM_INT);

if (!is_siteadmin()) {
echo("Sorry! You don't have rights to use this page");
}
echo ".$cmid.";

function generateGraph($cmid,$threshold,$color,$thickness,$articulationPoint){
 global $OUTPUT, $DB, $USER, $CFG;
  $myfile = fopen("graph.dot", "w");
  $txt = "digraph D {\n";
  fwrite($myfile, $txt);
  $rs = $DB->get_record('plagiarism_moss_result', array('cmid' => $cmid));
  $count=$DB->count_records('plagiarism_moss_result', array('cmid' => $cmid));
  $id=$rs->id;
  $website1=$rs->resultlink;
  $readFile=explode('/',$website1)[5];
  $numberFile=explode('/',$website1)[4];

  //Assumed values from HTMLPage


  $articulation=true;
  $clusters=true;

  for($i=0;$i<$count;++$i){
  $records = $DB->get_record('plagiarism_moss_result', array('id' => $id));
  $match='match'.$i.'.html';
  $std1=$records->student1_name;
  $std2=$records->student2_name;
  $s1Similar=$records->similarity1;
  $s2Similar=$records->similarity2;

  if ($s1Similar<$threshold){
    $s1Similar='';
  }
  if ($s2Similar<$threshold){
    $s2Similar='';
  }
  $Similar=intval($s1Similar).'/'.intval($s2Similar).' ';

  $penwidth='';
  if($thickness=true && $s1Similar!='' || $s2Similar=''){
    $thickValue=(($s1Similar+$s2Similar)/200.0)*3.0;
    $penwidth='penwidth= '.$thickValue;
  }

  if($s1Similar!=''||$s2Similar!=''){
  $txt = $std1.'->'.$std2.' [dir=none, label="'.$Similar.'"'.$penwidth.',edgeURL="'.$CFG->wwwroot.'/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'/'.$match.'"];'."\n";
  fwrite($myfile, $txt);
  }



  $id=$id+1;
  }
      $txt = "}\n";
      fwrite($myfile, $txt);
      fclose($myfile);

      shell_exec('/usr/local/bin/dot -Tsvg /Applications/MAMP/htdocs/moodle38/plagiarism/moss/graph.dot  -o /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/mossGraph.svg');

    }

echo $_GET["threshold"];
$color=false;
$articulationPoint=false;
$thickness=false;
Echo "<html>";
Echo
"<title>HTML With PHP</title>";

if(isset($_GET['option1']) &&
   $_GET['option1'] == 'Yes')
{
    $thickness=true;
}


if(isset($_GET['option2']) &&
   $_GET['option2'] == 'Yes')
{
    $color='true';
}


if(isset($_GET['option3']) &&
   $_GET['option3'] == 'Yes')
{
  $articulationPoint='true';
}
generateGraph($cmid,$_GET['threshold'],$color,$thickness,$articulationPoint);




echo file_get_contents('/Applications/MAMP/data/moodle38/temp/plagiarism_moss/13/HTMLPage1.html');

echo file_get_contents('/Applications/MAMP/data/moodle38/temp/plagiarism_moss/13/mossGraph.svg');

echo $_GET["threshold"];

echo $thickness;

//your php code here
