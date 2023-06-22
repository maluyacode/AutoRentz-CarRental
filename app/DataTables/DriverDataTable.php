<?php

namespace App\DataTables;

use App\Models\Driver;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverDataTable extends DataTable
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
            ->addColumn('image_path', function ($row) {
                $image_path = [];
                foreach (explode('=', $row->image_path) as $key => $image) {
                    $image_path[] = '<a href="' . asset("storage/images/" . $image) . '" target="_blank">
                        <img src=" ' . asset("storage/images/" . $image) . '" width="50px" height="50px" style="margin: 5px"></a>';
                }
                $displayImage = implode("", $image_path);
                $container = '<div style="display: flex; flex:direction: row;">' . $displayImage . '</div>';
                return $container;
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('drivers.edit', $row->id) . '" style="display: inline-block;">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm" >Edit</button>
            </a>
            <form action="' . route('drivers.destroy', $row->id) . '"method="POST"
                style="display: inline-block;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
            </form>';
                return $actionBtn;
            })->rawColumns(['action', 'image_path']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Driver $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Driver $model)
    {
        $drivers = DB::table('drivers')
            ->whereNotIn('id', [1]);
        return $drivers;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('driver-table')
            ->addTableClass('table-bordered')
            ->responsive(true)
            ->autoWidth(true)
            ->columns($this->getColumns())
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
            Column::make('id'),
            Column::make('fname')
                ->title('First Name'),
            Column::make('lname')
                ->title('Last Name'),
            Column::make('licensed_no'),
            Column::make('description'),
            Column::make('address'),
            Column::make('driver_status')
                ->title('Status'),
            Column::make('image_path')
                ->title('IMAGES'),
            // Column::make('created_at')
            //     ->title('CREATED AT')
            //     ->addClass('text-center')
            //     ->width(100),
            // Column::make('updated_at')
            //     ->title('LAST UPDATE')
            //     ->addClass('text-center')
            //     ->width(100),
            Column::computed('action')
                ->exportable(true)
                ->printable(true)
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
        return 'Driver_' . date('YmdHis');
    }
}
