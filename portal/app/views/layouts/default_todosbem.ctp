<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Todos Bem</title>
		
		<link href="/portal/css/bootstrap_3_2_0.min.css" rel="stylesheet" />
		<link href="/portal/css/jquery-ui.css" rel="stylesheet" />

		<link rel="stylesheet" href="/portal/css/todosbem.css" />
		<link rel="stylesheet" href="/portal/css/sweetalert.css" />
		
		<style>
			body {
				background: #DAEFF7;
			}
		</style>
		
		<?php echo $this->Html->scriptBlock('var baseUrl = "http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'] . $this->webroot.'";'); ?>

		<script src="/portal/js/jquery_1_11_2.min.js"></script>
		<script src="/portal/js/jquery-ui_1_11_4.js"></script>
		<script src="/portal/js/bootstrap_3_2_0.min.js"></script>
		
		<?php echo $this->Buonny->link_js('jquery.meiomask'); ?>
		<?php echo $this->Buonny->link_js('sweetalert.min'); ?>
		<?php // echo $this->Buonny->link_js('jquery-maskedinput_min'); ?>
		<?php echo $this->Buonny->link_js('comum'); ?>
		
	</head>
	<body>
		<?php echo $content_for_layout ?>
		<?php echo $scripts_for_layout; ?>
	</body>
</html>