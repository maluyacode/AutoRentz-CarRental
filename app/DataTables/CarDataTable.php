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
            // ->addColumn('accessories', function ($cars) {
            //     return $cars->accessories->map(function ($accessory) {
            //         return $accessory->name;
            //     })->implode('<br>');
            // })
            // ->addColumn('image_path', function ($cars) {
            //     // $images[] = explode('=', $cars->image_path);
            //     // $carImages = array_map(function ($image){
            //     //     Debugbar::info($image);
            //     //     return '<a href="' . asset("storage/images/" . $image) . '" target="_blank">
            //     //     <img src=" ' . asset("storage/images/" . $image) . '" width="50px" height="50px" style="margin: 5px"></a>';
            //     // }, $images);
            //     $image_path = [];
            //     foreach (explode('=', $cars->image_path) as $key => $image) {
            //         $image_path[] = '<a href="' . asset("storage/images/" . $image) . '" target="_blank">
            //                 <img src=" ' . asset("storage/images/" . $image) . '" width="50px" height="50px" style="margin: 5px"></a>';
            //     }
            //     $displayImage = implode("", $image_path);
            //     $container = '<div style="display: flex; flex:direction: row;">' . $displayImage . '</div>';
            //     return $container;
            // })
            // ->addColumn('model', function($car){
            //     return $car->modelo->name;
            // })
            // ->addColumn('manufacturer', function($car){
            //     return $car->modelo->manufacturer->name;
            // })
            // ->addColumn('type', function($car){
            //     return $car->modelo->type->name;
            // })
            // ->addColumn('fuel', function($car){
            //     return $car->fuel->name;
            // })
            // ->addColumn('transmission', function($car){
            //     return $car->transmission->name;
            // })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('car.edit', $row->id) . '"style="display: inline-block; width:80%; margin: 2px 0;">
                <button type="button" class="btn btn-block bg-gradient-secondary btn-sm" >Details</button>
            </a>
            <a href="' . route('car.edit', $row->id) . '" style="display: inline-block; width:80%;  margin: 2px 0;">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm" >Edit</button>
            </a>
            <form action="' . route('car.delete', $row->id) . '" method="POST" style="display: inline-block; width:80%;  margin: 2px 0;">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
            </form>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'image_path', 'accessories', 'description', 'price_per_day', 'cost_price']); // Combined rawColumns method call

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Car $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        // $cars = DB::table('cars')
        //     ->join('fuels', 'fuels.id', 'cars.fuel_id')
        //     ->join('transmissions', 'transmissions.id', 'cars.transmission_id')
        //     ->join('modelos', 'modelos.id', 'cars.modelos_id')
        //     ->join('types', 'types.id', 'modelos.type_id')
        //     ->join('manufacturers', 'manufacturers.id', 'modelos.manufacturer_id')
        //     ->select(
        //         'cars.*',
        //         'fuels.name as fuel',
        //         'transmissions.name as transmission',
        //         'modelos.name as model',
        //         'modelos.year as year',
        //         'types.name as type',
        //         'manufacturers.name as manufacturer',
        //     );

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
            // Column::make('manufacturer')
            //     ->addClass('text-center'),
            // Column::make('type')
            //     ->addClass('text-center'),
            // Column::make('fuel')
            //     ->addClass('text-center'),
            // Column::make('transmission')
            //     ->addClass('text-center'),
            // Column::make('image_path')
            //     ->addClass('text-center'),
            // Column::make('created_at'),
            // Column::make('updated_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                // ->width(120)
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
