@extends('layouts.app')

@section('content')
    <div class="row">
        <form action="{{ route('locations', 'search') }}" method="POST" style="display: flex; justify-content: center;" >
            @csrf
            <div class="form-group">
                <input type="text" class="form-control" style="width: 300px;" name="searchInput">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    <div class="row">
        @foreach ($locations as $location)
            <div class="col-md-6" style="display:flex; justify-content: center; margin-top:50px; ">
                <div class="card" style="width:90%;">
                    <div class="slider">
                        @foreach (explode('=', $location->image_path) as $key => $image)
                            <div class="slide">
                                <img class="card-img-top" src="{{ '/storage/images/' . $image }}" alt="Card image cap"
                                    style="height: 400px">
                            </div>
                        @endforeach
                        <button class="prev btn btn-dark"><</button>
                        <button class="next btn btn-dark">></button>
                    </div>
                    <div class="card-body">
                        <h2 class="card-text">
                            <span><strong>{{ $location->street }} -</strong></span>
                            <span><strong>{{ $location->baranggay }} -</strong></span>
                            <span><strong>{{ $location->city }}</strong></span>
                        </h2>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('styles')
    <style>
        .slider {
            position: relative;
        }

        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: transparent;
            border: none;
            color: white;
            font-size: 32px;
            cursor: pointer;
        }

        .prev:hover,
        .next:hover {
            color: #ccc;
        }

        .prev {
            left: 10px;
        }

        .next {
            right: 10px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Add slider functionality to each slider div
        let sliders = document.querySelectorAll('.slider');
        sliders.forEach(slider => {
            let slides = slider.querySelectorAll('.slide');
            let currentSlide = 0;
            let prevButton = slider.querySelector('.prev');
            let nextButton = slider.querySelector('.next');

            function showSlide() {
                slides.forEach(slide => {
                    slide.style.display = 'none';
                });
                slides[currentSlide].style.display = 'block';
            }

            function nextSlide() {
                currentSlide++;
                if (currentSlide >= slides.length) {
                    currentSlide = 0;
                }
                showSlide();
            }

            function prevSlide() {
                currentSlide--;
                if (currentSlide < 0) {
                    currentSlide = slides.length - 1;
                }
                showSlide();
            }

            showSlide();
            prevButton.addEventListener('click', prevSlide);
            nextButton.addEventListener('click', nextSlide);
        });
    </script>
@endsection
