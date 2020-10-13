<?php
function generateGraph($cmid,$threshold,$clusters,$thickness,$articulation,$studentname,$studentid){
    global $OUTPUT, $DB, $USER, $CFG,$SET;
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



    shell_exec($SET->dot.' -Tsvg '.$CFG->dirroot.'/plagiarism/moss/graph.dot  -o '.$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'/mossGraph.svg');
      shell_exec('cp '.$CFG->dataroot.'/temp/HTMLPage1.html '.$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'');

  }

?>
