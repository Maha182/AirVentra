<?php
namespace App\DataTables;

use App\Models\Product;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
class ProductsDataTable extends DataTable
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
            ->addColumn('action', 'products.action')
            ->rawColumns(['action']);
            // ->addColumn('action', function ($product) {
            //     return '<a href="'.route('products.edit', $product->id).'" class="btn btn-sm btn-warning">Edit</a>
            //             <form action="'.route('products.destroy', $product->id).'" method="POST" style="display:inline-block;">
            //                 '.csrf_field().method_field('DELETE').'
            //                 <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
            //             </form>';
            // });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Product $model)
    {
        return $model->newQuery();
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
            ['data' => 'title', 'name' => 'title', 'title' => 'Title'],
            ['data' => 'description', 'name' => 'description', 'title' => 'Description'],
            ['data' => 'main_category', 'name' => 'main_category', 'title' => 'Main Category'],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => 'Quantity'],
            ['data' => 'location_id', 'name' => 'location_id', 'title' => 'Location ID'],
            ['data' => 'barcode_path', 'name' => 'barcode_path', 'title' => 'Barcode Path'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
    
}
