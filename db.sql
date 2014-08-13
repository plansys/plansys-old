<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<title>Export: dsi - Adminer</title>
<link rel="stylesheet" type="text/css" href="adminer.php?file=default.css&amp;version=4.1.0">
<script type="text/javascript" src="adminer.php?file=functions.js&amp;version=4.1.0"></script>
<link rel="shortcut icon" type="image/x-icon" href="adminer.php?file=favicon.ico&amp;version=4.1.0">
<link rel="apple-touch-icon" href="adminer.php?file=favicon.ico&amp;version=4.1.0">

<body class="ltr nojs" onkeydown="bodyKeydown(event);" onclick="bodyClick(event);">
<script type="text/javascript">
document.body.className = document.body.className.replace(/ nojs/, ' js');
</script>

<div id="help" class="jush-sql jsonly hidden" onmouseover="helpOpen = 1;" onmouseout="helpMouseout(this, event);"></div>

<div id="content">
<p id="breadcrumb"><a href="adminer.php">MySQL</a> &raquo; <a href='adminer.php?username=root' accesskey='1' title='Alt+Shift+1'>Server</a> &raquo; <a href="adminer.php?username=root&amp;db=dsi">dsi</a> &raquo; Export
<h2>Export: dsi</h2>

<form action="" method="post">
<table cellspacing="0">
<tr><th>Output<td><label><input type='radio' name='output' value='text' checked>open</label><label><input type='radio' name='output' value='file'>save</label><label><input type='radio' name='output' value='gz'>gzip</label>
<tr><th>Format<td><label><input type='radio' name='format' value='sql' checked>SQL</label><label><input type='radio' name='format' value='csv'>CSV,</label><label><input type='radio' name='format' value='csv;'>CSV;</label><label><input type='radio' name='format' value='tsv'>TSV</label>
<tr><th>Database<td><select name='db_style'><option selected><option>USE<option>DROP+CREATE<option>CREATE</select><label><input type='checkbox' name='routines' value='1' checked>Routines</label><label><input type='checkbox' name='events' value='1' checked>Events</label><tr><th>Tables<td><select name='table_style'><option><option selected>DROP+CREATE<option>CREATE</select><label><input type='checkbox' name='auto_increment' value='1'>Auto Increment</label><label><input type='checkbox' name='triggers' value='1' checked>Triggers</label><tr><th>Data<td><select name='data_style'><option><option>TRUNCATE+INSERT<option selected>INSERT<option>INSERT+UPDATE</select></table>
<p><input type="submit" value="Export">
<input type="hidden" name="token" value="612432:752411">

<table cellspacing="0">
<thead><tr><th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables' checked onclick='formCheck(this, /^tables\[/);'>Tables</label><th style='text-align: right;'><label class='block'>Data<input type='checkbox' id='check-data' checked onclick='formCheck(this, /^data\[/);'></label></thead>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='audit_trail' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">audit_trail</label><td align='right'><label class='block'><span id='Rows-audit_trail'></span><input type='checkbox' name='data[]' value='audit_trail' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='lubang' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">lubang</label><td align='right'><label class='block'><span id='Rows-lubang'></span><input type='checkbox' name='data[]' value='lubang' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='pemboran' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">pemboran</label><td align='right'><label class='block'><span id='Rows-pemboran'></span><input type='checkbox' name='data[]' value='pemboran' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='proyek' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">proyek</label><td align='right'><label class='block'><span id='Rows-proyek'></span><input type='checkbox' name='data[]' value='proyek' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='regu' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">regu</label><td align='right'><label class='block'><span id='Rows-regu'></span><input type='checkbox' name='data[]' value='regu' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='report' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">report</label><td align='right'><label class='block'><span id='Rows-report'></span><input type='checkbox' name='data[]' value='report' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='test' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">test</label><td align='right'><label class='block'><span id='Rows-test'></span><input type='checkbox' name='data[]' value='test' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='user' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">user</label><td align='right'><label class='block'><span id='Rows-user'></span><input type='checkbox' name='data[]' value='user' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<tr><td><label class='block'><input type='checkbox' name='tables[]' value='wilayah' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-tables&#039;);">wilayah</label><td align='right'><label class='block'><span id='Rows-wilayah'></span><input type='checkbox' name='data[]' value='wilayah' checked onclick="checkboxClick(event, this); formUncheck(&#039;check-data&#039;);"></label>
<script type='text/javascript'>ajaxSetHtml('adminer.php?username=root&db=dsi&script=db');</script>
</table>
</form>
</div>

<form action='' method='post'>
<div id='lang'>Language: <select name='lang' onchange="this.form.submit();"><option value="en" selected>English<option value="ar">العربية<option value="bn">বাংলা<option value="ca">Català<option value="cs">Čeština<option value="de">Deutsch<option value="es">Español<option value="et">Eesti<option value="fa">فارسی<option value="fr">Français<option value="hu">Magyar<option value="id">Bahasa Indonesia<option value="it">Italiano<option value="ja">日本語<option value="ko">한국어<option value="lt">Lietuvių<option value="nl">Nederlands<option value="no">Norsk<option value="pl">Polski<option value="pt">Português<option value="pt-br">Português (Brazil)<option value="ro">Limba Română<option value="ru">Русский язык<option value="sk">Slovenčina<option value="sl">Slovenski<option value="sr">Српски<option value="ta">த‌மிழ்<option value="th">ภาษาไทย<option value="tr">Türkçe<option value="uk">Українська<option value="vi">Tiếng Việt<option value="zh">简体中文<option value="zh-tw">繁體中文</select> <input type='submit' value='Use' class='hidden'>
<input type='hidden' name='token' value='669091:530152'>
</div>
</form>
<form action="" method="post">
<p class="logout">
<input type="submit" name="logout" value="Logout" id="logout">
<input type="hidden" name="token" value="612432:752411">
</p>
</form>
<div id="menu">
<h1>
<a href='http://www.adminer.org/' target='_blank' id='h1'>Adminer</a> <span class="version">4.1.0</span>
<a href="http://www.adminer.org/#download" target="_blank" id="version"></a>
</h1>
<script type="text/javascript" src="adminer.php?file=jush.js&amp;version=4.1.0"></script>
<script type="text/javascript">
var jushLinks = { sql: [ 'adminer.php?username=root&db=dsi&table=$&', /\b(audit_trail|lubang|pemboran|proyek|regu|report|test|user|wilayah)\b/g ] };
jushLinks.bac = jushLinks.sql;
jushLinks.bra = jushLinks.sql;
jushLinks.sqlite_quo = jushLinks.sql;
jushLinks.mssql_bra = jushLinks.sql;
bodyLoad('5.6');
</script>
<form action="">
<p id="dbs">
<input type="hidden" name="username" value="root"><span title='database'>DB</span>: <select name='db' onmousedown='dbMouseDown(event, this);' onchange='dbChange(this);'><option value=""><option>information_schema<option>cdcol<option>con5<option selected>dsi<option>mobi<option>mobiwp<option>mysql<option>performance_schema<option>phpmyadmin<option>pju<option>test<option>webauth</select><input type='submit' value='Use' class='hidden'>
<input type="hidden" name="dump" value=""></p></form>
<p class='links'><a href='adminer.php?username=root&amp;db=dsi&amp;sql='>SQL command</a>
<a href='adminer.php?username=root&amp;db=dsi&amp;import='>Import</a>
<a href='adminer.php?username=root&amp;db=dsi&amp;dump=' id='dump' class='active '>Dump</a>
<a href="adminer.php?username=root&amp;db=dsi&amp;create=">Create table</a>
<p id='tables' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=audit_trail">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=audit_trail" title='Show structure'>audit_trail</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=lubang">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=lubang" title='Show structure'>lubang</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=pemboran">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=pemboran" title='Show structure'>pemboran</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=proyek">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=proyek" title='Show structure'>proyek</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=regu">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=regu" title='Show structure'>regu</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=report">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=report" title='Show structure'>report</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=test">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=test" title='Show structure'>test</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=user">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=user" title='Show structure'>user</a><br>
<a href="adminer.php?username=root&amp;db=dsi&amp;select=wilayah">select</a> <a href="adminer.php?username=root&amp;db=dsi&amp;table=wilayah" title='Show structure'>wilayah</a><br>
</div>
<script type="text/javascript">setupSubmitHighlight(document);</script>
