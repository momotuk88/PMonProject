function doPlot(position,jsonarray) {
	$.plot("#placeholder", [
		{ data: jsonarray, label: "Сигнал RX" }
	], {
		series: {
			lines: {lineWidth: 2}
		},
		xaxes: [ { mode: "time", timeBase: "milliseconds" } ],
		yaxes: [ { min: 0 }, {
			alignTicksWithAxis: position == "right" ? 1 : null,
			position: position
		} ],
		legend: { position: "sw" }
	});
}