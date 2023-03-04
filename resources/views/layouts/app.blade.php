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

    <title>Dashboard KMKP</title>

    <link href="{{ asset('admin/dist/bootstrap/bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/template.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/dist/toastr/toastr.min.css') }}" rel="stylesheet">
    @stack('css')
</head>

<body>
    <div class="loader show">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="{{ route('home') }}">
                    <span class="align-middle">Dashboard SIM</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Menu
                    </li>
                    <li
                        class="sidebar-item {{ in_array(request()->route()->getName(),['insiden.dashboard', 'insiden.dashboard.index', 'mutu.dashboard'])? 'active': '' }}">
                        <a data-bs-target="#mutu" data-bs-toggle="collapse" class="sidebar-link collapsed">
                            <i class="align-middle fas fa-person-booth"></i>
                            <span class="align-middle">Dashboard</span>
                        </a>
                        <ul id="mutu"
                            class="sidebar-dropdown list-unstyled collapse
                        {{ in_array(request()->route()->getName(),['insiden.dashboard', 'mutu.dashboard', 'insiden.dashboard.index'])? 'show': '' }}"
                            data-bs-parent="#sidebar">
                            @canany(['ppi', 'supersu'])
                                <li
                                    class="sidebar-item {{ request()->routeIs('insiden.dashboard.index') ? 'active' : '' }}">
                                    <a class="sidebar-link" href="{{ route('insiden.dashboard.index') }}">PPI</a>
                                </li>
                            @endcan
                            @canany(['mutu', 'supersu'])
                                <li class="sidebar-item {{ request()->routeIs('mutu.dashboard') ? 'active' : '' }}"><a
                                        class="sidebar-link" href="{{ route('mutu.dashboard') }}">MUTU</a>
                                </li>
                            @endcan
                        </ul>
                    </li>

                    {{-- <li
                        class="sidebar-item {{ in_array(request()->route()->getName(),['insiden.index'])? 'active': '' }}">
                        <a class="sidebar-link" href="{{ route('insiden.index') }}">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Insiden</span>
                        </a>
                    </li> --}}
                    @canany(['ppi', 'supersu'])
                        <li
                            class="sidebar-item {{ in_array(request()->route()->getName(),['insiden.index', 'insiden.history.index'])? 'active': '' }}">
                            <a data-bs-target="#insiden" data-bs-toggle="collapse" class="sidebar-link collapsed">
                                <i class="align-middle" data-feather="layout"></i>
                                <span class="align-middle">Insiden</span>
                            </a>
                            <ul id="insiden"
                                class="sidebar-dropdown list-unstyled collapse
                        {{ in_array(request()->route()->getName(),['insiden.index', 'insiden.history.index'])? 'show': '' }}"
                                data-bs-parent="#sidebar">
                                <li class="sidebar-item {{ request()->routeIs('insiden.index') ? 'active' : '' }}"><a
                                        class="sidebar-link" href="{{ route('insiden.index') }}">List</a>
                                </li>
                                <li class="sidebar-item {{ request()->routeIs('insiden.history.index') ? 'active' : '' }}">
                                    <a class="sidebar-link" href="{{ route('insiden.history.index') }}">History</a>
                                </li>

                            </ul>
                        </li>
                    @endcan
                    @can('supersu')
                        <li
                            class="sidebar-item {{ in_array(request()->route()->getName(),['mutu.generate.index', 'mutu.generate.file'])? 'active': '' }}">
                            <a class="sidebar-link" href="{{ route('mutu.generate.index') }}">
                                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Generate
                                    File</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle d-flex">
                    <i class="hamburger align-self-center"></i>
                </a>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#"
                                data-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#"
                                data-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}"
                                    class="avatar img-fluid rounded mr-1" alt="Charles Hall" /> <span
                                    class="text-dark">{{ auth()->user()->name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="dropdown-item">Log Out</button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content">
                {{-- <div class="container-fluid p-0"> --}}

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
                @yield('content')
                {{-- </div> --}}
            </main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-left">
                            <p class="mb-0">
                                <a href="index.html" class="text-muted"><strong>RSGS IT Salman</strong></a> &copy;
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
        </div>
    </div>
    <script src="{{ asset('admin/dist/bootstrap/bootstrap5.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/js/app.js') }}"></script>
    <script src="{{ asset('admin/dist/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/dist/toastr/toastr.min.js') }}"></script>
    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var API_TOKEN = $('meta[name="api-token"]').attr('content');

        function showLoader() {
            let loader = document.querySelector('.loader');
            loader.classList.remove("hide");
            loader.classList.add("show");
        }

        function hideLoader() {
            let loader = document.querySelector('.loader');
            loader.classList.remove("show");
            loader.classList.add("hide");
        }

        function toastrConfig() {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        }

        function toastrSuccess(messages) {
            return toastr.success(messages)
        }

        function toastrError(messages) {
            return toastr.error(messages)
        }

        function toastrWarning(messages) {
            return toastr.warning(messages)
        }

        toastrConfig();
    </script>
    @stack('javascript')

</body>

</html>
