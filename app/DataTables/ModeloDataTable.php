<?php

namespace App\DataTables;

use App\Models\Modelo;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ModeloDataTable extends DataTable
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
            ->query($query)
            // ->addColumn('created_at', function ($row) {
            //     return Carbon::parse($row->created_at)->format('F j, Y - g:i a');
            // })
            // ->addColumn('updated_at', function ($row) {
            //     return Carbon::parse($row->updated_at)->format('F j, Y - g:i a');
            // })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('model.edit', $row->id) . '" style="display: inline-block;">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm" >Edit</button>
            </a>
            <form action="' . route('model.destroy', $row->id) . '"method="POST"
                style="display: inline-block;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
            </form>';
                return $actionBtn;
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Modelo $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $modelos = DB::table('modelos')
            ->join('manufacturers', 'manufacturers.id', 'modelos.manufacturer_id')
            ->join('types', 'types.id', 'modelos.type_id')
            ->select('modelos.*', 'types.name as type', 'manufacturers.name as manufacturer');
        return $modelos;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('modelo-table')
            ->columns($this->getColumns())
            ->addTableClass('table-bordered')
            ->responsive(true)
            ->autoWidth(true)
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
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
            Column::make('year')
                ->addClass('text-center'),
            Column::make('type')
                ->addClass('text-center')
                ->searchable(false),
            Column::make('manufacturer')
                ->addClass('text-center')
                ->searchable(false),
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
        return 'Modelo_' . date('YmdHis');
    }
}
