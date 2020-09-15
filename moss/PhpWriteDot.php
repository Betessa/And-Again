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



      for($i=0;$i<$count;++$i){
      $records = $DB->get_record('plagiarism_moss_result', array('id' => $id));
      $match='match'.$i.'.html';
      $std1=$records->student1_name;
      $std2=$records->student2_name;
      $s1Similar=$records->similarity1;
      $s2Similar=$records->similarity2;
      if ($s1Similar<50){
        $s1Similar='';
      }
      if ($s2Similar<50){
        $s2Similar='';
      }

      $Similar=$s1Similar.'/'.$s2Similar;
      if($s1Similar!==''||$s2Similar!==''){
      $txt = $std1.'->'.$std2.' [dir=none, headlabel= '.$s1Similar.',taillabel= '.$s2Similar.',edgeURL="'.$CFG->wwwroot.'/'.$cmid.'/moss.stanford.edu/results/'.$numberFile.'/'.$readFile.'/'.$match.'"];'."\n";
      fwrite($myfile, $txt);
      }

      $id=$id+1;



    }
      //$content1.=$rs;
      //$rs->close();
      $txt = "}\n";
      fwrite($myfile, $txt);
      fclose($myfile);



      shell_exec('/usr/local/bin/dot -Tsvg /Applications/MAMP/htdocs/moodle38/mod/assign/graph.dot  -o /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'/mossGraph.svg');
      shell_exec('cp /Applications/MAMP/data/moodle38/temp/HTMLPage2.html /Applications/MAMP/data/moodle38/temp/plagiarism_moss/'.$cmid.'');
    }
?>
