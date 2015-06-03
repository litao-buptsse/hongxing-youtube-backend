<?php

$res = array();
if(empty($_POST['url']) || empty($_POST['str'])) {
    echo json_encode($res);
}

$url = $_POST['url'];
$str = $_POST['str'];

passthru("python py/youtube_sig_decrypt.py $url $str");
?>
