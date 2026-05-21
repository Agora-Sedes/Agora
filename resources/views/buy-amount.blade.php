<div>
    <h1>Cuantos vas a comprar</h1>

    <form action="/buy" method="post">
        @csrf
        <label for="entry">Cantidad:</label>
        <select name="entry" id="entry">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <div>
            <input type="radio" id="cash" name="payment_method" value="cash" required>
            <label for="cash">Efectivo</label>
            <input type="radio" id="mp" name="payment_method" value="mp" required>
            <label for="mp">Mercado Pago</label>
        </div>
        <button type="submit">Siguiente</button>
    </form>
</div>
