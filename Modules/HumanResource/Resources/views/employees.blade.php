<html>

    <tr>
	    <!--  Bold -->
	    <td><b>Name</b></td>
	    <td><b>Cnic</b></td>
	    <td><b>Phone</b></td>
	    <td><b>Position</b></td>
	    <td><b>Type</b></td>
	    <td><b>Salary</b></td>
	</tr>

    @foreach ($employees as $employee)
    <tr>
	    <td>{{ $employee->name }}</td>
	    <td>{{ $employee->cnic }}</td>
	    <td>{{ $employee->phone }}</td>
	    <td>{{ $employee->position }}</td>
	    <td>{{ $employee->type }}</td>
	    <td>{{ $employee->salary }}</td>
    </tr>
    @endforeach

</html>