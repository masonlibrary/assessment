

<div id="dialog" title="<?php echo $_SESSION['dialogTitle']?>" >
	<?php  
        echo $_SESSION['dialogText'];
        $_SESSION['dialogText']=''; 
        $_SESSION['dialogTitle']='';
        ?>
</div>