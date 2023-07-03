@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="padding-bottom: 50px; padding-top: 50px; height: max-content; background-color: #FFF9C9">
        <div class="row" style="display: flex; justify-content: center;">
            <div class="col-6">
                There are {{ $searchResults->count() }} results.
                @foreach ($searchResults->groupByType() as $type => $modelSearchResults)
                    <h2 style="text-transform: capitalize">{{ $type }}</h2>

                    @foreach ($modelSearchResults as $searchResult)
                        <ul class="list-group">
                            <li class="list-group-item" style="margin: 3px"><a href="{{ $searchResult->url }}">{{ $searchResult->title }}</a></li>
                        </ul>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
@endsection
