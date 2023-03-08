var loadingImage = 'ajax-loader-big.gif';
var LoadingBar = '<div style="padding:20px;text-align:center;"><img src="../style/img/'+loadingImage+'" /></div>';
function startmaster(getid,init) {
	getid = parseInt(getid);		
	init = parseInt(init);		
	$('#master').html(LoadingBar);
	$.post(root+'?do=master',{init:init,getid:getid}, 
	function(response){ 
		$('#master').empty();
		$('#master').html(response);
	}, 'html');
}
function sendcomments(id){
	id = parseInt(id);
	var name = $('#commentonu').val();	
	$('#comment').html(LoadingBar);
    $.post(root+'ajax/commentonu.php',{'act':'save',id:id,name:name},function(response){
	$('#comment').empty();
	$('#comment').html(response);},'html');
}
function ajaxbilling(id){
	id = parseInt(id);
	$('#ajax-billing').html(LoadingBar);
    $.post(root+'ajax/billing.php',{id:id},function(response){
	$('#ajax-billing').empty();
	$('#ajax-billing').html(response);},'html');
}
function editcomments(id){
	id = parseInt(id);    
	$('#comment').html(LoadingBar);
	$.post(root+'ajax/commentonu.php',{'act':'edit',id:id},function(response){
	$('#comment').empty();
	$('#comment').html(response);},'html');
}
function historycomment(id){
	id = parseInt(id);   
	$('#btnhistorycomm').html(LoadingBar);	
	$.post(root+'ajax/commentonu.php',{'act':'list',id:id},function(response) { 
	$('#historycomment').html(response);},'html');
}
function ajaxmain(act,id) {
	$("#main-" + act).html(LoadingBar);
	$.post(root+"ajax/main.php",{act:act,id:id},function(response){ 
		$("#main-" + act).empty();
		$("#main-" + act).html(response);
	}, "html");
	setTimeout('ajaxmain('+act+','+id+');',10000);	
}
function funcpanel(act,idonu,id) {
	$("#block-ont-"+ idonu).html(LoadingBar);
	$.post(root+"ajax/function.php",{act:act,idonu:idonu,id:id}, 
	function(response){ 
		$("#block-ont-"+ idonu).empty();
		$("#block-ont-"+ idonu).html(response);
	}, 'html');
}

function ajaxont(id) {
	$('#ont').html(LoadingBar);
	$.post(root+"ajax/ont.php",{id:id},function(response){ 
		$('#ont').empty();
		$('#ont').html(response);
	}, "html");
	setTimeout('ajaxont('+id+');',5000000);	
}
function ajaxpanel(id) {
	$('#panel-ont').html(LoadingBar);
	$.post(root+"ajax/panel.php",{id:id},
		function(response){ 
			$('#panel-ont').empty();
			$('#panel-ont').html(response);
		}, "html");
	setTimeout('ajaxpanel('+id+');',5000000);	
}
function ajaxdevicestatus(id) {
	$('#ajaxdevice').html(LoadingBar);
	$.post(root+"ajax/status.php",{id:id},function(response){ 
		$('#ajaxdevice').empty();
		$("#ajaxdevice").html(response);
	}, "html");
	setTimeout('ajaxdevicestatus('+id+');',50000);	
}
function ajaxuid(act,id) {
	$.post(root+"ajax/comment.php",{act:act,id:id},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function copymac(copyText) {
  alert("Скопіював " + copyText);
}
function Realontdata(id) {
	$(location).attr('href');
	location.reload(); 
}
function convertFormToJSON(form) {
    const array = $(form).serializeArray();
    const json = {};
    $.each(array, function () {
        json[this.name] = this.value || "";
    });
    return json;
}
function nextregonuzte(id) {
	$("#regonu-" + id).hide();
	$("#load_info").show();
	const json = convertFormToJSON("#regonu-" + id);
	$.post(root+"ajax/olt.php",{act:'nextregonuzte',data:json},function(response){ 
	$("#load_info").hide();
	$("#nextreg-" + id).html(response);
	}, "html");
}
function regonuzte(id) {
	$("#nextreg-" + id).hide();
	$("#load_info").show();
	const json = convertFormToJSON("#goregonu-" + id);
	$.post(root+"ajax/olt.php",{act:'regonuzte',data:json},function(response){ 
	$("#load_info").hide();
	$("#nextreg-" + id).html(response);
	}, "html");
}
function ajaxzte320(id) {
	$.post(root+"ajax/ontzte320.php",{id:id},function(response){ 
	$("#ajaxzte320").html(response);
	}, "html");
	setTimeout('ajaxzte320('+id+');',500000);	
}
function ajaxhuawei5608(id) {
	$.post(root+"ajax/onthuawei5608.php",{id:id},function(response){ 
	$("#onthuawei5608").html(response);
	}, "html");
	setTimeout('onthuawei5608('+id+');',500000);	
}
function ajaxcmdpanel(act,onu) {
	$("#loading").show();
	$.post(root+"ajax/cmd.php",{act:act,onu:onu},function(response){ 
	$("#ajax").html(response);
	$("#loading").hide();
	}, "html");	
}
function ajaxswitch(olt,act) {
	$.post(root+"ajax/switch.php",{act:act,olt:olt},function(response){ 
	$("#ajax").html(response);
	}, "html");	
}
function ajaxviewphoto(id) {
	$.post(root+"ajax/photo.php",{act:'view',id:id},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function fun_ajax(id,oidid,act) {
	$.post(root+"ajax/function.php",{act:act,id:id,oidid:oidid},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function ajaxcmd(act,olt,onu) {
	$.post(root+"ajax/cmd.php",{act:act,olt:olt,onu:onu},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function getolt(id) {
	$.post("system.php",{jobid:'3',olt:id});
	$("#ajaxolt").hide();
}
function bdcommaconu_ajax(id,idonu,act) {
	$("#loading").show();
	$.post(root+"ajax/function.php",{act:act,id:id,idonu:idonu},function(response){ 
	$("#ont-"+ idonu).html(response);
	}, "html");
	$("#loading").hide();
}
function snmpsetsave(id,act){
	var name = jQuery('#nameonu').val();
	$("#form_rename").hide();
	$.post(root+"ajax/function.php",{act:act,id:id,name:name},function(response){ 
	$("#resname").html(response);
		}, "html");
}
function showblockform1(){
	$(".zte_edit_name").hide();
	$("#form_rename").show();
}
function showblockform2(){
	$(".zte_edit_vlan").hide();
	$("#formeditvlan").show();
}
function showblockform3(){
	$(".zte_edit_note").hide();
	$("#form_renote").show();
}
function bdcomvlanonu_ajax(id,idonu,act) {
	$("#loading").show();
	var vlan = $('#vlan').val();
	$.post(root+"ajax/function.php",{act:act,id:id,idonu:idonu,vlan:vlan},function(response){ 
	$("#edit-vlan-"+ idonu).html(response);
		$("#btn-edit-vlan-"+ idonu).hide();
	}, "html");
	$("#loading").hide();
}
function rebootonu_ajax(id,idonu,act) {
	$.post(root+"ajax/function.php",{act:act,id:id,idonu:idonu});
	$(location).attr('href');
	location.reload(); 
}
function zteonufunreload(id,idonu,act) {
	$("#loading").show();
	$.post(root+"ajax/function.php",{act:act,id:id,idonu:idonu});
	setTimeout(sayHi,4000);
}
function zteonufunpage(id,idonu,act) {
	$("#loading").show();
	$("#block-ont-" + id).hide();
	$.post(root+"ajax/function.php",{act:act,id:id,idonu:idonu},
	function(response){ 
		$("#block-ont-"+ idonu).html(response);
	}, "html");
	$("#loading").hide(); 
}
function sayHi() {
	$(location).attr('href');
	location.reload(); 
}
function ajaxzteonuport(statusport,idonu,port) {
	$("#loading").show();
	$.post(root+"ajax/function.php",{act: statusport + 'zteport',idonu:idonu,port:port});
	setTimeout(sayHi, 11000);
}
function ajaxzteonu(act,onu,port,dia,view) {
	if (view === "href") {
		$.post(root+"ajax/ajaxzte.php",{act:act,onu:onu,port:port,dia:dia});
		$(location).attr('href');
		location.reload(); 	
	} else if (view === "popup"){
		$.post(root+"ajax/ajaxzte.php",{act:act,onu:onu,port:port,dia:dia},function(response){ 
			$("#ajax").html(response);
		}, "html");
	}else{
		$.post(root+"ajax/ajaxzte.php",{act:act,onu:onu,port:port,dia:dia},function(response){ 
			$("#js-"+ view).html(response);
		}, "html");
	}
}
function ztetvport(act,id) {
	$.post(root+"ajax/function.php",{act:act,id:id});
	$(location).attr('href');
	location.reload(); 
}
function ajaxhideonu() {
	$.post(root+"ajax/function.php",{act:'hideonu'});
	$(location).attr('href');
	location.reload(); 
}
function getolt(id) {
	$.post("system.php",{jobid:'3',olt:id});
	$("#ajaxolt").hide();
}
function ajaxponboxonu(act,ponboxid,onuid) {
	$.post(root+"ajax/unit.php",{act:act,ponboxid:ponboxid,onuid:onuid},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function ajaxponmodule(act,id,unitid) {
	$.post(root+"ajax/unit.php",{act:act,id:id,unitid:unitid},function(response){ 

	}, "html");
}
function ajaxponcore(act,id) {
	$.post(root+"ajax/unit.php",{act:act,id:id},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function ajaxpongeo(id,lan,lon) {
	$.post(root+"ajax/unit.php",{act:'getponbox',lan:lan,lon:lon,id:id},function(response){ 
	$("#result_box").html(response);
	}, "html");
}
function ajaxaddphoto(id) {
	$.post(root+"ajax/photo.php",{act:'add',id:id},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function ajaxsavegeoloc(id,lan,lon) {
	$.post(root+"?do=send",{act:'savegeolocation',id:id,lan:lan,lon:lon},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function ajaxontponbox(id) {
	$.post(root+"ajax/ponbox.php",{id:id},function(response){ 
	$("#ont-ponbox").html(response);
	}, "html");
	setTimeout('ajaxontponbox('+id+');',50000);	
}
function ajaxbillingdata(id) {
	$.post(root+"ajax/billing.php",{id:id},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function ajaxoid(act,id) {
	$.post(root+"ajax/oid.php",{act:act,id:id},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function ajaxRxGraphdata(id) {
	$.post(root+"ajax/graph.php",{act:'rx',id:id},function(response){ 
	$(".onu-inf-graph").html(response);
	}, "html");
}
function ajaxhistoryRXdata(id) {
	$.post(root+"ajax/history.php",{id:id},function(response){ 
	$("#ajax").html(response);
	}, "html");
}
function block_switch(id,userid) {
    var klappText = document.getElementById('sb' + id);
    var klappBild = document.getElementById('picb' + id);
    if (klappText.style.display == 'block') {
        klappBild.src = '/style/img/plus.gif';
        type = "hide";
    } else {
        klappBild.src = '/style/img/minus.gif';
        type = "show";
    }
	$.post(root+'ajax/port.php',{act:'hidden',userid:userid,id:id,type:type});
    $(document).ready(function () {
        $('#sb' + id).slideToggle("medium");
    });
}
function show_hide(id){
    var klappText = document.getElementById('s' + id);
    var klappBild = document.getElementById('pic' + id);
    if (klappText.style.display == 'none') {
        klappText.style.display = 'block';
        klappBild.src = '/style/img/minus.gif';
        klappBild.title = 'Скрыть';
    } else {
        klappText.style.display = 'none';
        klappBild.src = '/style/img/plus.gif';
        klappBild.title = 'Показать';
    }
}
// Закрити вікно
function oknoclose(){
	$(".overlay").hide();
	$("#okno").hide();
}
// вікно - вибрати свіч
function okno(id){
	$.post(root+'ajax/device.php',{id:id}, 
		function(response) { 
			$('#ajax').html(response);
		}, 'html'
    );
}
// connect
function ajaxconnect(act,id){
	$.post(root+'ajax/connect.php',{act:act,id:id}, 
		function(response) { 
			$('#ajax').html(response);
		}, 'html'
    );
}
// port
function port(act,id,style){
	$.post(root+'ajax/port.php',{act:act,id:id}, 
		function(response) { 
			$('#ajax').html(response);
		}, 'html'
    );
}
// port
function savedescr(act,id,style){
	$.post(root+'ajax/port.php',{act:act,id:id}, 
		function(response) { 
			$('#descr_port_').html(response);
		}, 'html'
    );
}
// checker
function ajaxcore(act,id){
	$.post(root+'ajax/core.php',{act:act,id:id}, 
		function(response) { 
			$('#ajax').html(response);
		}, 'html'
    );
}
// checker
// unit
function viewunit(act,id){
	$.post(root+'ajax/unit.php',{act:act,id:id}, 
		function(response) { 
			$('#ajax').html(response);
		}, 'html'
    );
}
function editfibermap(act,id,css){
	$.post(root+'ajax/unit.php',{act:act,id:id}, 
		function(response) { 
			$('#' + css).html(response);
		}, 'html'
    );
}
function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    // if present, the header is where you move the DIV from:
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    // otherwise, move the DIV from anywhere inside the DIV:
    elmnt.onmousedown = dragMouseDown;
  }
function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
}
function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
}
function closeDragElement() {
    // stop moving when mouse button is released:
    document.onmouseup = null;
    document.onmousemove = null;
  }
}