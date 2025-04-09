<!-- Include jQuery before your other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>


<x-app-layout :assets="$assets ?? []">
  <!-- Meta tag for CSRF token -->
   <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                    <th>Re - Assign</th>
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
                        <td>
                            <span class="deadline-display" data-task-id="{{ $task->id }}">
                                {{ $task->deadline ? date('d/m/Y H:i', strtotime($task->deadline)) : 'N/A' }}
                            </span>
                            <i class="bi bi-calendar-event edit-deadline-icon" data-task-id="{{ $task->id }}" style="cursor: pointer; margin-left: 5px;"></i>
                            <input type="datetime-local" class="form-control deadline-input d-none" data-task-id="{{ $task->id }}" value="{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i') : '' }}">
                        </td>

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
                url: `admin/${taskId}/reassign`,
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


        // Show date input when icon is clicked
        $('.edit-deadline-icon').on('click', function () {
            const taskId = $(this).data('task-id');
            $(`.deadline-display[data-task-id="${taskId}"]`).addClass('d-none');
            $(this).addClass('d-none');
            $(`.deadline-input[data-task-id="${taskId}"]`).removeClass('d-none').focus();
        });

        // Handle change of deadline input with row fade effect
        $('.deadline-input').on('change', function () {
            const taskId = $(this).data('task-id');
            const newDeadline = $(this).val();
            const row = $(this).closest('tr');

            // Apply fade-out effect
            row.css('transition', 'opacity 0.3s ease');
            row.css('opacity', '0.5');

            $.ajax({
                url: `admin/${taskId}/update-deadline`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    deadline: newDeadline
                },
                success: function (response) {
                    if (response.success) {
                        const formatted = new Date(newDeadline).toLocaleString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                        });

                        $(`.deadline-display[data-task-id="${taskId}"]`).text(formatted).removeClass('d-none');
                        $(`.edit-deadline-icon[data-task-id="${taskId}"]`).removeClass('d-none');
                        $(`.deadline-input[data-task-id="${taskId}"]`).addClass('d-none');

                        // Optional: Highlight success
                        row.css('background-color', '#d4edda');

                        // Restore opacity
                        setTimeout(() => {
                            row.css('opacity', '1');
                        }, 200);
                    } else {
                        alert('Error updating deadline.');
                        row.css('opacity', '1');
                    }
                },
                error: function () {
                    alert('Error occurred while updating deadline.');
                    row.css('opacity', '1');
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
