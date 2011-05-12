
<div style="padding:10px;margin:50px auto;width:300px;border:1px #ccc solid">
<?php if (isset($message)):?><p class="error"><?php h($message); ?></p><?php endif;?>
	<form method="post">
	<table>
		<tr>
			<td><?php hm("admin"); ?>:</td>
			<td><input type="text" name="username" value="<?php echo $username;?>" style="width:150px"/></td>
		</tr>
		<tr>
			<td><?php hm("password"); ?>:</td>
			<td><input type="password" name="password" style="width:150px"/></td>
		</tr>
		<tr>
			<td><?php hm("language"); ?>:</td>
			<td><select name="lang" style="width:200px">
			<?php foreach ($languages as $code => $lang):?>
			<option value="<?php h($code);?>" <?php if(x("lang") == $code || (x("lang") ==""&&isset($_COOKIE["ROCK_LANG"])&&$_COOKIE["ROCK_LANG"]==$code)): ?>selected="selected"<?php endif;?> ><?php h($lang); ?></option>
			<?php endforeach;?>
			</select></td>
		</tr>
		<tr>
			<td><?php hm("alive"); ?>:</td>
			<td>
			<select name="expire" style="width:150px">
			<?php foreach ($expires as $long => $name):?>
			<option value="<?php h($long);?>"><?php h($name);?></option>
			<?php endforeach;?>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="<?php hm("loginandrock"); ?>"/></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="gap"></div>
				<ul>
					<li><?php hm("changeconfig"); ?></li>
					<li><?php hm("rockmongocredits"); ?></li>
				</ul>
				</td>
		</tr>
	</table>
	</form>
</div>