<div id="onu-speedbar">
<a class="brmhref" href="/?do=device"><i class="fi fi-rr-apps"></i>{listdevice}</a>
<a class="brmhref" href="/?do=detail&act=olt&id={olt}"><i class="fi fi-rr-angle-left"></i>{oltname}</a>
<span class="brmspan"><i class="fi fi-rr-angle-left"></i>{listonunoreg}</span>
</div>
<STYLE>
.head_noreg {
    display: block;
    color: #5590d3;
    padding: 0 4px 4px 4px;
    border-bottom: 1px solid #608bbe59;
}
.head_noreg .sn {
    display: inline-block;
    width: 25%;
    padding: 0 10px;
}
.head_noreg .key{
    display: inline-block;
    width: 15%;
    padding: 0 10px;
}
.head_noreg .port{
    display: inline-block;
    width: 10%;
    padding: 0 10px;
}
.regonu:hover{
    background: #0da015;
    color: #cafbca;
	transition: background 0.5s, border 0.5s, border-radius 0.5s, box-shadow 0.5s;
	cursor: pointer;
}
.regonu{
    background: #cafbca;
    color: #0da015;
    padding: 2px 6px;
}
.head_noreg .knopka{
    display: inline-block;
    width: 10%;
    padding: 0 10px;
}
.head_noreg_list:hover {
    color: #1257a6;
    background: #00800008;
}
.head_noreg_list {
    display: block;
	transition: background 0.5s, border 0.5s, border-radius 0.5s, box-shadow 0.5s;
    color: #8e9eb0;
	cursor: pointer;
    padding: 5px;
    border-bottom: 1px solid #608bbe14;
}
.head_noreg_list .sn {
    display: inline-block;
    width: 25%;
	    text-decoration: underline;
    padding: 0 10px;
}
.head_noreg_list .key{
    display: inline-block;
    width: 15%;
    padding: 0 10px;
}
.head_noreg_list .port{
    display: inline-block;
    width: 10%;
    padding: 0 10px;
}
.head_noreg_list .knopka{
    display: inline-block;
    width: 10%;
    padding: 0 10px;
}
.formregonu .pole div .forminput:hover{
    border: 1px solid tomato;
}
.formregonu .pole div .forminput {
    line-height: 20px;
    font-size: 13px;
    padding: 0 5px;
    margin: 0 5px;
    background: #fff;
    border-radius: 0;
    height: 25px;
    width: 42px;
    text-align: center;
    border: 1px solid #5b89e1;
}
.inputonuselect:hover {
    border: 1px solid tomato;
}
.formregonu .pole .btnregonu:hover{
	background: #82a0df;
}
.formregonu .pole .btnregonu {
    font-size: 14px;
	transition: background 0.5s, border 0.5s, border-radius 0.5s, box-shadow 0.5s;
	cursor: pointer;
    border-radius: 4px;
    padding: 4px 12px;
    line-height: 11px;
    height: 30px;
    background: #32abdf;
}
.inputonuselect {
    height: 27px;
    padding: 0 7px;
    background-color: #fff;
    color: #515356;
    border-radius: 1px;
    font-size: 14px;
    box-shadow: none;
    border: 1px solid #2423232e;
}
.formregonu .pole div b {
    color: #2ba0e3;
    margin: 0 5px;
}
.formregonu .pole div {
    display: flex;
    flex-wrap: nowrap;
    justify-content: flex-start;
    align-items: center;
    margin-right: 5px;
    margin-left: 5px;
    margin-bottom: 5px;
}
.formregonu .pole {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: flex-start;
    align-items: center;
    margin: 10px 0px;
}
.formregonu {
    background: #fff;
    padding: 10px;
    box-shadow: 0 1px 20px 0 rgb(69 90 100 / 8%);
}
</STYLE>
<div id="onu">
	<div class="ont-sys" id="result-ont"><div id="load_info">load...<img src="../style/img/load.gif"></div></div>
</div>
{jsloadsnmp}
