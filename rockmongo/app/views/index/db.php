<script language="javascript">

/** show more menus **/
function showMoreMenus(link) {
	var obj = $(link);
	setManualPosition(".menu", obj.position().left, obj.position().top + obj.height() + 4);
}


/** show manual links **/
function setManualPosition(className, x, y) {
	if ($(className).is(":visible")) {
		$(className).hide();
	}
	else {
		window.setTimeout(function () {
			$(className).show();
			$(className).css("left", x);
			$(className).css("top", y)
		}, 100);
		$(className).find("a").click(function () {
			hideMenus();
		});
	}
}
 
/** hide menus **/
function hideMenus() {
	$(".menu").hide();
}

$(function () {
	$(document).click(hideMenus);
});
</script>

<h3><?php render_navigation($db); ?></h3>

<div class="operation">
	<strong><?php hm("statistics"); ?></strong> | 
	<a href="<?php h(url("newCollection",array("db"=>$db))); ?>"><?php hm("create_collection"); ?></a> | 
	<a href="<?php h(url("command",array("db"=>$db))); ?>"><?php hm("command"); ?></a> | 
	<a href="<?php h(url("execute",array("db"=>$db))); ?>"><?php hm("execute"); ?></a> |
	<a href="<?php h(url("dbTransfer",array("db"=>$db))); ?>"><?php hm("transfer");?></a> |
	<a href="<?php h(url("dbExport",array("db"=>$db))); ?>"><?php hm("export"); ?></a> |
	<a href="<?php h(url("dbImport",array("db"=>$db))); ?>"><?php hm("import"); ?></a> |
	<a href="#" onclick="showMoreMenus(this);return false;"><?php hm("more");?> &raquo;</a> 
	<div class="menu">
		<a href="<?php h(url("dbTransfer",array("db"=>$db))); ?>"><?php hm("transfer");?></a><br/>
		<a href="<?php h(url("profile",array("db"=>$db))); ?>"><?php hm("profile"); ?></a><br/>
		<a href="<?php h(url("repairDatabase", array("db"=>$db))); ?>" onclick="return window.confirm('<?php hm("repairdbmsg"); ?> <?php h($db);?>?');"><?php hm("repair"); ?></a><br/>
		<a href="<?php h(url("auth",array("db"=>$db))); ?>"><?php hm("authentication"); ?></a><br/>
		<a href="<?php h(url("dropDatabase", array("db"=>$db))); ?>" onclick="return window.confirm('<?php hm("dropwarning"); ?> <?php h($db);?>? <?php hm("dropwarning2"); ?>');"><?php hm("drop"); ?></a> 
	</div>
</div>

<table bgcolor="#cccccc" cellpadding="2" cellspacing="1" width="600">
	<?php foreach ($stats as $param=>$value):?>
	<tr bgcolor="#fffeee">
		<td width="120" valign="top"><?php h($param);?></td>
		<td><?php h($value);?></td>
	</tr>
	<?php endforeach; ?>
</table>