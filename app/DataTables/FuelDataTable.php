<?php

namespace App\DataTables;

use App\Models\Fuel;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class FuelDataTable extends DataTable
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
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('fuel.edit', $row->id) . '" style="display: inline-block;">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm" >Edit</button>
            </a>
            <form action="' . route('fuel.destroy', $row->id) . '"method="POST"
                style="display: inline-block;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
            </form>';
                return $actionBtn;
            })->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Fuel $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Fuel $model)
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
            ->setTableId('fuel-table')
            ->columns($this->getColumns())
            ->addTableClass('table-bordered')
            ->responsive(true)
            ->autoWidth(true)
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('colvis'),
                // Button::make('export'),
                // Button::make('print'),
                // Button::make('reset'),
                // Button::make('reload')
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
                ->title('ID NO:')
                ->addClass('text-center'),
            Column::make('name')
                ->title('NAME')
                ->addClass('text-center'),
            // Column::make('created_at')
            //     ->title('CREATED AT')
            //     ->width(100)
            //     ->addClass('text-center'),
            // Column::make('updated_at')
            //     ->title('LAST UPDATE')
            //     ->width(100)
            //     ->addClass('text-center'),
            Column::computed('action')
                ->title('ACTION')
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
        return 'Fuel_' . date('YmdHis');
    }
}
