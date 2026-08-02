@extends('layouts.admin')

@section('title', 'Gestion des matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Matières')

@section('content')
@php
    $oldLevels = old('levels');

    if (!is_array($oldLevels) || count($oldLevels) === 0) {
        $oldLevels = [
            [
                'name' => '',
                'description' => '',
                'classes' => [
                    ['name' => ''],
                ],
            ],
        ];
    }

    $totalLevels = $subjects->sum(
        fn ($subject) => (int) ($subject->validated_level_count ?? 0)
    );

    $totalClasses = $subjects->sum(
        fn ($subject) => (int) ($subject->validated_class_count ?? 0)
    );
@endphp

<style>
.subjects-page {
    --subj-blue: #3b82f6;
    --subj-purple: #8b5cf6;
    --subj-cyan: #06b6d4;
    --subj-green: #10b981;
    --subj-orange: #f59e0b;
    --subj-red: #ef4444;
}

.subjects-page * {
    box-sizing: border-box;
}

.subjects-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 22px;
}

.subjects-title-wrap {
    min-width: 0;
}

.subjects-kicker {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 8px;
    color: var(--adm-primary, #818cf8);
    font-size: .76rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.subjects-title {
    display: flex;
    align-items: center;
    gap: 11px;
    margin: 0;
    color: var(--adm-text, #f8fafc);
    font-size: clamp(1.65rem, 3vw, 2.15rem);
    font-weight: 850;
    letter-spacing: -.035em;
}

.subjects-title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    border-radius: 14px;
    color: #fff;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    box-shadow: 0 12px 28px rgba(79, 70, 229, .28);
}

.subjects-subtitle {
    max-width: 690px;
    margin: 9px 0 0;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .92rem;
    line-height: 1.65;
}

.subjects-add-btn {
    min-height: 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    flex: 0 0 auto;
    padding: 0 18px;
    border: 0;
    border-radius: 13px;
    color: #fff;
    cursor: pointer;
    font-size: .84rem;
    font-weight: 800;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    box-shadow: 0 13px 28px rgba(79, 70, 229, .25);
    transition: transform .2s ease, box-shadow .2s ease;
}

.subjects-add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 17px 34px rgba(79, 70, 229, .34);
}

.subjects-overview {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 16px;
    margin-bottom: 22px;
}

.subjects-flow-card,
.subjects-metrics-card,
.subjects-search-card {
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .15));
    border-radius: 17px;
    background: var(--adm-card-bg, rgba(15, 23, 42, .74));
    box-shadow: 0 14px 36px rgba(2, 6, 23, .12);
}

.subjects-flow-card {
    min-height: 86px;
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 16px 18px;
}

.subjects-flow-intro {
    min-width: 170px;
}

.subjects-flow-intro strong {
    display: block;
    margin-bottom: 3px;
    color: var(--adm-text, #f8fafc);
    font-size: .84rem;
}

.subjects-flow-intro span {
    color: var(--adm-text-muted, #94a3b8);
    font-size: .73rem;
}

.subjects-flow-steps {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.subjects-flow-step {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    padding: 0 13px;
    border: 1px solid rgba(129, 140, 248, .17);
    border-radius: 11px;
    color: var(--adm-text, #e2e8f0);
    background: rgba(99, 102, 241, .07);
    font-size: .78rem;
    font-weight: 760;
}

.subjects-flow-step i {
    color: #818cf8;
}

.subjects-flow-arrow {
    color: var(--adm-text-muted, #64748b);
    font-size: .8rem;
}

.subjects-metrics-card {
    min-width: 300px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 10px;
}

.subjects-metric {
    min-width: 82px;
    padding: 10px 12px;
    text-align: center;
}

.subjects-metric + .subjects-metric {
    border-left: 1px solid var(--adm-border, rgba(148, 163, 184, .13));
}

.subjects-metric strong {
    display: block;
    color: var(--adm-text, #fff);
    font-size: 1.22rem;
    font-weight: 850;
}

.subjects-metric span {
    display: block;
    margin-top: 4px;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .67rem;
}

.subjects-list-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
}

.subjects-list-head h2 {
    margin: 0;
    color: var(--adm-text, #f8fafc);
    font-size: 1rem;
    font-weight: 800;
}

.subjects-search {
    position: relative;
    width: min(100%, 300px);
}

.subjects-search i {
    position: absolute;
    top: 50%;
    left: 14px;
    color: var(--adm-text-muted, #94a3b8);
    transform: translateY(-50%);
    pointer-events: none;
}

.subjects-search input {
    width: 100%;
    min-height: 42px;
    padding: 0 14px 0 40px;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .17));
    border-radius: 12px;
    outline: none;
    color: var(--adm-text, #f8fafc);
    background: var(--adm-card-bg, rgba(15, 23, 42, .7));
    font-size: .78rem;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.subjects-search input:focus {
    border-color: rgba(99, 102, 241, .6);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, .11);
}

.subjects-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 17px;
}

.subject-card {
    --subject-accent: #6366f1;
    position: relative;
    min-width: 0;
    min-height: 265px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .14));
    border-radius: 18px;
    color: inherit;
    text-decoration: none;
    background: var(--adm-card-bg, rgba(15, 23, 42, .78));
    box-shadow: 0 14px 34px rgba(2, 6, 23, .12);
    transition: transform .23s ease, border-color .23s ease, box-shadow .23s ease;
}

.subject-card:hover {
    color: inherit;
    text-decoration: none;
    transform: translateY(-5px);
    border-color: color-mix(in srgb, var(--subject-accent) 46%, transparent);
    box-shadow: 0 22px 46px rgba(2, 6, 23, .2);
}

.subject-card::before {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    height: 3px;
    content: "";
    background: var(--subject-accent);
}

.subject-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 20px 20px 14px;
}

.subject-card-icon {
    width: 50px;
    height: 50px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 50px;
    border-radius: 15px;
    color: #fff;
    background: var(--subject-accent);
    box-shadow: 0 12px 24px color-mix(in srgb, var(--subject-accent) 28%, transparent);
    font-size: 1.25rem;
}

.subject-type {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 0 10px;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .14));
    border-radius: 999px;
    color: var(--adm-text-muted, #94a3b8);
    background: rgba(148, 163, 184, .06);
    font-size: .64rem;
    font-weight: 780;
    letter-spacing: .045em;
    text-transform: uppercase;
}

.subject-card-body {
    min-height: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 0 20px 20px;
}

.subject-card-title {
    margin: 0 0 7px;
    color: var(--adm-text, #f8fafc);
    font-size: 1.08rem;
    font-weight: 850;
    line-height: 1.35;
}

.subject-card-description {
    min-height: 42px;
    margin: 0 0 17px;
    display: -webkit-box;
    overflow: hidden;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .76rem;
    line-height: 1.55;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.subject-card-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 9px;
    margin-top: auto;
}

.subject-card-stat {
    padding: 11px;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .12));
    border-radius: 11px;
    background: rgba(148, 163, 184, .045);
}

.subject-card-stat strong {
    display: block;
    color: var(--adm-text, #fff);
    font-size: 1rem;
    font-weight: 850;
}

.subject-card-stat span {
    display: block;
    margin-top: 3px;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .65rem;
}

.subject-card-action {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 13px;
    padding-top: 13px;
    border-top: 1px solid var(--adm-border, rgba(148, 163, 184, .12));
    color: var(--subject-accent);
    font-size: .73rem;
    font-weight: 800;
}

.subject-card-action i {
    transition: transform .2s ease;
}

.subject-card:hover .subject-card-action i {
    transform: translateX(4px);
}

.subjects-empty,
.subjects-no-result {
    grid-column: 1 / -1;
    padding: 52px 24px;
    border: 1px dashed var(--adm-border, rgba(148, 163, 184, .23));
    border-radius: 18px;
    text-align: center;
    color: var(--adm-text-muted, #94a3b8);
    background: rgba(148, 163, 184, .035);
}

.subjects-empty i,
.subjects-no-result i {
    display: block;
    margin-bottom: 12px;
    color: #818cf8;
    font-size: 2rem;
}

.subjects-empty h3,
.subjects-no-result h3 {
    margin: 0 0 7px;
    color: var(--adm-text, #f8fafc);
    font-size: 1rem;
}

.subjects-no-result {
    display: none;
}

/* Modal de construction */
.subject-builder-overlay {
    position: fixed;
    inset: 0;
    z-index: 2200;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(2, 6, 23, .72);
    backdrop-filter: blur(8px);
}

.subject-builder-overlay.is-open {
    display: flex;
}

.subject-builder-modal {
    width: min(1120px, 100%);
    max-height: min(90vh, 900px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, .2);
    border-radius: 22px;
    background: var(--adm-bg, #0b1220);
    box-shadow: 0 34px 90px rgba(2, 6, 23, .55);
}

.subject-builder-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 22px;
    border-bottom: 1px solid var(--adm-border, rgba(148, 163, 184, .14));
    background: linear-gradient(135deg, rgba(79, 70, 229, .12), rgba(124, 58, 237, .05));
}

.subject-builder-header h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: var(--adm-text, #f8fafc);
    font-size: 1.18rem;
    font-weight: 850;
}

.subject-builder-header p {
    margin: 6px 0 0;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .75rem;
}

.subject-builder-close {
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 40px;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .15));
    border-radius: 11px;
    color: var(--adm-text-muted, #94a3b8);
    cursor: pointer;
    background: rgba(148, 163, 184, .05);
}

.subject-builder-body {
    min-height: 0;
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(260px, .8fr);
    overflow: hidden;
}

.subject-builder-form-pane {
    min-height: 0;
    overflow-y: auto;
    padding: 22px;
}

.subject-builder-preview-pane {
    min-height: 0;
    overflow-y: auto;
    padding: 22px;
    border-left: 1px solid var(--adm-border, rgba(148, 163, 184, .14));
    background: rgba(148, 163, 184, .025);
}

.subject-builder-section {
    margin-bottom: 22px;
}

.subject-builder-section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.subject-builder-section-title h3 {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    color: var(--adm-text, #f8fafc);
    font-size: .88rem;
    font-weight: 820;
}

.subject-builder-section-title span {
    color: var(--adm-text-muted, #94a3b8);
    font-size: .68rem;
}

.subject-form-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(180px, .7fr);
    gap: 13px;
}

.subject-form-group {
    min-width: 0;
}

.subject-form-group.is-full {
    grid-column: 1 / -1;
}

.subject-form-label {
    display: block;
    margin-bottom: 7px;
    color: var(--adm-text, #e2e8f0);
    font-size: .7rem;
    font-weight: 750;
}

.subject-required {
    color: #fb7185;
}

.subject-form-control {
    width: 100%;
    min-height: 43px;
    padding: 10px 12px;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .17));
    border-radius: 11px;
    outline: none;
    color: var(--adm-text, #f8fafc);
    background: var(--adm-card-bg, rgba(15, 23, 42, .7));
    font: inherit;
    font-size: .78rem;
    transition: border-color .2s ease, box-shadow .2s ease;
}

textarea.subject-form-control {
    min-height: 82px;
    resize: vertical;
}

.subject-form-control:focus {
    border-color: rgba(99, 102, 241, .6);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, .1);
}

.subject-level-list {
    display: flex;
    flex-direction: column;
    gap: 13px;
}

.subject-level-builder {
    overflow: hidden;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .15));
    border-radius: 15px;
    background: var(--adm-card-bg, rgba(15, 23, 42, .66));
}

.subject-level-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    border-bottom: 1px solid var(--adm-border, rgba(148, 163, 184, .12));
    background: rgba(99, 102, 241, .05);
}

.subject-level-index {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--adm-text, #f8fafc);
    font-size: .75rem;
    font-weight: 800;
}

.subject-level-number {
    width: 27px;
    height: 27px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: #c7d2fe;
    background: rgba(99, 102, 241, .14);
}

.subject-icon-btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .15));
    border-radius: 10px;
    color: var(--adm-text-muted, #94a3b8);
    cursor: pointer;
    background: rgba(148, 163, 184, .04);
}

.subject-icon-btn.is-danger {
    color: #fda4af;
    border-color: rgba(244, 63, 94, .2);
    background: rgba(244, 63, 94, .07);
}

.subject-level-content {
    padding: 14px;
}

.subject-level-fields {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 11px;
    margin-bottom: 14px;
}

.subject-class-block {
    padding-top: 13px;
    border-top: 1px solid var(--adm-border, rgba(148, 163, 184, .11));
}

.subject-class-block-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
}

.subject-class-block-head strong {
    color: var(--adm-text, #e2e8f0);
    font-size: .7rem;
}

.subject-add-small,
.subject-add-level {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid rgba(99, 102, 241, .22);
    border-radius: 10px;
    color: #c7d2fe;
    cursor: pointer;
    background: rgba(99, 102, 241, .08);
    font-size: .68rem;
    font-weight: 760;
}

.subject-add-small {
    min-height: 34px;
    padding: 0 11px;
}

.subject-add-level {
    width: 100%;
    min-height: 43px;
    margin-top: 13px;
}

.subject-class-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
}

.subject-class-row {
    display: flex;
    align-items: center;
    gap: 7px;
}

.subject-class-row .subject-form-control {
    min-width: 0;
}

.subject-builder-errors {
    margin-bottom: 18px;
    padding: 13px 15px;
    border: 1px solid rgba(244, 63, 94, .23);
    border-radius: 12px;
    color: #fecdd3;
    background: rgba(244, 63, 94, .08);
    font-size: .73rem;
}

.subject-builder-errors strong {
    display: block;
    margin-bottom: 6px;
}

.subject-builder-errors ul {
    margin: 0;
    padding-left: 18px;
}

.subject-preview-title {
    margin-bottom: 15px;
}

.subject-preview-title strong {
    display: block;
    color: var(--adm-text, #f8fafc);
    font-size: .86rem;
}

.subject-preview-title span {
    display: block;
    margin-top: 4px;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .68rem;
    line-height: 1.5;
}

.subject-preview-card {
    padding: 15px;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .14));
    border-radius: 15px;
    background: var(--adm-card-bg, rgba(15, 23, 42, .72));
}

.subject-preview-subject {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--adm-border, rgba(148, 163, 184, .11));
}

.subject-preview-subject i {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: #fff;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
}

.subject-preview-subject strong {
    min-width: 0;
    overflow: hidden;
    color: var(--adm-text, #f8fafc);
    font-size: .78rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.subject-preview-levels {
    display: flex;
    flex-direction: column;
    gap: 11px;
    margin-top: 13px;
}

.subject-preview-level {
    position: relative;
    padding-left: 20px;
}

.subject-preview-level::before {
    position: absolute;
    top: 3px;
    bottom: -12px;
    left: 5px;
    width: 1px;
    content: "";
    background: rgba(129, 140, 248, .25);
}

.subject-preview-level:last-child::before {
    bottom: 11px;
}

.subject-preview-level-name {
    display: flex;
    align-items: center;
    gap: 7px;
    color: var(--adm-text, #e2e8f0);
    font-size: .71rem;
    font-weight: 760;
}

.subject-preview-level-name::before {
    position: absolute;
    top: 8px;
    left: 2px;
    width: 7px;
    height: 7px;
    content: "";
    border: 2px solid #818cf8;
    border-radius: 50%;
    background: var(--adm-bg, #0b1220);
}

.subject-preview-classes {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}

.subject-preview-class {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 0 8px;
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .12));
    border-radius: 8px;
    color: var(--adm-text-muted, #94a3b8);
    background: rgba(148, 163, 184, .045);
    font-size: .62rem;
}

.subject-preview-empty {
    padding: 14px 4px;
    color: var(--adm-text-muted, #94a3b8);
    font-size: .68rem;
    line-height: 1.55;
    text-align: center;
}

.subject-builder-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 15px 22px;
    border-top: 1px solid var(--adm-border, rgba(148, 163, 184, .14));
    background: var(--adm-card-bg, rgba(15, 23, 42, .78));
}

.subject-builder-footer-note {
    color: var(--adm-text-muted, #94a3b8);
    font-size: .67rem;
}

.subject-builder-actions {
    display: flex;
    align-items: center;
    gap: 9px;
}

.subject-cancel-btn,
.subject-submit-btn {
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 15px;
    border-radius: 11px;
    cursor: pointer;
    font-size: .75rem;
    font-weight: 800;
}

.subject-cancel-btn {
    border: 1px solid var(--adm-border, rgba(148, 163, 184, .16));
    color: var(--adm-text-muted, #94a3b8);
    background: transparent;
}

.subject-submit-btn {
    border: 0;
    color: #fff;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    box-shadow: 0 10px 24px rgba(79, 70, 229, .22);
}

body.subject-builder-open {
    overflow: hidden;
}

@media (max-width: 1100px) {
    .subjects-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .subjects-overview {
        grid-template-columns: 1fr;
    }

    .subjects-metrics-card {
        min-width: 0;
    }
}

@media (max-width: 860px) {
    .subject-builder-body {
        grid-template-columns: 1fr;
        overflow-y: auto;
    }

    .subject-builder-form-pane,
    .subject-builder-preview-pane {
        overflow: visible;
    }

    .subject-builder-preview-pane {
        border-top: 1px solid var(--adm-border, rgba(148, 163, 184, .14));
        border-left: 0;
    }

    .subject-builder-modal {
        max-height: calc(100vh - 24px);
    }
}

@media (max-width: 680px) {
    .subjects-toolbar,
    .subjects-list-head,
    .subjects-builder-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .subjects-add-btn,
    .subjects-search {
        width: 100%;
    }

    .subjects-flow-card {
        align-items: flex-start;
        flex-direction: column;
    }

    .subjects-flow-intro {
        min-width: 0;
    }

    .subjects-flow-arrow {
        display: none;
    }

    .subjects-flow-steps {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr;
    }

    .subjects-grid {
        grid-template-columns: 1fr;
    }

    .subject-builder-overlay {
        padding: 0;
    }

    .subject-builder-modal {
        width: 100%;
        height: 100%;
        max-height: none;
        border-radius: 0;
    }

    .subject-form-grid,
    .subject-level-fields,
    .subject-class-list {
        grid-template-columns: 1fr;
    }

    .subject-builder-header,
    .subject-builder-form-pane,
    .subject-builder-preview-pane,
    .subject-builder-footer {
        padding-right: 16px;
        padding-left: 16px;
    }

    .subject-builder-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .subject-builder-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<div class="subjects-page">
    <div class="subjects-toolbar">
        <div class="subjects-title-wrap">
            <span class="subjects-kicker">
                <i class="bi bi-diagram-3"></i>
                Structure pédagogique
            </span>

            <h1 class="subjects-title">
                <span class="subjects-title-icon">
                    <i class="bi bi-book"></i>
                </span>
                Gestion des matières
            </h1>

            <p class="subjects-subtitle">
                Gérez uniquement les trois matières officielles : Arabe, Coran et Soutien Lycée, avec leurs niveaux et leurs classes.
            </p>
        </div>

        <button
            type="button"
            class="subjects-add-btn"
            id="openSubjectBuilder"
        >
            <i class="bi bi-plus-lg"></i>
            Configurer une matière
        </button>
    </div>

    @if(session('success'))
        <div class="adm-alert adm-alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="adm-alert mb-4" style="background:rgba(6,182,212,.09);border:1px solid rgba(6,182,212,.2);color:#67e8f9;">
            <i class="bi bi-info-circle me-2"></i>
            {{ session('info') }}
        </div>
    @endif

    <div class="subjects-overview">
        <div class="subjects-flow-card">
            <div class="subjects-flow-intro">
                <strong>Structure pédagogique</strong>
                <span>Arabe, Coran ou Soutien Lycée</span>
            </div>

            <div class="subjects-flow-steps" aria-label="Matière vers niveaux vers classes">
                <span class="subjects-flow-step">
                    <i class="bi bi-book"></i>
                    Matière
                </span>
                <i class="bi bi-chevron-right subjects-flow-arrow"></i>
                <span class="subjects-flow-step">
                    <i class="bi bi-layers"></i>
                    Niveaux
                </span>
                <i class="bi bi-chevron-right subjects-flow-arrow"></i>
                <span class="subjects-flow-step">
                    <i class="bi bi-mortarboard"></i>
                    Classes
                </span>
            </div>
        </div>

        <div class="subjects-metrics-card">
            <div class="subjects-metric">
                <strong>{{ $subjects->count() }}</strong>
                <span>Matières</span>
            </div>
            <div class="subjects-metric">
                <strong>{{ $totalLevels }}</strong>
                <span>Niveaux</span>
            </div>
            <div class="subjects-metric">
                <strong>{{ $totalClasses }}</strong>
                <span>Classes</span>
            </div>
        </div>
    </div>

    <div class="subjects-list-head">
        <h2>Matières disponibles</h2>

        <label class="subjects-search">
            <i class="bi bi-search"></i>
            <input
                type="search"
                id="subjectSearch"
                placeholder="Rechercher une matière..."
                autocomplete="off"
            >
        </label>
    </div>

    <div class="subjects-grid" id="subjectsGrid">
        @forelse($subjects as $subject)
            @php
                $normalizedName =
                    \App\Models\VocalTestPrompt::normalizePathName($subject->name);

                $palette = match ($normalizedName) {
                    'arabe' => ['#2563eb', 'bi-translate'],
                    'coran', 'quran', 'القران' => ['#7c3aed', 'bi-book-half'],
                    'soutien lycee' => ['#4f46e5', 'bi-journal-bookmark-fill'],
                    default => [
                        ['#0ea5e9', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'][$loop->index % 5],
                        'bi-journal-bookmark-fill',
                    ],
                };

                $levelCount = (int) ($subject->validated_level_count ?? 0);
                $classCount = (int) ($subject->validated_class_count ?? 0);
            @endphp

            <a
                href="{{ route('admin.subjects.levels', $subject) }}"
                class="subject-card"
                data-subject-card
                data-subject-name="{{ \Illuminate\Support\Str::lower($subject->name) }}"
                style="--subject-accent:{{ $palette[0] }};"
            >
                <div class="subject-card-top">
                    <span class="subject-card-icon">
                        <i class="bi {{ $palette[1] }}"></i>
                    </span>

                    <span class="subject-type">
                        {{ $subject->type === 'religieux' ? 'Religieux' : 'Scolaire' }}
                    </span>
                </div>

                <div class="subject-card-body">
                    <h2 class="subject-card-title">
                        {{ $subject->name }}
                    </h2>

                    <p class="subject-card-description">
                        {{ $subject->description ?: 'Structure pédagogique organisée par niveaux et classes.' }}
                    </p>

                    <div class="subject-card-stats">
                        <div class="subject-card-stat">
                            <strong>{{ $levelCount }}</strong>
                            <span>{{ $levelCount === 1 ? 'Niveau' : 'Niveaux' }}</span>
                        </div>

                        <div class="subject-card-stat">
                            <strong>{{ $classCount }}</strong>
                            <span>{{ $classCount === 1 ? 'Classe' : 'Classes' }}</span>
                        </div>
                    </div>

                    <span class="subject-card-action">
                        Voir la structure
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
            </a>
        @empty
            <div class="subjects-empty">
                <i class="bi bi-journal-plus"></i>
                <h3>Aucune matière officielle enregistrée</h3>
                <p>Configurez Arabe, Coran ou Soutien Lycée avec ses niveaux et ses classes.</p>
            </div>
        @endforelse

        <div class="subjects-no-result" id="subjectsNoResult">
            <i class="bi bi-search"></i>
            <h3>Aucune matière trouvée</h3>
            <p>Essayez avec un autre mot-clé.</p>
        </div>
    </div>
</div>

<div
    class="subject-builder-overlay {{ $errors->any() ? 'is-open' : '' }}"
    id="subjectBuilderOverlay"
    aria-hidden="{{ $errors->any() ? 'false' : 'true' }}"
>
    <form
        method="POST"
        action="{{ route('admin.subjects.hierarchy.store') }}"
        class="subject-builder-modal"
        id="subjectHierarchyForm"
    >
        @csrf

        <div class="subject-builder-header">
            <div>
                <h2>
                    <i class="bi bi-diagram-3"></i>
                    Configuration de la matière
                </h2>
                <p>Sélectionnez Arabe, Coran ou Soutien Lycée, puis organisez ses niveaux et ses classes.</p>
            </div>

            <button
                type="button"
                class="subject-builder-close"
                data-close-subject-builder
                aria-label="Fermer"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="subject-builder-body">
            <div class="subject-builder-form-pane">
                @if($errors->any())
                    <div class="subject-builder-errors">
                        <strong>Veuillez corriger les informations suivantes :</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="subject-builder-section">
                    <div class="subject-builder-section-title">
                        <h3>
                            <i class="bi bi-book"></i>
                            1. Matière officielle
                        </h3>
                        <span>3 choix autorisés</span>
                    </div>

                    <div class="subject-form-grid">
                        <div class="subject-form-group">
                            <label class="subject-form-label" for="subjectName">
                                Matière <span class="subject-required">*</span>
                            </label>
                            <select
                                class="subject-form-control"
                                id="subjectName"
                                name="name"
                                required
                            >
                                <option value="Arabe" @selected(old('name', 'Arabe') === 'Arabe')>
                                    Arabe
                                </option>
                                <option value="Coran" @selected(old('name') === 'Coran')>
                                    Coran
                                </option>
                                <option value="Soutien Lycée" @selected(old('name') === 'Soutien Lycée')>
                                    Soutien Lycée
                                </option>
                            </select>
                            <small style="display:block;margin-top:7px;color:var(--adm-text-muted,#94a3b8);font-size:.66rem;">
                                Aucune autre matière ne peut être ajoutée depuis cette page.
                            </small>
                        </div>

                        <div class="subject-form-group">
                            <label class="subject-form-label" for="subjectType">
                                Type
                            </label>
                            <select
                                class="subject-form-control"
                                id="subjectType"
                                disabled
                                aria-disabled="true"
                            >
                                <option value="scolaire">Scolaire</option>
                                <option value="religieux">Religieux</option>
                            </select>
                            <small style="display:block;margin-top:7px;color:var(--adm-text-muted,#94a3b8);font-size:.66rem;">
                                Le type est défini automatiquement selon la matière.
                            </small>
                        </div>

                        <div class="subject-form-group is-full">
                            <label class="subject-form-label" for="subjectDescription">
                                Description
                            </label>
                            <textarea
                                class="subject-form-control"
                                id="subjectDescription"
                                name="description"
                                maxlength="1000"
                                placeholder="Une courte description de la matière..."
                            >{{ old('description') }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="subject-builder-section">
                    <div class="subject-builder-section-title">
                        <h3>
                            <i class="bi bi-layers"></i>
                            2. Niveaux et classes
                        </h3>
                        <span id="levelCounter">1 niveau</span>
                    </div>

                    <div class="subject-level-list" id="subjectLevelList"></div>

                    <button
                        type="button"
                        class="subject-add-level"
                        id="addSubjectLevel"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Ajouter un niveau
                    </button>
                </section>
            </div>

            <aside class="subject-builder-preview-pane">
                <div class="subject-preview-title">
                    <strong>Aperçu de la structure</strong>
                    <span>Cette organisation sera utilisée dans les autres modules.</span>
                </div>

                <div class="subject-preview-card" id="subjectStructurePreview"></div>
            </aside>
        </div>

        <div class="subject-builder-footer">
            <span class="subject-builder-footer-note">
                <i class="bi bi-shield-check me-1"></i>
                Toutes les données seront créées en une seule opération.
            </span>

            <div class="subject-builder-actions">
                <button
                    type="button"
                    class="subject-cancel-btn"
                    data-close-subject-builder
                >
                    Annuler
                </button>

                <button type="submit" class="subject-submit-btn">
                    <i class="bi bi-check2-circle"></i>
                    Enregistrer la structure
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('subjectBuilderOverlay');
    const openButton = document.getElementById('openSubjectBuilder');
    const closeButtons = document.querySelectorAll('[data-close-subject-builder]');
    const levelList = document.getElementById('subjectLevelList');
    const addLevelButton = document.getElementById('addSubjectLevel');
    const levelCounter = document.getElementById('levelCounter');
    const preview = document.getElementById('subjectStructurePreview');
    const subjectNameInput = document.getElementById('subjectName');
    const subjectTypeInput = document.getElementById('subjectType');
    const searchInput = document.getElementById('subjectSearch');
    const noResult = document.getElementById('subjectsNoResult');
    const initialLevels = @json($oldLevels);
    const subjectTypes = {
        'Arabe': 'scolaire',
        'Coran': 'religieux',
        'Soutien Lycée': 'scolaire'
    };

    let levels = Array.isArray(initialLevels) && initialLevels.length
        ? initialLevels.map(function (level) {
            const classes = Array.isArray(level.classes) && level.classes.length
                ? level.classes
                : [{ name: '' }];

            return {
                name: level.name || '',
                description: level.description || '',
                classes: classes.map(function (classItem) {
                    return { name: classItem.name || '' };
                })
            };
        })
        : [{ name: '', description: '', classes: [{ name: '' }] }];

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function openBuilder() {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('subject-builder-open');

        window.setTimeout(function () {
            subjectNameInput.focus();
        }, 80);
    }

    function closeBuilder() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('subject-builder-open');
    }

    function renderLevels() {
        levelList.innerHTML = levels.map(function (level, levelIndex) {
            const classRows = level.classes.map(function (classItem, classIndex) {
                return `
                    <div class="subject-class-row">
                        <input
                            type="text"
                            class="subject-form-control"
                            name="levels[${levelIndex}][classes][${classIndex}][name]"
                            value="${escapeHtml(classItem.name)}"
                            placeholder="Ex. Classe A"
                            maxlength="120"
                            required
                            data-level-index="${levelIndex}"
                            data-class-index="${classIndex}"
                            data-field="class-name"
                        >

                        <button
                            type="button"
                            class="subject-icon-btn is-danger"
                            title="Supprimer cette classe"
                            data-action="remove-class"
                            data-level-index="${levelIndex}"
                            data-class-index="${classIndex}"
                            ${level.classes.length === 1 ? 'disabled style="opacity:.45;cursor:not-allowed;"' : ''}
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                `;
            }).join('');

            return `
                <article class="subject-level-builder">
                    <div class="subject-level-head">
                        <span class="subject-level-index">
                            <span class="subject-level-number">${levelIndex + 1}</span>
                            Niveau ${levelIndex + 1}
                        </span>

                        <button
                            type="button"
                            class="subject-icon-btn is-danger"
                            title="Supprimer ce niveau"
                            data-action="remove-level"
                            data-level-index="${levelIndex}"
                            ${levels.length === 1 ? 'disabled style="opacity:.45;cursor:not-allowed;"' : ''}
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    <div class="subject-level-content">
                        <div class="subject-level-fields">
                            <div class="subject-form-group">
                                <label class="subject-form-label">
                                    Nom du niveau <span class="subject-required">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="subject-form-control"
                                    name="levels[${levelIndex}][name]"
                                    value="${escapeHtml(level.name)}"
                                    placeholder="Ex. Débutant"
                                    maxlength="120"
                                    required
                                    data-level-index="${levelIndex}"
                                    data-field="level-name"
                                >
                            </div>

                            <div class="subject-form-group">
                                <label class="subject-form-label">
                                    Description du niveau
                                </label>
                                <input
                                    type="text"
                                    class="subject-form-control"
                                    name="levels[${levelIndex}][description]"
                                    value="${escapeHtml(level.description)}"
                                    placeholder="Optionnel"
                                    maxlength="500"
                                    data-level-index="${levelIndex}"
                                    data-field="level-description"
                                >
                            </div>
                        </div>

                        <div class="subject-class-block">
                            <div class="subject-class-block-head">
                                <strong>Classes de ce niveau</strong>

                                <button
                                    type="button"
                                    class="subject-add-small"
                                    data-action="add-class"
                                    data-level-index="${levelIndex}"
                                >
                                    <i class="bi bi-plus-lg"></i>
                                    Ajouter une classe
                                </button>
                            </div>

                            <div class="subject-class-list">
                                ${classRows}
                            </div>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        levelCounter.textContent = levels.length + (levels.length > 1 ? ' niveaux' : ' niveau');
        renderPreview();
    }

    function syncSubjectType() {
        subjectTypeInput.value = subjectTypes[subjectNameInput.value] || 'scolaire';
    }

    function renderPreview() {
        syncSubjectType();

        const subjectName = subjectNameInput.value.trim() || 'Arabe';
        const typeLabel = subjectTypeInput.value === 'religieux' ? 'Religieux' : 'Scolaire';

        const validLevels = levels.filter(function (level) {
            return level.name.trim() || level.classes.some(function (classItem) {
                return classItem.name.trim();
            });
        });

        const levelMarkup = validLevels.map(function (level, levelIndex) {
            const classMarkup = level.classes
                .filter(function (classItem) { return classItem.name.trim(); })
                .map(function (classItem) {
                    return `<span class="subject-preview-class">${escapeHtml(classItem.name)}</span>`;
                })
                .join('');

            return `
                <div class="subject-preview-level">
                    <div class="subject-preview-level-name">
                        ${escapeHtml(level.name.trim() || ('Niveau ' + (levelIndex + 1)))}
                    </div>
                    <div class="subject-preview-classes">
                        ${classMarkup || '<span class="subject-preview-class">Classe à définir</span>'}
                    </div>
                </div>
            `;
        }).join('');

        preview.innerHTML = `
            <div class="subject-preview-subject">
                <i class="bi bi-book"></i>
                <div style="min-width:0;">
                    <strong>${escapeHtml(subjectName)}</strong>
                    <span style="display:block;margin-top:2px;color:var(--adm-text-muted,#94a3b8);font-size:.61rem;">${typeLabel}</span>
                </div>
            </div>
            ${levelMarkup
                ? `<div class="subject-preview-levels">${levelMarkup}</div>`
                : '<div class="subject-preview-empty">Ajoutez les niveaux et les classes pour voir la structure complète.</div>'}
        `;
    }

    openButton.addEventListener('click', openBuilder);
    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeBuilder);
    });

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closeBuilder();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeBuilder();
        }
    });

    addLevelButton.addEventListener('click', function () {
        levels.push({
            name: '',
            description: '',
            classes: [{ name: '' }]
        });
        renderLevels();

        const latestLevel = levelList.lastElementChild;
        if (latestLevel) {
            latestLevel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const firstInput = latestLevel.querySelector('input');
            if (firstInput) firstInput.focus();
        }
    });

    levelList.addEventListener('click', function (event) {
        const button = event.target.closest('[data-action]');
        if (!button) return;

        const action = button.dataset.action;
        const levelIndex = Number(button.dataset.levelIndex);
        const classIndex = Number(button.dataset.classIndex);

        if (action === 'add-class') {
            levels[levelIndex].classes.push({ name: '' });
            renderLevels();
            return;
        }

        if (action === 'remove-class' && levels[levelIndex].classes.length > 1) {
            levels[levelIndex].classes.splice(classIndex, 1);
            renderLevels();
            return;
        }

        if (action === 'remove-level' && levels.length > 1) {
            levels.splice(levelIndex, 1);
            renderLevels();
        }
    });

    levelList.addEventListener('input', function (event) {
        const input = event.target;
        const field = input.dataset.field;
        const levelIndex = Number(input.dataset.levelIndex);
        const classIndex = Number(input.dataset.classIndex);

        if (!field || Number.isNaN(levelIndex)) return;

        if (field === 'level-name') {
            levels[levelIndex].name = input.value;
        } else if (field === 'level-description') {
            levels[levelIndex].description = input.value;
        } else if (field === 'class-name' && !Number.isNaN(classIndex)) {
            levels[levelIndex].classes[classIndex].name = input.value;
        }

        renderPreview();
    });

    subjectNameInput.addEventListener('change', renderPreview);

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim().toLocaleLowerCase('fr');
            const cards = Array.from(document.querySelectorAll('[data-subject-card]'));
            let visibleCount = 0;

            cards.forEach(function (card) {
                const isVisible = card.dataset.subjectName.includes(query);
                card.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount += 1;
            });

            noResult.style.display = cards.length > 0 && visibleCount === 0 ? 'block' : 'none';
        });
    }

    syncSubjectType();
    renderLevels();

    if (overlay.classList.contains('is-open')) {
        document.body.classList.add('subject-builder-open');
    }
});
</script>
@endsection
