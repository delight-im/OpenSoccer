<?php
if (!isset($_POST['stat'])) { exit; }
	$statistik = trim($_POST['stat']);
	$temp1 = str_replace('/', '', $statistik);
	$temp2 = str_replace('.', '', $statistik);
	if ($statistik != $temp1 OR $statistik != $temp2) { exit; }
	header('Location: /stat_'.$statistik.'.php');
	exit;
?>