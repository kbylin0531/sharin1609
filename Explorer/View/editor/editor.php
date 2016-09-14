<?php include_once TEMPLATE.'common/head.php'; ?>
<link href="./static/style/skin/<?php echo $config['user']['theme'];?>app_editor.css" rel="stylesheet" id='link_css_list'/>


<?php if(isset($_GET['project'])){?>
<style>.topbar{display: none;}.frame-header{top:0;}.frame-main{top:0px;}</style>
<?php } ?>

<body style="overflow:hidden;" oncontextmenu="return core.contextmenu();">
	<?php include(TEMPLATE.'common/navbar.html');?>
	<div class="frame-main">
		<div class='frame-left'>
			<div class="tools-left">
				<a class="home" href="#"  title='<?php echo $L['root_path'];?>'><i class="icon-home"></i></a>
				<a class="view" href="#"  title='<?php echo $L['manage_folder'];?>'><i class="icon-laptop"></i></a>
				<a class="folder" href="#" title='<?php echo $L['newfolder'];?>'><i class="icon-folder-close-alt"></i></a>
				<a class="file" href="#" title='<?php echo $L['newfile'];?>'><i class="icon-file-alt"></i></a>		
				<a class="refresh" href="#" title='<?php echo $L['refresh'];?>'><i class="icon-refresh"></i></a>		 
			</div>
			<ul id="folderList" class="ztree"></ul>
		</div><!-- / frame-left end-->
		<div class='frame-resize'></div>
		<div class='frame-right'>
			<div class="frame-right-main"  style="height:100%;padding:0;margin:0;">
				<div class="resizeMask"></div>
				<div class="messageBox"><div class="content"></div></div>
				<div class="menuTreeRoot"></div>
				<div class="menuTreeFolder"></div>
				<div class="menuTreeFile"></div>				
				<div class ='frame'>
					 <iframe name="OpenopenEditor" src="./<?php echo ENTRY_FILE; ?>?editor/edit" style="width:100%;height:100%;border:0;" frameborder=0></iframe>
				</div>	
			</div>
		</div><!-- / frame-right end-->
	</div><!-- / frame-main end-->
<?php include(TEMPLATE.'common/footer.html');?>
<script src="<?php echo STATIC_PATH;?>js/lib/seajs/sea.js"></script>
<script src="./<?php echo ENTRY_FILE; ?>?user/common_js#id=<?php echo rand_string(8);?>"></script>
<script type="text/javascript">
	G.project = "<?php echo (isset($_GET['project'])?$_GET['project']:'') ;?>";
	seajs.config({
		base: "<?php echo STATIC_PATH;?>js/",
		preload: ["lib/jquery-1.8.0.min"],
		map:[
			[ /^(.*\.(?:css|js))(.*)$/i,'$1$2?ver='+G.version]
		]
	});
	seajs.use("app/src/editor/main");
</script>
</body>
</html>
