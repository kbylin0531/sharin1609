<?php include_once TEMPLATE.'common/head.php'; ?>
<link href="./static/style/skin/simple/app_code_edit.css" rel="stylesheet" id='link_css_list'/>
	
<style type="text/css">
	body{
		-khtml-user-select: all;
	  -webkit-user-select: all;
	  -moz-user-select: all;
	  -ms-user-select: all;
	  -o-user-select: all;
	  user-select: all;
	}
</style>
<body style="overflow:hidden;">
	<?php include(TEMPLATE.'common/navbar_share.html');?>
	<div class="frame-main">
		<?php if($msg=='password'){?>
		<div class="share_page_passowrd">
			<b><?php echo $L['share_password'];?>:</b>
			<input type="text" class="form-control"/>
			<a href="javascript:void(0);" class="btn btn-primary share_login"><?php echo $L['button_ok'];?></a>
		</div>
		<?php }else{?>
		<div class="share_page_error"><b>tips:</b><?php echo $msg;?></div>
		<?php }?>		
	</div><!-- / frame-main end-->
<?php include(TEMPLATE.'common/footer.html');?>
<script src="<?php echo STATIC_PATH;?>js/lib/seajs/sea.js?ver=<?php echo KOD_VERSION;?>"></script>
<script src="./<?php echo ENTRY_FILE; ?>?share/common_js&user=<?php echo $_GET['user'];?>&sid=<?php echo $_GET['sid'];?>&#=<?php echo rand_string(8);?>"></script>
<script type="text/javascript">
	seajs.config({
		base: "<?php echo STATIC_PATH;?>js/",
		preload: ["lib/jquery-1.8.0.min"],
		map:[
			[ /^(.*\.(?:css|js))(.*)$/i,'$1$2?ver='+G.version]
		]
	});
	seajs.use("app/src/share_index/main");
</script>
</body>
</html>
