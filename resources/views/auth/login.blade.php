<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Insiden Rate Infeksius - TIM PPI RS Graha Sehat">
    <meta name="author" content="IRI PPI RSGS">
    <meta name="keywords"
        content="iri, infeksius, insiden, insiden rate, insiden rate infeksius, rsgs, graha sehat, kraksaan, rs, rumah sakit, graha sehat, ppi rsgs, gs, akreditasi">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />

    <title>Login Dashboard</title>

    <link href="{{ asset('admin/dist/bootstrap/bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/template.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/dist/toastr/toastr.min.css') }}" rel="stylesheet">
    @stack('css')
</head>

<body>
    {{-- {{ dd(Auth::user()) }} --}}
    <main class="d-flex w-100" style="background-color: #84dcc6; height: 94%">
        @if (session()->get('error'))
            <div class="alert alert-danger py-2 justify-content-center">
                <h3>{{ session()->get('error') }}</h3>
            </div>
        @endif
        @if (session()->get('success'))
            <div class="alert alert-success py-2 justify-content-center">
                <h3>{{ session()->get('success') }}</h3>
            </div>
        @endif
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">

                        <div class="card py-3 pb-5">
                            <div class="card-header">
                                <div class="text-center mt-4">
                                    <h1 class="h2">Selamat Datang</h1>
                                    <p class="lead">
                                        Silahkan login untuk melanjutkan
                                    </p>
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="{{ route('login') }}" autocomplete="false">
                                    @csrf

                                    <div class="form-group row mb-3">
                                        <label for="email"
                                            class="col-md-4 col-form-label text-md-right">{{ __('E-Mail') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="email" autofocus
                                                autocomplete="false" placeholder="Masukkan Email">

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label for="password"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password" required autocomplete="false"
                                                placeholder="Masukkan Password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-8 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Login') }}
                                            </button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer" style="height: 6%">
        <div class="container-fluid">
            <div class="row text-muted">
                <div class="col-6 text-left">
                    <p class="mb-0">
                        <a href="index.html" class="text-muted"><strong>RSGS IT</strong></a> &copy;
                    </p>
                </div>
                <div class="col-6 text-right">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a class="text-muted" href="#">Support</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script type="text/javascript">
        window.addEventListener("load", (event) => {
            console.log("page is fully loaded");
            hideLoader()
        });
    </script>
</body>

</html>
