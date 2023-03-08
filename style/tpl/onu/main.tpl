<div id="onu-speedbar">
<a class="brmhref" href="/?do=detail&act=olt&id={olt_id}"><i class="fi fi-rr-apps"></i>{olt_place}</a>
<a class="brmhref" href="/?do=terminal&id={olt_id}&port={port_id}"><i class="fi fi-rr-angle-left"></i>{olt_port_ont}</a>
<span class="brmspan"><i class="fi fi-rr-angle-left"></i>{inface_ont}</span>
</div>
<div id="panel-ont"></div>
<div id="onu">
<div class="ont-sys">
<div class="ontmac">{number_ont}</div>
<div class="ontinface"><span class="n">{type_ont}</span><span class="m">{inface}</span></div>
{monitoronu}
<div class="ont-panel">{telnet}</div>
</div>
<div class="onu-inf">
	<div class="onu-data">
		<div id="ont"><img src="../style/img/load.gif"></div>
	</div>		
	<div class="onu-olt">
		<div class="ont-block">
			<div class="ont-content">
				<div class="olt-data"><span class="name">{olt}:</span><span class="data">{olt_place}</span></div>						
				<div class="olt-data"><span class="name">{model}:</span><span class="data">{olt_model}</span></div>	
				<div class="olt-data"><span class="name">{uptimeolt}:</span><span class="data">{olt_updates}</span></div>			
				{netip}					
				<div class="olt-data"><span class="name">{port}:</span><span class="data">{olt_port_ont}</span></div>					
				<div class="olt-data"><span class="name">{supportcountonu}:</span><span class="data">1:{supportonuport}</span></div>					
				<div class="olt-data"><span class="name">{langcountonu}:</span><span class="data">{countonuport}</span></div>					
			</div>
		</div>		
		<div id="ajax-billing"></div>		
		<div class="ont-base">{billing}{tag}{comments}{logonu}</div>
	</div>
</div>	
<div id="ont-{id}"></div>
</div>
<script type="text/javascript">
ajaxont({id});
ajaxpanel({id});
{ajaxbilling}
</script>
