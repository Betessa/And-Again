<?php
 class ToTestGraph
 {
     private $hi;

     public function __construct($hi)
     {
         $this->hi = $hi;
     }




     public function generateGraph($threshold, $Sim1, $Sim2, $thickness, $studentname, $studentid, $studentname1, $studentid1, $studentname2, $studentid2)
     {
         global $OUTPUT, $DB, $USER, $CFG;


         $id1 = $studentid1;
         $id2 = $studentid2;

         //Assumed values from HTMLPage
         //$articulation=true;
         $clusters = true;

         //required for bfs
         //required articulation points

         //intialise $adjacencyMatrix and $visited

         //$records = $DB->get_record('plagiarism_moss_result', array('id' => $id));

         if ($studentname && $studentid) {
             $std1 = $studentname1 . '_' . $studentid1;
             $std2 = $studentname2 . '_' . $studentid2;
         } else if ($studentname && !$studentid) {
             $std1 = $studentname1;
             $std2 = $studentname2;
         } else if (!$studentname && !$studentid) {
             $std1 = $studentid1;
             $std2 = $studentid2;
         } else if (!$studentname && $studentid) {
             $std1 = $studentid1;
             $std2 = $studentid2;
         }


         $s1Similar = $Sim1;
         $s2Similar = $Sim2;

         //reseting id so that we can create dot file
        $txt=NULL;

         if ($studentname && $studentid) {
             $std1 = $studentname1 . '_' . $studentid1;
             $std2 = $studentname2 . '_' . $studentid2;
         } else if ($studentname && !$studentid) {
             $std1 = $studentname1;
             $std2 = $studentname2;
         } else if (!$studentname && !$studentid) {
             $std1 = $studentid1;
             $std2 = $studentid2;
         } else if (!$studentname && $studentid) {
             $std1 = $studentid1;
             $std2 = $studentid2;
         }

         $s1Similar = $Sim1;
         $s2Similar = $Sim2;

         if ($s1Similar < $threshold) {
             $s1Similar = '';
         }
         if ($s2Similar < $threshold) {
             $s2Similar = '';
         }
         $Similar = intval($s1Similar) . '/' . intval($s2Similar) . ' ';

         $penwidth = '';
         if ($thickness && $s1Similar != '' || $s2Similar = '') {
             $thickValue = (($s1Similar + $s2Similar) / 200.0) * 5.0;
             $penwidth = 'penwidth= ' . $thickValue;
         }

         if ($s1Similar != '' || $s2Similar != '') {
             $txt = $std1 . '->' . $std2 . ' [dir=none, label="' . $Similar . '"' . $penwidth . '"];';
         }

         return $txt;

     }
 }
