<h3><?php render_navigation($db); ?> &raquo; <?php hm("dropdatabase"); ?></h3>

<div style="border:2px #ccc solid;margin-bottom:5px;background-color:#eeefff">
<?php hm("responseserver"); ?>
	<div style="margin-top:5px">
		<?php h($ret);?>
	</div>
</div>

<p>
	<input type="button" value="<?php hm("gotodbs"); ?>" onclick="window.location='<?php h(url("databases")); ?>'"/>
</p>

<script language="javascript">
window.parent.frames["left"].location = "<?php h(url("dbs"));?>";
</script>