<div class="block-monitor"><div class="block-head"><h2>{setup}</h2></div><div class="block-center"><div id="billing">
	<div class="block-setup">
	<form action="/?do=send" method="post">
	<input name="act" type="hidden" value="savebilling">
		<div class="pole1">
			<div class="form1">{status}<b>{descrstatus}</b></div>
			<div class="form2">{selstbill}</div>
		</div>		
		<div class="pole1">
			<div class="form1">{type}<b>{typedescr}</b></div>
			<div class="form2">{selbill}</div>
		</div>		
		<div class="pole1">
			<div class="form1">{urlapi}<b>{urlpaidescr}</b></div>
			<div class="form2"><input required="" name="billingurl" class="input1" type="text" value="{billingurl}"></div>
		</div>		
		<div class="pole1">
			<div class="form1">{keyapi}<b>{keyapidescr}</b></div>
			<div class="form2"><input required="" name="billingapikey" class="input1" type="text" value="{billingapikey}"></div>
		</div>
		<div class="navigation mbottom20 mtop10px">
		<button type="submit" class="button" value="submit">{savesetup}</button>
		</div>
	</form>
	</div>
<div class="block-connect-list">
<div class="pole1">
<div class="form1">{alluid}<b>{alluiddescr}</b></div>
<div class="form2">...</div>
</div>	
</div>
</div></div></div>
