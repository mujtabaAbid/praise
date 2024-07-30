@extends('layout.app')
@section('title', 'Users | Praisy')

@section('content')
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Users</h4>
                    {{-- <a class="btn btn-primary" href="{{ route('users.add') }}">Add</a> --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="barberTable" class="display" style="width: 1160px !important;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>About</th>
                                    <th>status</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal -->
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteUser">
                    @csrf
                    <input type="hidden" name="id" id="deleteUserId">
                    <div class="modal-body">
                        Are you sure you want to delete this record?
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                        <button id="submitDelForm" type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('custom-script')
    <script>
        // Data Table
        $(document).ready(function() {
            let table = $('#barberTable').DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "pageLength": 10,
                "bLengthChange": false,
                "lengthMenu": [
                    [5, 10, 20, 50],
                    ['5', '10', '20', '50']
                ],
                "ajax": {
                    "url": "{{ route('get.users') }}",
                    "type": "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    }
                },
                'columns': [
                    // {data: 'checkbox', orderable: false},
                    {
                        data: 'id',
                        orderable: false
                    },
                    {
                        data: 'name',
                        orderable: false
                    },
                    {
                        data: 'email',
                        orderable: false
                    },
                    {
                        data: 'about',
                        orderable: false
                    },
                    {
                        data: 'status',
                        orderable: false
                    },
                    // {
                    //     data: 'action',
                    //     orderable: false
                    // },
                ],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                }
            });
        });




        // Edit
        // function editRecord(id) {
        //     $('#editUserId').val(id);
        //     $.ajax({
        //         type: "GET",
        //         url: "{{ url('users.detail') }}",
        //         dataType: 'json',
        //         data: {
        //             id: id
        //         },
        //         beforeSend: function() {
        //             // Code to execute before sending the request (optional)
        //         },
        //         success: function(res) {
        //             if (res.success === true) {
        //                 $('#name').val(res.data.name);
        //                 $('#email').val(res.data.email);
        //             } else {
        //                 // Code to handle unsuccessful response
        //                 console.log(res.message); // Log error message
        //             }
        //         },
        //         error: function(e) {
        //             // Code to execute if the request fails
        //             console.log("Error:", e);
        //         }
        //     });
        // }

        // $("#editUser").on('submit', function(e) {
        //     e.preventDefault();
        //     const btn = $("#submitEditForm");
        //     const modal = $('#editModal');
        //     let formData = new FormData($("#editUser")[0]);
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ url('user.update') }}",
        //         dataType: 'json',
        //         contentType: false,
        //         processData: false,
        //         cache: false,
        //         data: formData,
        //         beforeSend: function() {
        //             btn.prop('disabled', true);
        //             btn.html(
        //                 '<div class="spinner-border" role="status" style="height: 14px;width: 14px;accent-color: ;"> <span class="visually-hidden">Loading...</span> </div>'
        //             );
        //             $('.error-message').html('');
        //         },
        //         success: function(res) {
        //             if (res.success === true) {
        //                 btn.prop('disabled', false);
        //                 btn.html('Submit');
        //                 modal.modal('hide');
        //                 notyf.success({
        //                     message: res.message,
        //                     duration: 3000
        //                 });
        //                 setTimeout(function() {
        //                     window.location.reload();
        //                 }, 2000);

        //             } else {
        //                 btn.prop('disabled', false);
        //                 btn.html('Submit');
        //                 modal.modal('hide');
        //                 notyf.error({
        //                     message: res.message,
        //                     duration: 3000
        //                 });
        //             }
        //         },
        //         error: function(e) {
        //             btn.prop('disabled', false);
        //             btn.html('Submit');
        //         }
        //     });
        // });

        // Delete
        function deleteRecord(id) {
            $('#deleteUserId').val(id);
        }

        // $("#deleteUser").on('submit', function(e) {
        //     e.preventDefault();
        //     const btn = $("#submitDelForm");
        //     const modal = $('#deleteModal');
        //     let formData = new FormData($("#deleteUser")[0]);
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ url('users.delete') }}",
        //         dataType: 'json',
        //         contentType: false,
        //         processData: false,
        //         cache: false,
        //         data: formData,
        //         beforeSend: function() {
        //             btn.prop('disabled', true);
        //             btn.html(
        //                 '<div class="spinner-border" role="status" style="height: 14px;width: 14px;accent-color: ;"> <span class="visually-hidden">Loading...</span> </div>'
        //             );
        //             $('.error-message').html('');
        //         },
        //         success: function(res) {
        //             if (res.success === true) {
        //                 btn.prop('disabled', false);
        //                 btn.html('Delete');
        //                 modal.modal('hide');
        //                 notyf.success({
        //                     message: res.message,
        //                     duration: 3000
        //                 });
        //                 setTimeout(function() {
        //                     window.location.reload();
        //                 }, 2000);

        //             } else {
        //                 btn.prop('disabled', false);
        //                 btn.html('Delete');
        //                 modal.modal('hide');
        //                 notyf.error({
        //                     message: res.message,
        //                     duration: 3000
        //                 });
        //             }
        //         },
        //         error: function(e) {
        //             btn.prop('disabled', false);
        //             btn.html('Delete');
        //         }
        //     });
        // });
    </script>
@endsection
