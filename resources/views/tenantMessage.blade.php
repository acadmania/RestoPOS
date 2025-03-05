<form method="post" action="/tenant/message">

    <input type="text" name="message" required placeholder="Message*">
    <input type="hidden" name="id" required value="{{$id}}">

    @csrf
<button type="submit">ADD</button>
</form>

