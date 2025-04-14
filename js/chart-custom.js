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
	
			let chart = Highcharts.chart('high-barwithnagative-chart', {
				chart: {
					type: 'bar',
					marginBottom: 70
				},
				title: { text: 'Delayed vs On-Time Tasks' },
				xAxis: {
					categories: response.categories,
					title: { 
						text: viewBy === 'employee' ? 'Employee' : (viewBy === 'month' ? 'Month' : 'Date') 
					}
				},
				yAxis: {
					title: { text: 'Number of Tasks' },
					labels: { formatter: function() { return Math.abs(this.value); } }
				},
				legend: {
					align: 'center',
					verticalAlign: 'top',
					layout: 'horizontal',
					y: 10,
					margin: 20,
					itemDistance: 20
				},
				plotOptions: {
					series: {
						stacking: 'normal',
						events: {
							legendItemClick: function () {
								let series = this.chart.series;
								let visibleSeries = series.filter(s => s.visible);
	
								if (visibleSeries.length === 2) {
									// Hide all except the clicked one
									series.forEach(s => {
										if (s !== this) s.setVisible(false, false);
									});
								} else {
									// Restore all series when clicking again
									series.forEach(s => s.setVisible(true, false));
								}
	
								// Ensure all series are fully processed before redraw
								setTimeout(() => this.chart.redraw(), 50);
								return false;
							}
						}
					}
				},
				tooltip: {
					formatter: function() {
						return `<b>${this.series.name}</b><br/>${this.key}: ${Math.abs(this.y)}`;
					}
				},
				series: [
					{ 
						name: 'Delayed', 
						data: response.delayed ?? [], // Ensure data is defined
						color: '#e74c3c',
						visible: true
					},
					{ 
						name: 'On-Time', 
						data: response.on_time ?? [], // Ensure data is defined
						color: '#27ae60',
						visible: true
					}
				]
			});
	
			// Reset any hidden series if switching views
			chart.series.forEach(s => s.setVisible(true, false));
			chart.redraw();
	
		}).fail(function(jqXHR, textStatus, errorThrown) {
			console.error("❌ Error:", textStatus, errorThrown);
		});
	}
	
	// Initialize chart
	fetchChartData();
	
	// Dropdown event listener
	$(document).on('click', '.filter-option', function(e) {
		e.preventDefault();
		const viewBy = $(this).data('value');
		$('#dropdownMenuButton22').text($(this).text());
		fetchChartData(viewBy);
	});
	
}(jQuery);