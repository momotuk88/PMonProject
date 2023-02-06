<div id="onu-speedbar">
<a class="brmhref" href="/?do=detail&act=olt&id={olt_id}"><i class="fi fi-rr-apps"></i>{olt_place}</a>
<a class="brmhref" href="/?do=terminal&id={olt_id}&port={port_id}"><i class="fi fi-rr-angle-left"></i>{olt_port_ont}</a>
<a class="brmhref" href="/?do=onu&id={id}"><i class="fi fi-rr-angle-left"></i>{inface_ont}</a>
<span class="brmspan"><i class="fi fi-rr-angle-left"></i>Графік сигналів</span>
</div>
<div id="panel-ont"></div>
<div id="onu">
<div class="ont-sys">
<div class="ontmac">{number_ont}</div>
<div class="ontinface"><span class="n">{type_ont}</span><span class="m">{inface}</span></div>
</div>
<div id="container" style="height: 400px; min-width: 600px"></div>
{jsgraph}
<script src="../style/js/highstock.js"></script>
<script src="../style/js/exporting.js"></script>

