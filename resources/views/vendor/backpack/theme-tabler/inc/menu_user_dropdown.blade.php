<form method="post" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-link">Log out</button>
</form>
