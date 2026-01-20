@extends('user.layouts.app')

@section('title', __('messages.static.create'))

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
@endpush

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title">{{ __('messages.static.create') }}</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.dashboard') }}">{{ __('labels.fields.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.fardeauMO.operations.index') }}">Activitées</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Ajouter un Type d'Activités
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row" id="table-head">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <button title="Retour" onclick="window.location.href='{{ route('user.fardeauMO.operations.index') }}'" class="btn btn-outline-info">
                                <i data-feather="arrow-left"></i>
                            </button>
                        </div>

                        <div class="card-body">
                            <form method="post" action="{{ route('user.fardeauMO.operation_types.store') }}">
                                @csrf

                                <!-- 🔹 Nom du Type d'Opération -->
                                <div class="form-group">
                                    <label>Nom du Type d'Opération</label>
                                    <input type="text"
                                           class="form-control"
                                           name="name"
                                           placeholder="Ex: Opération CCTV"
                                           value="{{ old('name') }}"
                                           required>
                                </div>

                                <!-- 🔹 Modèle CCQ (booléen) -->
                                <!-- IMPORTANT : input hidden pour forcer l'envoi de 0 si la checkbox est décochée -->
                                <input type="hidden" name="is_ccq" value="0">
                                <div class="form-group form-check mt-2">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="is_ccq"
                                           name="is_ccq"
                                           value="1"
                                           {{ old('is_ccq') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_ccq">
                                        Modèle CCQ (afficher les colonnes CCQ)
                                    </label>
                                </div>

                                <!-- 🔹 Boutons -->
                                <div class="mt-3 d-flex gap-1">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    <button type="reset" class="btn btn-secondary">Réinitialiser</button>
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
