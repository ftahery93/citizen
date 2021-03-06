@extends('backend.layout')
@section('content')
    @if(@Auth::user()->permissionsGroup->webmaster_status)
        @include('backend.users.permissions.view')
    @endif
    <div class="padding">
        <div class="box">

            <div class="box-header dker">
                <h3>{{ trans('backend.users') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ trans('backend.home') }}</a> /
                    <a href="">{{ trans('backend.settings') }}</a>
                </small>
            </div>
            @if($Users->total() > 0)
                @if(@Auth::user()->permissionsGroup->webmaster_status)
                    <div class="row p-a pull-right" style="margin-top: -70px;">
                        <div class="col-sm-12">
                            <a class="btn btn-fw primary" href="{{route("users_create")}}">
                                <i class="material-icons">&#xe7fe;</i>
                                &nbsp; {{ trans('backend.newUser') }}
                            </a>
                        </div>
                    </div>
                @endif
            @endif
            @if($Users->total() == 0)
                <div class="row p-a">
                    <div class="col-sm-12">
                        <div class=" p-a text-center ">
                            {{ trans('backend.noData') }}
                        </div>
                    </div>
                </div>
            @endif
            @if($Users->total() > 0)
                {{Form::open(['route'=>'users_update_all','method'=>'post'])}}
                <div class="table-responsive">
                    <table class="table table-striped  b-t">
                        <thead>
                        <tr>
                            <th style="width:20px;">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <th>{{ trans('backend.fullName') }}</th>
                            <th>{{ trans('backend.loginEmail') }}</th>
                            <th>{{ trans('backend.Permission') }}</th>
                            <th class="text-center" style="width:50px;">{{ trans('backend.status') }}</th>
                            <th class="text-center" style="width:200px;">{{ trans('backend.options') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($Users as $User)
                            <tr>
                                <td><label class="ui-check m-a-0">
                                        <input type="checkbox" name="ids[]" value="{{ $User->id }}"><i
                                                class="dark-white"></i>
                                        {!! Form::hidden('row_ids[]',$User->id, array('class' => 'form-control row_no')) !!}
                                    </label>
                                </td>
                                <td>{!! $User->name   !!}</td>
                                <td><small>{!! $User->email   !!}</small></td>
                                <td><small>{{!! $User->permissionsGroup->name !!}}</small></td>
                                <td class="text-center">
                                    <i class="fa {{ ($User->status==1) ? "fa-check text-success":"fa-times text-danger" }} inline"></i>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm success"
                                       href="{{ route("users_edit",["id"=>$User->id]) }}">
                                        <small><i class="material-icons">&#xe3c9;</i> {{ trans('backend.edit') }}
                                        </small>
                                    </a>
                                    @if(@Auth::user()->permissionsGroup->webmaster_status)
                                        <button class="btn btn-sm warning" data-toggle="modal"
                                                data-target="#m-{{ $User->id }}" ui-toggle-class="bounce"
                                                ui-target="#animate">
                                            <small><i class="material-icons">&#xe872;</i> {{ trans('backend.delete') }}
                                            </small>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            <!-- .modal -->
                            <div id="m-{{ $User->id }}" class="modal fade" data-backdrop="true">
                                <div class="modal-dialog" id="animate">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ trans('backend.confirmation') }}</h5>
                                        </div>
                                        <div class="modal-body text-center p-lg">
                                            <p>
                                                {{ trans('backend.confirmationDeleteMsg') }}
                                                <br>
                                                <strong>[ {{ $User->name }} ]</strong>
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn dark-white p-x-md"
                                                    data-dismiss="modal">{{ trans('backend.no') }}</button>
                                            <a href="{{ route("users_delete",["id"=>$User->id]) }}"
                                               class="btn danger p-x-md">{{ trans('backend.yes') }}</a>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div>
                            </div>
                            <!-- / .modal -->
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <footer class="dker p-a">
                    <div class="row">
                        <div class="col-sm-3 hidden-xs">
                            <!-- .modal -->
                            <div id="m-all" class="modal fade" data-backdrop="true">
                                <div class="modal-dialog" id="animate">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ trans('backend.confirmation') }}</h5>
                                        </div>
                                        <div class="modal-body text-center p-lg">
                                            <p>
                                                {{ trans('backend.confirmationDeleteMsg') }}
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn dark-white p-x-md"
                                                    data-dismiss="modal">{{ trans('backend.no') }}</button>
                                            <button type="submit"
                                                    class="btn danger p-x-md">{{ trans('backend.yes') }}</button>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div>
                            </div>
                            <!-- / .modal -->
                            @if(@Auth::user()->permissionsGroup->webmaster_status)
                                <select name="action" id="action" class="input-sm form-control w-sm inline v-middle"
                                        required>
                                    <option value="">{{ trans('backend.bulkAction') }}</option>
                                    <option value="activate">{{ trans('backend.activeSelected') }}</option>
                                    <option value="block">{{ trans('backend.blockSelected') }}</option>
                                    <option value="delete">{{ trans('backend.deleteSelected') }}</option>
                                </select>
                                <button type="submit" id="submit_all"
                                        class="btn btn-sm white">{{ trans('backend.apply') }}</button>
                                <button id="submit_show_msg" class="btn btn-sm white" data-toggle="modal"
                                        style="display: none"
                                        data-target="#m-all" ui-toggle-class="bounce"
                                        ui-target="#animate">{{ trans('backend.apply') }}
                                </button>
                            @endif
                        </div>
                        <div class="col-sm-3 text-center">
                            <small class="text-muted inline m-t-sm m-b-sm">{{ trans('backend.showing') }} {{ $Users->firstItem() }}
                                -{{ $Users->lastItem() }} {{ trans('backend.of') }}
                                <strong>{{ $Users->total()  }}</strong> {{ trans('backend.records') }}</small>
                        </div>
                        <div class="col-sm-6 text-right text-center-xs">
                            {!! $Users->links() !!}
                        </div>
                    </div>
                </footer>
                {{Form::close()}}
                <script type="text/javascript">
                    $("#checkAll").click(function () {
                        $('input:checkbox').not(this).prop('checked', this.checked);
                    });
                    $("#action").change(function () {
                        if (this.value == "delete") {
                            $("#submit_all").css("display", "none");
                            $("#submit_show_msg").css("display", "inline-block");
                        } else {
                            $("#submit_all").css("display", "inline-block");
                            $("#submit_show_msg").css("display", "none");
                        }
                    });
                </script>
            @endif
        </div>
    </div>
@endsection
@section('footerInclude')
    <script type="text/javascript">
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $("#action").change(function () {
            if (this.value == "delete") {
                $("#submit_all").css("display", "none");
                $("#submit_show_msg").css("display", "inline-block");
            } else {
                $("#submit_all").css("display", "inline-block");
                $("#submit_show_msg").css("display", "none");
            }
        });
    </script>
@endsection
