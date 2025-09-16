@extends('layouts.app')
@section('title') Human Resources @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('headings') Overtime Application @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Overtime</li>
        </ol>
    </nav>

    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" id="otApplication" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="employee_id" name="employee_id" value="{{$id}}">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input type="date" id="date" class="form-control py-3"
                                                placeholder="" name="date">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_start">Time Start</label>
                                            <input type="time" id="time_start" class="form-control py-3"
                                                placeholder="" name="time_start">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="time_end">Time End</label>
                                            <input type="time" id="time_end" class="form-control py-3" placeholder=""
                                                name="time_end">
                                        </div>
                                    </div>
                                    <div class="w-100 mb-10">
                                        <div class="form-group">
                                            <label for="reason" class="form-label">Note</label>
                                            <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-start">
                                        <button id="reqOt" type="submit" class="btn icon icon-left btn-primary me-1 mb-1 w-100 py-3">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            Submit Application</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-header">
                My Overtime Requests
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="otRequests" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                                <th>Total Minutes</th>
                                <th>Notes</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')

    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}   "></script>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    {{--
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script> --}}
    <script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{ asset('assets/js/main2.js') }}"></script>
    <script src="{{ asset('js/overtimeApplication.js') }}"></script>
@endsection