<html>

    <tr>
	    <!--  Bold -->
	    <td><b>Name</b></td>
	    <td><b>Position</b></td>
	    <td><b>Type</b></td>
	    <td><b>Date</b></td>
	    <td><b>Check In</b></td>
	    <td><b>Check Out</b></td>
	    <!-- <td><b>Overtime</b></td> -->
	</tr>

    @foreach ($employees as $employee)
    <tr>
	    <td>{{ $employee->name }}</td>
	    <td>{{ $employee->position }}</td>
	    <td>{{ $employee->type }}</td>
	    <td>{{ $employee->day }}</td>
	    <td>{{ ($employee->time_in !="1970-01-01 00:00:00")?date('H:i',strtotime($employee->time_in)):"-" }}</td>
	    <td>{{ ($employee->time_out !="1970-01-01 00:00:00")?date('H:i',strtotime($employee->time_out)):"-" }}</td>
	    <!-- <td>{{ $employee->time_out }}</td> -->
	    <!-- <td>{{ $employee->overtime }}</td> -->
    </tr>
    @endforeach

</html>