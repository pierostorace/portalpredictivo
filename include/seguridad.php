<?php
	if(isset($_SESSION['usuario']))
	{
		$usuario = $_SESSION['usuario'];
	}
	else
	{		
		?>
        	<script language="javascript" type="text/javascript">
				location.href="../../index.php?res=4";
			</script>
		<?php
	}
?>