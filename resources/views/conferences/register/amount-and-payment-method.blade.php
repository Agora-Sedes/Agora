@extends('layouts.app')

@section('title', 'Comprar entradas')

@section('content')
<div class="center-page">
    <div class="panel">
        <h1 class="page-title">¿Cuántas entradas?</h1>

        <form action="{{ route('conferences.register.step-2', [ 'id' => $conference->id ]) }}" method="POST" class="stack">
            @csrf
            <div class="field">
                <label for="entry">Cantidad</label>
                <select name="amount" id="entry" class="select">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <div class="field">
                <label>Método de pago</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" id="cash" name="payment_method" value="cash" required>
                        Efectivo
                    </label>
                    <label class="radio-option">
                        <input type="radio" id="mp" name="payment_method" value="mp" required>
                        Mercado Pago
                    </label>
                </div>
            </div>

            <p class="muted" style="text-align: center; font-size: 1.25rem;">
                Total:
                <span id="total-display"></span>
            </p>

            <button type="submit" class="btn btn--primary btn--block">Siguiente</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const price = {{ $conference->price }};
    const select = document.querySelector('select[name="amount"]');
    const totalDisplay = document.getElementById('total-display');

    const updateTotal = () => {
        const amount = parseInt(select.value, 10);
        totalDisplay.textContent = `$${(amount*price).toLocaleString('es-AR')} (${amount} × $${price.toLocaleString('es-AR')})`;
    };

    select.addEventListener('change', updateTotal);
    updateTotal();
</script>
@endpush
@endsection
