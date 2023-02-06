var lat = '{geo_lan}'; 
var lon = '{geo_lon}';
var map = L.map('map');
map.setView([lat, lon], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
