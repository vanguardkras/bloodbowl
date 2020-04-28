@extends('layouts.app')

@section('content')
    <h1 class="mb-3">Your teams</h1>
    <button class="mb-3 btn btn-primary btn-lg" style="width: 200px">Create a new team</button>
    <div class="row">
        <div class="align-content-center col-xl-3 col-lg-4 col-md-6 col-sm-12 pb-4">
            <div class="card team_card">
                <label class="action_image_container team_logo_upload">
                    <input type="hidden" name="team_id" value="">
                    <input type="file" name="team_logo">
                    <img class="action_image card-img-top" src="/img/defaults/team.jpg" alt="Logo">
                    <img class="hover_action" src="/img/icons/upload.png" alt="upload">
                    <div class="hover_action_text">Upload new logo</div>
                </label>
                <div class="card-header">
                    <h4>Название команды</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <small>Фракция: <strong>Люди</strong></small>
                    </li>
                    <li class="list-group-item">
                        Информация
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
