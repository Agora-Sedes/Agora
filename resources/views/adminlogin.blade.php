<div>
   <h2>Admin Login</h2>
   <form method="POST" action="/login">
    @csrf
    <div>
        <label for="token">Admin Token</label>
        <input type="password" name="token" id="token" required>
        @error('token')
            <div>{{ $message }}</div>
        @enderror
    </div>
    <button type="submit">Iniciar Sesion</button>
</form>
</div>
