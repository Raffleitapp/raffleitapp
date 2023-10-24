@extends('layouts.admin.adminlayout')
@section('master-admin')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<style>
    h2.head {
        color: #000;

        /* H3 Bold */
        font-family: Poppins;
        font-size: 1.5rem;
        font-style: normal;
        font-weight: 700;
        line-height: 90%;
    }

    p.head-desc {
        color: #000;

        /* Title 1 */
        font-family: Poppins;
        font-size: .8rem;
        font-style: normal;
        font-weight: 400;
        line-height: 90%;
    }

    table thead {
        background: var(--primary-color);
        color: var(--white-color);
        padding: 8px 0px;

    }

    table thead tr {
        padding: 8px 0px;
        color: var(--white-color, #FDFDFD);
        /* Title 3 Bold */
        font-family: Poppins;
        font-size: 14px;
        font-style: normal;
        font-weight: 700;
        line-height: 140%;
    }
</style>
<div class="p-3">
    <h2 class="head">All Category</h2>
    <p class="head-desc">Manage category</p>

</div>
<div class="card p-3">
  <div class="row justify-between my-3">
    <div class="col-12 col-md-6">
        <h2 class="head">All Category</h2>
    </div>
    <div class="col-12 col-md-6">
       <button class="btn btn-outline-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#myModal">Add New</button>
    </div>
  </div>
    <div class="table-responsive my-3">
        <table class="table table-striped table-hover table-responsive">
            <thead>
                <tr class="text-center">
                    <th>S/No</th>
                    <th style="min-width: 150px">Category</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="flex justify-center">

                        <span class="mx-2 text-success text-xl"><i class="bi bi-eye-fill" style="font-size:1rem"></i></span>

                        <span class="mx-2 text-danger text-xl"><i class="bi bi-trash-fill" style="font-size:1rem"></i></span>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="myModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog" >
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title text-orange-600 text-center">Add New Category</h4>
                    {{-- <button type="button" class="btn-close text-orange-600" data-bs-dismiss="modal"></button> --}}
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div>
                        <form id="project_type" action="#">
                            @csrf
                            <div class="mb-3">
                                <label for="name">Category name</label>
                                <input type="text" name="name" id="name" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <input type="submit" class="form-control btn btn-outline-success btn-sm"
                                    value="Save">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="{{asset("js/jquery.min.js")}}"></script>
<script src="{{asset("js/sweetalert.js")}}"></script>
<script>
    $(document).ready(function() {

        $("#project_type").submit(function(e) {

            e.preventDefault();

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('admin/save_category') }}",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.status == 401) {
                        Swal.fire({
                            icon: 'error',
                            title: response.error,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        // $(".spinner-border").css("display", "none");
                        // $(".login_btn").css("display", "block");
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1000
                        });
                        window.location = "{{ url('admin/dashboard') }}"
                    }
                }
            });
        });





    });
</script>
<script>
    $(document).ready(function() {
        $(".change_stat").click(function(e) {
            e.preventDefault();
            // alert( "Handler for .click() called." );
            const id = $(this).data('id');
            const value = $(this).data('value');

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('update_project_type') }}",
                data: {
                    "id": id,
                    "status": value == 1 ? 0 : 1,
                    "name": ''
                },
                // dataType: "dataType",
                success: function(response) {
                    if (response.status == 401) {
                        Swal.fire({
                            icon: 'error',
                            title: response.error,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        // $(".spinner-border").css("display", "none");
                        // $(".login_btn").css("display", "block");
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        window.location.reload(true);
                    }
                }
            });
        });

        $(".edit").click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const {
                value: nar
            } = Swal.fire({
                title: 'Edit Project Type',
                input: 'text',
                inputValue: name,
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Update',
                showLoaderOnConfirm: true,
                preConfirm: (nar) => {
                    if (nar != "") {
                        $.ajax({
                            type: "post",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            url: "{{ url('update_project_type') }}",
                            data: {
                                "id": id,
                                "name": nar
                            },
                            // dataType: "dataType",
                            success: function(response) {
                                if (response.status == 401) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.error,
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    // $(".spinner-border").css("display", "none");
                                    // $(".login_btn").css("display", "block");
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    window.location.reload(true);
                                }
                            }
                        });

                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {

                } else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info')
                }
            });



        });
    });
</script>
@endsection