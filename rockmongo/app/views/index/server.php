<div class="operation">
	<?php render_server_ops("server"); ?>
</div>

<table bgcolor="#cccccc" cellpadding="2" cellspacing="1" width="600">
	<tr>
		<th colspan="2"><?php hm("command_line"); ?> (db.serverCmdLineOpts())</th>
	</tr>
	<tr bgcolor="#fffeee">
		<td colspan="2"><?php h($commandLine);?></td>
	</tr>
</table>
<div class="gap"></div>

<table bgcolor="#cccccc" cellpadding="2" cellspacing="1" width="600">
	<tr>
		<th colspan="2"><?php hm("connection"); ?></th>
	</tr>
	<?php foreach ($connections as $param=>$value):?>
	<tr bgcolor="#fffeee">
		<td width="120" ><?php h($param);?></td>
		<td><?php h($value);?></td>
	</tr>
	<?php endforeach; ?>
</table>
<div class="gap"></div>

<table bgcolor="#cccccc" cellpadding="2" cellspacing="1" width="600">
	<tr>
		<th colspan="2"><?php hm("web"); ?> <?php hm("server"); ?></th>
	</tr>
	<?php foreach ($webServers as $param=>$value):?>
	<tr bgcolor="#fffeee">
		<td width="120" ><?php h($param);?></td>
		<td><?php h($value);?></td>
	</tr>
	<?php endforeach; ?>
</table>
<div class="gap"></div>

<table bgcolor="#cccccc" cellpadding="2" cellspacing="1" width="600">
	<tr>
		<th colspan="3"><?php hm("directives"); ?></th>
	</tr>
	<tr bgcolor="#fffeee">
		<th><?php hm("directive"); ?></th>
		<th><?php hm("global_value"); ?></th>
		<th><?php hm("local_value"); ?></th>
	</tr>
	<?php foreach ($directives as $param=>$value):?>
	<tr bgcolor="#fffeee">
		<td width="200" ><?php h($param);?></td>
		<td><?php h($value["global_value"]);?></td>
		<td><?php h($value["local_value"]);?></td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="gap"></div>

<table bgcolor="#cccccc" cellpadding="2" cellspacing="1" width="600">
	<tr>
		<th colspan="2"><?php hm("build_info"); ?> ({buildinfo:1})</th>
	</tr>
	<?php foreach ($buildInfos as $param=>$value):?>
	<tr bgcolor="#fffeee">
		<td width="120" valign="top"><?php h($param);?></td>
		<td><?php h($value);?></td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="gap"></div>

