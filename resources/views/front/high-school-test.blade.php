@extends('layouts.front')

@section('title', $test['title'])

@section('content')

@php
    $normalizedClassName =
        \Illuminate\Support\Str::slug(
            \Illuminate\Support\Str::ascii(
                $class->name
            )
        );

    $theme = $normalizedClassName
        === 'mathematiques'
            ? [
                'icon' => 'bi-calculator-fill',
                'gradient' =>
                    'linear-gradient(135deg,#F59E0B,#D97706)',
                'accent' => '#FBBF24',
                'soft' => 'rgba(245,158,11,0.12)',
            ]
            : [
                'icon' => 'bi-lightning-charge-fill',
                'gradient' =>
                    'linear-gradient(135deg,#06B6D4,#2563EB)',
                'accent' => '#38BDF8',
                'soft' => 'rgba(6,182,212,0.12)',
            ];

    $totalPoints = collect(
        $test['questions']
    )->sum('points');
@endphp

<section
    class="written-test-hero"
    style="
        --test-gradient: {{ $theme['gradient'] }};
        --test-accent: {{ $theme['accent'] }};
        --test-soft: {{ $theme['soft'] }};
    "
>
    <div class="container">
        <a
            href="{{
                route(
                    'front.subject.levels',
                    $subject
                )
            }}"
            class="written-test-back"
        >
            <i class="bi bi-arrow-left"></i>
            Retour aux matières du BAC
        </a>

        <div class="written-test-heading">
            <span class="written-test-icon">
                <i class="bi {{ $theme['icon'] }}"></i>
            </span>

            <div>
                <span class="written-test-badge">
                    Test diagnostic interne
                </span>

                <h1>{{ $test['title'] }}</h1>

                <p>{{ $test['subtitle'] }}</p>
            </div>
        </div>

        <div class="written-test-meta">
            <span>
                <i class="bi bi-clock"></i>
                {{ $test['duration_minutes'] }} minutes
            </span>

            <span>
                <i class="bi bi-list-check"></i>
                {{ count($test['questions']) }} exercices
            </span>

            <span>
                <i class="bi bi-award"></i>
                {{ $totalPoints }} points
            </span>

            <span>
                <i class="bi bi-camera"></i>
                Réponse en images
            </span>
        </div>
    </div>
</section>

<section
    class="written-test-content"
    style="
        --test-gradient: {{ $theme['gradient'] }};
        --test-accent: {{ $theme['accent'] }};
        --test-soft: {{ $theme['soft'] }};
    "
>
    <div class="container">
        <div class="written-test-layout">
            <main>
                <div class="written-test-instructions">
                    <div class="instructions-icon">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>

                    <div>
                        <h2>Consignes</h2>

                        <ul>
                            @foreach($test['instructions'] as $instruction)
                                <li>{{ $instruction }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="written-test-questions">
                    @foreach($test['questions'] as $question)
                        <article class="written-question-card">
                            <div class="written-question-header">
                                <span class="question-number">
                                    {{ $loop->iteration }}
                                </span>

                                <div>
                                    <h2>{{ $question['title'] }}</h2>

                                    <span>
                                        {{ $question['points'] }}
                                        points
                                    </span>
                                </div>
                            </div>

                            <p class="question-statement">
                                {{ $question['statement'] }}
                            </p>

                            <ol>
                                @foreach($question['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ol>
                        </article>
                    @endforeach
                </div>
            </main>

            <aside class="written-test-submit-card">
                <div class="submit-card-icon">
                    <i class="bi bi-images"></i>
                </div>

                <h2>Envoyer mes réponses</h2>

                <p>
                    Répondez sur papier, puis importez des photos
                    nettes de toutes vos feuilles.
                </p>

                @guest
                    <div class="written-flow-notice">
                        <i class="bi bi-info-circle-fill"></i>

                        <span>
                            Passez le test maintenant. Après le
                            rendez-vous, la page d’inscription au
                            compte sera affichée automatiquement.
                        </span>
                    </div>
                @endguest

                <form
                    method="POST"
                    action="{{
                        route(
                            'high-school-test.store',
                            [
                                $subject,
                                $level,
                                $class,
                            ]
                        )
                    }}"
                    enctype="multipart/form-data"
                    id="writtenTestForm"
                >
                    @csrf

                    <label
                        class="written-upload-zone"
                        for="answer_images"
                    >
                        <i class="bi bi-cloud-arrow-up-fill"></i>

                        <strong>
                            Choisir les images
                        </strong>

                        <span>
                            JPG, PNG ou WEBP · 5 Mo par image
                        </span>

                        <input
                            id="answer_images"
                            name="answer_images[]"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            multiple
                            required
                        >
                    </label>

                    @error('answer_images')
                        <div class="written-test-error">
                            {{ $message }}
                        </div>
                    @enderror

                    @error('answer_images.*')
                        <div class="written-test-error">
                            {{ $message }}
                        </div>
                    @enderror

                    <div
                        id="writtenImagePreview"
                        class="written-image-preview"
                    ></div>

                    <label class="written-confirmation">
                        <input
                            type="checkbox"
                            name="confirmation"
                            value="1"
                            required
                        >

                        <span>
                            Je confirme que les images sont lisibles
                            et contiennent toutes mes réponses.
                        </span>
                    </label>

                    @error('confirmation')
                        <div class="written-test-error">
                            {{ $message }}
                        </div>
                    @enderror

                    <button
                        type="submit"
                        class="written-test-submit-button"
                    >
                        <i class="bi bi-calendar-check-fill"></i>

                        Importer et prendre rendez-vous
                    </button>
                </form>

            </aside>
        </div>
    </div>
</section>

<style>

.written-flow-notice {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 11px;
    margin-bottom: 13px;
    color: #BFDBFE;
    border: 1px solid rgba(59,130,246,.17);
    border-radius: 11px;
    background: rgba(37,99,235,.07);
    font-size: .66rem;
    line-height: 1.5;
}

.written-flow-notice i {
    flex: 0 0 auto;
    margin-top: 2px;
    color: #93C5FD;
}

.written-test-hero {
    position: relative;
    padding: 2.4rem 0 2.7rem;
    overflow: hidden;
    background:
        radial-gradient(
            circle at 78% 25%,
            var(--test-soft),
            transparent 34%
        ),
        linear-gradient(
            135deg,
            #071323,
            #181036 55%,
            #0B1D30
        );
}

.written-test-back {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 1.4rem;
    color: rgba(255,255,255,0.48);
    font-size: 0.78rem;
    text-decoration: none;
}

.written-test-back:hover {
    color: #ffffff;
}

.written-test-heading {
    display: flex;
    align-items: center;
    gap: 17px;
}

.written-test-icon {
    width: 72px;
    height: 72px;
    flex: 0 0 72px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 21px;
    color: #ffffff;
    background: var(--test-gradient);
    box-shadow: 0 16px 34px rgba(0,0,0,0.2);
    font-size: 1.75rem;
}

.written-test-badge {
    display: inline-flex;
    margin-bottom: 7px;
    padding: 5px 10px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 999px;
    color: var(--test-accent);
    background: var(--test-soft);
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
}

.written-test-heading h1 {
    margin: 0;
    color: #ffffff;
    font-family: "Poppins", sans-serif;
    font-size: clamp(1.55rem, 4vw, 2.45rem);
    font-weight: 850;
}

.written-test-heading p {
    margin: 6px 0 0;
    color: rgba(255,255,255,0.52);
}

.written-test-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
    margin-top: 1.5rem;
}

.written-test-meta span {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 10px;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 10px;
    color: rgba(255,255,255,0.58);
    background: rgba(255,255,255,0.035);
    font-size: 0.7rem;
}

.written-test-meta i {
    color: var(--test-accent);
}

.written-test-content {
    padding: 2rem 0 4rem;
    background: #07111F;
}

.written-test-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    align-items: start;
    gap: 24px;
}

.written-test-instructions {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    margin-bottom: 1rem;
    padding: 1rem;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 16px;
    background: rgba(255,255,255,0.03);
}

.instructions-icon {
    width: 39px;
    height: 39px;
    flex: 0 0 39px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    color: var(--test-accent);
    background: var(--test-soft);
}

.written-test-instructions h2 {
    margin: 0 0 6px;
    color: rgba(255,255,255,0.9);
    font-size: 0.9rem;
}

.written-test-instructions ul {
    margin: 0;
    padding-left: 1.1rem;
    color: rgba(255,255,255,0.48);
    font-size: 0.72rem;
    line-height: 1.65;
}

.written-test-questions {
    display: grid;
    gap: 14px;
}

.written-question-card {
    padding: 1.15rem;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 17px;
    background:
        linear-gradient(
            145deg,
            rgba(17,27,47,0.96),
            rgba(9,17,32,0.98)
        );
}

.written-question-header {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 0.85rem;
}

.question-number {
    width: 35px;
    height: 35px;
    flex: 0 0 35px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: #ffffff;
    background: var(--test-gradient);
    font-weight: 850;
}

.written-question-header h2 {
    margin: 0;
    color: rgba(255,255,255,0.93);
    font-size: 0.88rem;
}

.written-question-header span {
    color: var(--test-accent);
    font-size: 0.64rem;
}

.question-statement {
    margin: 0 0 0.75rem;
    padding: 9px 11px;
    border-left: 3px solid var(--test-accent);
    border-radius: 0 9px 9px 0;
    color: rgba(255,255,255,0.73);
    background: var(--test-soft);
    font-size: 0.76rem;
    line-height: 1.55;
}

.written-question-card ol {
    margin: 0;
    padding-left: 1.3rem;
    color: rgba(255,255,255,0.53);
    font-size: 0.73rem;
    line-height: 1.75;
}

.written-test-submit-card {
    position: sticky;
    top: 95px;
    padding: 1.2rem;
    border: 1px solid rgba(255,255,255,0.075);
    border-radius: 19px;
    background:
        linear-gradient(
            150deg,
            rgba(17,27,47,0.98),
            rgba(9,17,32,0.99)
        );
    box-shadow: 0 18px 45px rgba(0,0,0,0.22);
}

.submit-card-icon {
    width: 54px;
    height: 54px;
    display: grid;
    place-items: center;
    margin-bottom: 0.9rem;
    border-radius: 16px;
    color: #ffffff;
    background: var(--test-gradient);
    font-size: 1.25rem;
}

.written-test-submit-card h2 {
    margin: 0 0 0.35rem;
    color: #ffffff;
    font-size: 1rem;
}

.written-test-submit-card > p {
    margin: 0 0 1rem;
    color: rgba(255,255,255,0.43);
    font-size: 0.7rem;
    line-height: 1.55;
}

.written-upload-zone {
    min-height: 145px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 1rem;
    border: 1px dashed rgba(255,255,255,0.16);
    border-radius: 15px;
    color: rgba(255,255,255,0.58);
    background: rgba(255,255,255,0.025);
    text-align: center;
    cursor: pointer;
}

.written-upload-zone:hover {
    border-color: var(--test-accent);
    background: var(--test-soft);
}

.written-upload-zone > i {
    color: var(--test-accent);
    font-size: 1.7rem;
}

.written-upload-zone strong {
    color: rgba(255,255,255,0.82);
    font-size: 0.78rem;
}

.written-upload-zone span {
    font-size: 0.61rem;
}

.written-upload-zone input {
    display: none;
}

.written-image-preview {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 7px;
    margin-top: 9px;
}

.written-image-preview:empty {
    display: none;
}

.written-preview-item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
}

.written-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.written-confirmation {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin: 0.9rem 0;
    color: rgba(255,255,255,0.48);
    font-size: 0.65rem;
    line-height: 1.45;
}

.written-confirmation input {
    margin-top: 2px;
    accent-color: var(--test-accent);
}

.written-test-submit-button {
    width: 100%;
    min-height: 45px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 13px;
    border: 0;
    border-radius: 13px;
    color: #ffffff;
    background: var(--test-gradient);
    box-shadow: 0 11px 25px rgba(0,0,0,0.18);
    font-size: 0.72rem;
    font-weight: 800;
    text-decoration: none;
}

.written-test-submit-button:hover {
    color: #ffffff;
    filter: brightness(1.06);
}

.written-test-error {
    margin-top: 7px;
    color: #FCA5A5;
    font-size: 0.64rem;
}

.written-login-notice {
    text-align: center;
}

.written-login-notice > i {
    color: var(--test-accent);
    font-size: 1.5rem;
}

.written-login-notice p {
    margin: 0.7rem 0 1rem;
    color: rgba(255,255,255,0.45);
    font-size: 0.69rem;
    line-height: 1.55;
}

.written-login-link {
    display: inline-flex;
    margin-top: 10px;
    color: rgba(255,255,255,0.52);
    font-size: 0.66rem;
}

@media (max-width: 900px) {
    .written-test-layout {
        grid-template-columns: 1fr;
    }

    .written-test-submit-card {
        position: static;
    }
}

@media (max-width: 575px) {
    .written-test-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .written-test-icon {
        width: 58px;
        height: 58px;
        flex-basis: 58px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input =
        document.getElementById('answer_images');

    const preview =
        document.getElementById(
            'writtenImagePreview'
        );

    if (!input || !preview) {
        return;
    }

    input.addEventListener('change', () => {
        preview.innerHTML = '';

        Array.from(input.files)
            .slice(0, 5)
            .forEach(file => {
                const item =
                    document.createElement('div');

                item.className =
                    'written-preview-item';

                const image =
                    document.createElement('img');

                image.alt =
                    'Aperçu de la réponse';

                image.src =
                    URL.createObjectURL(file);

                item.appendChild(image);
                preview.appendChild(item);
            });
    });
});
</script>

@endsection
