<table class="view-unit" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="content-unit" width="80%">{list}</td>
		<td class="content-unit unit-list-device" width="20%">
			<div class="vyzolpanel">
				<span class="key-vyzol" onclick="ajaxponcore('add',{id});"><img src="../style/img/m11.png"><span>Встановити обладнання</span></span>		
				<span class="key-vyzol" onclick="ajaxponcore('addtree',{id});"><img src="../style/img/technology.png"><span>Створити Pon дерево</span></span>		
				<a href="/?do=pon&act=maper&id={id}" class="key-vyzol"><img src="../style/img/m6.png"><span>Карта вузла</span></a>		
				<a href="/?do=pon&act=myfta&id={id}" class="key-vyzol"><img src="../style/ponmap/myfta.png"><span>Муфти</span></a>		
				<span class="key-vyzol" onclick="ajaxponcore('addto',{id});"><img src="../style/img/addconnect.png"><span>Створити звіт ТО</span></span>
				<span class="key-vyzol" onclick="ajaxponcore('note',{id});"><img src="../style/img/img3.png"><span>Нотатки вузла</span></span>
				<span class="key-vyzol"><img src="../style/img/setting.png"><span>налаштування</span></span>
			</div>	
			<div class="zvit-unit">
			<h2><img src="../style/img/addconnect.png">Звіти ТО</h2>
			{zvit}
			</div>
		</td>
	</tr>	
</table>
<!---
<table class="view-unit" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="head-unit"><b>Обладання додаткове</b></td>
	</tr>
	<tr>
		<td class="content-unit insert-device">{insert}</td>
	</tr>	
</table>
-->
