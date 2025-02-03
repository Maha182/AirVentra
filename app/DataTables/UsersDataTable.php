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
            ->addColumn('action', function ($query) {
                return '<a href="' . route('users.edit', $query->id) . '" class="btn btn-sm btn-primary">Edit</a> 
                        <button class="btn btn-sm btn-danger delete-user" data-id="' . $query->id . '">Delete</button>';
            })
            ->rawColumns(['action']);
    }
    // public function dataTable($query)
    // {
    //     return datatables()
    //         ->eloquent($query)
    //         ->editColumn('userProfile.country', function($query) {
    //             return $query->userProfile->country ?? '-';
    //         })
    //         ->editColumn('userProfile.company_name', function($query) {
    //             return $query->userProfile->company_name ?? '-';
    //         })
    //         ->editColumn('status', function($query) {
    //             $status = 'warning';
    //             switch ($query->status) {
    //                 case 'active':
    //                     $status = 'primary';
    //                     break;
    //                 case 'inactive':
    //                     $status = 'danger';
    //                     break;
    //                 case 'banned':
    //                     $status = 'dark';
    //                     break;
    //             }
    //             return '<span class="text-capitalize badge bg-'.$status.'">'.$query->status.'</span>';
    //         })
    //         ->editColumn('created_at', function($query) {
    //             return date('Y/m/d',strtotime($query->created_at));
    //         })
    //         ->filterColumn('full_name', function($query, $keyword) {
    //             $sql = "CONCAT(users.first_name,' ',users.last_name)  like ?";
    //             return $query->whereRaw($sql, ["%{$keyword}%"]);
    //         })
    //         ->filterColumn('userProfile.company_name', function($query, $keyword) {
    //             return $query->orWhereHas('userProfile', function($q) use($keyword) {
    //                 $q->where('company_name', 'like', "%{$keyword}%");
    //             });
    //         })
    //         ->filterColumn('userProfile.country', function($query, $keyword) {
    //             return $query->orWhereHas('userProfile', function($q) use($keyword) {
    //                 $q->where('country', 'like', "%{$keyword}%");
    //             });
    //         })
    //         ->addColumn('action', 'users.action')
    //         ->rawColumns(['action','status']);
    // }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function query()
    // {
    //     $model = User::query()->with('userProfile');
    //     return $this->applyScopes($model);
    // }
    public function query()
    {
        return User::select(['id', 'first_name', 'last_name', 'email', 'role', 'phone_number', 'hire_date', 'supervisor_id', 'created_at']);
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    // public function html()
    // {
    //     return $this->builder()
    //                 ->setTableId('dataTable')
    //                 ->columns($this->getColumns())
    //                 ->minifiedAjax()
    //                 ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')

    //                 ->parameters([
    //                     "processing" => true,
    //                     "autoWidth" => false,
    //                 ]);
    // }
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
