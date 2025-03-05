<form method="post" action="/tenant-add">
    <input type="text" name="name" required placeholder="Business Name*">
    Creation Date:<input type="date" name="date" >(leave it blank if it is today)
    <input type="text" name="domains" required placeholder="Domains seperated by (,)*">
    <input type="text" name="admin_name" required placeholder="Admin Name*">
    <input type="text" name="admin_email" required placeholder="Admin Email*">
    <input type="text" name="password" required placeholder="Password*">
    @csrf
<button type="submit">ADD</button>
</form>

