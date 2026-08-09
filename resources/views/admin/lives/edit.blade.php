@extends('layouts.admin')

@section('title', 'Modifier le live')
@section('page_title', 'Modifier live')
@section(
    'breadcrumb',
    'Matière → Niveau → Classe → Créneau → Modifier'
)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="adm-page-header">
            <div>
                <h1>Modifier le live</h1>
                <div class="subtitle">
                    Conservez le live dans une structure exacte :
                    Matière → Niveau → Classe → Créneau.
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="adm-alert adm-alert-danger mb-4">
                <strong>
                    La modification n’a pas été enregistrée.
                </strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.lives.update', $live) }}"
        >
            @csrf
            @method('PUT')

            @include(
                'components.pedagogical-path-edit',
                [
                    'hierarchy' => $editHierarchy,
                    'prefix' => 'adminLiveEdit',
                    'selectedSubject' =>
                        $selectedSubjectId,
                    'selectedLevel' =>
                        $selectedLevelId,
                    'selectedClass' =>
                        $selectedClassId,
                    'selectedSlot' =>
                        $selectedSlotId,
                ]
            )

            <div class="adm-card mb-4">
                <div class="adm-card-header">
                    <h4>
                        <i
                            class="bi bi-broadcast-pin"
                            style="color:#FB7185;"
                        ></i>
                        Informations du live
                    </h4>
                </div>

                <div class="adm-card-body">
                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="liveTitle"
                        >
                            Titre du live
                        </label>

                        <input
                            id="liveTitle"
                            type="text"
                            name="title"
                            value="{{
                                old(
                                    'title',
                                    $live->title
                                )
                            }}"
                            class="adm-form-control
                                @error('title') error @enderror"
                            maxlength="255"
                            required
                        >

                        @error('title')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="adm-form-group">
                        <label
                            class="adm-form-label"
                            for="streamUrl"
                        >
                            Lien du live
                        </label>

                        <input
                            id="streamUrl"
                            type="url"
                            name="stream_url"
                            value="{{
                                old(
                                    'stream_url',
                                    $live->stream_url
                                )
                            }}"
                            class="adm-form-control
                                @error('stream_url') error @enderror"
                            placeholder="https://..."
                            required
                        >

                        @error('stream_url')
                            <div class="adm-form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveDate"
                                >
                                    Date
                                </label>

                                <input
                                    id="liveDate"
                                    type="date"
                                    name="live_date"
                                    value="{{
                                        old(
                                            'live_date',
                                            optional(
                                                $live->live_date
                                            )->format('Y-m-d')
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('live_date') error @enderror"
                                    required
                                >

                                @error('live_date')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveStart"
                                >
                                    Heure de début
                                </label>

                                <input
                                    id="liveStart"
                                    type="time"
                                    name="start_time"
                                    value="{{
                                        old(
                                            'start_time',
                                            substr(
                                                (string) $live->start_time,
                                                0,
                                                5
                                            )
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('start_time') error @enderror"
                                    required
                                >

                                @error('start_time')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="adm-form-group">
                                <label
                                    class="adm-form-label"
                                    for="liveEnd"
                                >
                                    Heure de fin
                                </label>

                                <input
                                    id="liveEnd"
                                    type="time"
                                    name="end_time"
                                    value="{{
                                        old(
                                            'end_time',
                                            substr(
                                                (string) $live->end_time,
                                                0,
                                                5
                                            )
                                        )
                                    }}"
                                    class="adm-form-control
                                        @error('end_time') error @enderror"
                                    required
                                >

                                @error('end_time')
                                    <div class="adm-form-error">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a
                    href="{{ route('admin.lives.index') }}"
                    class="adm-btn adm-btn-ghost flex-fill text-center"
                >
                    <i class="bi bi-arrow-left"></i>
                    Annuler
                </a>

                <button
                    type="submit"
                    class="adm-btn adm-btn-primary flex-fill"
                >
                    <i class="bi bi-save"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
