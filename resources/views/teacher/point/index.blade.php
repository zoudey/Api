@extends('teacher.layout.app')
@section('content')
    <h1>Crud</h1>
    @if (auth('teacher')->user())
        Xin chào {{ auth('teacher')->user()->email }}

        <img src="" alt="">
        <a href="{{route('teacher.logout')}}">Logout</a>
        <a href="{{route('teacher.profile')}}">Profile</a>
    @else

    @endif
    <br>
    {{-- @dd($data) --}}
    <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Point</th>
                <th>Student</th>
                <th>Teacher</th>
                <th>Subject</th>
                <th>Room</th>
                <th width="280px">Action</th>
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
                        <input type="hidden" name="teacher_id" id="teacher_id" value="{{auth('teacher')->user()->id}}">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Point</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="value" name="value"
                                    placeholder="Enter Name" value="" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Student</label>
                            <div class="col-sm-12">
                                <select name="student_id" class="form-control" id="student_id" data-show-subtext="true" data-live-search="true">
                                    <option value="">Mời Chọn Sinh Viên</option>
                                    @foreach ($student as $student)
                                        <option selected="selected" value="{{$student->id}}">{{$student->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Room</label>
                            <div class="col-sm-12">
                                <select name="subject_id" class="form-control" id="subject_id" data-show-subtext="true" data-live-search="true">
                                    <option value="">Mời Chọn Môn Học</option>
                                    @foreach ($subject as $subject)
                                        <option selected="selected" value="{{$subject->id}}">{{$subject->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Subject</label>
                            <div class="col-sm-12">
                                <select name="room_id" class="form-control" id="room_id" data-show-subtext="true" data-live-search="true">
                                    <option value="">Mời Chọn Môn Học</option>
                                    @foreach ($room as $room)
                                        <option selected="selected" value="{{$room->id}}">{{$room->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    @parent
    <script type="text/javascript">
        $(function() {
            /*Pass Header Token*/
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            /*Render DataTable*/
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('point.index') }}",
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
            /*Click to Button*/
            $('#createNewProduct').click(function() {
                $('#saveBtn').val("create-product");
                $('#_id').val('');
                $('#productForm').trigger("reset");
                $('#modelHeading').html("Create New Product");
                $('#ajaxModel').modal('show');
            });
            /*Click to Edit Button*/
            $('body').on('click', '.editProduct', function() {
                var _id = $(this).data('id');
                $.get("{{ route('point.index') }}" + '/' + _id + '/edit', function(data) {
                    $('#modelHeading').html("Point");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#_id').val(data.id);
                    $('#value').val(data.value);
                    $('#teacher_id').val(data.teacher_id);
                    $('#student_id').val(data.student_id);
                    $('#subject_id').val(data.subject_id);
                    $('#room_id').val(data.room_id);
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
                        $('#productForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('Save Changes');
                    }
                });
            });
            /* Delete Product Code */
            $('body').on('click', '.deleteProduct', function() {
                var _id = $(this).data("id");
                confirm("Are You sure want to delete !");

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('room.index') }}" + '/' + _id,
                    success: function(data) {
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });
        });
    </script>
@endsection

</html>