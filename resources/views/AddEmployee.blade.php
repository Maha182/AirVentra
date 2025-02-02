<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h2 class="card-title">Add Employee</h2>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="custom-datatable-entries">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Age</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $employees = [
                            [
                                'name' => 'Tiger Nixon',
                                'position' => 'System Architect',
                                'office' => 'Edinburgh',
                                    'age' => 61,
                                    'start_date' => '2011/04/25',
                                    'salary' => '$320,800',
                                ],
                                [
                                    'name' => 'Garrett Winters',
                                    'position' => 'Accountant',
                                    'office' => 'Tokyo',
                                    'age' => 63,
                                    'start_date' => '2011/07/25',
                                    'salary' => '$170,750',
                                ],
                                // Add more employees here...
                            ];
                            ?>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td>{{ $employee['name'] }}</td>
                                        <td>{{ $employee['position'] }}</td>
                                        <td>{{ $employee['office'] }}</td>
                                        <td>{{ $employee['age'] }}</td>
                                        <td>{{ $employee['start_date'] }}</td>
                                        <td>{{ $employee['salary'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Age</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

