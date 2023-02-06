<div class="block-olt">
	<div class="block-olt-img">
		<a href="/?do=detail&act=olt&id={id}"><img src="../style/device/{photomodel}"></a>	
	</div>
	<div class="block-olt-content">
		<h1>{place}</h1>
		<div class="listcheck">
			<span><img src="../style/img/netip.png">IP<b>{netip}</b></span>
			{sn}{mac}{countonu}{countport}
		</div>		
		{blockonline}	
		{blockoffline}
		<div class="listcheck">
		{interval}
		{updates}
		</div>
		<!---
		<div class="listcheck">
		{interval}
		{updates}
		{updates_port}
		{updates_rx}
		{timecheck}
		{timechecklast}
		</div>
		-->
	</div>	
	<div class="block-olt-content">
		<div class="listcheck">
		{script_device_ajax}
		<div id="ajaxdevice"></div>	
		</div>	
	</div>
</div>
{panel}