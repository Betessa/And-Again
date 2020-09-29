<?php
public function generateGraph($cmid){
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
      $threshold=28;
      $thick=true;
      $articulation=true;
      $clusters=true;

      //required for bfs
      $adjacencyMatrix= array(array());//matrix of edges
      $visited=array();
      //required articulation points
      $articulationPoints=array();

      //intialise $adjacencyMatrix and $visited
      for($i=0;$i<$count;++$i){
        $records = $DB->get_record('plagiarism_moss_result', array('id' => $id));
        $match='match'.$i.'.html';
        $std1=$records->student1_name;
        $std2=$records->student2_name;
        $s1Similar=$records->similarity1;
        $s2Similar=$records->similarity2;

        if($s1Similar>$threshold||$s2Similar>$threshold){
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
      if($thick){
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



      shell_exec('/usr/local/bin/dot -Tsvg /Applications/MAMP/htdocs/moodle38/mod/assign/graph.dot  -o /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/mossGraph.svg');
      shell_exec('cp /Applications/MAMP/data/moodle38/temp/HTMLPage2.html /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'');
    }
?>
