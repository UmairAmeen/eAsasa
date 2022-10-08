<html>

    <tr>
	    <!--  Bold -->
	    <td><b>Name</b></td>
	    <td><b>Position</b></td>
	    <td><b>Attendances</b></td>
	    <td><b>Overtime</b></td>
	    <td><b>Leaves</b></td>
	</tr>

    @foreach ($employees as $employee)
    <tr>
	    <td>{{ $employee->name }}</td>
	    <td>{{ $employee->position }}</td>
	    <td>{{ $employee->total_attendance }}</td>
	    <td>{{ $employee->total_overtime }}</td>
	    <td>{{ $employee->total_leaves }}</td>
    </tr>
    @endforeach

</html>