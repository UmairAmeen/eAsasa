<html>

    <tr>
	    <!--  Bold -->
	    <td><b>Name</b></td>
	    <td><b>Position</b></td>
	    <td><b>Type</b></td>
	    <td><b>Bonus Count</b></td>
	    <td><b>Total Bonus</b></td>
	</tr>

    @foreach ($employees as $employee)
    <tr>
	    <td>{{ $employee->name }}</td>
	    <td>{{ $employee->position }}</td>
	    <td>{{ $employee->type }}</td>
	    <td>{{ $employee->bonus_count }}</td>
	    <td>{{ $employee->bonus_total }}</td>
    </tr>
    @endforeach

</html>