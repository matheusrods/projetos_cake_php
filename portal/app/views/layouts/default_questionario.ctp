<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Todos Bem</title>
		
		<link href="/portal/css/bootstrap_3_2_0.min.css" rel="stylesheet" />		
		<link href="/portal/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="/portal/css/todosbem.css" />
		<link rel="stylesheet" href="/portal/css/icheck.css" />
		<link rel="stylesheet" href="/portal/css/sweetalert.css" />

		<?php echo $this->Html->scriptBlock('var baseUrl = "http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'] . $this->webroot.'";'); ?>
		
		<script src="/portal/js/jquery_1_11_2.min.js"></script>
		<script src="/portal/js/jquery-ui_1_11_4.js"></script>
		<script src="/portal/js/bootstrap_3_2_0.min.js"></script>
		
		<?php echo $this->Buonny->link_js('icheck'); ?>
		<?php echo $this->Buonny->link_js('script'); ?>
		<?php echo $this->Buonny->link_js('sweetalert.min'); ?>
	</head>
	<body>
		<nav class="navbar navbar-default">
			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container-barra">
					<div class="row">
						<div class="col-md-3">
							<a href="/portal/dados_saude/dashboard" class="icon-back pull-left"> <img src="/portal/img/todosbem/icon-back.png" alt="">
							</a>
						</div>
						<div class="col-md-9 right">
							<img class="logo" src="/portal/img/todosbem/logo.png" alt="logomarca">
						</div>
					</div>
				</div>
			</div>
		</nav>
		
		<!-- 
		<div class="main">
			<div id="page-wrap" class="theme-dark">
				<h1>Informações gerais</h1>
				<a href="#" class="btn-lg btn-danger"> Refazer Check-up <span class="arrow"></span></a>
			</div>		
		</div>
		-->
		
		<?php echo $content_for_layout ?>
			
	</body>
</html>



        <!-- globalWrapper -->
<!--         <div id="globalWrapper"> -->

			<?php // echo $this->element('todosbem/header') ?>

<!-- 			<div class="container"> -->
<!-- 			    <div class="alert-warning"> -->
					<?php // echo $this->Buonny->flash(); ?>
<!-- 				</div> -->
<!-- 			</div> -->

			<!-- Main -->
<!-- 			<section> -->
			    <?php // echo $content_for_layout ?>
			<!-- </section><!-- linha 2 -->

            <!-- globalWrapper -->
<!--         </div> -->