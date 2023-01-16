<div style="text-align: center">
	<applet height="150" width="140"
		codebase='http://<?php echo $_SERVER['HTTP_HOST']?>/portal/jars'
		archive="dpotjni.jar?v=<?php echo time(); ?>,
	    	dpfpverification.jar?v=<?php echo time(); ?>,
	    	dpfpenrollment.jar?v=<?php echo time(); ?>,
	    	dpotapi.jar?v=<?php echo time(); ?>,
	    	jtds-1.2.5.jar?v=<?php echo time(); ?>,
			biometria.jar?v=<?php echo time(); ?>"
		code="biometria\Biometria.class"
	    style="border: 1px solid black">
	    <param name="separate_jvm" value="true" />
	    <param name="server" value="<?php echo $_SERVER['HTTP_HOST']?>" />
	    <param name="tipo_acao" value='Cadastro' />
	    <param name="usuario" value='<?php echo $codigo_usuario; ?>' />
	    <param name="ip" value='<?php echo $_SERVER['REMOTE_ADDR']; ?>' />
	</applet>
</div>