<?php

namespace App\DataTables;

use App\Models\Confirm;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;
use App\CustomerClass;
use App\Models\Driver;

class ConfirmDataTable extends DataTable
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
            ->addColumn('drivetype', function ($row) {
                if ($row->drivetype == "with driver") {
                    if ($row->driver_id > 1) {
                        $driverInfo = Driver::find($row->driver_id);
                        return 'With Driver: ' . $driverInfo->fname . ' ' . $driverInfo->lname;
                    } else {
                        return 'With Driver: Please assign';
                    }
                } else {
                    return 'Self Drive';
                }
            })
            ->addColumn('totalRentPrice', function ($row) {
                $compute = new CustomerClass();
                $accessory = DB::table('cars as ca')
                    ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
                    ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
                    ->get();
                return '<b>PHP</b>' . $compute->computationDisplay($row->start_date, $row->end_date, $row->price_per_day, $accessory, $row->car_id);
            })
            ->addColumn('modelName', function ($row) {
                return '<a href="' . route('cardetails', $row->car_id) . '">
                ' . $row->modelName . ' ' . $row->year . '
                </a>';
            })
            ->addColumn('transaction', function ($row) {
                if ($row->address == null) {
                    $transaction = 'Pick Up<br>' . $row->days . ' day(s)';
                } else {
                    $transaction = 'Delivery<br>' . $row->days . ' days';
                }
                return $transaction;
            })
            ->addColumn('locations', function ($row) {
                if ($row->address == null) {
                    $locations = '<p style="font-size: 14px"><strong>Pick Up Location: </strong> ' . $row->pickuplocation . '<br>
                                    <strong>Return Location: </strong>' . $row->returnlocation . '</p>';
                } else {
                    $locations = '<p style="font-size: 14px"><strong>Address: </strong> ' . $row->address . '</br>';
                }
                return $locations;
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('finishedBooking', $row->id) . '" class="btn btn-block bg-gradient-success btn-sm" style="display: inline-block; margin:5px; width:100px">Finished</a>';
            })
            ->rawColumns(['action', 'transaction', 'locations', 'modelName', 'totalRentPrice']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Confirm $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $bookings = DB::table('users as us')
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->join('bookings as bo', 'cu.id', 'bo.customer_id')
            ->join('cars as ca', 'ca.id', 'bo.car_id')
            ->join('modelos as mo', 'mo.id', 'ca.modelos_id')
            ->leftJoin('locations as lo1', 'lo1.id', 'bo.pickup_location_id')
            ->leftJoin('locations as lo2', 'lo2.id', 'bo.return_location_id')
            ->select(
                'bo.*',
                'mo.name as modelName',
                'ca.price_per_day as price_per_day',
                'mo.year',
                'cu.name as customer_name',
                'cu.email as customer_email',
                'cu.phone as customer_phone',
                'ca.platenumber',
                DB::raw('CONCAT(lo1.street, ", ", lo1.baranggay, ", ", lo1.city) as pickuplocation'),
                DB::raw('CONCAT(lo2.street, ", ", lo2.baranggay, ", ", lo2.city) as returnlocation'),
                DB::raw('(DATEDIFF(bo.end_date, bo.start_date)) + 1 as days'),
                DB::raw("CASE
                    WHEN bo.driver_id IS NOT NULL THEN 'with driver'
                    WHEN bo.driver_id IS NULL THEN 'self drive'
                    END as drivetype")
            )->where('bo.status', 'confirmed');

        if (request()->has('search.value')) {
            $searchValue = request('search.value');
            $bookings->where(function ($query) use ($searchValue) {
                $query->where('bo.id', 'LIKE', "%{$searchValue}%")
                    ->orWhere('bo.address', 'LIKE', "%{$searchValue}%")
                    ->orWhere('bo.status', 'LIKE', "%{$searchValue}%")
                    ->orWhere('ca.platenumber', 'LIKE', "%{$searchValue}%")
                    ->orWhere('cu.name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('cu.email', 'LIKE', "%{$searchValue}%")
                    ->orWhere('cu.phone', 'LIKE', "%{$searchValue}%")
                    ->orWhere('bo.start_date', 'LIKE', "%{$searchValue}%")
                    ->orWhere('bo.end_date', 'LIKE', "%{$searchValue}%")
                    ->orWhere('lo1.street', 'LIKE', "%{$searchValue}%")
                    ->orWhere('lo1.baranggay', 'LIKE', "%{$searchValue}%")
                    ->orWhere('lo1.city', 'LIKE', "%{$searchValue}%")
                    ->orWhere('lo2.street', 'LIKE', "%{$searchValue}%")
                    ->orWhere('lo2.baranggay', 'LIKE', "%{$searchValue}%")
                    ->orWhere('lo2.city', 'LIKE', "%{$searchValue}%")
                    ->orWhere('mo.name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('ca.platenumber', 'LIKE', "%{$searchValue}%");
            });
        }

        return $bookings;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('confirm-table')
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
                ->searchable(false),
            Column::make('start_date')
                ->className('booking-class')
                ->searchable(false)
                ->title('START DATE'),
            Column::make('end_date')
                ->className('booking-class')
                ->searchable(false)
                ->title('END DATE'),
            Column::make('totalRentPrice')
                ->className('booking-class')
                ->title('TOTAL RENT PRICE')
                ->addClass('text-center'),
            Column::make('transaction')
                ->className('booking-class')
                ->title('TRANSACTION')
                ->addClass('text-center'),
            Column::make('locations')
                ->className('booking-class')
                ->title('LOCATION(S)'),
            Column::make('drivetype')
                ->className('booking-class')
                ->title('DRIVE TYPE')
                ->searchable(false)
                ->addClass('text-center')
                ->width('200px'),
            Column::make('customer_name')
                ->className('booking-class')
                ->searchable(false)
                ->title('CUSTOMER')
                ->addClass('text-center'),
            Column::make('customer_email')
                ->searchable(false)
                ->title('EMAIL')
                ->addClass('text-center'),
            Column::make('customer_phone')
                ->className('booking-class')
                ->searchable(false)
                ->title('PHONE')
                ->addClass('text-center'),
            Column::make('platenumber')
                ->className('booking-class')
                ->searchable(false)
                ->title('CAR PLATE NO')
                ->addClass('text-center'),
            Column::make('modelName')
                ->className('booking-class')
                ->searchable(false)
                ->title('CAR MODEL')
                ->addClass('text-center'),
            // Column::make('status')
            //     ->className('booking-class')
            //     ->searchable(false)
            //     ->title('STATUS')
            //     ->addClass('text-center'),
            // Column::make('created_at'),
            // Column::make('updated_at'),
            Column::computed('action')
                ->title('ACTION')
                ->exportable(false)
                ->printable(false)
                ->width(60)
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
        return 'Confirm_' . date('YmdHis');
    }
}
