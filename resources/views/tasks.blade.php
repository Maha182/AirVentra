<!-- Include jQuery before your other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0"></script>
<script src="{{ asset('js/charts/dashboard.js') }}"></script>
<script src="js/jquery.min.js"></script>
<script src="js/rtl.js"></script>
<script src="js/customizer.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.appear.js"></script>
<script src="js/countdown.min.js"></script>
<script src="js/waypoints.min.js"></script>
<script src="js/jquery.counterup.min.js"></script>
<script src="js/wow.min.js"></script>
<script src="js/apexcharts.js"></script>
<script src="js/slick.min.js"></script>
<script src="js/select2.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/smooth-scrollbar.js"></script>
<script src="js/lottie.js"></script>
<script src="js/highcharts.js"></script>
<script src="js/highcharts-3d.js"></script>
<script src="js/highcharts-more.js"></script>


<x-app-layout :assets="$assets ?? []">
    <!-- Meta tag for CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <!-- Task Summary Widgets -->
        <div class="col-md-12">
            <div class="row d-flex justify-content-center">
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-tasks fa-lg text-primary"></i>
                            <h4 class="mt-2 all-tasks-count">{{ $tasks->count() }}</h4> <!-- Added class -->
                            <p class="text-muted small">All Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-pencil-alt fa-lg text-info"></i>
                            <h4 class="mt-2 incomplete-tasks-count">{{ $tasks->where('status', '!=', 'completed')->count() }}</h4> <!-- Added class -->
                            <p class="text-muted small">Incomplete</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-exclamation-circle fa-lg text-danger"></i>
                            <h4 class="mt-2 overdue-tasks-count">
                                {{ $tasks->filter(function ($task) {
                                    return ($task->status !== 'completed' && $task->deadline < now()) ||
                                        ($task->status === 'completed' && $task->completed_at && $task->completed_at > $task->deadline);
                                })->count() }}
                            </h4> <!-- Added class -->
                            <p class="text-muted small">Overdue</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-bell fa-lg text-warning"></i>
                            <h4 class="mt-2 due-today-count">
                                {{ $tasks->filter(function ($task) {
                                    return $task->status !== 'completed' && \Carbon\Carbon::parse($task->deadline)->toDateString() === now()->toDateString();
                                })->count() }}
                            </h4>
                            <p class="text-muted small">Due Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="row">  <!-- Add this row wrapper -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Task Completion Trend</h4>
                            </div>
                            <div id="taskCompletionFilter" class="dropdown">
                                <a href="#" class="text-gray dropdown-toggle" id="dropdownMenuButtonTask" data-bs-toggle="dropdown" aria-expanded="false">
                                    By Date
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu-end" aria-labelledby="dropdownMenuButtonTask">
                                    <li><a class="dropdown-item filter-option" href="#" data-value="day">Daily</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-value="week">Weekly</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-value="month">Monthly</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div id="completed-tasks-chart" style="width: 100%; height: 400px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Task Breakdown</h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div id="taskBreakdownChart" style="width: 100%; height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-header text-center d-flex justify-content-between">
                    <h4 class="card-title">My Tasks</h4>
                    <select id="filterStatus" class="form-select w-25">
                        <option value="all">All Tasks</option>
                        <option value="incomplete">Incomplete Tasks</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="custom-datatable-entries">
                        <table id="datatable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th></th> <!-- Checkbox -->
                                    <th>Task Type</th>
                                    <th>Details</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr class="task-row" data-status="{{ $task->status }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="complete-checkbox" 
                                                data-task-id="{{ $task->id }}" 
                                                {{ $task->status == 'completed' ? 'checked' : '' }}
                                                style="width: 20px; height: 20px;">
                                        </td>
                                        <td>{{ $task->error_type }}</td>
                                        <td>
                                            <button class="btn btn-info details-btn" 
                                                data-task-id="{{ $task->id }}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#taskDetailsModal">
                                                Details
                                            </button>
                                        </td>
                                        <td>{{ $task->deadline ? date('d/m/Y', strtotime($task->deadline)) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $task->status == 'completed' ? 'success' : 'warning' }} state">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <!-- modal for detail button -->
        <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #001f3f;">
                        <h5 class="modal-title text-white" id="taskDetailsModalLabel">Task Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="taskError" class="alert alert-danger text-center d-none" role="alert">
                            No details found for this task!
                        </div>

                        <!-- Misplacement Details -->
                        <div id="misplacedDetails" class="d-none">
                            <div class="mb-3">
                                <label class="form-label bg-light p-2 d-block">Wrong Location: 
                                    <span id="wrong_location" class="fw-bold text-dark"></span>
                                </label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label bg-light p-2 d-block">Correct Location: 
                                    <span id="correct_location" class="fw-bold text-dark"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Capacity Details -->
                        <div id="capacityDetails" class="d-none">
                            <div class="mb-3">
                                <label class="form-label bg-light p-2 d-block">Location ID: 
                                    <span id="location_id" class="fw-bold text-dark"></span>
                                </label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label bg-light p-2 d-block">Detected Capacity: 
                                    <span id="detected_capacity" class="fw-bold text-dark"></span>
                                </label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label bg-light p-2 d-block">Status: 
                                    <span id="capacity_status" class="fw-bold text-dark"></span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                let dataTable = $('#datatable').DataTable();
                let taskCompletionChart;
                let filterType = "day"; // Default filter
                let taskBreakdownChart;
                // Function to filter tasks based on selected status
                function filterTasks() {
                    let filter = $("#filterStatus").val();
                    $(".task-row").each(function () {
                        let status = $(this).attr("data-status");
                        if (filter === "all" || (filter === "incomplete" && status !== "completed")) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
                

                $("#filterStatus").change(filterTasks);
                function loadTaskBreakdownChart() {
                    $.get('/AirVentra/task-breakdown', function(response) {
                        if (taskBreakdownChart) {
                            taskBreakdownChart.destroy();
                        }

                        taskBreakdownChart = Highcharts.chart('taskBreakdownChart', {
                            chart: {
                                type: 'pie'
                            },
                            title: { text: 'Task Status Breakdown' },
                            tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
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
                                data: response.data
                            }]
                        });
                    });
                }

                // This function will load the completed tasks trend chart with the selected filter
                function loadCompletedTasksChart(filter) {
                    $.get('/AirVentra/completed-tasks-trend/' + filter, function(response) {  // Pass the filter as part of the URL
                        Highcharts.chart('completed-tasks-chart', {
                            chart: {
                                type: 'line'  // Use line chart
                            },
                            title: {
                                text: 'Completed Tasks Trend'
                            },
                            xAxis: {
                                categories: response.dates,  // Dates returned from the backend
                                title: {
                                    text: 'Date'
                                },
                                crosshair: true
                            },
                            yAxis: {
                                title: {
                                    text: 'Completed Tasks'
                                },
                                labels: {
                                    format: '{value} tasks'
                                }
                            },
                            tooltip: {
                                shared: true,
                                valueSuffix: ' tasks'
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
                                name: 'Completed Tasks',
                                data: response.completed,  // Data for completed tasks
                                color: '#2ecc71',  // Green color for completed tasks
                                tooltip: {
                                    valueSuffix: ' tasks'
                                }
                            }]
                        });
                    });
                }


                

                loadTaskBreakdownChart();
                loadCompletedTasksChart(filterType);

                $(".filter-option").click(function () {
                    filterType = $(this).data("value");
                    $("#dropdownMenuButtonTask").text($(this).text());
                    loadCompletedTasksChart(filterType);
                });

                function updateWidgets() {
                    $.ajax({
                        url: "{{ route('tasks.stats') }}",
                        type: "GET",
                        success: function (response) {
                            $(".all-tasks-count").text(response.all_tasks);
                            $(".incomplete-tasks-count").text(response.incomplete_tasks);
                            $(".overdue-tasks-count").text(response.overdue_tasks);
                            $(".due-today-count").text(response.due_today);
                        },
                        error: function (xhr, status, error) {
                            console.error("Failed to fetch task stats:", error);
                        }
                    });
                }

                // Initial update & auto-refresh every 10 seconds
                updateWidgets();
                setInterval(updateWidgets, 10000);

                $(".complete-checkbox").change(function () {
                    let taskId = $(this).data("task-id");
                    let newStatus = $(this).is(":checked") ? "completed" : "pending"; // Update based on checkbox state
                    let statusCell = $(this).closest("tr").find(".state");
                    let row = $(this).closest("tr");

                    $.ajax({
                        url: "{{ route('tasks.complete', ':id') }}".replace(":id", taskId),
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        data: { status: newStatus },
                        success: function (response) {
                            if (response.success) {
                                statusCell.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1))
                                    .removeClass("bg-warning bg-success")
                                    .addClass(newStatus === "completed" ? "bg-success" : "bg-warning");

                                row.attr("data-status", newStatus);
                                updateWidgets();
                                filterTasks();
                                loadTaskBreakdownChart();
                                loadCompletedTasksChart(filterType);
                            } else {
                                console.error("Error: " + response.message);
                                alert("An error occurred while updating the task: " + response.message);
                            }
                        },
                        error: function (xhr) {
                            console.error("AJAX Error: ", xhr);
                            alert("An error occurred while updating the task.");
                        }
                    });
                });


                $(".details-btn").click(function () {
                    let taskId = $(this).data("task-id");

                    $.ajax({
                        url: "{{ route('tasks.details', '') }}/" + taskId,
                        type: "GET",
                        success: function (response) {
                            $("#misplacedDetails, #capacityDetails, #taskError").addClass("d-none");

                            if (response.error) {
                                $("#taskError").removeClass("d-none").text(response.error);
                            } else {
                                if (response.type === "misplaced") {
                                    $("#wrong_location").text(response.wrong_location);
                                    $("#correct_location").text(response.correct_location);
                                    $("#misplacedDetails").removeClass("d-none");
                                } else if (response.type === "capacity") {
                                    $("#location_id").text(response.location_id);
                                    $("#detected_capacity").text(response.detected_capacity);
                                    $("#capacity_status").text(response.status);
                                    $("#capacityDetails").removeClass("d-none");
                                }
                            }
                        },
                        error: function (xhr) {
                            console.error(xhr);
                            $("#taskError").removeClass("d-none").text("Error fetching task details!");
                        }
                    });
                });
            });

        </script>
</x-app-layout>
