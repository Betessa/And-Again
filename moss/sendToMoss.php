<?php



require_once('../../config.php');
require_once(__DIR__.'/scan_assignment.php');
require_once(__DIR__.'/moss.php');
global $OUTPUT, $DB, $USER, $CFG;

$cmid = required_param('cmid', PARAM_INT);

function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

        foreach( $files as $file ){
            delete_files( $file );
        }

        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );
    }
}

$target=$CFG->dataroot.'/temp/plagiarism_moss/'.$cmid.'';

delete_files($target);
  ////////console.log("'$CFG->wwwroot.'/mod/assign/view.php?id='.$cmid.'&action=grading");
$hello=$CFG->wwwroot.'/mod/assign/view.php?id='.$cmid.'&action=grading';
echo '<script type="text/javascript">
  console.log("hi");
  window.open("'.$hello.'","self");
</script>';
