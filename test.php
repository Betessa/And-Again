
<?php
global $OUTPUT, $DB, $USER, $CFG;


require_once('../../config.php');
require_once($CFG->dirroot.'/scan_assignment.php');
require_once($CFG->dirroot.'/moss.php');



require_login();
$cmid = required_param('cmid', PARAM_INT);

$context = context_module::instance($cmid);
if($isteacher = has_capability('mod/assignment:grade', $context)){
function generateGraph($cmid,$threshold,$clusters,$thickness,$articulation,$studentname,$studentid){
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


    //$articulation=true;
    $clusters=True;

    //required for bfs
    $adjacencyMatrix= array(array());//matrix of edges
    $visited=array();
    //required articulation points
    $articulationPoints=array();

    //intialise $adjacencyMatrix and $visited
    for($i=0;$i<$count;++$i){
      $records = $DB->get_record('plagiarism_moss_result', array('id' => $id));
      $match='match'.$i.'.html';

      if($studentname && $studentid){
      $std1=$records->student1_name.'_'.$records->student1_id;
      $std2=$records->student2_name.'_'.$records->student2_id;
      }
      else if($studentname && !$studentid){
      $std1=$records->student1_name;
      $std2=$records->student2_name;
      }
      else if(!$studentname && !$studentid){
        $std1=$records->student1_id-98;
        $std2=$records->student2_id-98;
      }
      else if(!$studentname && $studentid){
      $std1=$records->student1_id;
      $std2=$records->student2_id;
      }


      $s1Similar=$records->similarity1;
      $s2Similar=$records->similarity2;

      if($s1Similar>=$threshold||$s2Similar>=$threshold){
        $adjacencyMatrix[$std1][$std2]= 1;
        $adjacencyMatrix[$std2][$std1]= 1;
      }else{
        $adjacencyMatrix[$std1][$std2]= 0;
        $adjacencyMatrix[$std2][$std1]= 0;
      }
      $visited[$std1]=false;
      $visited[$std2]=false;

      $id=$id+1;
    }
    //We need to perform BFS on every node
        foreach(array_keys($adjacencyMatrix) as $node) {

          //We work with a copy of the matrix so that we can remove nodes in peace
          $copy=$adjacencyMatrix;
          //intialise SearchGoals
          $goals=array();
          foreach(array_keys($adjacencyMatrix[$node]) as $posGoals) {
            if($adjacencyMatrix[$node][$posGoals]==1){
              array_push($goals,$posGoals);
              $copy[$node][$posGoals]=0;
              $copy[$posGoals][$node]=0;
            }
          }
          //if there is 1 edge or less we know it cannot be a articulation point
          if (count($goals)>1) {
            $queue = new SplQueue();
            $queue->enqueue($goals[0]);
            $visited[$goals[0]]=true;

            // actual BFS
            while (!$queue->isEmpty()){
              $v=$queue->bottom();
              $queue->dequeue();
              if (($key = array_search($v, $goals)) !== false) {
                unset($goals[$key]);
              }
              foreach (array_keys($copy[$v]) as $traversal) {
                if ($copy[$v][$traversal]==1 && (!$visited[$traversal])){
                  $queue->enqueue($traversal);

                  $visited[$traversal]=true;
                }
              }
            }
            unset($queue);
            //add it to articualtion Points
            if (!empty($goals)){
              array_push($articulationPoints,$node);
            }
            foreach ($visited as &$vis) {
              $vis=false;
            }
          }
        }
      if ($clusters)  {//adding cluster
      $colour=substr(str_shuffle('ABCDEF0123456789'), 0, 6);


      foreach(array_keys($adjacencyMatrix) as $node) {
        if (isset($visited[$node])&&$visited[$node]==false){

          $queue = new SplQueue();
          $queue->enqueue($node);
          $visited[$node]=true;
          //assumes node is not connected
          $connec=false;
          // actual BFS
          while (!$queue->isEmpty()){
            $v=$queue->bottom();
            $queue->dequeue();
            foreach (array_keys($adjacencyMatrix[$v]) as $traversal) {
              if ($adjacencyMatrix[$v][$traversal]==1 && (!$visited[$traversal])){
                $queue->enqueue($traversal);
                $visited[$traversal]=true;
                //$clusterColour[$traversal]=$colour;
                $txt = $traversal.'[color="#'.$colour.'"];'."\n";
                fwrite($myfile, $txt);
                //starting node is connected and on graph
                $connec=true;
              }
            }
          }
          if($connec){//colour node if it ins't independent
            $txt = $node.'[color="#'.$colour.'"];'."\n";
            fwrite($myfile, $txt);
          }
        } else {
          $colour=substr(str_shuffle('ABCDEF0123456789'), 0, 6);
        }
      }
    }
    //draw articualtion points on graph
    if ($articulation) {
      foreach ($articulationPoints as $ap) {
        $txt = $ap.'[shape=triangle];'."\n";
        fwrite($myfile, $txt);
      }
    }
    //reseting id so that we can create dot file
    $id=$rs->id;

    for($i=0;$i<$count;++$i){
    $records = $DB->get_record('plagiarism_moss_result', array('id' => $id));
    $match='match'.$i.'.html';

    if($studentname && $studentid){
    $std1=$records->student1_name.'_'.$records->student1_id;
    $std2=$records->student2_name.'_'.$records->student2_id;
    }
    else if($studentname && !$studentid){
    $std1=$records->student1_name;
    $std2=$records->student2_name;
    }
    else if(!$studentname && !$studentid){
      $std1=$records->student1_id-98;
      $std2=$records->student2_id-98;
    }
    else if(!$studentname && $studentid){
    $std1=$records->student1_id;
    $std2=$records->student2_id;
    }

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
    if($thickness && ($s1Similar!='' || $s2Similar!='')){
      $thickValue=(($s1Similar+$s2Similar)/200.0)*5.0;
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



    shell_exec('/usr/local/bin/dot -Tsvg '.$CFG->dirroot.'/plagiarism/moss/graph.dot  -o '.$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'/mossGraph.svg');
    shell_exec('cp '.$CFG->dataroot.'/temp/HTMLPage1.html '.$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'');

  }

$threshold=50;
$color=False;
$articulationPoint=False;
$thickness=False;
$studentname=False;
$studentid=False;
Echo "<html>";
Echo
"<title>HTML With PHP</title>";

$input=file_get_contents($CFG->dataroot.'/temp/plagiarism_moss/13/HTMLPage1.html');
$input=explode("\n",$input);

if(isset($_GET['option1']) && $_GET['option1'] == 'Yes'){
  $thickness=True;
  $input[26]='<input type="checkbox" name= "option1" value="Yes" input checked>';
}
if(isset($_GET['option2']) && $_GET['option2'] == 'Yes'){
    $color=True;
    $input[29]='<input type="checkbox" name= "option2" value="Yes" input checked>';
}
if(isset($_GET['option3']) && $_GET['option3'] == 'Yes'){
  $articulationPoint=True;
  $input[32]='<input type="checkbox" name= "option3" value="Yes" input checked>';
}
if(isset($_GET['option4']) && $_GET['option4'] == 'Yes'){
  $studentname=True;
  $input[35]='<input type="checkbox" name= "option4" value="Yes" input checked>';
}
if(isset($_GET['option5']) && $_GET['option5'] == 'Yes'){
  $studentid=True;
  $input[38]='<input type="checkbox" name= "option5" value="Yes" input checked>';

}
$input[41]='Threshold <0,100>: <input type="number" name="threshold" value="'.$_GET['threshold'].'" min="0" max="100"><br>';
generateGraph($cmid,$_GET['threshold'],$color,$thickness,$articulationPoint,$studentname,$studentid);


$input[42]='<input type="hidden" id="cmid" name="cmid" value='.$cmid.'>';
$input=implode("\n",$input);

echo $input;

echo file_get_contents($CFG->dataroot.'/temp/plagiarism_moss/13/mossGraph.svg');
}
else if($isteacher = has_capability('mod/assignment:grade', $context)){
echo "You do not have the capability to view this page";
}

//your php code here
