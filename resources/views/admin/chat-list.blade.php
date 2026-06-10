@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div class="adm-flex adm-gap-3">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-chat-dots-fill" style="font-size:1.25rem;color:white;"></i>
                </div>
                <div>
                    <h1 class="admin-header-title"><span class="gradient">Chat Administration</span></h1>
                    <p class="admin-header-subtitle">Toutes les matières — Discussions</p>
                </div>
            </div>
        </div>

        @if(isset($subjects) && $subjects->count() > 0)
            <div class="adm-grid-4">
                @foreach($subjects->unique('name') as $subject)
                    <a href="{{ route('admin.chat', $subject->id) }}" style="text-decoration:none;display:block;">
                        <div class="adm-card" style="height:100%;border:1px solid rgba(99,102,241,0.15);">
                            <div class="adm-card-body">
                                <div class="adm-flex adm-gap-3 adm-mb-2">
                                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-chat-square-text-fill" style="font-size:1.25rem;color:white;"></i>
                                    </div>
                                    <div style="min-width:0;">
                                        <h4 style="font-size:1rem;font-weight:700;color:var(--adm-text);margin:0 0 0.25rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $subject->name }}</h4>
                                        @if($subject->messages_count > 0)
                                            <p style="font-size:0.85rem;color:var(--adm-text-secondary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                {{ $subject->messages_count }} discussion{{ $subject->messages_count > 1 ? 's' : '' }}
                                            </p>
                                        @else
                                            <p style="font-size:0.85rem;color:var(--adm-text-secondary);margin:0;">{{ $subject->description ?? 'Pas de description' }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="adm-flex adm-flex-between" style="padding-top:0.75rem;border-top:1px solid var(--adm-border);">
                                    @if($subject->messages_count > 0)
                                        <span class="adm-badge adm-badge-primary">{{ $subject->messages_count }} message{{ $subject->messages_count > 1 ? 's' : '' }}</span>
                                    @else
                                        <span class="adm-badge adm-badge-gray">En attente</span>
                                    @endif
                                    <span style="font-size:0.8rem;font-weight:600;color:var(--adm-primary);">
                                        Ouvrir <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="adm-card" style="text-align:center;padding:3rem;">
                <div class="adm-empty-icon" style="margin:0 auto 1.5rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>
                <h3>Aucune matière disponible</h3>
                <p style="color:var(--adm-text-secondary);margin-bottom:1.5rem;">Les matières de chat seront bientôt disponibles.</p>
            </div>
        @endif

    </div>
</div>
@endsection
