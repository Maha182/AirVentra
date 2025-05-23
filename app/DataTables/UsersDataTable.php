<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            
            ->addColumn('full_name', function ($query) {
                return $query->first_name . ' ' . $query->last_name;
            })
            ->editColumn('created_at', function ($query) {
                return date('Y/m/d', strtotime($query->created_at));
            })

            
            ->addColumn('action', 'users.action')
            ->rawColumns(['action']);
    }
    

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    
    public function query()
    {
        $model = User::select(['id', 'first_name', 'last_name', 'email', 'role', 'phone_number', 'hire_date', 'supervisor_id', 'created_at']);
        return $this->applyScopes($model); 
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    
    public function html()
    {
        return $this->builder()
                ->setTableId('dataTable')
                ->columns($this->getColumns())
                ->minifiedAjax()
                ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')
                ->parameters([
                    "processing" => true,
                    "autoWidth" => false,
                    "columnDefs" => [
                        [
                            "targets" => [0], // ID column
                            "width" => "100px",
                            "render" => function ($data, $type, $row) {
                                return $data; // Ensure the full ID is displayed
                            }
                        ]
                    ]
                ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'full_name', 'name' => 'full_name', 'title' => 'Full Name', 'orderable' => false],
            ['data' => 'phone_number', 'name' => 'phone_number', 'title' => 'Phone Number'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'role', 'name' => 'role', 'title' => 'Role'],
            ['data' => 'hire_date', 'name' => 'hire_date', 'title' => 'Hire Date'],
            ['data' => 'supervisor_id', 'name' => 'supervisor_id', 'title' => 'Supervisor ID'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Join Date'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }

}
