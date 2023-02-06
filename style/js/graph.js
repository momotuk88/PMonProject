function doPlot(position,jsonarray) {
	$.plot("#placeholder", [
		{ data: jsonarray, label: "Сигнал RX" }
	], {
		series: {
			lines: {lineWidth: 2}
		},
		xaxes: [ { mode: "time", timeformat: '%Y-%m-%d %H:%M:%S' } ],
		yaxes: [ { min: 0 }, {
			alignTicksWithAxis: position == "right" ? 1 : null,
			position: position
		} ],
		legend: { position: "sw" }
	});
}