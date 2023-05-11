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
            ->query($query)
            ->addColumn('description', function ($row) {
                return '<details style="text-align:left; ">
                        <summary>Show more</summary>
                        <p>
                        ' . $row->description . '
                        </p>
                        </details>';
            })
            ->addColumn('accessories', function ($row) {
                $accessories = new Accessorie;
                $accessoryColumn = []; // Initialize empty array
                foreach ($accessories->accessory($row->id) as $accessory) {
                    $accessoryColumn[] = '<li>' . $accessory->name . '</li>';
                }
                $displayAccessories = implode("", $accessoryColumn);
                return '<details style="text-align:left; ">
                        <summary>Show</summary>
                        <p>
                        <ul>' . $displayAccessories . '</ul>
                        </p>
                        </details>';
            })
            ->addColumn('image_path', function ($row) {
                $image_path = []; // Initialize empty array
                foreach (explode('=', $row->image_path) as $key => $image) {
                    $image_path[] = '<a href="' . asset("storage/images/" . $image) . '" target="_blank">
                        <img src=" ' . asset("storage/images/" . $image) . '" width="50px" height="50px" style="margin: 5px"></a>';
                }
                $displayImage = implode("", $image_path);
                $container = '<div style="display: flex; flex:direction: row;">' . $displayImage . '</div>';
                return $container;
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('car.edit', $row->id) . '" style="display: inline-block;">
            <button type="button" class="btn btn-block bg-gradient-primary btn-sm" >Edit</button>
        </a>
        <form action="' . route('car.delete', $row->id) . '" method="POST" style="display: inline-block;">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="' . csrf_token() . '">
            <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
        </form>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'image_path', 'accessories', 'description']); // Combined rawColumns method call

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Car $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $cars = DB::table('cars')
            ->join('fuels', 'fuels.id', 'cars.fuel_id')
            ->join('transmissions', 'transmissions.id', 'cars.transmission_id')
            ->join('modelos', 'modelos.id', 'cars.modelos_id')
            ->join('types', 'types.id', 'modelos.type_id')
            ->join('manufacturers', 'manufacturers.id', 'modelos.manufacturer_id')
            ->select(
                'cars.*',
                'fuels.name as fuel',
                'transmissions.name as transmission',
                'modelos.name as model',
                'modelos.year as year',
                'types.name as type',
                'manufacturers.name as manufacturer',
            );
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
                ->title('ID NO:')
                ->addClass('text-center'),
            Column::make('platenumber')
                ->title('PLATE NUMBER')
                ->addClass('text-center'),
            Column::make('price_per_day')
                ->title('RENT PRICE')
                ->addClass('text-center'),
            Column::make('accessories')
                ->title('ACCESSORIES')
                ->addClass('text-center')
                ->searchable(true),
            Column::make('seats')
                ->title('SEATS')
                ->addClass('text-center'),
            Column::make('cost_price')
                ->title('COST PRICE')
                ->addClass('text-center'),
            Column::make('car_status')
                ->title('STATUS')
                ->addClass('text-center'),
            Column::make('model')
                ->title('MODEL')
                ->addClass('text-center')
                ->searchable(false),
            Column::make('manufacturer')
                ->title('MANUFACTURER')
                ->addClass('text-center')
                ->searchable(false),
            Column::make('type')
                ->title('TYPE')
                ->addClass('text-center')
                ->searchable(false),
            Column::make('fuel')
                ->title('FUEL')
                ->addClass('text-center')
                ->searchable(false),
            Column::make('transmission')
                ->title('TRANSMISSION')
                ->addClass('text-center')
                ->searchable(false),
            Column::make('description')
                ->title('DESCRIPTION')
                ->addClass('text-center'),
            Column::make('image_path')
                ->title('IMAGES')
                ->addClass('text-center'),
            // Column::make('created_at'),
            // Column::make('updated_at'),
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
        return 'Car_' . date('YmdHis');
    }
}
