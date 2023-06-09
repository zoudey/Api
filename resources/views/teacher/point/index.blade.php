@extends('teacher.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Điểm</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Điểm</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <a class="btn btn-success xs btn-sm" href="javascript:void(0)" id="createNewProduct"><i
                        class="fa-solid fa-plus"></i></a>
                <select class="select2" style="width:15%;" id="search" data-placeholder="Chọn lớp" style="width: 100%;">
                    @foreach ($room as $row)
                        <option value="{{ $row->room_id }}">
                            {{ $row->rooms->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm" id="btn-search"
                    style="padding-bottom: 5px;border-radius: 5px;height:30px"><i
                        class="fa-solid fa-magnifying-glass"></i></button>
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Điểm</th>
                            <th>Sinh viên</th>
                            <th>Giáo viên</th>
                            <th>Môn học</th>
                            <th>Lớp học</th>
                            <th width="280px">Quản lý</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="modal fade" id="ajaxModel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="modelHeading"></h4>
                            </div>
                            <div class="modal-body">
                                <form id="productForm" name="productForm" class="form-horizontal">
                                    <input type="hidden" name="_id" id="_id">
                                    <input type="hidden" name="teacher_id" id="teacher_id"
                                        value="{{ auth('teacher')->user()->id }}">
                                    <div class="form-group">
                                        <label for="name" class="col-sm control-label">Điểm</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="value" name="value"
                                                placeholder="Enter Name" value="" maxlength="50" required="">
                                            <span class="text-danger error-text value_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="col-sm control-label">Lớp học</label>
                                        <div class="col-sm-12">
                                            <select name="room_id" class="select2" style="width:100%" id="room_id">
                                                <option value="">Mời Chọn lớp học</option>
                                                @foreach ($room as $room)
                                                    <option value="{{ $room->room_id }}">
                                                        {{ $room->rooms->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text room_id_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="col-sm control-label">Sinh viên</label>
                                        <div class="col-sm-12">
                                            <select name="student_id" class="select2" style="width:100%" id="student_id">
                                                <option value="">Mời Chọn Sinh Viên</option>
                                                @foreach ($student as $student)
                                                    <option value="{{ $student->id }}">
                                                        {{ $student->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text student_id_err"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="col-sm control-label">Môn học</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" style="width:100%" data-placeholder="Môn học" id="subject_id" name="subject_id">
                                                @foreach ($subject as $subject)
                                                    <option value="{{ $subject->id }}">
                                                        {{ $subject->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text subject_id_err"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Lưu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    @parent
    <script>
        $('.select2').select2()
    </script>
    <script type="text/javascript">
        $(function() {
            /*Pass Header Token*/
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            /*Render DataTable*/
            $(document).ready(function() {
                fill_datatable();

                function fill_datatable(search = '') {
                    var table = $('.data-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('point.index') }}",
                            data: {
                                search: search,
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex'
                            },
                            {
                                data: 'value',
                            },
                            {
                                data: 'students',
                            },
                            {
                                data: 'teachers',
                            },
                            {
                                data: 'subjects',
                            },
                            {
                                data: 'rooms',
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            },
                        ]
                    });

                }
                $('#createNewProduct').click(function() {
                    $('#saveBtn').val("create-product");
                    $('#_id').val('');
                    $('#productForm').trigger("reset");
                    $('#modelHeading').html("Thêm điểm");
                    $('#ajaxModel').modal('show');
                });
                /*Click to Edit Button*/
                $('body').on('click', '.editProduct', function() {
                    var _id = $(this).data('id');
                    $.get("{{ route('point.index') }}" + '/' + _id + '/edit', function(data) {
                        $('#modelHeading').html("Sửa điểm");
                        $('#saveBtn').val("edit-user");
                        $('#ajaxModel').modal('show');
                        $('#_id').val(data.id);
                        $('#value').val(data.value);
                        $('#teacher_id').val(data.teacher_id);
                        $("#student_id").val(data.student_id).trigger('change');
                        $("#subject_id").val(data.subject_id).trigger('change');
                        $("#room_id").val(data.room_id).trigger('change');
                    })
                });
                /* Create Product Code -*/
                $('#saveBtn').click(function(e) {
                    e.preventDefault();
                    $(this).html('Sending..');
                    $.ajax({
                        data: $('#productForm').serialize(),
                        url: "{{ route('point.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            if ($.isEmptyObject(data.message)) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter',
                                            Swal.stopTimer)
                                        toast.addEventListener('mouseleave',
                                            Swal.resumeTimer)
                                    }
                                })
                                Toast.fire({
                                    icon: 'success',
                                    title: data.success
                                })
                                setTimeout(() => {
                                    $('#productForm').trigger("reset");
                                    $('#ajaxModel').modal('hide');
                                    $('.data-table').DataTable().destroy();
                                    fill_datatable();
                                }, 200);
                            } else {
                                printErrorMsg(data.message);
                            }
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            $('#saveBtn').html('Save Changes');
                        }
                    });
                });
                /* Delete Product Code */
                $('body').on('click', '.deleteProduct', function() {
                    Swal.fire({
                        title: 'Are you sure you want to delete?',
                        text: "You won't be able to undo this once you do!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var _id = $(this).data("id");
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('point.index') }}" + '/' + _id,
                                success: function(data) {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast
                                                .addEventListener(
                                                    'mouseenter',
                                                    Swal
                                                    .stopTimer)
                                            toast
                                                .addEventListener(
                                                    'mouseleave',
                                                    Swal
                                                    .resumeTimer
                                                )
                                        }
                                    })
                                    Toast.fire({
                                        icon: 'success',
                                        title: data.success
                                    })
                                    $('.data-table').DataTable().destroy();
                                    fill_datatable();
                                },
                                error: function(data) {
                                    console.log('Error:', data);
                                }
                            });
                        }
                    })
                });
                $('#btn-search').click(function() {
                    var search = $('#search').val();
                    $.ajax({
                        data: {
                            search: search,
                        },
                        url: "{{ route('point.index') }}",
                        type: "GET",
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            $('.data-table').DataTable().destroy();
                            fill_datatable(search);
                        },
                    });
                });
                $('#reset').click(function() {
                    $('#semester').val('');
                    $('#data-table').DataTable().destroy();
                    fill_datatable();
                });
            });
            /*Click to Button*/
        });
        $('#room_id').on('change', function() {
            room_id = $('#room_id').val();
            $.ajax({
                type: 'get',
                url: 'point',
                data: {
                    'room_id': room_id
                },
                success: function(data) {
                    var html = `<option value="` + data.subject.id + `">` + data.subject.name +
                        `</option>`;
                    $('#subject_id').html(html);

                    let text = "";
                    for (let item of data.student) {
                        text += `<option value="` + item.id + `">` + item.name +
                        `</option>`;
                        $('#student_id').html(text);
                    }
                }

            });
        });

        function printErrorMsg(msg) {
            $.each(msg, function(key, value) {
                console.log(key);
                $('.' + key + '_err').text(value);
            });
        }
    </script>
@endsection
