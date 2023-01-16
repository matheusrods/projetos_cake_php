<style>
	.botao-cliente {
		margin: 7px 0 0 0;
		padding: 5px;
		border: 2px solid #005580;
		border-radius: 5px;
		background: #a6cbdd;
		color: #005580;
		display: inline-block;
		font-size: 12px;
		font-weight: bold;
		line-height: 9px;
		text-decoration: none;
	}
</style>


<div class="navbar navbar-fixed-top">
  <div class="navbar-inner" style="background: <?php echo ((Ambiente::getServidor() == Ambiente::SERVIDOR_DESENVOLVIMENTO) && isset($dbprod)) ? '#6019B7' : ((isset($authUsuario['Usuario']['cor_menu']) && !empty($authUsuario['Usuario']['cor_menu'])) ? '#' . $authUsuario['Usuario']['cor_menu'] : ''); ?>;">
    <div class="container" style="width: 1270px;">
    <!-- <div class="container" style="width: 1400px; margin: 0px 0px 0px 30px;"> -->

      <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      <span id="logomarca-do-sistema">
	      <?php if(isset($authUsuario['Usuario']['logomarca']) && !empty($authUsuario['Usuario']['logomarca'])) : ?>

	      	<!--   <a href="/portal/usuarios/inicio" style="width: 21px;height: 16px;" class="brand logo" title="<?php //echo $_SERVER['SERVER_ADDR']; ?>" id="img_thumb"><img src="https://api.rhhealth.com.br<?php //echo $authUsuario['Usuario']['logomarca']; ?>" alt="" /></a> -->
	      
	      <?php endif; ?>      
      </span>
      </a>

      <?php echo $this->element('menu_usuario') ?>
      <div class="nav-collapse" style="font-size: 11px;">
        <ul class="nav">
			<?php $this->Session->read('modulo_selecionado') ?>
			<?php $menu_modulos = ''; ?>

			<?php if(isset($authUsuario['Usuario']) && empty($authUsuario['Usuario']['codigo_empresa'])) : ?>
				<?php $menu_modulos .= $this->BMenu->link('Sistema',array('controller'=>'painel','action'=>'modulo_admin'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::ADMIN?'active':'') , 'title' =>'Modulo Sistema' )); ?>
			<?php endif; ?>

			<?php $menu_modulos .= $this->BMenu->link('Administrativo',array('controller'=>'painel','action'=>'modulo_administrativo'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::ADMINISTRATIVO?'active':'') , 'title' =>'Modulo Administrativo' )); ?>

			<?php if(isset($authUsuario['Usuario']) && (($authUsuario['Usuario']['codigo_empresa'] <= 6) || empty($authUsuario['Usuario']['codigo_empresa']))) : ?>
				<?php $menu_modulos .= $this->BMenu->link('Financeiro',array('controller'=>'painel','action'=>'modulo_financeiro'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::FINANCEIRO?'active':'') , 'title' =>'Modulo Financeiro' )); ?>
			<?php endif;?>

			<?php $menu_modulos .= $this->BMenu->link('Covid',array('controller'=>'painel','action'=>'modulo_covid'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::COVID?'active':'') , 'title' =>'Módulo Covid' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('Comercial',array('controller'=>'painel','action'=>'modulo_comercial'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::COMERCIAL?'active':'') , 'title' =>'Modulo Comercial' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('Contas Médicas',array('controller'=>'painel','action'=>'modulo_contas_medicas'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::CONTASMEDICAS?'active':'') , 'title' =>'Modulo Contas Médicas' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('Credenciamento',array('controller'=>'painel','action'=>'modulo_credenciamento'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::CREDENCIAMENTO?'active':'') , 'title' =>'Modulo Credenciamento' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('Gestão Doc.',array('controller'=>'painel','action'=>'modulo_gestao_documentos'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::GESTAODOCUMENTOS?'active':'') , 'title' =>'Modulo Gestão Documentos' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('Saúde',array('controller'=>'painel','action'=>'modulo_saude'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::SAUDE?'active':'') , 'title' =>'Módulo Saúde' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('Segurança',array('controller'=>'painel','action'=>'modulo_seguranca'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::SEGURANCA?'active':'') , 'title' =>'Módulo Segurança' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Mapeamento Risco',array('controller'=>'painel','action'=>'modulo_mapeamento_risco'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::MAPEAMENTORISCO?'active':'') , 'title' =>'Módulo Mapeamento de Risco' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('eSocial',array('controller'=>'painel','action'=>'modulo_e_social'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==
				Modulo::ESOCIAL?'active':'') , 'title' =>'Módulo eSocial' )); ?>

            <?php
            //Configuração de menus POS
            if (!empty($authUsuario['Usuario']['codigo_empresa'])) {
                App::import('Controller', 'Clientes');

                $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];

                if ($authUsuario['Usuario']['codigo_uperfil'] == 1 || $authUsuario['Usuario']['admin'] == 1) {
                    $menu_modulos .= $this->BMenu->link('Plano de Ação',array('controller'=>'painel','action'=>'modulo_plano_de_acao'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::PLANO_DE_ACAO?'active':'') , 'title' =>'Módulo Plano de Ação' ));
                    $menu_modulos .= $this->BMenu->link('Walk & Talk',array('controller'=>'painel','action'=>'modulo_walk_talk'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::WALK_TALK?'active':'') , 'title' =>'Módulo Walk & Talk' ));

                    $menu_modulos .= $this->BMenu->link('Obs',array('controller'=>'painel','action'=>'modulo_observador_ehs'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::OBSERVADOR_EHS?'active':'') , 'title' =>'Módulo Observador EHS' ));

                } elseif($authUsuario['Usuario']['codigo_uperfil'] != 1 || $authUsuario['Usuario']['admin'] != 1 && !empty($codigo_cliente)) {

                    if(!empty($codigo_cliente)) {
                    	$assinaturas = ClientesController::getClienteAssinatura($codigo_cliente);

	                    if (!empty($assinaturas)) {

	                        if (in_array("PLANO_DE_ACAO", $assinaturas)) {
	                            $menu_modulos .= $this->BMenu->link('Plano de Ação',array('controller'=>'painel','action'=>'modulo_plano_de_acao'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::PLANO_DE_ACAO?'active':'') , 'title' =>'Módulo Plano de Ação' ));
	                        }

	                       if (in_array("SAFETY_WALK_TALK", $assinaturas)) {
	                           $menu_modulos .= $this->BMenu->link('Walk & Talk',array('controller'=>'painel','action'=>'modulo_walk_talk'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::WALK_TALK?'active':'') , 'title' =>'Módulo Walk & Talk' ));
	                       }

	                        if (in_array("OBSERVADOR_EHS", $assinaturas)) {
	                            $menu_modulos .= $this->BMenu->link('Obs',array('controller'=>'painel','action'=>'modulo_observador_ehs'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::OBSERVADOR_EHS?'active':'') , 'title' =>'Módulo Observador EHS' ));
	                        }

	                    }
                	}
                	else {

                		$menu_modulos .= $this->BMenu->link('Plano de Ação',array('controller'=>'painel','action'=>'modulo_plano_de_acao'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::PLANO_DE_ACAO?'active':'') , 'title' =>'Módulo Plano de Ação' ));
	                    $menu_modulos .= $this->BMenu->link('Walk & Talk',array('controller'=>'painel','action'=>'modulo_walk_talk'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::WALK_TALK?'active':'') , 'title' =>'Módulo Walk & Talk' ));

	                    $menu_modulos .= $this->BMenu->link('Obs',array('controller'=>'painel','action'=>'modulo_observador_ehs'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::OBSERVADOR_EHS?'active':'') , 'title' =>'Módulo Observador EHS' ));

                	}

                }
            }
            ?>

			<?php if(!$authUsuario['Usuario']['codigo_proposta_credenciamento'] && !$authUsuario['Usuario']['codigo_fornecedor'] && $authUsuario['Usuario']['codigo_empresa'] =='1') : ?>

				<?php

					if(Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) :
			            $setHost = 'http://ocomon.buonny.com.br/';
			        elseif(Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO) :
			            $setHost = 'http://tstocomon.buonny.com.br/';
			        else :
			        	$setHost = 'http://tstocomon.buonny.com.br';
					endif;

					$categoria = '';
					//$menu_modulos .= $this->Html->tag('li', $this->Html->link('Help Desk', $setHost, array('title' =>'Help Desk', 'target'=>'_blank')));
				?>
			<?php endif; ?>

			<?php echo $menu_modulos;?>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>
</div>

<div class="subnav subnav-fixed">

	<div class="container" style="width: 1278px;">

		<?php if ($modulo_selecionado==Modulo::ADMIN && (isset($authUsuario['Usuario']) && empty($authUsuario['Usuario']['codigo_empresa']))):?>
			<?php echo $this->element('menu_admin'); ?>
		<?php elseif($modulo_selecionado==Modulo::ADMINISTRATIVO):?>
			<?php echo $this->element('menu_administrativo'); ?>
		<?php elseif ($modulo_selecionado==Modulo::FINANCEIRO): ?>
				<?php echo $this->element('menu_financeiro'); ?>
		<?php elseif ($modulo_selecionado==Modulo::COMERCIAL): ?>
			<?php echo $this->element('menu_comercial'); ?>
		<?php elseif ($modulo_selecionado==Modulo::GESTAOCONTRATOS): ?>
			<?php echo $this->element('menu_gestao_contratos'); ?>
		<?php elseif ($modulo_selecionado==Modulo::CREDENCIAMENTO): ?>
			<?php echo $this->element('menu_credenciamento'); ?>
		<?php elseif ($modulo_selecionado==Modulo::AGENDA): ?>
			<?php echo $this->element('menu_agenda'); ?>
		<?php elseif ($modulo_selecionado==Modulo::SAUDE): ?>
			<?php echo $this->element('menu_saude'); ?>
		<?php elseif ($modulo_selecionado==Modulo::SEGURANCA): ?>
			<?php echo $this->element('menu_seguranca'); ?>
		<?php elseif ($modulo_selecionado==Modulo::MAPEAMENTORISCO): ?>
			<?php echo $this->element('menu_mapeamento_risco'); ?>
		<?php elseif ($modulo_selecionado==Modulo::ESOCIAL): ?>
			<?php echo $this->element('menu_e_social'); ?>
		<?php elseif ($modulo_selecionado==Modulo::COVID): ?>
			<?php echo $this->element('menu_covid'); ?>
		<?php elseif ($modulo_selecionado==Modulo::GESTAODOCUMENTOS): ?>
			<?php echo $this->element('menu_gestao_documentos'); ?>
		<?php elseif ($modulo_selecionado==Modulo::CONTASMEDICAS): ?>
			<?php echo $this->element('menu_contas_medicas'); ?>
        <?php elseif ($modulo_selecionado==Modulo::PLANO_DE_ACAO): ?>
            <?php echo $this->element('menu_plano_de_acao'); ?>
        <?php elseif ($modulo_selecionado==Modulo::WALK_TALK): ?>
            <?php echo $this->element('menu_walk_talk');?>
        <?php elseif ($modulo_selecionado==Modulo::OBSERVADOR_EHS): ?>
            <?php echo $this->element('menu_observador_ehs');?>

		<?php endif;?>

		<!-- <span style="position: absolute; float: right; right: 397px; bottom: 5px;" class="pull-right margin-top-12"> -->
			<!-- GTranslate: https://gtranslate.io/ -->
			<!-- <style type="text/css"> -->
			<!--
			/*#goog-gt-tt {display:none !important;}
			.goog-te-banner-frame {display:none !important;}
			.goog-te-menu-value:hover {text-decoration:none !important;}
			.goog-te-gadget-icon {background-image:url(//gtranslate.net/flags/gt_logo_19x19.gif) !important;background-position:0 0 !important;}
			body {top:0 !important;}*/
			-->
			<!-- </style> -->
			<!-- <div id="google_translate_element"></div> -->
			<script type="text/javascript">
			//function googleTranslateElementInit() {new google.translate.TranslateElement({pageLanguage: 'pt', layout: google.translate.TranslateElement.InlineLayout.SIMPLE,autoDisplay: false, includedLanguages: 'pt,en,es'}, 'google_translate_element');}
			</script>
			<!-- <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script> -->
			<!-- GTranslate: https://gtranslate.io/ -->
		<!-- </span> -->


		<span style="position: absolute; float: right; right: 40px" class="pull-right margin-top-12 ajuda"><a href="https://rhhealth1.freshdesk.com/support/solutions/32000023075" target="_blank">Help</a></span>

		<span style="float: right; padding-right: 40px">

			<?php if(isset($authUsuario['Usuario']['codigo_cliente']) && $authUsuario['Usuario']['codigo_cliente']) : ?>

    	    	<?php echo $this->Html->link($authUsuario['Usuario']['codigo_cliente'] . ' - ' . $this->Text->truncate($authUsuario['Usuario']['nome_cliente'], 25,
    	    			array(
    	    				'ellipsis' => '...',
    	    				'exact' => false
    	    			)),
    	    			array(
    	    				'controller' => 'usuarios_multi_cliente',
    	    				'action' => 'selecionar_cliente'
    	    			),
    	    			array(
    	    				'data-placement' => 'bottom',
    	    				'data-toggle' => 'tooltip',
    	    				'title' => $authUsuario['Usuario']['nome_cliente'],
    	    				'class' => 'botao-cliente'
    	    			)
    	    		) ?>

    	    	<?php echo $this->Html->link('[trocar]', array('controller' => 'usuarios_multi_cliente', 'action' => 'selecionar_cliente'), array('class' => 'botao-cliente')) ?>
			<?php endif; ?>

    	    <?php if(isset($authUsuario['Usuario']['nome_empresa']) && !empty($authUsuario['Usuario']['nome_empresa'])) : ?>

    	    	<?php echo $this->Html->link($authUsuario['Usuario']['codigo_empresa'] . ' - ' . $this->Text->truncate($authUsuario['Usuario']['nome_empresa'], 25,
    	    		array(
    	    			'ellipsis' => '...',
    	    			'exact' => false
    	    		)),
    	    		array(
    	    			'controller' => 'multi_empresas',
    	    			'action' => 'selecionar_empresa'
    	    		),
    	    		array(
    	    			'data-placement' => 'bottom',
    	    			'data-toggle' => 'tooltip',
    	    			'title' => $authUsuario['Usuario']['nome_empresa'],
    	    			'class' => 'label label-inverse', 'style' => 'margin: 7px 0 0 0; padding: 5px;'
    	    		)
    	    	) ?>

    	    	<?php echo $this->Html->link('X', array('controller' => 'multi_empresas', 'action' => 'limpa_empresa'), array('class' => 'label label-danger', 'style' => 'margin: 7px 0 0 0; padding: 5px;')) ?>
    	    <?php else : ?>

        		<?php if(($authUsuario['Usuario']['codigo_uperfil'] == '1' && empty($authUsuario['Usuario']['codigo_empresa'])) || ($authUsuario['Usuario']['usuario_multi_empresa'] == '1')) : ?>

       				<?php echo $this->Html->link('Alternar Entre Empresas', array('controller' => 'multi_empresas', 'action' => 'selecionar_empresa'), array('class' => 'label label-success', 'style' => 'margin: 7px 0 0 0; padding: 5px;')) ?>
        		<?php endif; ?>
    	    <?php endif; ?>
		</span>
	</div>
</div>
<?php
    if(isset($mensagem['MensagemDeAcesso']['mensagem'])):
		if(isset($_SERVER['HTTPS'])):
			$msg = str_replace('http', 'https',$mensagem['MensagemDeAcesso']['mensagem']);
		else:
			$msg = str_replace('https', 'http',$mensagem['MensagemDeAcesso']['mensagem']);
		endif;
		   $msg = str_replace("\r","",str_replace("\n","",str_replace("'", "\'", $msg)));
	?>
<?php endif ?>

<script>
	$(document).ready(function() {
		//Se é link do menu principal tem a tag painel no endereço e faz o efeito na Ajuda, senão não faz...
		var url = $(location).attr('href')
		if(url.indexOf('painel') != '-1'){
			//Faz a animação da fonte do Ajudar... Do tamanho 13px->3.5em e depois 3.5em->13px
			$('.ajuda').animate({'font-size': '3.5em'},1000).animate({'font-size':'13px'},500);
		}
	})
</script>
