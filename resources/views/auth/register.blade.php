@extends('layouts/app', ['activePage' => 'register', 'title' => 'Caramel Yoghurt Register'])

@section('content')
    <div class="full-page section-image" data-color="black" data-image="{{ asset('light-bootstrap/img/yoghurtbg.jpg') }}">
        <div class="content pt-5">
            <div class="container mt-5">    
                <div class="col-md-4 col-sm-6 ml-auto mr-auto">
                    <form class="form" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="card card-login card-hidden">
                            <!-- Centered, resized, and circular image -->
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <img src="{{ asset('light-bootstrap/img/yoghurtbg.jpg') }}" 
                                alt="Caramel Yoghurt Logo" 
                                style="width: 55px; height: 55px; border-radius: 50%;" />
                                </div>
                            <div class="card-header ">
                                <h3 class="header text-center">{{ __('Caramel Yoghurt') }}</h3>
                                <p class="header text-center">{{ __('Creamy perfection in every spoonful') }}</p>
                            </div>
                            <div class="card-body ">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name" class="col-md-6 col-form-label">{{ __('Full Name') }}</label>
            
                                          <div class="form-group">
                                                <input type="text" name="name" id="name" class="form-control" placeholder="{{ __('Enter your full name') }}" value="{{ old('name') }}" required autofocus>
                                            </div>
                                        <div class="form-group">   {{-- is-invalid make border red --}}
                                                <input type="email" name="email" value="{{ old('email') }}" placeholder="apexdev@gmail.com" class="form-control" required>
                                            </div>
                                        <div class="form-group">
                                                <input type="password" name="password" class="form-control" required >
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="password_confirmation" placeholder="Password Confirmation" class="form-control" required autofocus>
                                            </div>
                                       
                                    </div>
                                </div>
                                <div class="card-footer ml-auto mr-auto">
                                    <div class="container text-center" >
                                        <button type="submit" class="btn btn-warning btn-wd">{{ __('Create Account') }}</button>
                                    </div>
                                    <div class="disc"><p>{{ __('Have an account?') }} 
                                        <a href="{{ route('login') }}">{{ __('Sign in here') }}</a><br>
                                        
                                        <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                                    </div>
                                    </p></div>
                                </div>
                            </div>
                        </div>
                    </form>
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