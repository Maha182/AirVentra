<!-- Include jQuery before your other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0"></script>
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
<script src="https://code.highcharts.com/highcharts.js"></script>


<x-app-layout :assets="$assets ?? []">
  <!-- Meta tag for CSRF token -->
   <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

   </head>

  <!-- Include Required Scripts -->
  
  <div class="container mt-5">
        <!-- Dashboard Cards -->
        <div class="row text-center">
        <div class="col-md-3">
            <div class="card p-3">
            <h5>All Tasks</h5>
            <h4>{{ $totalTasks }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
            <h5>Incomplete</h5>
            <h4>{{ $incompleteTasks }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
            <h5>Overdue</h5>
            <h4>{{ $overdueTasks }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
            <h5>Due Today</h5>
            <h4>{{ $dueTodayTasks }}</h4>
            </div>
        </div>
        </div>

        <!-- Charts -->
        <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
            <div class="card-header">Task Completion Trend</div>
            <div class="card-body">
                <div id="taskCompletionChart" style="height: 300px;"></div>
            </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
            <div class="card-header">Task Breakdown</div>
            <div class="card-body">
                <div id="taskBreakdownChart" style="height: 300px;"></div>
            </div>
            </div>
        </div>
        </div>

        <!-- Task Table with Search -->
        <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Employees Tasks</h4>
            <!-- <input type="text" id="taskSearch" placeholder="Search tasks..." class="form-control w-25"> -->
        </div>
        <div class="card-body " >
            <div class="custom-datatable-entries">
                <table id="taskTable" class="table table-bordered">
                <thead>
                    <tr>
                    <th>Employee</th>
                    <th>Task Type</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->user ? $task->user->first_name . ' ' . $task->user->last_name : 'Unassigned' }}</td>
                        <td>{{ $task->error_type }}</td>
                        <td>
                        <span class="badge bg-{{ $task->status == 'completed' ? 'success' : 'warning' }}">
                            {{ ucfirst($task->status) }}
                        </span>
                        </td>
                        <td>{{ $task->deadline ? date('d/m/Y', strtotime($task->deadline)) : 'N/A' }}</td>
                        <td>
                            @if($task->status !== 'completed')
                                <select class="form-select d-inline w-50 reassign-dropdown" data-task-id="{{ $task->id }}">
                                <option selected disabled>Reassign to...</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                                @endforeach
                                </select>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
        </div>
  </div>

  <!-- Charts Script -->
  <script>
    $(document).ready(function () {

        let dataTable = $('#taskTable').DataTable();
        // Handle the reassign dropdown change
        // $('#taskTable').DataTable();

        // Search functionality for the task table
        // $('#taskSearch').on('keyup', function () {
        //     $('#taskTable').DataTable().search(this.value).draw();
        // });



        $('.reassign-dropdown').on('change', function () {
            const userId = $(this).val();
            const taskId = $(this).data('task-id');
            const dropdown = $(this);

            console.log(`Reassigning Task ID: ${taskId} to User ID: ${userId}`);

            const row = dropdown.closest('tr');

            // Apply a subtle fade-out effect (opacity change)
            row.css('transition', 'opacity 0.3s ease');
            row.css('opacity', '0.5');  // Lower opacity to make it appear "fading"

            $.ajax({
                url: `tasks/${taskId}/reassign`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    new_user_id: userId
                },
                success: function (response) {
                    if (response.success) {
                        // Update the assigned user text in the table
                        row.find('td:first').text(response.new_assignee);  // Update the Employee column

                        // Optionally, you can apply a background color to indicate success
                        row.css('background-color', '#d4edda');  // Green background (Bootstrap success color)

                        // Quickly fade the row back in (restore opacity)
                        row.css('opacity', '1');  // Reset opacity to fully visible
                    } else {
                        alert('Failed to reassign task. Please try again.');
                        row.css('opacity', '1');  // Reset opacity in case of error
                    }
                },
                error: function () {
                    alert('An error occurred while reassigning the task.');
                    row.css('opacity', '1');  // Reset opacity in case of error
                }
            });
        });




        // Highcharts setup for task breakdown (pie chart)
        Highcharts.chart('taskBreakdownChart', {
            chart: { type: 'pie' },
            title: { text: 'Task Status Breakdown' },
            series: [{
                name: 'Tasks',
                colorByPoint: true,
                data: [
                    { name: 'Completed', y: {{ $completedTasks }} },
                    { name: 'Pending', y: {{ $pendingTasks }} },
                    { name: 'Overdue', y: {{ $overdueTasks }} }
                ]
            }]
        });

        // Highcharts setup for task completion trend (line chart)
        Highcharts.chart('taskCompletionChart', {
            chart: { type: 'line' },
            title: { text: 'Task Completion Over Time' },
            xAxis: {
                categories: {!! json_encode($completionDates) !!},
                title: { text: 'Date' }
            },
            yAxis: {
                title: { text: 'Completed Tasks' }
            },
            series: [{
                name: 'Tasks Completed',
                data: {!! json_encode($completionCounts) !!}
            }]
        });
    });
  </script>

</x-app-layout>
