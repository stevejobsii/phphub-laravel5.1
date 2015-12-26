@extends('layouts.default')

@section('title')
编辑个人资料_@parent
@stop

@section('content')

<div class="users-show">

  <div class="col-md-3 box" style="padding: 15px 15px;">
    @include('users.partials.basicinfo')
  </div>

  <div class="main-col col-md-9 left-col">

    <div class="panel panel-default">

      <div class="panel-body ">

        @include('layouts.partials.errors')

        {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'patch']) !!}
          <h5><i class="glyphicon glyphicon-tasks"></i>&nbsp;&nbsp;个人信息</h5><hr>
          <div class="form-group">
            {!! Form::text('real_name', null, ['class' => 'form-control', 'placeholder' => lang('Real Name')]) !!}
          </div>
          <div class="form-group">
            {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => lang('City')]) !!}
          </div>
          <div class="form-group">
            {!! Form::text('company', null, ['class' => 'form-control', 'placeholder' => lang('Company')]) !!}
          </div>
          <div class="form-group">
            {!! Form::text('weibo_account', null, ['class' => 'form-control', 'placeholder' => lang('twitter_placeholder')]) !!}
          </div>
          <div class="form-group">
            {!! Form::text('personal_website', null, ['class' => 'form-control', 'placeholder' => lang('personal_website_placebolder')]) !!}
          </div>
          <div class="form-group">
            {!! Form::textarea('signature', null, ['class' => 'form-control',
                                              'rows' => 3,
                                              'placeholder' => lang('signature_placeholder')]) !!}
          </div>
          <div class="form-group">
            {!! Form::textarea('introduction', null, ['class' => 'form-control',
                                              'rows' => 3,
                                              'placeholder' => lang('introduction_placeholder')]) !!}
          </div>
          <div class="form-group status-post-submit">
            {!! Form::submit(lang('Publish'), ['class' => 'btn btn-primary', 'id' => 'user-edit-submit']) !!}
          </div>
        {!! Form::close() !!}

        <form class="form-horizontal" method="post" action="/settings/update-avatar" enctype="multipart/form-data" id="avatar-form">
                {{ csrf_field() }}
                <h5><i class="glyphicon glyphicon-user"></i>&nbsp;&nbsp;头像设置</h5><hr>
                <div class="form-group">
                    <div class="col-sm-4 avatar-setting-container">
                       <img src="{{Auth::user()->avatar}}" id="avatar">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 status-post-submit">
                    <button type="button" class="btn btn-primary" onclick="$('#avatarinput').click()">
                        上传新头像
                    </button>
                    <span class="loading"></span>
                    <input type="file" id="avatarinput" name="avatar" style="display: none">
                    <span class="help-block">
                        头像支持jpg和png格式，上传的文件大小不超过 2M</span>
                    <button type="submit" class="btn btn-primary hidden" id="avatarinput-submit">更新</button>
                    </div>
                </div>
        </form>

        <form class="form-horizontal" method="post" action="/settings/resetPassword" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <h5><i class="glyphicon glyphicon-wrench"></i>&nbsp;&nbsp;密码设置</h5><hr>
            <div class="form-group">
                <div class="col-sm-4">
                <input type="password" name="old_password" placeholder="请输入您当前的密码" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4">
                <input type="password" name="password" placeholder="请输入新密码" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4">
                <input type="password" name="password_confirmation" placeholder="请再次输入新密码" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 status-post-submit">
                <button type="submit" class="btn btn-primary" id="update-password">更  新</button>
                </div>
            </div>
      </form>
      </div>
    </div>
  </div>


</div>




@stop
