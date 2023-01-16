<!DOCTYPE html>
<html lang="pt">
  <head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KPS5ZWM');</script>
    <!-- End Google Tag Manager -->

    <!-- Hotjar Tracking Code for portal.ithealth.com.br -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:1406422,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>

    <?php echo $this->Html->charset(); ?>
    <title><?php echo $title_for_layout; ?> - RHHealth</title>
    <meta name="resource-type" content="document" />	
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="Henrique" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta name="language" content="pt-br" />

    <!-- Le styles -->
    <?php echo $this->Buonny->link_css('potistrap'); ?>
    <?php echo $this->Buonny->link_css('combined'); ?>
    <?php //echo $this->Buonny->link_css('twiter/bootstrap'); ?>
    <?php //echo $this->Buonny->link_css('twiter/bootstrap-responsive'); ?>
    <?php //echo $this->Buonny->link_css('jqueryui/redmond/jquery-ui-1.10.1.custom'); ?>
    <?php //echo $this->Buonny->link_css('fam-icons/cus-icons'); ?>
    <?php //echo $this->Buonny->link_css('app'); ?>
    <?php echo $this->Html->scriptBlock('var baseUrl = "'.$this->webroot.'";'); ?>
    <?php //echo $this->Buonny->link_js('jquery') ?>
    <?php echo $this->Buonny->link_js('combined') ?>
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="/portal/js/html5.js"></script>
      <script src="/portal/js/respond.min.js"></script>
    <![endif]-->

    <style>
      body {
        padding-top: 20px;
      }
      .message {
        top: 0 !important;
      }
    </style>

    <!-- Le fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/portal/img/twiter/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/portal/img/twiter/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/portal/img/twiter/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/portal/img/twiter/apple-touch-icon-57-precomposed.png">
  </head>

  <body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KPS5ZWM"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div class="">
        <div class="message">
            <?php echo $this-> Buonny->flash(); ?>
        </div>
        <?php echo $content_for_layout; ?>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php //echo $this->Buonny->link_js('twiter/bootstrap.min') ?>
    <?php //echo $this->Buonny->link_js('jqueryui/jquery-ui-1.10.1.custom.min'); ?>
    <?php //echo $this->Buonny->link_js('jqueryui/jquery-ui-timepicker-addon'); ?>
    <?php //echo $this->Buonny->link_js('jquery.blockUI'); ?>
	<?php //echo $this->Buonny->link_js('jquery-maskedinput') ?>
    <?php //echo $this->Buonny->link_js('comum'); ?>
    <?php echo $scripts_for_layout; ?>
    
    <?php echo $this->Javascript->codeBlock("jQuery(document).ready(function(){
		jQuery('div.message').delay(4000).animate({opacity:0,height:0,margin:0}, function(){ jQuery(this).slideUp() })
	});");?>
  </body>
</html>