@extends('layouts.app')

@section('title', 'Inscritos')

@section('header_action')
    <a href="{{ route('intranet.conferences.dashboard', [ 'id' => $conference->id ]) }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="container section">
    <p class="eyebrow"><span></span> {{ $conference->title }}</p>
    <h2 class="page-title" style="margin-top: 12px;">Inscritos</h2>

    @if ($attendants->isEmpty())
        <div class="empty-state card" style="padding: 48px 24px;">
            <h2>Sin inscriptos todavía</h2>
            <p>Cuando alguien se registre, aparecerá en este listado.</p>
        </div>
    @else
        <div class="card" style="overflow-x: auto;">
            <table class="confirmation-list" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="muted" style="text-align: left;">Nombre completo</th>
                        <th class="muted" style="text-align: left;">DNI</th>
                        <th class="muted" style="text-align: left;">Email</th>
                        <th class="muted" style="text-align: left;">Teléfono</th>
                        <th class="muted" style="text-align: left;">Estado</th>
                        <th class="muted" style="text-align: left;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendants as $attendant)
                        <tr>
                            <td>{{ $attendant->full_name }}</td>
                            <td>{{ $attendant->government_id }}</td>
                            <td>{{ $attendant->email }}</td>
                            <td>{{ $attendant->phone_number }}</td>
                            <td>{{ $attendant->is_draft ? 'Pendiente' : 'Confirmado' }}</td>
                            <td style="white-space: nowrap;">
                                <a href="{{ route('intranet.conferences.attendants.edit', [ 'id' => $conference->id, 'attendantId' => $attendant->id ]) }}">Editar</a>
                                <form action="{{ route('intranet.conferences.attendants.destroy', [ 'id' => $conference->id, 'attendantId' => $attendant->id ]) }}" method="post" style="display: inline; margin-left: 12px;" onsubmit="return confirm('¿Eliminar inscripto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="header-action">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
