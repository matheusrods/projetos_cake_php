<!DOCTYPE html>
<html lang="pt">
  <head>
<!--     
    <script language='javascript' type='text/javascript' src='https://www.geoportal.com.br/Api_Js_v3/v3.js'></script>
    <link rel="stylesheet" type="text/css" href="https://www.geoportal.com.br/Api_Js_v3/theme/default/style.css"> -->

    <!-- Google Tag Manager -->
    <!-- <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KPS5ZWM');</script> -->
    <!-- End Google Tag Manager -->

    <!-- Hotjar Tracking Code for portal.ithealth.com.br -->
<!--     
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:1406422,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script> -->

    <?php echo $this->Html->charset(); ?>
    <title><?php echo $title_for_layout; ?> - RHHealth</title>
    <?php  echo $this->Html->meta('favicon.ico',$this->webroot.'img/favicon.ico',array('type' => 'icon')); ?>
    <meta name="resource-type" content="document" />    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Nelson Tatsuo Ota" />
    <meta content="no-cache" http-equiv="pragma">
    <meta name="language" content="pt-br" />
    <meta name="description" content="Portal Gerenciamento de Riscos" />
    <meta name="keywords" content="gerenciamento de riscos, logística" />


    <!-- Le styles -->
    <?php //echo $this->Buonny->link_css('potistrap'); ?>
    <?php echo $this->Buonny->link_css('combined'); ?>
    <?php // echo $this->Buonny->link_css('sweetalert'); ?>
    <?php echo $this->Buonny->link_css('rhhealth/ithealthComum'); ?>
    <style>
      body {
        padding-top: 80px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    

    <?php echo $this->Html->scriptBlock('var baseUrl = "'.$this->webroot.'";'); ?>
    <?php echo $this->Buonny->link_js('combined'); ?>
    <?php // echo $this->Buonny->link_js('sweetalert.min'); ?>
    <?php // echo $this->Buonny->link_js('multiselect_multi'); ?>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="/portal/js/html5.js"></script>
      <script src="/portal/js/respond.min.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <!-- <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/portal/img/twiter/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/portal/img/twiter/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/portal/img/twiter/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/portal/img/twiter/apple-touch-icon-57-precomposed.png"> -->
  </head>

  <body class="ithealth">
    <!-- Google Tag Manager (noscript) -->
    <!-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KPS5ZWM"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
    <!-- End Google Tag Manager (noscript) -->
    <?php echo $this->element('modulos'); ?>
        
    <div class="container">
        <div class="message container">
            <?php echo $this->Buonny->flash(); ?>
        </div>
        <div class='page-title'><h3><?php echo $title_for_layout ?></h3></div>
        <?php echo $content_for_layout; ?>
    </div> <!-- /container -->
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php // echo $this->Buonny->link_js('script'); ?>
    <?php echo $this->Buonny->link_js('jquery.html5-placeholder-shim') ?>
    <?php echo $this->Javascript->codeBlock("jQuery(document).ready(function(){
            jQuery('div.message').delay(4000).animate({opacity:0,height:0,margin:0}, function(){ jQuery(this).slideUp() });
        jQuery('div#messageAbaixo').delay(20000).animate({opacity:0,height:0,margin:0}, function(){ jQuery(this).slideUp() });
            jQuery('ul.sf-menu').superfish();
        setTimeout(function () {
            $.placeholder.shim();  
        }, 500);

        $(document).ready(function(){
          $(\".bselect2\").select2();
        });
  
        $.placeholder.shim();  
              init_alertas();
        });");?>
  <?php echo $scripts_for_layout; ?>
    <?php echo $this->element('sql_dump'); ?>
  <style type="text/css">
    #messageAbaixo {
        bottom: 50px !important;
        position: fixed !important;
        width: 100% !important;
        z-index: 999999 !important;
    }
    #messageAbaixo div {
      font-size: 15px;
      font-weight: bold;
      text-align: center;
    }
  </style>
  <?php 
    if($this->Session->Read('emmanutencao')){
      echo "<div id='messageAbaixo' class='container' style='opacity: 1; height: 0px; margin: 0px; display: block;'>
        <div class='alert alert-danger'><button aria-hidden='true' data-dismiss='alert' class='close' type='button'>×</button>
        Informamos que o sistema se encontra em <strong>manutenção</strong>!</div></div>";
    } 
  ?>
  </body>
</html>