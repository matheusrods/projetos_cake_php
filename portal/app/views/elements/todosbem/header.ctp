<header>
    <div class="header">
        <div class="container">
            <button type="button" class="navbar-toggle btn" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="glyphicon glyphicon-align-justify btnMenu"></span>            
            </button>            
            <h1 class="text-muted pull-left">
            <?php echo $this->Html->image('logo-rhhealth.png', array('alt' => 'Logo Todos Bem', 'url' => '/')) ?>                
            </h1>            
            <div class="collapse navbar-collapse">
                <nav>
                    <ul class="nav pull-right">
                       	<li><?php echo $this->Html->link('Cadastre-se', array('controller' => 'dados_saude', 'action' => 'cadastro')); ?></li>    
                        <li class="dropdown right" id="menu1">                            
                            <a class="dropdown-toggle item-last mLoginNormal" data-toggle="dropdown" href="#menu1">
                                Login
                                <b class="caret"></b>
                            </a>                            
                            <div class="dropdown-menu">
                                <span class="caretUp"></span>
                                <?php echo $this->element('todosbem/form_login'); ?>
                            </div>
                        </li> 
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>