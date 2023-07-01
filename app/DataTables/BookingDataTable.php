<?php

namespace App\DataTables;

use App\CustomerClass;
use App\Models\Booking;
use App\Models\Driver;
use Barryvdh\Debugbar\Facades\Debugbar;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class BookingDataTable extends DataTable
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
            ->addColumn('start_date', function ($book) {
                return date_format(date_create($book->start_date), "M d, Y");
            })
            ->addColumn('end_date', function ($book) {
                return date_format(date_create($book->end_date), "M d, Y ");
            })
            ->addColumn('drive_type', function ($book) {
                return $book->driver ?
                    "With Driver: {$book->driver->fname} {$book->driver->lname}" :
                    "Self Drive";
            })
            ->addColumn('rent_price', function ($book) {
                $days = date_diff(
                    date_create($book->end_date),
                    date_create($book->start_date)
                )->format('%a') + 1;
                $accessoriesFee = $book->car->accessories->map(function ($accessory) {
                    return $accessory->fee;
                })->sum();
                return "&#8369;" . number_format(($book->car->price_per_day + $accessoriesFee) * $days, 2);
            })
            ->addColumn('transaction', function ($book) {
                $days = date_diff(
                    date_create($book->end_date),
                    date_create($book->start_date)
                )->format('%a') + 1;
                return $book->address ? "Delivery: {$days} days" : "Pick Up: {$days} days";
            })
            ->addColumn('car', function ($book) {
                return '<a href="' . route('cardetails', $book->car->id) . '">
                ' . $book->car->platenumber . '
                </a>';
            })
            ->addColumn('locations', function ($row) {
                return $row->address ?
                    "<b>Deliver</b>: {$row->address}" :
                    "<b>PickUp:</b> {$row->picklocation->street} {$row->picklocation->baranggay} {$row->picklocation->city} <br><br>
                 <b>Return:</b> {$row->returnlocation->street} {$row->picklocation->baranggay} {$row->picklocation->city}";
            })
            ->addColumn('customer', function ($book) {
                return $book->customer->name;
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('editBooking', $row->id) . '" class="btn btn-block bg-gradient-primary btn-sm"  style="display: inline-block; width: 100px; margin:5px;"> Edit </a>
            <form action="' . route('deleteBooking', $row->id) . '" method="POST" style="display: inline-block; width: 100px; margin:5px;">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="' . csrf_token() . '">
            <button type="submit" class="btn btn-block bg-gradient-danger btn-sm">Delete</button>
            </form>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'transaction', 'locations', 'car', 'rent_price']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Booking $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Booking $model)
    {
        $bookings = Booking::with([
            'customer',
            'driver',
            'car',
            'car.accessories',
            'car.modelo',
            'picklocation',
            'returnlocation'
        ])->withTrashed()->get();
        Debugbar::info($bookings);
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
            ->setTableId('booking-table')
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
                ->title('ID'),
            Column::make('start_date')
                ->className('booking-class'),
            Column::make('end_date')
                ->className('booking-class'),
            Column::make('customer')
                ->className('booking-class'),
            Column::make('rent_price')
                ->className('booking-class')
                ->addClass('text-center'),
            Column::make('transaction')
                ->className('booking-class'),
            Column::make('locations')
                ->className('booking-class'),
            Column::make('drive_type')
                ->className('booking-class'),
            Column::make('car')
                ->className('booking-class'),
            Column::make('status')
                ->className('booking-class')
                ->addClass('text-center'),
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
        return 'Booking_' . date('YmdHis');
    }
}
