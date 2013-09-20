<?php
require_once("FileLoader.php");

$temp = "PROGRAM GOKE". "\n".
    "IMPLICIT NONE". "\n".
    "WRITE(*,*)'Type Your Program In The Text Editor :)'". "\n".
    "STOP". "\n".
    "END PROGRAM GOKE";
$check = false;
if(isset($_POST["code"])){
    $check = strlen($_POST["code"]) > 0;
}
$cli_args = "";
if(isset($_POST["stdin"])){
    $args = $_POST["stdin"];
    $args_array = explode(",",$args);
    $cli_args = implode(" ",$args_array);
}
$string = $check ? $_POST["code"] : $temp;
$fortranCompiler = new FortranCompiler();
$fortranCompiler->createErrorFile();
$fortranCompiler->createFortranFile($string);
$fortranCompiler->compile($fortranCompiler->createCompileCommand());
$result = array();
if($fortranCompiler->isCompiled() && $check){
    $result["status"] = true;
    $result["result"] = $fortranCompiler->executeProgram($cli_args);
    $result["link"] =  $fortranCompiler->filename;
    echo json_encode($result);
}else{
    $error_msg = !$check ? $fortranCompiler->executeProgram($cli_args) : $fortranCompiler->compileError();
    $result = array("status" => false, "error_message" => $error_msg);
    echo json_encode($result);
}