$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
console.log("🚀 Chart script is running!");

! function(e) {

	"use strict";
	// for apexchart
	function apexChartUpdate(chart, detail) {
		let color = getComputedStyle(document.documentElement).getPropertyValue('--dark');
		if (detail.dark) {
		color = getComputedStyle(document.documentElement).getPropertyValue('--white');
		}
		chart.updateOptions({
		chart: {
			foreColor: color
		}
		})
	}

	function t(t) {
		t ? e(".right-sidebar-mini").addClass("right-sidebar") : e(".right-sidebar-mini").removeClass("right-sidebar")
	}
	e(document).ready(function() {
		var a = !1;
		t(a), e(document).on("click", ".right-sidebar-toggle", function() {
			t(a = !a)
		})
	})

	var lastDate = 0,
		data = [],
		TICKINTERVAL = 864e5;
	let XAXISRANGE = 7776e5;

	function getDayWiseTimeSeries(e, t, a) {
		for (var n = 0; n < t;) {
			var o = e,
				r = Math.floor(Math.random() * (a.max - a.min + 1)) + a.min;
			data.push({
				x: o,
				y: r
			}), lastDate = e, e += TICKINTERVAL, n++
		}
	}

	getDayWiseTimeSeries(new Date("11 Feb 2017 GMT").getTime(), 10, {
		min: 10,
		max: 90
	});

	function generateData(e, t, a) {
		for (var n = 0, o = []; n < t;) {
			var r = Math.floor(750 * Math.random()) + 1,
				i = Math.floor(Math.random() * (a.max - a.min + 1)) + a.min,
				c = Math.floor(61 * Math.random()) + 15;
			o.push([r, i, c]), 864e5, n++
		}
		return o
	}

// if (jQuery("#high-basicline-chart").length && Highcharts.chart("high-basicline-chart", {
// 		chart: {
// 			type: "spline",
// 			inverted: !0
// 		},
// 		title: {
// 			text: "Atmosphere Temperature by Altitude"
// 		},
// 		subtitle: {
// 			text: "According to the Standard Atmosphere Model"
// 		},
// 		xAxis: {
// 			reversed: !1,
// 			title: {
// 				enabled: !0,
// 				text: "Altitude"
// 			},
// 			labels: {
// 				format: "{value} km"
// 			},
// 			maxPadding: .05,
// 			showLastLabel: !0
// 		},
// 		yAxis: {
// 			title: {
// 				text: "Temperature"
// 			},
// 			labels: {
// 				format: "{value}°"
// 			},
// 			lineWidth: 2
// 		},
// 		legend: {
// 			enabled: !1
// 		},
// 		tooltip: {
// 			headerFormat: "<b>{series.name}</b><br/>",
// 			pointFormat: "{point.x} km: {point.y}°C"
// 		},
// 		plotOptions: {
// 			spline: {
// 				marker: {
// 					enable: !1
// 				}
// 			}
// 		},
// 		series: [{
// 			name: "Temperature",
// 			color: "#827af3",
// 			data: [
// 				[0, 15],
// 				[10, -50],
// 				[20, -56.5],
// 				[30, -46.5],
// 				[40, -22.1],
// 				[50, -2.5],
// 				[60, -27.7],
// 				[70, -55.7],
// 				[80, -76.5]
// 			]
// 		}]
// 	}), jQuery("#high-area-chart").length && Highcharts.chart("high-area-chart", {
// 		chart: {
// 			type: "areaspline"
// 		},
// 		title: {
// 			text: "Average fruit consumption during one week"
// 		},
// 		legend: {
// 			layout: "vertical",
// 			align: "left",
// 			verticalAlign: "top",
// 			x: 150,
// 			y: 100,
// 			floating: !0,
// 			borderWidth: 1,
// 			backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || "#FFFFFF"
// 		},
// 		xAxis: {
// 			categories: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
// 			plotBands: [{
// 				from: 4.5,
// 				to: 6.5,
// 				color: "rgba(68, 170, 213, .2)"
// 			}]
// 		},
// 		yAxis: {
// 			title: {
// 				text: "Fruit units"
// 			}
// 		},
// 		tooltip: {
// 			shared: !0,
// 			valueSuffix: " units"
// 		},
// 		credits: {
// 			enabled: !1
// 		},
// 		plotOptions: {
// 			areaspline: {
// 				fillOpacity: .5
// 			}
// 		},
// 		series: [{
// 			name: "John",
// 			color: "#827af3",
// 			data: [3, 4, 3, 5, 4, 10, 12]
// 		}, {
// 			name: "Jane",
// 			color: "#27b345",
// 			data: [1, 3, 4, 3, 3, 5, 4]
// 		}]
// 	}), jQuery("#high-columnndbar-chart").length && Highcharts.chart("high-columnndbar-chart", {
// 		chart: {
// 			type: "bar"
// 		},
// 		title: {
// 			text: "Stacked bar chart"
// 		},
// 		xAxis: {
// 			categories: ["Apples", "Oranges", "Pears", "Grapes", "Bananas"]
// 		},
// 		yAxis: {
// 			min: 0,
// 			title: {
// 				text: "Total fruit consumption"
// 			}
// 		},
// 		legend: {
// 			reversed: !0
// 		},
// 		plotOptions: {
// 			series: {
// 				stacking: "normal"
// 			}
// 		},
// 		series: [{
// 			name: "John",
// 			color: "#827af3",
// 			data: [5, 3, 4, 7, 2]
// 		}, {
// 			name: "Jane",
// 			color: "#b47af3",
// 			data: [2, 2, 3, 2, 1]
// 		}, {
// 			name: "Joe",
// 			color: "#27b345",
// 			data: [3, 4, 4, 2, 5]
// 		}]
// 	}), jQuery("#high-pie-chart").length && Highcharts.chart("high-pie-chart", {
// 		chart: {
// 			plotBackgroundColor: null,
// 			plotBorderWidth: null,
// 			plotShadow: !1,
// 			type: "pie"
// 		},
// 		colorAxis: {},
// 		title: {
// 			text: "Browser market shares in January, 2018"
// 		},
// 		tooltip: {
// 			pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>"
// 		},
// 		plotOptions: {
// 			pie: {
// 				allowPointSelect: !0,
// 				cursor: "pointer",
// 				dataLabels: {
// 					enabled: !0,
// 					format: "<b>{point.name}</b>: {point.percentage:.1f} %"
// 				}
// 			}
// 		},
// 		series: [{
// 			name: "Brands",
// 			colorByPoint: !0,
// 			data: [{
// 				name: "Chrome",
// 				y: 61.41,
// 				sliced: !0,
// 				selected: !0,
// 				color: "#827af3"
// 			}, {
// 				name: "Internet Explorer",
// 				y: 11.84,
// 				color: "#b47af3"
// 			}, {
// 				name: "Firefox",
// 				y: 10.85,
// 				color: "#c8c8c8"
// 			}, {
// 				name: "Edge",
// 				y: 4.67,
// 				color: "#6ce6f4"
// 			}, {
// 				name: "Other",
// 				y: 2.61
// 			}]
// 		}]
// 	}), jQuery("#high-scatterplot-chart").length && Highcharts.chart("high-scatterplot-chart", {
// 		chart: {
// 			type: "scatter",
// 			zoomType: "xy"
// 		},
// 		accessibility: {
// 			description: "A scatter plot compares the height and weight of 507 individuals by gender. Height in centimeters is plotted on the X-axis and weight in kilograms is plotted on the Y-axis. The chart is interactive, and each data point can be hovered over to expose the height and weight data for each individual. The scatter plot is fairly evenly divided by gender with females dominating the left-hand side of the chart and males dominating the right-hand side. The height data for females ranges from 147.2 to 182.9 centimeters with the greatest concentration between 160 and 165 centimeters. The weight data for females ranges from 42 to 105.2 kilograms with the greatest concentration at around 60 kilograms. The height data for males ranges from 157.2 to 198.1 centimeters with the greatest concentration between 175 and 180 centimeters. The weight data for males ranges from 53.9 to 116.4 kilograms with the greatest concentration at around 80 kilograms."
// 		},
// 		title: {
// 			text: "Height Versus Weight of 507 Individuals by Gender"
// 		},
// 		subtitle: {
// 			text: "Source: Heinz  2003"
// 		},
// 		xAxis: {
// 			title: {
// 				enabled: !0,
// 				text: "Height (cm)"
// 			},
// 			startOnTick: !0,
// 			endOnTick: !0,
// 			showLastLabel: !0
// 		},
// 		yAxis: {
// 			title: {
// 				text: "Weight (kg)"
// 			}
// 		},
// 		legend: {
// 			layout: "vertical",
// 			align: "left",
// 			verticalAlign: "top",
// 			x: 100,
// 			y: 70,
// 			floating: !0,
// 			backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
// 			borderWidth: 1
// 		},
// 		plotOptions: {
// 			scatter: {
// 				marker: {
// 					radius: 5,
// 					states: {
// 						hover: {
// 							enabled: !0,
// 							lineColor: "rgb(100,100,100)"
// 						}
// 					}
// 				},
// 				states: {
// 					hover: {
// 						marker: {
// 							enabled: !1
// 						}
// 					}
// 				},
// 				tooltip: {
// 					headerFormat: "<b>{series.name}</b><br>",
// 					pointFormat: "{point.x} cm, {point.y} kg"
// 				}
// 			}
// 		},
// 		series: [{
// 			name: "Female",
// 			color: "rgba(223, 83, 83, .5)",
// 			data: [
// 				[161.2, 51.6],
// 				[167.5, 59],
// 				[159.5, 49.2],
// 				[157, 63],
// 				[155.8, 53.6],
// 				[170, 59],
// 				[159.1, 47.6],
// 				[166, 69.8],
// 				[176.2, 66.8],
// 				[160.2, 75.2],
// 				[172.7, 62],
// 				[155, 49.2],
// 				[156.5, 67.2],
// 				[164, 53.8],
// 				[160.9, 54.4]
// 			],
// 			color: "#827af3"
// 		}, {
// 			name: "Male",
// 			color: "rgba(119, 152, 191, .5)",
// 			data: [
// 				[174, 65.6],
// 				[175.3, 71.8],
// 				[193.5, 80.7],
// 				[186.5, 72.6],
// 				[187.2, 78.8],
// 				[181.5, 74.8],
// 				[184, 86.4],
// 				[184.5, 78.4],
// 				[175, 62],
// 				[184, 81.6],
// 				[180.1, 93],
// 				[175.5, 80.9],
// 				[180.6, 72.7],
// 				[184.4, 68],
// 				[175.5, 70.9],
// 				[180.3, 83.2],
// 				[180.3, 83.2]
// 			],
// 			color: "#b47af3"
// 		}]
// 	}), jQuery("#high-linendcolumn-chart").length && Highcharts.chart("high-linendcolumn-chart", {
// 		chart: {
// 			zoomType: "xy"
// 		},
// 		title: {
// 			text: "Average Monthly Temperature and Rainfall in Tokyo"
// 		},
// 		subtitle: {
// 			text: "Source: WorldClimate.com"
// 		},
// 		xAxis: [{
// 			categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
// 			crosshair: !0
// 		}],
// 		yAxis: [{
// 			labels: {
// 				format: "{value}°C",
// 				style: {
// 					color: Highcharts.getOptions().colors[1]
// 				}
// 			},
// 			title: {
// 				text: "Temperature",
// 				style: {
// 					color: Highcharts.getOptions().colors[1]
// 				}
// 			}
// 		}, {
// 			title: {
// 				text: "Rainfall",
// 				style: {
// 					color: Highcharts.getOptions().colors[0]
// 				}
// 			},
// 			labels: {
// 				format: "{value} mm",
// 				style: {
// 					color: Highcharts.getOptions().colors[0]
// 				}
// 			},
// 			opposite: !0
// 		}],
// 		tooltip: {
// 			shared: !0
// 		},
// 		legend: {
// 			layout: "vertical",
// 			align: "left",
// 			x: 120,
// 			verticalAlign: "top",
// 			y: 100,
// 			floating: !0,
// 			backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || "rgba(255,255,255,0.25)"
// 		},
// 		series: [{
// 			name: "Rainfall",
// 			type: "column",
// 			yAxis: 1,
// 			data: [49.9, 71.5, 106.4, 129.2, 144, 176, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
// 			color: "#fbc647",
// 			tooltip: {
// 				valueSuffix: " mm"
// 			}
// 		}, {
// 			name: "Temperature",
// 			type: "spline",
// 			data: [7, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
// 			color: "#827af3",
// 			tooltip: {
// 				valueSuffix: "°C"
// 			}
// 		}]
// 	}), jQuery("#high-dynamic-chart").length && Highcharts.chart("high-dynamic-chart", {
// 		chart: {
// 			type: "spline",
// 			animation: Highcharts.svg,
// 			marginRight: 10,
// 			events: {
// 				load: function() {
// 					var e = this.series[0];
// 					setInterval(function() {
// 						var t = (new Date).getTime(),
// 							a = Math.random();
// 						e.addPoint([t, a], !0, !0)
// 					}, 1e3)
// 				}
// 			}
// 		},
// 		time: {
// 			useUTC: !1
// 		},
// 		title: {
// 			text: "Live random data"
// 		},
// 		accessibility: {
// 			announceNewData: {
// 				enabled: !0,
// 				minAnnounceInterval: 15e3,
// 				announcementFormatter: function(e, t, a) {
// 					return !!a && "New point added. Value: " + a.y
// 				}
// 			}
// 		},
// 		xAxis: {
// 			type: "datetime",
// 			tickPixelInterval: 150
// 		},
// 		yAxis: {
// 			title: {
// 				text: "Value"
// 			},
// 			plotLines: [{
// 				value: 0,
// 				width: 1,
// 				color: "#808080"
// 			}]
// 		},
// 		tooltip: {
// 			headerFormat: "<b>{series.name}</b><br/>",
// 			pointFormat: "{point.x:%Y-%m-%d %H:%M:%S}<br/>{point.y:.2f}"
// 		},
// 		legend: {
// 			enabled: !1
// 		},
// 		exporting: {
// 			enabled: !1
// 		},
// 		series: [{
// 			name: "Random data",
// 			color: "#827af3",
// 			data: function() {
// 				var e, t = [],
// 					a = (new Date).getTime();
// 				for (e = -19; e <= 0; e += 1) t.push({
// 					x: a + 1e3 * e,
// 					y: Math.random()
// 				});
// 				return t
// 			}()
// 		}]
// 	}), jQuery("#high-3d-chart").length) {
// 	var chart = new Highcharts.Chart({
// 		chart: {
// 			renderTo: "high-3d-chart",
// 			type: "column",
// 			options3d: {
// 				enabled: !0,
// 				alpha: 15,
// 				beta: 15,
// 				depth: 50,
// 				viewDistance: 25
// 			}
// 		},
// 		title: {
// 			text: "Chart rotation demo"
// 		},
// 		subtitle: {
// 			text: "Test options by dragging the sliders below"
// 		},
// 		plotOptions: {
// 			column: {
// 				depth: 25
// 			}
// 		},
// 		series: [{
// 			data: [29.9, 71.5, 106.4, 129.2, 144, 176, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
// 			color: "#827af3"
// 		}]
// 	});

// 	function showValues() {
// 		$("#alpha-value").html(chart.options.chart.options3d.alpha), $("#beta-value").html(chart.options.chart.options3d.beta), $("#depth-value").html(chart.options.chart.options3d.depth)
// 	}
// 	$("#sliders input").on("input change", function() {
// 		chart.options.chart.options3d[this.id] = parseFloat(this.value), showValues(), chart.redraw(!1)
// 	}), showValues()
// }
// if (jQuery("#high-gauges-chart").length && Highcharts.chart("high-gauges-chart", {
// 		chart: {
// 			type: "gauge",
// 			plotBackgroundColor: null,
// 			plotBackgroundImage: null,
// 			plotBorderWidth: 0,
// 			plotShadow: !1
// 		},
// 		title: {
// 			text: "Speedometer"
// 		},
// 		pane: {
// 			startAngle: -150,
// 			endAngle: 150,
// 			background: [{
// 				backgroundColor: {
// 					linearGradient: {
// 						x1: 0,
// 						y1: 0,
// 						x2: 0,
// 						y2: 1
// 					},
// 					stops: [
// 						[0, "#FFF"],
// 						[1, "#333"]
// 					]
// 				},
// 				borderWidth: 0,
// 				outerRadius: "109%"
// 			}, {
// 				backgroundColor: {
// 					linearGradient: {
// 						x1: 0,
// 						y1: 0,
// 						x2: 0,
// 						y2: 1
// 					},
// 					stops: [
// 						[0, "#333"],
// 						[1, "#FFF"]
// 					]
// 				},
// 				borderWidth: 1,
// 				outerRadius: "107%"
// 			}, {}, {
// 				backgroundColor: "#DDD",
// 				borderWidth: 0,
// 				outerRadius: "105%",
// 				innerRadius: "103%"
// 			}]
// 		},
// 		yAxis: {
// 			min: 0,
// 			max: 200,
// 			minorTickInterval: "auto",
// 			minorTickWidth: 1,
// 			minorTickLength: 10,
// 			minorTickPosition: "inside",
// 			minorTickColor: "#666",
// 			tickPixelInterval: 30,
// 			tickWidth: 2,
// 			tickPosition: "inside",
// 			tickLength: 10,
// 			tickColor: "#666",
// 			labels: {
// 				step: 2,
// 				rotation: "auto"
// 			},
// 			title: {
// 				text: "km/h"
// 			},
// 			plotBands: [{
// 				from: 0,
// 				to: 120,
// 				color: "#55BF3B"
// 			}, {
// 				from: 120,
// 				to: 160,
// 				color: "#DDDF0D"
// 			}, {
// 				from: 160,
// 				to: 200,
// 				color: "#DF5353"
// 			}]
// 		},
// 		series: [{
// 			name: "Speed",
// 			data: [80],
// 			tooltip: {
// 				valueSuffix: " km/h"
// 			}
// 		}]
// 	}, function(e) {
// 		e.renderer.forExport || setInterval(function() {
// 			var t, a = e.series[0].points[0],
// 				n = Math.round(20 * (Math.random() - .5));
// 			((t = a.y + n) < 0 || t > 200) && (t = a.y - n), a.update(t)
// 		}, 3e3)
// 	}), jQuery("#high-barwithnagative-chart").length) {
// 	var categories = ["0-4", "5-9", "10-14", "15-19", "20-24", "25-29", "30-34", "35-39", "40-44", "45-49", "50-54", "55-59", "60-64", "65-69", "70-74", "75-79", "80-84", "85-89", "90-94", "95-99", "100 + "];
// 	Highcharts.chart("high-barwithnagative-chart", {
// 		chart: {
// 			type: "bar"
// 		},
// 		title: {
// 			text: "Population pyramid for Germany, 2018"
// 		},
// 		subtitle: {
// 			text: 'Source: <a href="http://populationpyramid.net/germany/2018/">Population Pyramids of the World from 1950 to 2100</a>'
// 		},
// 		accessibility: {
// 			point: {
// 				descriptionFormatter: function(e) {
// 					return e.index + 1 + ", Age " + e.category + ", " + Math.abs(e.y) + "%. " + e.series.name + "."
// 				}
// 			}
// 		},
// 		xAxis: [{
// 			categories: categories,
// 			reversed: !1,
// 			labels: {
// 				step: 1
// 			},
// 			accessibility: {
// 				description: "Age (male)"
// 			}
// 		}, {
// 			opposite: !0,
// 			reversed: !1,
// 			categories: categories,
// 			linkedTo: 0,
// 			labels: {
// 				step: 1
// 			},
// 			accessibility: {
// 				description: "Age (female)"
// 			}
// 		}],
// 		yAxis: {
// 			title: {
// 				text: null
// 			},
// 			labels: {
// 				formatter: function() {
// 					return Math.abs(this.value) + "%"
// 				}
// 			},
// 			accessibility: {
// 				description: "Percentage population",
// 				rangeDescription: "Range: 0 to 5%"
// 			}
// 		},
// 		plotOptions: {
// 			series: {
// 				stacking: "normal"
// 			}
// 		},
// 		tooltip: {
// 			formatter: function() {
// 				return "<b>" + this.series.name + ", age " + this.point.category + "</b><br/>Population: " + Highcharts.numberFormat(Math.abs(this.point.y), 1) + "%"
// 			}
// 		},
// 		series: [{
// 			name: "Male",
// 			data: [-2.2, -2.1, -2.2, -2.4, -2.7, -3, -3.3, -3.2, -2.9, -3.5, -4.4, -4.1, -0],
// 			color: "#827af3"
// 		}, {
// 			name: "Female",
// 			data: [2.1, 2, 2.1, 2.3, 2.6, 2.9, 3.2, 3.1, 2.9, 3.4, 0],
// 			color: "#27b345"
// 		}]
// 	})
// }
// First, let's set up CSRF token for AJAX requests


	// 1. Task Completion Trend (Line Chart)
	$.get(window.location.origin + '/AirVentra/charts/task-completion-trend', function(response) {
		Highcharts.chart('high-basicline-chart', {
			chart: {
				type: 'spline'
			},
			title: {
				text: 'Task Completion Trend Over Time'
			},
			xAxis: {
				categories: response.dates,
				title: {
					text: 'Date'
				}
			},
			yAxis: {
				title: {
					text: 'Tasks Completed'
				}
			},
			tooltip: {
				headerFormat: '<b>{series.name}</b><br>',
				pointFormat: '{point.x}: {point.y} tasks'
			},
			series: [{
				name: 'Completed Tasks',
				data: response.counts,
				color: '#827af3',
				marker: {
					enabled: false
				}
			}]
		});
	}).fail(function(jqXHR, textStatus, errorThrown) {
		console.error("Error loading task completion trend:", textStatus, errorThrown);
	});

	// 2. Task Distribution by Employee (Bar Chart)
	$.get(window.location.origin + '/AirVentra/charts/task-distribution', function(response) {
		Highcharts.chart('high-columnndbar-chart', {
			chart: {
				type: 'bar'
			},
			title: {
				text: 'Task Distribution by Employee'
			},
			xAxis: {
				categories: response.employees,
				title: {
					text: 'Employees'
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Number of Tasks'
				}
			},
			legend: {
				reversed: true
			},
			plotOptions: {
				series: {
					stacking: 'normal'
				}
			},
			series: [{
				name: 'Tasks',
				data: response.task_counts,
				color: '#827af3'
			}]
		});
	});

	// 3. Task Status Breakdown (Pie Chart)
	$.get(window.location.origin + '/AirVentra/charts/task-status', function(response) {
		Highcharts.chart('high-pie-chart', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: 'Task Status Breakdown'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.1f} %'
					}
				}
			},
			series: [{
				name: 'Tasks',
				colorByPoint: true,
				data: response.data.map(item => ({
					...item,
					color: item.name === 'Pending' ? '#f39c12' : 
						item.name === 'In-progress' ? '#3498db' : 
						'#2ecc71' // Completed
				}))
			}]
		});
	});

	// 4. Live Task Updates (Gauge Chart)
	setInterval(function() {
		$.get(window.location.origin + '/AirVentra/charts/live-updates', function(response) {
			Highcharts.chart('high-gauges-chart', {
				chart: {
					type: 'gauge',
					plotBackgroundColor: null,
					plotBackgroundImage: null,
					plotBorderWidth: 0,
					plotShadow: false
				},
				title: {
					text: 'Task Completion Status'
				},
				pane: {
					startAngle: -150,
					endAngle: 150,
					background: [{
						backgroundColor: {
							linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
							stops: [
								[0, '#FFF'],
								[1, '#333']
							]
						},
						borderWidth: 0,
						outerRadius: '109%'
					}]
				},
				yAxis: {
					min: 0,
					max: response.total_tasks,
					minorTickInterval: 'auto',
					minorTickWidth: 1,
					minorTickLength: 10,
					minorTickPosition: 'inside',
					tickPixelInterval: 30,
					tickWidth: 2,
					tickPosition: 'inside',
					tickLength: 10,
					labels: {
						step: 2,
						rotation: 'auto'
					},
					title: {
						text: 'Tasks'
					},
					plotBands: [{
						from: 0,
						to: response.total_tasks * 0.5,
						color: '#DF5353' // Red
					}, {
						from: response.total_tasks * 0.5,
						to: response.total_tasks * 0.8,
						color: '#DDDF0D' // Yellow
					}, {
						from: response.total_tasks * 0.8,
						to: response.total_tasks,
						color: '#55BF3B' // Green
					}]
				},
				series: [{
					name: 'Completed',
					data: [response.completed],
					tooltip: {
						valueSuffix: ' tasks'
					}
				}]
			});
		});
	}, 5000); // Update every 5 seconds

	// 5. Task Types Over Time (Area Chart)
	$.get(window.location.origin + '/AirVentra/charts/task-types-distribution', function(response) {
		Highcharts.chart('high-area-chart', {
			chart: {
				type: 'areaspline'
			},
			title: {
				text: 'Task Types Over Time'
			},
			xAxis: {
				categories: response.dates,
				title: {
					text: 'Date'
				}
			},
			yAxis: {
				title: {
					text: 'Number of Tasks'
				}
			},
			tooltip: {
				shared: true,
				valueSuffix: ' tasks'
			},
			plotOptions: {
				areaspline: {
					fillOpacity: 0.5
				}
			},
			series: response.series.map(series => ({
				...series,
				color: series.name === 'Misplaced' ? '#e74c3c' : '#f39c12'
			}))
		});
	});

	// 6. Task Completion Time per Employee (Scatter Plot)
	$.get(window.location.origin + '/AirVentra/charts/task-completion-time', function(response) {
		Highcharts.chart('high-scatterplot-chart', {
			chart: {
				type: 'scatter',
				zoomType: 'xy'
			},
			title: {
				text: 'Task Completion Time per Employee'
			},
			subtitle: {
				text: 'Average time taken to complete tasks'
			},
			xAxis: {
				title: {
					enabled: true,
					text: 'Employee'
				},
				categories: response.employees
			},
			yAxis: {
				title: {
					text: 'Average Completion Time (minutes)'
				}
			},
			legend: {
				enabled: false
			},
			plotOptions: {
				scatter: {
					marker: {
						radius: 8,
						states: {
							hover: {
								enabled: true,
								lineColor: 'rgb(100,100,100)'
							}
						}
					},
					states: {
						hover: {
							marker: {
								enabled: false
							}
						}
					},
					tooltip: {
						headerFormat: '<b>{point.key}</b><br>',
						pointFormat: 'Avg time: {point.y:.1f} minutes'
					}
				}
			},
			series: [{
				name: 'Completion Time',
				color: '#827af3',
				data: response.employees.map((emp, i) => ({
					name: emp,
					y: response.times[i]
				}))
			}]
		});
	});

	// 7. Assigned vs Completed Tasks (Dual Axes Chart)
	$.get(window.location.origin + '/AirVentra/charts/assigned-vs-completed', function(response) {
		Highcharts.chart('high-linendcolumn-chart', {
			chart: {
				zoomType: 'xy'
			},
			title: {
				text: 'Assigned vs Completed Tasks'
			},
			xAxis: [{
				categories: response.dates,
				crosshair: true
			}],
			yAxis: [{ // Primary yAxis
				labels: {
					format: '{value}',
					style: {
						color: Highcharts.getOptions().colors[1]
					}
				},
				title: {
					text: 'Assigned',
					style: {
						color: Highcharts.getOptions().colors[1]
					}
				}
			}, { // Secondary yAxis
				title: {
					text: 'Completed',
					style: {
						color: Highcharts.getOptions().colors[0]
					}
				},
				labels: {
					format: '{value}',
					style: {
						color: Highcharts.getOptions().colors[0]
					}
				},
				opposite: true
			}],
			tooltip: {
				shared: true
			},
			legend: {
				layout: 'vertical',
				align: 'left',
				x: 120,
				verticalAlign: 'top',
				y: 100,
				floating: true,
				backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'rgba(255,255,255,0.25)'
			},
			series: [{
				name: 'Assigned',
				type: 'column',
				yAxis: 0,
				data: response.assigned,
				color: '#f1c40f',
				tooltip: {
					valueSuffix: ' tasks'
				}
			}, {
				name: 'Completed',
				type: 'spline',
				yAxis: 1,
				data: response.completed,
				color: '#2ecc71',
				tooltip: {
					valueSuffix: ' tasks'
				}
			}]
		});
	});

	// 8. Workload Distribution (3D Chart)
	$.get(window.location.origin + '/AirVentra/charts/workload-3d', function(response) {
		var chart = new Highcharts.Chart({
			chart: {
				renderTo: 'high-3d-chart',
				type: 'column',
				options3d: {
					enabled: true,
					alpha: 15,
					beta: 15,
					depth: 50,
					viewDistance: 25
				}
			},
			title: {
				text: 'Workload Distribution (3D View)'
			},
			xAxis: {
				categories: response.employees
			},
			yAxis: {
				title: {
					text: 'Number of Tasks'
				}
			},
			plotOptions: {
				column: {
					depth: 25,
					stacking: true
				}
			},
			series: [{
				name: 'Pending',
				data: response.pending,
				color: '#f39c12'
			}, {
				name: 'In Progress',
				data: response.in_progress,
				color: '#3498db'
			}, {
				name: 'Completed',
				data: response.completed,
				color: '#2ecc71'
			}]
		});
	});

	// 9. Delayed vs On-Time Tasks (Bar Chart with Negative Values)
	function fetchChartData(viewBy = 'date') {
		$.get(window.location.origin + `/AirVentra/charts/delayed-vs-on-time?view_by=${viewBy}`, function(response) {
			console.log("✅ API Response:", response);
	
			Highcharts.chart('high-barwithnagative-chart', {
				chart: { 
					type: 'bar',
					marginBottom: 70 // Space for X-axis labels and legend
				},
				title: { text: 'Delayed vs On-Time Tasks' },
				xAxis: [{ 
					// Bottom X-axis
					categories: response.categories, 
					title: { text: viewBy === 'employee' ? 'Employee' : (viewBy === 'month' ? 'Month' : 'Date') },
					opposite: false
				}, {
					// Top X-axis (mirror of bottom)
					categories: response.categories,
					opposite: true,
					linkedTo: 0
				}],
				yAxis: { 
					title: { 
						text: 'Number of Tasks',
					},
					labels: {
						formatter: function() {
							return Math.abs(this.value);
						}
					}
				},
				legend: {
					align: 'center',
					verticalAlign: 'bottom',
					layout: 'horizontal',
					y: 10,           // Pushes legend down
					margin: 20,      // Space above legend
					itemDistance: 20
				},
				plotOptions: { 
					bar: {
						stacking: 'normal',
						borderWidth: 0
					},
					series: {
						point: {
							events: {
								mouseOver: function() {
									this.y = Math.abs(this.y);
								}
							}
						}
					}
				},
				tooltip: {
					formatter: function() {
						const value = Math.abs(this.y);
						return `<b>${this.series.name}</b><br/>${this.key}: ${value}`;
					}
				},
				series: [
					{ 
						name: 'Delayed', 
						data: response.delayed, 
						color: '#e74c3c',
						tooltip: { valueDecimals: 0 }
					},
					{ 
						name: 'On-Time', 
						data: response.on_time, 
						color: '#27ae60',
						tooltip: { valueDecimals: 0 }
					}
				]
			});
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.error("❌ Error:", textStatus, errorThrown);
		});
	}

	// Initialize with default 'date' view
	fetchChartData();

	// Event listeners for the new dropdown filter
	$(document).on('click', '.filter-option', function(e) {
		e.preventDefault();
		const viewBy = $(this).data('value');
		const text = $(this).text();
		
		// Update the dropdown toggle text
		$('#dropdownMenuButton22').text(text);
		
		// Fetch data with the new filter
		fetchChartData(viewBy);
	});
	
	
	
	

}(jQuery);