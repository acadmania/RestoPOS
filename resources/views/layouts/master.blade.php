<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>restoPOS</title>


  <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

  <style media="print">
  .table td, .table th {
    background-color: unset !important;
}
  </style>

  <script type="text/javascript">
    window.Laravel = {
        csrfToken: "{{ csrf_token() }}",
        jsPermissions: {!! auth()->check()?auth()->user()->jsPermissions():null !!}
    }






</script>

<style>
  body
  {
    color:blue;
  }
</style>
</head>
<body class="hold-transition sidebar-mini sidebar-collapse">
<div class="wrapper" id="app">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
      </li>
    </ul>

    <!-- SEARCH FORM
    <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
    </form> -->
    <a href="/pos" class="btn btn-sm btn-primary">OPEN POS</a>




  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <router-link to="/dashboard" class="brand-link" style="text-align:center" >
      <img src="{{ global_asset('/images/logo.png') }}" style="max-height:unset; width: 135px;" alt="The Logo" class="brand-image"><br>
      <span class="brand-text font-weight-light"></span>
    </router-link>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional)
        <router-link to="/profile">
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
              <div class="image">
                <img src="{{ auth()->user()->photo }}" class="img-circle elevation-2" alt="User Image">
              </div>
              <div class="info">

                  {{ Auth::user()->name }}
                  <span class="d-block text-muted">
                    {{ Ucfirst(Auth::user()->type) }}
                  </span>
              </div>
          </div>
        </router-link>-->

      <!-- Sidebar Menu -->
      @include('layouts.sidebar-menu')
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  {{-- Content Wrapper. Contains page content --}}
  <div class="content-wrapper">
    {{-- Main content --}}

    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        {{-- <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row --> --}}
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <router-view></router-view>

    <vue-progress-bar></vue-progress-bar>

    {{-- /.content --}}
  </div>
  {{-- /.content-wrapper --}}

  {{--
  <footer class="main-footer">

    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.0.0
    </div>

    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>--}}
</div>
{{-- ./wrapper --}}

@auth
<script>
    window.user = @json(auth()->user())
</script>
@endauth
<script src="{{ mix('/js/app.js') }}"></script>





<script src="/js/moment.min.js"></script>

<script>

document.addEventListener("DOMContentLoaded", () => {
    var now_new = new Date();


        now_new.setDate(now_new.getDate() + 1);

    if(getCookie('checked_license') == null)
    {
        var now = new Date();


now.setDate(now.getDate() + 1);
document.cookie =
'checked_license=true; expires=' + now.toUTCString() +
'; path=/';

        $.get("/api/license/check", function(data, status){
            if(data == '' || data == null)
   {

    document.cookie =
'checked_license=notvalid; expires=' + now_new.toUTCString() +
'; path=/';
        $('body').html('Your License is Missing Or Not Valid! Please contact software Company');
        appendLicenseForm();
   }
   else
   {

    let lic = moment(data);
    let now = moment(new Date());
    if(now.isSameOrAfter(lic))
    {


        document.cookie =
'checked_license=expired; expires=' + now_new.toUTCString() +
'; path=/';
        $('body').html('Your License Has Been Expired! Please contact Admin.'+
        +'Enter Your License Key <input type="text">');
        appendLicenseForm();
    }
    else if(lic.diff(now, 'days') < 30)
    {

        alert("Your license will be expired in "+ lic.diff(now, 'days') +" days. Please contact Admin")
    }
   }







  });


    }
    else if(getCookie('checked_license') == 'expired')
    {
        $('body').html('Your License Has Been Expired! Please contact restoPOS');
        appendLicenseForm();
    }


    else if(getCookie('checked_license') == 'notvalid')
    {
        $('body').html('Your License is Missing Or Not Valid! Please contact restoPOS');
        appendLicenseForm();
    }


});

function appendLicenseForm()
{
    $('body').append('<form method="POST" action="/submit/license"> @csrf  <textarea name="license" cols="100" rows="4" placeholder="Enter Your License Key"></textarea> <button onclick="clearLicenseCookie()" type="submit">SUBMIT</button> </form>');
}
function clearLicenseCookie()
{
    document.cookie = 'checked_license=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function getCookie(name) {
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");

    // Loop through the array elements
    for(var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");

        /* Removing whitespace at the beginning of the cookie name
        and compare it with the given string */
        if(name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }

    // Return null if not found
    return null;
}

document.onkeydown = function(event) {


    e = event || window.event;
    if (e.keyCode == '117') {
        e.preventDefault();
        window.location.href = '/pos';

}
}
</script>
</body>
</html>
