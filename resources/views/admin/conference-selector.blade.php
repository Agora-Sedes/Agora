<div>
    <div>
        <h2>Seleccionar Conferencia</h2>
            @csrf
            @php $conferences=[["name" => "Conferencia 1"], ["name" => "Conferencia 2"]]; @endphp
            @foreach($conferences as $conference)
                 <div>
                    <a href="{{ route('dashboard', ['name' => $conference['name']]) }}">{{ $conference['name'] }}</a>
                </div>
             @endforeach
    </div>
</div>
