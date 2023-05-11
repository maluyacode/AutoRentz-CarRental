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
                        <li class="breadcrumb-item active">Create</li>
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
                            <h3 class="card-title">Add New Car</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('car.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>Plate No:</label>
                                            <input type="text" class="form-control" placeholder="Enter ..."
                                                name="platenumber" value="{{ old('platenumber') }}">
                                            @error('platenumber')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Price Per Day</label>
                                            <input type="text" class="form-control" placeholder="Enter ..."
                                                name="price_per_day" value="{{ old('price_per_day') }}">
                                            @error('price_per_day')
                                                <small style="color: red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Cost Price</label>
                                            <input type="text" class="form-control" placeholder="Enter ..."
                                                name="cost_price" value="{{ old('cost_price') }}">
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
                                            <textarea type="text" class="form-control" rows="5" placeholder="Enter ..." name="description">{{ old('description') }}</textarea>
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
                                                        name="image_path[]" multiple>

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
                                        @error('image_path')
                                            <small style="color: red">{{ $message }}</small>
                                        @enderror
                                        <div class="form-control-sm" style="width: 300px">
                                            <label class="col-form-label" for="inputSuccess">No: of Seats</label>
                                            <input type="number" class="form-control" id="inputSuccess"
                                                placeholder="Enter ..." name="seats" min="2" max="15"
                                                value="{{ old('seats') }}">
                                            @error('seats')
                                                <small style="color: red;">{{ $message }}</small>
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
                                                @foreach ($models as $carmodel)
                                                    <option value="{{ $carmodel->id }}"
                                                        {{ old('model_id') == $carmodel->id ? 'selected' : '' }}>
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
                                                @foreach ($transmissions as $transmission)
                                                    <option value="{{ $transmission->id }}"
                                                        {{ old('transmission_id') == $transmission->id ? 'selected' : '' }}>
                                                        {{ $transmission->name }}
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
                                                @foreach ($fuels as $fuel)
                                                    <option value="{{ $fuel->id }}"
                                                        {{ old('fuel_id') == $fuel->id ? 'selected' : '' }}>
                                                        {{ $fuel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach ($accessories as $accessory)
                                        <div class="col-sm-2">
                                            <!-- checkbox -->
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="accessories_id[]" value="{{ $accessory->id }}"
                                                        {{ is_array(old('accessories_id')) && in_array($accessory->id, old('accessories_id')) ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $accessory->name }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
