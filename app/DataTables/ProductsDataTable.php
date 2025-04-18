<?php

namespace App\DataTables;

use App\Models\Product;
use App\Models\ProductBatch;
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
            ->addColumn('stock_status', function ($product) {
                $totalStock = $product->batches->sum('quantity');
    
                if ($product->max_stock !== null && $totalStock > $product->max_stock) {
                    return '<span class="badge bg-danger">Overstock</span>';
                } elseif ($totalStock < $product->min_stock) {
                    return '<span class="badge bg-warning">Understock</span>';
                } else {
                    return '<span class="badge bg-success">Normal</span>';
                }
            })
            ->editColumn('created_at', function ($product) {
                return $product->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function ($product) {
                return $product->updated_at->format('Y-m-d H:i:s');
            })
            ->addColumn('action', 'products.action')
            ->rawColumns(['stock_status', 'action']); // Allows rendering HTML badges
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
                        "targets" => [0],
                        "width" => "100px",
                    ],
                    [
                        "targets" => "_all", // Apply to all columns
                        "className" => "text-start" // Use "text-left" if not using Bootstrap 5
                    ],
                    [
                        "targets" => -1, // Action column
                        "className" => "text-center"
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
        ['data' => 'main_category', 'name' => 'main_category', 'title' => 'Main Category'],
        ['data' => 'min_stock', 'name' => 'min_stock', 'title' => 'Minimum Stock'],
        ['data' => 'max_stock', 'name' => 'max_stock', 'title' => 'Maximum Stock'],
        ['data' => 'stock_status', 'name' => 'stock_status', 'title' => 'Stock Status', 'orderable' => false, 'searchable' => false],
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
