<?php

namespace App\DataTables;

use App\Models\Driver;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\Debugbar\Twig\Extension\Debug;
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
            ->collection($query)
            ->addColumn('image', function ($row) {
                $media = $row->getMedia('images')->first();
                if ($media) {
                    return '<div class="data-image"><img class="model-image" src="' . $media->getUrl('thumb') . '"></div>';
                } else {
                    return "No images in media";
                }
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<button class="btn btn-primary bi bi-pencil-square edit"  data-toggle="modal" data-target="#ourModal" data-id="' . $row->id . '"></button>
                            <button class="btn btn-danger   bi bi-trash3 delete" data-id="' . $row->id . '"></button>';
                return $actionBtn;
            })->rawColumns(['action', 'image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Driver $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Driver $model)
    {
        $drivers = Driver::with(['media'])
            ->whereNotIn('id', [1])
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        Debugbar::info($drivers);
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
            ->autoWidth(false)
            ->responsive(true)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create')->action(false),
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
                ->title('ID'),
            Column::make('image')
                ->title('Image'),
            Column::make('fname')
                ->title('First Name'),
            Column::make('lname')
                ->title('Last Name'),
            Column::make('licensed_no'),
            Column::make('description'),
            Column::make('address'),
            Column::make('driver_status')
                ->title('Status'),
            // Column::make('image_path')
            //     ->title('Images'),
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
