<?php 
if(isset($info)){
    $retHora = trim(substr($info[0], 11,17));
    $retData = substr($info[0], 0,10);
    $dataExtenso = $retHora.' do dia '.$retData;
}
?>
<style type="text/css">
.boxMensagem {
    background: none repeat scroll 0 0 #EFEFFF;
    border: 5px solid #CCDDFF;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) inset;
    float: left;
    height: 500px;
    margin: 20px 0 0;
    padding: 20px 40px;
    position: relative;
    width: 420px;
}

.boxImg {
    float: left;
    height: 200px;
    margin: 0 auto;
    position: relative;
    width: 420px;
    z-index: 1;
}
.boxImg img {
    height: 128px;
    left: 50%;
    margin: -64px 0 0 -64px !important;
    top: 40%;
    width: 128px;
}
.boxTexto {
    float: left;
    height: 200px;
    margin: 0 auto;
    position: relative;
    width: 420px;
}
.boxTexto h1 {
	font-size: 3em !important;
    margin: -36px 0 0;
    position: absolute;
    text-align: center;
    text-transform: uppercase;
    top: 50%;
    width: 100%;
}
.boxTexto p {line-height: 25px;}
.boxOperador {
    float: left;
    height: 100px;
    margin: 0 auto;
    overflow: hidden;
    position: relative;
    width: 420px;
}
.boxOperador div {
    height: 20px;
    position: relative;
    text-align: right;
    top: 80%;
    width: 100%;
}
.negrito { font-weight: bold; }
</style>
<div id="login">
    <h1>Portal</h1>
    <div class='boxMensagem'>
        <div class="boxImg"><?php echo $this->Html->image('icone_manutencao.png'); ?></div>
        <div class="boxTexto">
            <p class="negrito">Prezados Clientes</p>
            <p>Informamos que o sistema se encontra em <span class="negrito">manutenção</span>, devendo retornar as <?php echo isset($info) ? $dataExtenso : '';?>.</p>
            <p>Agradecemos a compreensão</p>
            <p class="negrito">Equipe TI .: IT Health</p>
        </div>
        <div class="boxOperador"><!--div>Operador: <?php //echo isset($info) ? $info[1] : ''; ?></div--></div>
    </div>
    <span class='pull-right'>
        <?php   echo $this->BForm->create('Usuario', array('action' => 'login', 'class' => 'well')); ?>
        <div class="row-fluid">
            <p>Login</p>
            <?php   echo $this->BForm->hidden('Usuario.adendo', array('value' => isset($adendo) ? $adendo: 0)) ?>
            <?php   echo $this->BForm->input('Usuario.apelido', array('label' => 'Usuario', 'class' => 'input-large')); ?>
            <?php   echo $this->BForm->input('Usuario.senha', array('label' => 'Senha', 'type' => 'password', 'class' => 'input-large')); ?>
            <?php   echo $this->BForm->submit('Acessar', array('div' => false, 'class' => 'btn btn-primary')); ?>
            &nbsp;
            <?php 
                $host = ((Ambiente::getServidor()==Ambiente::SERVIDOR_PRODUCAO) ? 'portal.buonny.com.br' : ((Ambiente::getServidor()==Ambiente::SERVIDOR_HOMOLOGACAO) ? 'tstportal.buonny.com.br' : 
                    /*'portal.localhost'));*/ $_SERVER['HTTP_HOST']));
                echo $html->link('Esqueci minha senha', "http://{$host}/portal/usuarios/recuperar_senha_cliente");
            ?>

        </div>
        <?php   echo $this->BForm->end(); ?>
    </span>
</div>

