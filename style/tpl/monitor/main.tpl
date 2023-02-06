<STYLE>
#stats_monitor{}
.monitor .delmonitor i {
    font-size: 9px;
    position: relative;
    left: 3px;
    top: -3px;
	transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
}
.monitor .delmonitor:hover{
	background: red;
}
.monitor .delmonitor {
    background: #f9320e54;
    display: none;
    position: relative;
    height: 17px;
    width: 16px;
    color: #fff;
    float: right;
    border-radius: 4px;
    margin-right: -6px;
}
.monitor {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 5px;
}
.monitor .device:hover{
    background: #fff;
	box-shadow: rgb(0 0 0 / 10%) 0px 4px 10px;
}
.monitor .device:hover .delmonitor {
	transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
	display: block;
}
.monitor .device:hover .subs {
	transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
	display: block;
}
.monitor .device {
	transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
    display: flex;
	cursor: pointer;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
    justify-content: flex-start;
    align-items: center;
    width: 100%;
    padding: 3px 10px;
    margin: 0 0 5px 0;
    border-radius: 5px;
    background: #ffffff8c;
}
.monitor .device .status {
    display: block;
    float: left;
    position: relative;
    top: 3px;
}
.monitor .subs {
    border: 1px solid #fff;
    box-shadow: rgb(0 0 0 / 10%) 0px 4px 10px;
    position: absolute;
    background: #fff;
    margin-top: 10px;
    padding: 5px;
    display: none;
    z-index: 9999;
    max-width: 300px;
    font-size: 13px;
    line-height: 16px;
    color: #889bb8;
}
.monitor .device h2 {
    font-size: 14px;
    color: #7090c2;
}
.monitor .subs b {
    color: #3e5c7fa1;
    margin-right: 10px;
}
.monitor .subs span {
    display: block;
    font-size: 13px;
    line-height: 16px;
    color: grey;
}
.monitor .subs {
    border: 1px solid #fff;
    box-shadow: rgb(0 0 0 / 10%) 0px 4px 10px;
    position: absolute;
    background: #fff;
    margin-top: 10px;
    padding: 5px;
    display: none;
    max-width: 300px;
    font-size: 13px;
    line-height: 16px;
    color: #889bb8;
}
.monitor .name {
    width: 100%;
    padding-bottom: 6px;
}
.monitor .device .types{line-height: 14px;}
.monitor .device .types i {
    position: relative;
    top: 2px;
    margin-right: 5px;
    font-size: 12px;
    opacity: 0.8;
}
.monitor .device .types b {
    font-size: 13px;
    margin-right: 5px;
    color: #99a5b7;
    font-weight: 300;
}
.monitor .device .types span {
    background: #22222282;
    color: #fff;
    padding: 0 4px;
    border-radius: 2px;
}
.monitor .device .online{line-height: 14px;}
.monitor .device .online i {
    position: relative;
    top: 2px;
    margin-right: 5px;
    font-size: 12px;
    opacity: 0.8;
	color: red;
}
.monitor .device .online b {
    font-size: 13px;
    margin-right: 5px;
    color: #99a5b7;
    font-weight: 300;
}
.monitor .device .online span{}
.monitor .device .check{line-height: 14px;}
.monitor .device .check i {
    position: relative;
    top: 2px;
    margin-right: 5px;
    font-size: 12px;
    opacity: 0.8;
	    color: #56af56;
}
.monitor .device .check b {
    font-size: 13px;
    margin-right: 5px;
    color: #99a5b7;
    font-weight: 300;
}
.monitor .device .check span{}
.monitor .device .status .stdown{
    background: tomato;
    color: #fff;
    padding: 1px 4px;
    margin-right: 10px;
    width: 35px;
    text-align: center;
    border-radius: 3px;
    line-height: 14px;
}
</STYLE>
<div id="stats_monitor">

</div>
<div id="list_monitor">
{block-main}
</div>