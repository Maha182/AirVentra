<!-- Include jQuery before your other scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0"></script>

<script src="{{ asset('js/charts/dashboard.js') }}"></script>

<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <!-- Task Summary Widgets -->
        <div class="col-md-12">
            <div class="row d-flex justify-content-center">
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-tasks fa-lg text-primary"></i>
                            <h4 class="mt-2">8</h4>
                            <p class="text-muted small">All Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-pencil-alt fa-lg text-info"></i>
                            <h4 class="mt-2">6</h4>
                            <p class="text-muted small">Incomplete</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-exclamation-circle fa-lg text-danger"></i>
                            <h4 class="mt-2">2</h4>
                            <p class="text-muted small">Overdue</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center p-2">
                        <div class="card-body">
                            <i class="fas fa-bell fa-lg text-warning"></i>
                            <h4 class="mt-2">2</h4>
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

        <!-- Employee Tasks Table -->
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
                                    <th>State of Task</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $fakeTasks = [
                                        ['employee_id' => '18933', 'first_name' => 'Ahmed', 'task_id' => '12233', 'title' => 'Fix misplaced items', 'due_date' => '2025-03-20', 'state' => 'pending'],
                                        ['employee_id' => '18934', 'first_name' => 'Sarah', 'task_id' => '12234', 'title' => 'Check warehouse levels', 'due_date' => '2025-03-21', 'state' => 'pending'],
                                        ['employee_id' => '18935', 'first_name' => 'John', 'task_id' => '12235', 'title' => 'Organize stock', 'due_date' => '2025-03-22', 'state' => 'pending'],
                                    ];
                                @endphp

                                @foreach($fakeTasks as $task)
                                    <tr>
                                        <td>#{{ $task['employee_id'] }}</td>
                                        <td>{{ $task['first_name'] }}</td>
                                        <td>#{{ $task['task_id'] }}</td>
                                        <td>{{ $task['title'] }}</td>
                                        <td>{{ isset($task['due_date']) ? date('d/m/Y', strtotime($task['due_date'])) : 'N/A' }}</td>
                                        <td><span class="badge bg-warning state">{{ $task['state'] }}</span></td>
                                        <td><input type="text" class="form-control comment" style="width: 100%; min-width: 200px;" placeholder="Add your comment"></td>
                                        <td><button class="btn btn-success complete-btn">Complete</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.complete-btn').click(function() {
                let row = $(this).closest('tr');
                row.find('.state').removeClass('bg-warning').addClass('bg-success').text('complete');
            });
        });

        var options = {
            series: [60, 40], // Example: 60% incomplete, 40% complete
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
    </script>
</x-app-layout>

