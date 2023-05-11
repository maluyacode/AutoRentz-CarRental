@extends('admin.index')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Car</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Car</a></li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10" style="margin: auto">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Update Car</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('car.update', $car->id) }}" method="POST" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>Plate No:</label>
                                            <input type="text" class="form-control" placeholder="Enter ..."
                                                name="platenumber" value="{{ $car->platenumber }}">
                                            @error('platenumber')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Price Per Day</label>
                                            <input type="text" class="form-control" placeholder="Enter ..."
                                                name="price_per_day" value="{{ $car->price_per_day }}">
                                            @error('price_per_day')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Cost Price</label>
                                            <input type="text" class="form-control" placeholder="Enter ..."
                                                name="cost_price" value="{{ $car->cost_price }}">
                                            @error('cost_price')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <!-- textarea -->
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea type="text" class="form-control" rows="4  " placeholder="Enter ..." name="description">{{ $car->description }}</textarea>
                                            @error('description')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" style="margin:0">
                                            <label for="exampleInputFile">Car Image</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="exampleInputFile"
                                                        onchange="displayFileName()" style="cursor: pointer;"
                                                        name="image_path[]" value="{{ $car->image_path }}" multiple>
                                                    <script>
                                                        function displayFileName() {
                                                            var input = document.getElementById('exampleInputFile');
                                                            var fileName = input.files[0].name;
                                                            var output = document.getElementById('file-name');
                                                            output.innerText = fileName;
                                                        }
                                                    </script>
                                                    <label class="custom-file-label" for="exampleInputFile"
                                                        id="file-name">Choose
                                                        file</label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('image_path.*')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                        <div class="form-control-sm" style="width: 300px">
                                            <label class="col-form-label" for="inputSuccess">No: of Seats</label>
                                            <input type="number" class="form-control" id="inputSuccess"
                                                placeholder="Enter ..." name="seats" min="2" max="15"
                                                value="{{ $car->seats }}">
                                            @error('seats')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 25px">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="inputSuccess">Car Model</label>
                                            <select class="form-control" id="inputSuccess" placeholder="Enter ..."
                                                name="model_id">
                                                <option value="{{ $car->modelID }}">
                                                    {{ $car->modelname . ' - ' . $car->typename . ' - ' . $car->manufacturername . ' - ' . $car->modelyear }}
                                                </option>
                                                @foreach ($models as $carmodel)
                                                    <option value="{{ $carmodel->id }}">
                                                        {{ $carmodel->name . ' - ' . $carmodel->typename . ' - ' . $carmodel->manufacturername . ' - ' . $carmodel->year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="col-form-label" for="inputWarning">Transmission Type</label>
                                            <select class="form-control" id="inputSuccess" placeholder="Enter ..."
                                                name="transmission_id">
                                                <option value="{{ $car->transmission_id }}">{{ $car->transmissionname }}
                                                </option>
                                                @foreach ($transmissions as $transmission)
                                                    <option value="{{ $transmission->id }}">{{ $transmission->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="col-form-label" for="inputError">Fuel Type</label>
                                            <select class="form-control" id="inputSuccess" placeholder="Enter ..."
                                                name="fuel_id">
                                                <option value="{{ $car->fuel_id }}">{{ $car->fuelname }}</option>
                                                @foreach ($fuels as $fuel)
                                                    <option value="{{ $fuel->id }}">{{ $fuel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach ($accessories as $accessory)
                                        @if (in_array($accessory->id, array_keys($carAccessory)))
                                            <div class="col-sm-2">
                                                <!-- checkbox -->
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="accessories_id[]" value="{{ $accessory->id }}" checked>
                                                        <label class="form-check-label">{{ $accessory->name }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-sm-2">
                                                <!-- checkbox -->
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="accessories_id[]" value="{{ $accessory->id }}">
                                                        <label class="form-check-label">{{ $accessory->name }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#exampleModalCenter">
                                    View Images
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">{{ $car->modelname . ' ' . $car->typename . ' ' . $car->manufacturername . ' ' . $car->modelyear }} - Images</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    @foreach ($images[] = explode('=', $car->image_path) as $key => $image)
                                                        <img src="{{ '/storage/images/' . $image }}" alt=""
                                                            width="100px" height="100px" style="margin:5px">
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="display:flex; justify-content:end">
                                    <button type="submit" class="btn btn-block bg-gradient-warning btn-lg"
                                        style="width: 100px;">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
