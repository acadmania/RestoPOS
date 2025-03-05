<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>DB NAME</th>
            <th>Date <br> (YYY-MM-DD)</th>
            <th>Domain</th>
            <th>Delete</th>
        </tr>
       
    </thead>
    <tbody>
        @foreach($tenants as $t)
            <tr>
                <td>{{$t->id}}</td>
                <td>{{$t->name}}</td>
                <td> {{$t->tenancy_db_name}}</td>
                <td>   {{$t->created_at}}</td>
                <td>   {{$t->domains->pluck('domain')}}</td>
              {{--  <td> <a href="/delete/tenant/{{$t->id}}">Delete</a></td>--}}
           
         </tr>
        @endforeach
    </tbody>
</table>

<a href="/add-new-client">ADD NEW CLIENT</a>


<style>
  table {
  
  border-collapse: collapse;
  }
td, th {
  border: 1px solid #000000;
  text-align: left;
  padding: 8px;
}
</style>