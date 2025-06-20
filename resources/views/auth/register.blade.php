@extends('layouts.app', ['activePage' => 'register', 'title' => 'Create Account'])

@section('content')
    <div class="full-page register-page section-image" data-color="blue" data-image="{{ asset('light-bootstrap/img/yg-bk.jpg') }}">
        <div class="content">
            <div class="container">
                <div class="card card-register card-plain text-center">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-md-5 ml-auto">
                                <div class="media">
                                    <div class="media-left">
                                        <div class="icon">
                                            <i class="nc-icon nc-circle-09"></i>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <h4 style="color: rgba(4, 0, 4, 0.957)">{{ __('Create Your Account') }}</h4>
                                        <p>{{ __('Discover our artisanal collection of caramel-infused yoghurts, crafted with the finest ingredients and traditional techniques. By creating an account, you\'ll gain access to exclusive products, personalized recommendations, and be the first to know about our latest creamy creations.') }}</p>
                                    </div>
                                </div>
                                <div class="media">
                                    <div class="media-left">
                                        <div class="icon">
                                            <i class="nc-icon nc-preferences-circle-rotate"></i>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <h4 style="color: rgba(0, 0, 0, 0.957)">{{ __('Awesome Performances') }}</h4>
                                        <p>{{ __('Here you can discover all the delicious benefits of joining our caramel yogurt community. Let us show you the creamy value we bring to your dairy experience.') }}</p>
                                    </div>
                                </div>
                                
                              
                                
                            
                            </div>
                            <div class="col-md-4 mr-auto">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="card card-plain">
                                        <div class="content">
                                            <div class="form-group">
                                                <input type="text" name="name" id="name" class="form-control" placeholder="{{ __('Name') }}" value="{{ old('name') }}" required autofocus>
                                            </div>

                                            <div class="form-group">   {{-- is-invalid make border red --}}
                                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email" class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control" required >
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="password_confirmation" placeholder="Password Confirmation" class="form-control" required autofocus>
                                            </div>
                                            <div class="form-group d-flex justify-content-center">
                                                <div class="form-check rounded col-md-10 text-left">
                                                    <label class="form-check-label text-white d-flex align-items-center">
                                                        <input class="form-check-input" name="agree" type="checkbox" required >
                                                        <span class="form-check-sign"></span>
                                                        <b>{{ __('Agree with terms and conditions') }}</b>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="footer text-center">
                                                <button type="submit" class="btn btn-fill btn-neutral btn-wd">{{ __('Create Free Account') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col">
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-warning alert-dismissible fade show" >
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close"> &times;</a>
                                        {{ $error }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            demo.checkFullPageBackgroundImage();

            setTimeout(function() {
                // after 1000 ms we add the class animated to the login/register card
                $('.card').removeClass('card-hidden');
            }, 700)
        });
    </script>
@endpush