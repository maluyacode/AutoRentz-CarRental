<?php

namespace App\DataTables;

use App\Models\Transmission;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class TransmissionDataTable extends DataTable
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
                $actionBtn = '<button type="button" data-toggle="modal" data-target="#exampleModal" class="btn btn-block bg-gradient-primary btn-sm edit" data-id=' . $row->id . ' data-name="' . $row->name . '">
                                Edit
                            </button>
                    <button type="button" class="btn btn-block bg-gradient-danger btn-sm delete" data-id=' . $row->id . ' >Delete</button>';
                return $actionBtn;
            })->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transmission $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transmission $model)
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
            ->setTableId('transmission-table')
            ->addTableClass('table-bordered')
            ->responsive(true)
            ->autoWidth(true)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
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
            Column::make('name')
                ->addClass('text-center'),
            // Column::make('created_at')
            //     ->width(100)
            //     ->addClass('text-center'),
            // Column::make('updated_at')
            //     ->width(100)
            //     ->addClass('text-center'),
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
        return 'Transmission_' . date('YmdHis');
    }
}
