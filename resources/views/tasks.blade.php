<!-- Include jQuery before your other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0"></script>
<script src="{{ asset('js/charts/dashboard.js') }}"></script>

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
                            <h4 class="mt-2">{{ $tasks->count() }}</h4>
                            <p class="text-muted small">All Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-pencil-alt fa-lg text-info"></i>
                            <h4 class="mt-2">{{ $tasks->where('status', '!=', 'complete')->count() }}</h4>
                            <p class="text-muted small">Incomplete</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-exclamation-circle fa-lg text-danger"></i>
                            <h4 class="mt-2">{{ $tasks->where('status', 'overdue')->count() }}</h4>
                            <p class="text-muted small">Overdue</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-bell fa-lg text-warning"></i>
                            <h4 class="mt-2">{{ $tasks->where('due_date', now()->toDateString())->count() }}</h4>
                            <p class="text-muted small">Due Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wide Pie Chart for Incomplete Tasks -->
        <div class="col-md-12 mt-3">
            <div class="card" data-aos="fade-up" data-aos-delay="900">
                <div class="card-header text-center">
                    <h4 class="card-title">Incomplete Tasks Distribution</h4>
                </div>
                <div class="card-body">
                    <div id="incompleteTasksChart" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- My Tasks Table -->
        <div class="col-md-12 mt-3">
            <div class="card" data-aos="fade-up" data-aos-delay="800">
                <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">My Tasks</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-datatable-entries">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>Task Title</th>
                                    <th>Details</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr>
                                        <td>{{ $task->error_type }}</td>
                                        <td>
                                            <button class="btn btn-info details-btn" data-task-id="{{ $task->id }}" data-bs-toggle="modal" data-bs-target="#taskDetailsModal">Details</button>
                                        </td>
                                        <td>{{ isset($task->deadline) ? date('d/m/Y', strtotime($task->deadline)) : 'N/A' }}</td>
                                        <td><span class="badge bg-warning state">{{ $task->status }}</span></td>
                                        <td>
                                            <input type="text" class="form-control comment"
                                                style="width: 100%; min-width: 200px;"
                                                value="{{ $task->comment }}"
                                                placeholder="Add your comment"
                                                data-task-id="{{ $task->id }}">
                                        </td>
                                        <td>
                                            <button class="btn btn-success complete-btn" data-task-id="{{ $task->id }}">Complete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Task Details -->
        <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-labelledby="taskDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskDetailsModalLabel">Task Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="task-details-content">Loading...</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).on('click', '.details-btn', function() {
                var taskId = $(this).data('task-id');
                $.ajax({
                    url: '/tasks/' + taskId + '/details',
                    method: 'GET',
                    success: function(response) {
                        var details = "";
                        if (response.wrong_location && response.correct_location) {
                            details += "<strong>Wrong Location:</strong> " + response.wrong_location + "<br>";
                            details += "<strong>Correct Location:</strong> " + response.correct_location + "<br>";
                        }
                        if (response.status && response.location_id) {
                            details += "<strong>Stock Status:</strong> " + response.status + "<br>";
                            details += "<strong>Location ID:</strong> " + response.location_id;
                        }
                        $('#task-details-content').html(details || "No details available.");
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            });
                        // Pie chart for task distribution
                        var options = {
                series: [
                    {{ $tasks->where('status', '!=', 'complete')->count() }},
                    {{ $tasks->where('status', 'complete')->count() }}
                ],
                chart: {
                    type: 'pie',
                    width: '100%',
                    height: 400
                },
                labels: ['Incomplete Tasks', 'Completed Tasks'],
                colors: ['#f39c12', '#2ecc71'],
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            width: '100%'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
        </script>
</x-app-layout>



        <!-- python main.py -->
<!--
         Employee Tasks Table 
        
        <div class="col-md-12 mt-3">
            <div class="card" data-aos="fade-up" data-aos-delay="800">
                <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Employee Tasks</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-datatable-entries">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>First Name</th>
                                    <th>Task ID</th>
                                    <th>Title</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allTasks as $task)
                                    <tr>
                                        <td>#{{ $task->assigned_to }}</td>
                                        <td>{{ $task->assignedUser->first_name }}</td>
                                        <td>#{{ $task->id }}</td>
                                        <td>{{ $task->error_type }}</td>
                                        <td>{{ isset($task->deadline) ? date('d/m/Y', strtotime($task->deadline)) : 'N/A' }}</td>
                                        <td><span class="badge bg-warning state">{{ $task->status }}</span></td>
                                        <td>{{ $task->comment }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->
<!--
        <script>
            // Pie chart for task distribution
            var options = {
                series: [
                    {{ $tasks->where('status', '!=', 'complete')->count() }},
                    {{ $tasks->where('status', 'complete')->count() }}
                ],
                chart: {
                    type: 'pie',
                    width: '100%',
                    height: 400
                },
                labels: ['Incomplete Tasks', 'Completed Tasks'],
                colors: ['#f39c12', '#2ecc71'],
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            width: '100%'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            var chart = new ApexCharts(document.querySelector("#incompleteTasksChart"), options);
            chart.render();

            // CSRF Token for all AJAX requests
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle clicking the "Complete" button for tasks in "My Tasks" table
            $(document).on('click', '.complete-btn', function() {
                var taskId = $(this).data('task-id'); // Get the task ID from the button data attribute

                // Send AJAX request to update the task status to "complete"
                $.ajax({
                    url: '/tasks/' + taskId + '/complete',
                    method: 'POST',
                    data: {
                        _method: 'PATCH',  // Override method to PATCH
                        _token: csrfToken, // CSRF Token for security
                    },
                    success: function(response) {
                        if (response.success) {
                            // Change the status in the table to "Complete"
                            $('button[data-task-id="' + taskId + '"]').closest('tr').find('.state')
                                .removeClass('bg-warning').addClass('bg-success').text('complete');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);  // For debugging
                    }
                });
            });

            // Handle comment change in "My Tasks" table
            $(document).on('change', '.comment', function() {
                var taskId = $(this).data('task-id');
                var comment = $(this).val();
                
                // Send AJAX request to update the comment
                $.ajax({
                    url: '/tasks/' + taskId + '/updateComment',
                    method: 'POST',
                    data: {
                        _method: 'PATCH',  // Override the method to PATCH
                        _token: csrfToken,  // CSRF Token
                        comment: comment
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Comment updated successfully');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);  // For debugging
                    }
                });
            });
        </script>
        -->