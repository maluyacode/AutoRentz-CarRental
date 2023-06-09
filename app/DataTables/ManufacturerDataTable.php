<?php

namespace App\DataTables;

use App\Models\Manufacturer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ManufacturerDataTable extends DataTable
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
            ->addColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('F j, Y - g:i a');
            })
            ->addColumn('updated_at', function ($row) {
                return Carbon::parse($row->updated_at)->format('F j, Y - g:i a');
            })
            ->addColumn('images', function ($row) {
                $images = $row->getMedia('images');
                foreach ($images as $image) {
                    return "<img src='{$image->getUrl('thumb')}'>";
                }
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('manufacturers.edit', $row->id) . '" style="display: inline-block;">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm" style="background-color: #6c757d">Edit</button>
            </a>
            <form action="' . route('manufacturers.destroy', $row->id) . '"method="POST"
                style="display: inline-block;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
            </form>';
                return $actionBtn;
            })->rawColumns(['action', 'images']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Manufacturer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Manufacturer $model)
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
            ->setTableId('manufacturer-table')
            ->addTableClass('table-bordered')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->responsive(true)
            ->autoWidth(true)
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id')
                ->addClass('text-center'),
            Column::make('images')
                ->addClass('text-center'),
            Column::make('name')
                ->addClass('text-center'),
            Column::make('created_at')
                ->width(100)
                ->addClass('text-center'),
            Column::make('updated_at')
                ->width(100)
                ->addClass('text-center'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Manufacturer_' . date('YmdHis');
    }
}
