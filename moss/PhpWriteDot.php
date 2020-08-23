<?php
$myfile = fopen("graph.dot", "w");
$txt = "digraph D {\n";
fwrite($myfile, $txt);

$std1=$DB->get_record('',array('cmid' =>$cmid));
$s1Similar=$DB->get_record('',array('cmid' =>$cmid));
$std2=$DB->get_record('',array('cmid' =>$cmid));
$s2Similar=$DB->get_record('',array('cmid' =>$cmid));

$txt = $std1+"->"+$std2+" [label= "+$s1Similar+"];\n";
fwrite($myfile, $txt);

$txt = $std2+"->"+$std1+" [label= "+$s2Similar+"];\n";
fwrite($myfile, $txt);

$txt = "}\n";
fwrite($myfile, $txt);
fclose($myfile);

shell_exec('dot graph.dot -Tpng -o /path/to/new/location/image.png');
?>
