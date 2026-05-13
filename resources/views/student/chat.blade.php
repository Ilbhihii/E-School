@extends('layouts.student')

@section('content')

<div class="container py-5">

    <h3 class="fw-bold mb-4 text-center">
        💬 Chat - {{ $subject->name }}
    </h3>

    <!-- Messages -->
    <div class="card p-4 mb-4" style="height:400px; overflow-y:auto;">

        @foreach($messages as $msg)
            <div class="mb-3">
                <strong>{{ $msg->user->name }} :</strong>
                <p class="mb-0">{{ $msg->message }}</p>
            </div>
        @endforeach

    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('student.chat.send') }}">
        @csrf

        <input type="hidden" name="subject_id" value="{{ $subject->id }}">

        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Écrire un message..." required>
            <button class="btn btn-primary">Envoyer</button>
        </div>
    </form>

</div>

@endsection

