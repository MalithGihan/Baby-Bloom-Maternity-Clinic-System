<?php
$con = mysqli_connect('localhost','bbadmin','qwertyuiop','babybloom');
if(!$con){
    die(mysqli_error("Warning!!! - "+$con));
}
?>