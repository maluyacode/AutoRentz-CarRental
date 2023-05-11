<?php

namespace App\DataTables;

use App\Models\Customer;
use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class UserDataTable extends DataTable
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
            ->addColumn('image_path', function ($row) {
                return '<img src=" ' . asset($row->image_path) . '" width="50px" height="50px" style="margin: 5px">';
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('users.edit', $row->user_id) . '" style="display: inline-block;">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm" >Edit</button>
            </a>
            <form action="' . route('users.destroy', $row->user_id) . '"method="POST"
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
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $searchValue = request('search.value');
        $users =  DB::table('users')
            ->join('customers', 'users.id', 'customers.user_id')
            ->select(
                'customers.name',
                'customers.id',
                'customers.email',
                'customers.address',
                'customers.phone',
                'customers.image_path',
                'users.id as user_id',
                'users.role as role'
            )->where('user_id', 'LIKE', "%{$searchValue}%")
            ->orWhere('customers.name', 'LIKE', "%{$searchValue}%")
            ->orWhere('customers.email', 'LIKE', "%{$searchValue}%")
            ->orWhere('customers.address', 'LIKE', "%{$searchValue}%")
            ->orWhere('customers.phone', 'LIKE', "%{$searchValue}%")
            ->orWhere('users.role', 'LIKE', "%{$searchValue}%");

        return $users;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('customer-table')
            ->columns($this->getColumns())
            ->addTableClass('table-bordered')
            ->responsive(true)
            ->autoWidth(true)
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create')->urldecode('/admin/create/users'),
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
            Column::make('user_id')
                ->title('ID')
                ->searchable(false),
            Column::make('name')
                ->title('NAME')
                ->searchable(false),
            Column::make('phone')
                ->searchable(false)
                ->title('PHONE'),
            Column::make('address')
                ->searchable(false)
                ->title('ADDRESS'),
            Column::make('image_path')
                ->title('IMAGES')
                ->searchable(false),
            Column::make('email')
                ->title('EMAIL')
                ->searchable(false),
            Column::make('role')
                ->title('ROLE')
                ->addClass('role'),
            // Column::make('created_at'),
            // Column::make('updated_at'),
            Column::computed('action')
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
        return 'User_' . date('YmdHis');
    }
}
