@extends('layouts.app')

@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $talkRows = old('talks', isset($talks) ? $talks->toArray() : []);
@endphp

@section('title', $isEdit ? 'Editar jornada' : 'Crear jornada')

@section('header_action')
    @if ($isEdit)
        <a href="{{ route('intranet.conferences.dashboard', [ 'id' => $conference->id ]) }}" class="header-action">← Panel</a>
    @else
        <a href="{{ route('intranet.conferences.list') }}" class="header-action">← Lista</a>
    @endif
@endsection

@section('content')
    <section class="hero">
        <div class="container section">
            <div class="hero__inner">
                <span class="meta-row">
                    {{
                        \Carbon\Carbon::parse(old('starts_at', $conference->starts_at ?? now()))
                            ->locale('es')
                            ->isoFormat('D [de] MMMM [de] YYYY')
                    }}
                    -
                    {{
                        \Carbon\Carbon::parse(old('ends_at', $conference->ends_at ?? now()))
                            ->locale('es')
                            ->isoFormat('D [de] MMMM [de] YYYY')
                    }}
                </span>

                <h1>{{ old('title', $conference->title ?: ($isEdit ? 'Editar jornada' : 'Nueva jornada')) }}</h1>

                <p class="hero__lead">
                    {{ old('description', $conference->description ?? 'Completá los datos de la jornada y cargá su itinerario de charlas.') }}
                </p>

                <p class="badge">
                    {{ count($talkRows) }} {{ count($talkRows) === 1 ? 'charla' : 'charlas' }} en el itinerario
                </p>
            </div>
        </div>
    </section>

    <section class="container section">
        <div class="panel" style="max-width: 960px;">
            <form
                action="{{ $isEdit ? route('intranet.conferences.update', [ 'id' => $conference->id ]) : route('intranet.conferences.store') }}"
                method="post"
                class="stack"
                id="conference-form"
            >
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="field">
                    <label for="title">Título</label>
                    <input type="text" id="title" name="title" class="input" value="{{ old('title', $conference->title) }}" required>
                    @error('title') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description" class="input" rows="5" required>{{ old('description', $conference->description) }}</textarea>
                    @error('description') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div class="field">
                        <label for="starts_at">Inicio</label>
                        <input type="datetime-local" id="starts_at" name="starts_at" class="input" value="{{ old('starts_at', optional($conference->starts_at)->format('Y-m-d\TH:i')) }}" required>
                        @error('starts_at') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="field">
                        <label for="ends_at">Fin</label>
                        <input type="datetime-local" id="ends_at" name="ends_at" class="input" value="{{ old('ends_at', optional($conference->ends_at)->format('Y-m-d\TH:i')) }}" required>
                        @error('ends_at') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

            </form>
        </div>
    </section>

    <section class="container section">
        <h2 class="page-title">Itinerario</h2>
        <p class="muted" style="margin-bottom: 18px;">Agregá, editá o quitá charlas antes de guardar la jornada.</p>

        <div id="talks-list" class="accordion" style="display: grid; gap: 12px;">
            @forelse ($talkRows as $index => $talk)
                <div class="talk-item">
                    <div class="talk-item__body" style="display: block; padding: 20px;">
                        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                            <div class="field">
                                <label>Título de la charla</label>
                                <input type="text" class="input" name="talks[{{ $index }}][title]" value="{{ $talk['title'] ?? '' }}" form="conference-form" required>
                            </div>
                            <div class="field">
                                <label>Invitado presentador</label>
                                <input type="text" class="input" name="talks[{{ $index }}][speaker]" value="{{ $talk['speaker'] ?? '' }}" form="conference-form" required>
                            </div>
                        </div>

                        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 16px;">
                            <div class="field">
                                <label>Inicio</label>
                                <input type="datetime-local" class="input" name="talks[{{ $index }}][starts_at]" value="{{ isset($talk['starts_at']) ? \Carbon\Carbon::parse($talk['starts_at'])->format('Y-m-d\TH:i') : '' }}" form="conference-form" required>
                            </div>
                            <div class="field">
                                <label>Fin</label>
                                <input type="datetime-local" class="input" name="talks[{{ $index }}][ends_at]" value="{{ isset($talk['ends_at']) ? \Carbon\Carbon::parse($talk['ends_at'])->format('Y-m-d\TH:i') : '' }}" form="conference-form" required>
                            </div>
                        </div>

                        <div class="field" style="margin-top: 16px;">
                            <label>Descripción</label>
                            <textarea class="input" rows="4" name="talks[{{ $index }}][description]" form="conference-form" required>{{ $talk['description'] ?? '' }}</textarea>
                        </div>

                        <div class="field" style="margin-top: 16px;">
                            <label>Biografía del invitado</label>
                            <textarea class="input" rows="4" name="talks[{{ $index }}][speaker_background]" form="conference-form" required>{{ $talk['speaker_background'] ?? '' }}</textarea>
                        </div>

                        <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
                            <button type="button" class="header-action" data-remove-talk>Quitar charla</button>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>

        <template id="talk-template">
            <div class="talk-item">
                <div class="talk-item__body" style="display: block; padding: 20px;">
                    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                        <div class="field">
                            <label>Título de la charla</label>
                            <input type="text" class="input" data-talk-field="title" required>
                        </div>
                        <div class="field">
                            <label>Speakers</label>
                            <input type="text" class="input" data-talk-field="speaker" required>
                        </div>
                    </div>

                    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 16px;">
                        <div class="field">
                            <label>Inicio</label>
                            <input type="datetime-local" class="input" data-talk-field="starts_at" required>
                        </div>
                        <div class="field">
                            <label>Fin</label>
                            <input type="datetime-local" class="input" data-talk-field="ends_at" required>
                        </div>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label>Descripción</label>
                        <textarea class="input" rows="4" data-talk-field="description" required></textarea>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label>Biografía del speaker</label>
                        <textarea class="input" rows="4" data-talk-field="speaker_background" required></textarea>
                    </div>

                    <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
                        <button type="button" class="header-action" data-remove-talk>Quitar charla</button>
                    </div>
                </div>
            </div>
        </template>
    </section>
    <section class="container section--tight">
        <div class="panel cta" style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="button" class="btn btn--ghost" id="add-talk">Agregar charla</button>
            </div>
        </div>
    </section>

    <section class="container section--tight">
        <div class="panel cta" style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: space-between; align-items: center;">
            <div>
                <h3>Guardado de jornada</h3>
                <p>La jornada se guarda junto con todo el itinerario cargado arriba.</p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="submit" form="conference-form" class="btn btn--primary">{{ $isEdit ? 'Actualizar jornada' : 'Crear jornada' }}</button>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('conference-form');
        const list = document.getElementById('talks-list');
        const template = document.getElementById('talk-template');
        const addTalkBtn = document.getElementById('add-talk');

        const renumber = () => {
            [...list.querySelectorAll('.talk-item')].forEach((item, index) => {
                item.querySelectorAll('[name^="talks["]').forEach((field) => {
                    field.name = field.name.replace(/talks\[\d+\]/, `talks[${index}]`);
                });
            });
        };

        const wireRemove = (item) => {
            item.querySelectorAll('[data-remove-talk]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    item.remove();
                    renumber();
                });
            });
        };

        [...list.querySelectorAll('.talk-item')].forEach(wireRemove);

        addTalkBtn.addEventListener('click', () => {
            const node = template.content.firstElementChild.cloneNode(true);
            const index = list.querySelectorAll('.talk-item').length;

            node.querySelectorAll('[data-talk-field]').forEach((field) => {
                field.name = `talks[${index}][${field.dataset.talkField}]`;
                field.setAttribute('form', 'conference-form');
            });

            wireRemove(node);
            list.appendChild(node);
        });
    })();
</script>
@endpush
