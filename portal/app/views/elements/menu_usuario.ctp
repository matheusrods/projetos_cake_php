<ul class="nav pull-right">
    <li class="dropdown">
  		<span style="margin: 0; padding: 0; font-size: 9px; line-height: 14px;">Tempo Restante:</span><br /> 
   		<b id="hora_sessao" style="font-size: 9px; margin: 0; line-height: 14px;"></b>
    </li>
</ul>

<ul class="nav pull-right">
	<?php if($authUsuario['Usuario']['alerta_portal']): ?>
		<?php //echo $this->BMenu->link('&nbsp;<i class="icon-warning-sign icon-white"></i>&nbsp;', 'obj_menu-alertas-pendentes', array('wrapper'=>'li id="menu_alerta"', 'title' =>'Alertas pendentes', 'escape'=>false)); ?>
	<?php endif; ?>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
   	    	<?php  echo (isset($authUsuario['Usuario']['displayname']) ? $this->Text->truncate($authUsuario['Usuario']['displayname'], 15, array('ending' => false, 'exact' => false)) : $this->Text->truncate($authUsuario['Usuario']['apelido'], 15, array('ending' => false, 'exact' => false))) ?>
            <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
        	<?php if($authUsuario['Usuario']['codigo_cliente']) : ?>
        		<li><?php echo $this->Html->link('Acessar Cliente', array('controller' => 'usuarios_multi_cliente', 'action' => 'selecionar_cliente')) ?></li>
        	<?php endif; ?>
        	        
        	<li><?php echo $this->Html->link('Minhas Configurações', array('controller' => 'usuarios', 'action' => 'minhas_configuracoes')) ?></li>
        	
        	<?php if (!empty($authUsuario['Usuario']['codigo_cliente'])): ?>
        		<li><?php echo $this->Html->link('Trocar Senha', array('controller' => 'usuarios', 'action' => 'trocar_senha')) ?></li>
                <?php if (isset($authUsuario['Usuario']['admin']) && $authUsuario['Usuario']['admin'] == 1): ?>
                    <li><?php echo $this->Html->link('Gerenciar Usuarios', array('controller' => 'usuarios', 'action' => 'index/minha_configuracao')) ?></li>
                    <li><?php echo $this->Html->link('Gerenciar Perfis', array('controller' => 'uperfis', 'action' => 'index')) ?></li>
                <?php endif ?>
        	<?php endif ?>

            <li><?php echo $this->Html->link('Processamentos', array('controller' => 'processamentos', 'action' => 'index')) ?></li>

            <li class="divider"></li>
            <li><?php echo $this->Html->link('Sair', array('controller' => 'usuarios', 'action' => 'logout')) ?></li>
        </ul>
    </li>
</ul> 

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setInterval(function(){ temporizador("'.$_SESSION['Auth']['Usuario']['max_login'].'"); }, 1000);
	});
		
	function temporizador(endtime){
	  	var t = Date.parse(endtime) - Date.parse(new Date());
		
	  	var seconds = ("0" + Math.floor( (t/1000) % 60 )).slice(-2);
	  	var minutes = ("0" + Math.floor( (t/1000/60) % 60 )).slice(-2);
	  	var hours = ("0" + Math.floor( (t/(1000*60*60)) % 24 )).slice(-2);
		
		if(t == 0) {
			$("#hora_sessao").parents("span").html("Encerrando Sessão!");
            $.ajax({
                type: \'POST\',
                url: baseUrl + \'usuarios/logout_por_ajax/\' + Math.random(),
                beforeSend: function(){
                },
                success: function(data){
                    if(data == 1){
                        location.reload();
                        //no momento que ele deslogar no php, ele atualizar a pagina e ela voltara a pagina de login, esse antigo reload nao estava funcionando
                        // location.reload("/portal/usuarios/logout");
                    }
                },
                error: function(erro){
                }
            });
		} else {
			$("#hora_sessao").html(hours + ":" + minutes + ":" + seconds);
		}
	}
'); ?>
