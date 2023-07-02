<?php

namespace App\DataTables;

use App\Models\Accessorie;
use App\Models\Car;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;
use Barryvdh\Debugbar\Facades\Debugbar;

class CarDataTable extends DataTable
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
            ->addColumn('price_per_day', function ($car) {
                Debugbar::info($car->accessories);
                $fee = $car->accessories->map(function ($accessory) {
                    return $accessory->fee;
                })->sum();
                return "&#8369;" . number_format($fee + $car->price_per_day, 2);
            })
            ->addColumn('cost_price', function ($car) {
                return "&#8369;" . number_format($car->cost_price, 2);
            })
            ->addColumn('model', function ($car) {
                return $car->modelo->name . " " . $car->modelo->manufacturer->name . " " . $car->modelo->type->name;
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('car.edit', $row->id) . '"style="display: inline-block; width:80%; margin: 2px 0; text-decoration: none;">
                <button type="button" class="btn btn-block bg-gradient-secondary btn-sm" >Details</button>
            </a>
            <a href="' . route('car.edit', $row->id) . '" style="display: inline-block; width:80%;  margin: 2px 0; text-decoration: none;">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm" >Edit</button>
            </a>
            <form action="' . route('car.delete', $row->id) . '" method="POST" style="display: inline-block; width:80%;  margin: 2px 0;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
            </form>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'image_path', 'accessories', 'description', 'price_per_day', 'cost_price']);

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Car $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $cars = Car::with([ // eager loading
            'accessories',
            'transmission',
            'fuel',
            'modelo.manufacturer', // nested eager loading
            'modelo.type'
        ])->get();
        DebugBar::info($cars);
        return $cars;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('car-table')
            ->columns($this->getColumns())
            ->addTableClass('table-bordered')
            ->autoWidth(false)
            ->responsive(true)
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
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
            Column::make('platenumber')
                ->addClass('text-center'),
            Column::make('model')
                ->addClass('text-center'),
            Column::make('price_per_day')
                ->addClass('text-center'),
            // Column::make('accessories')
            //     ->addClass('text-center'),
            Column::make('seats')
                ->addClass('text-center'),
            Column::make('cost_price')
                ->addClass('text-center'),
            Column::make('car_status')
                ->addClass('text-center')
                ->addClass('uppercase'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
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
        return 'Car_' . date('YmdHis');
    }
}
