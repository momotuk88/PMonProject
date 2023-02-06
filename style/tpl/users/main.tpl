<STYLE>
.users-list:nth-child(even) {
    background: #9bacb605;
}
.users-list {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
    justify-content: flex-start;
    align-items: center;
    width: 100%;
    padding: 4px;
    border-radius: 3px;
}
.users-list .username h2 {
    color: #698dce;
    margin: 0 10px 0 0px;
    padding: 0;
    font-size: 13px;
    display: inline-block;
}
.users-list .username b{}
.users-list .username{
padding: 0 10px;
}
.users-list .class{
padding: 0 10px;
}
.users-list .added {
    padding: 0 10px;
    color: #8bbe8b;
}
.users-list .last {
    padding: 0 10px;
    color: #f2a573;
}
.users-list .moder a:hover{
    color: red;
    border-bottom: 1px dashed red;
}
.users-list .moder a {
    margin: 0 5px;
    color: #5386cb;
    border-bottom: 1px dashed #5386cb;
}
.users-list .moder{
padding: 0 10px;
}
.users-list .page{
padding: 0 10px;
}
.users-list .userip {
    display: inline-block;
    margin: 0 5px;
    padding: 0 5px;
    background: #eee;
    border-radius: 3px;
}
.users-list .onlyip {
    display: inline-block;
    margin: 0 5px;
    padding: 0 5px;
    color: #d67878;
    background: #fef5f5;
    border-radius: 3px;
}
</STYLE>
{add}
<div class="block-monitor"><div class="block-head"><h2>{name}</h2></div><div class="block-center">{result}</div></div>